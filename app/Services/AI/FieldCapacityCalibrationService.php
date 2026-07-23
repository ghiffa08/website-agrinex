<?php

namespace App\Services\AI;

use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FieldCapacityCalibrationService
{
    protected GeminiService $gemini;
    
    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }
    
    /**
     * Start calibration process - user confirms they will saturate soil
     */
    public function startCalibration(int $deviceId): array
    {
        $device = Device::findOrFail($deviceId);
        
        // Check if already in progress
        if (in_array($device->ai_calibration_status, ['user_saturating', 'waiting_24h', 'analyzing'])) {
            return [
                'success' => false,
                'message' => 'Kalibrasi sedang berlangsung',
                'status' => $device->ai_calibration_status
            ];
        }
        
        $device->update([
            'ai_calibration_status' => 'user_saturating',
            'ai_calibration_started_at' => now(),
            'ai_saturation_completed_at' => null,
            'ai_calibration_completed_at' => null,
            'ai_analysis_iteration' => 0
        ]);
        
        Log::info("AI Calibration started for device {$deviceId}");
        
        return [
            'success' => true,
            'message' => 'Kalibrasi dimulai. Silakan siram tanah hingga jenuh.',
            'status' => 'user_saturating',
            'started_at' => now()->toIso8601String()
        ];
    }
    
    /**
     * User confirms soil saturation is complete
     */
    public function confirmSaturation(int $deviceId): array
    {
        $device = Device::findOrFail($deviceId);
        
        if ($device->ai_calibration_status !== 'user_saturating') {
            return [
                'success' => false,
                'message' => 'Device tidak dalam status user_saturating'
            ];
        }
        
        $device->update([
            'ai_calibration_status' => 'waiting_24h',
            'ai_saturation_completed_at' => now()
        ]);
        
        Log::info("Saturation confirmed for device {$deviceId}, waiting 24h");
        
        return [
            'success' => true,
            'message' => 'Tanah jenuh dikonfirmasi. Tunggu 24 jam untuk drainase gravitasi.',
            'status' => 'waiting_24h',
            'saturation_at' => now()->toIso8601String(),
            'analysis_ready_at' => now()->addHours(24)->toIso8601String()
        ];
    }
    
    /**
     * Check if device is ready for analysis (24h+ after saturation)
     */
    public function isReadyForAnalysis(Device $device): bool
    {
        if (!$device->ai_saturation_completed_at) {
            return false;
        }
        
        $hoursSince = now()->diffInHours($device->ai_saturation_completed_at);
        return $hoursSince >= 24;
    }
    
    /**
     * Run AI analysis on historical data
     */
    public function analyzeFieldCapacity(int $deviceId, bool $force = false): array
    {
        $device = Device::findOrFail($deviceId);
        
        // Check if ready for analysis
        if (!$force && $device->ai_calibration_status === 'waiting_24h') {
            if (!$this->isReadyForAnalysis($device)) {
                $hoursRemaining = 24 - now()->diffInHours($device->ai_saturation_completed_at);
                return [
                    'success' => false,
                    'message' => "Tunggu {$hoursRemaining} jam lagi untuk drainase gravitasi",
                    'hours_remaining' => $hoursRemaining
                ];
            }
        }
        
        // Update status to analyzing
        $device->update(['ai_calibration_status' => 'analyzing']);
        
        try {
            // Determine analysis window based on iteration
            $iteration = $device->ai_analysis_iteration;
            $hoursToAnalyze = 24 + ($iteration * 24); // 24h, 48h, 72h
            $hoursToAnalyze = min($hoursToAnalyze, 72); // Max 72 hours
            
            // Get historical sensor data
            $sensorData = $this->getHistoricalData($deviceId, $hoursToAnalyze);
            
            if ($sensorData->isEmpty()) {
                $device->update(['ai_calibration_status' => 'failed']);
                return [
                    'success' => false,
                    'message' => 'Data sensor tidak cukup untuk analisis (minimal 24 jam)'
                ];
            }
            
            // Prepare data for AI analysis
            $dataForAI = $this->prepareDataForAI($sensorData, $device);
            
            // Call Gemini AI
            $aiResponse = $this->callGeminiForAnalysis($dataForAI, $device);
            
            if (!$aiResponse['success']) {
                $device->update(['ai_calibration_status' => 'failed']);
                return $aiResponse;
            }
            
            // Parse and save results
            $result = $this->saveAnalysisResults($device, $aiResponse['analysis']);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error("AI Calibration analysis failed for device {$deviceId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $device->update(['ai_calibration_status' => 'failed']);
            
            return [
                'success' => false,
                'message' => 'Analisis gagal: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get historical sensor data for analysis
     */
    protected function getHistoricalData(int $deviceId, int $hours): \Illuminate\Support\Collection
    {
        return SensorData::where('device_id', $deviceId)
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at', 'asc')
            ->get(['recorded_at', 'soil_moisture', 'soil_adc', 'temperature']);
    }
    
    /**
     * Prepare data in optimal format for AI analysis
     */
    protected function prepareDataForAI($sensorData, Device $device): array
    {
        // Group data by hour untuk reduce token usage
        $hourlyData = $sensorData->groupBy(function ($item) {
            return Carbon::parse($item->recorded_at)->format('Y-m-d H:00:00');
        })->map(function ($group) {
            return [
                'timestamp' => $group->first()->recorded_at,
                'soil_moisture_avg' => round($group->avg('soil_moisture'), 2),
                'soil_adc_avg' => round($group->avg('soil_adc')),
                'soil_adc_min' => $group->min('soil_adc'),
                'soil_adc_max' => $group->max('soil_adc'),
                'temperature_avg' => round($group->avg('temperature'), 2),
                'samples_count' => $group->count()
            ];
        })->values();
        
        return [
            'device_id' => $device->id,
            'device_name' => $device->kode_perlakuan ?? "Device {$device->id}",
            'saturation_timestamp' => $device->ai_saturation_completed_at?->toIso8601String(),
            'analysis_hours' => now()->diffInHours($device->ai_saturation_completed_at ?? now()),
            'current_calibration' => [
                'soil_raw_wet' => 1200, // From config.h
                'soil_raw_dry' => 2800,
            ],
            'data_points' => $hourlyData->count(),
            'hourly_readings' => $hourlyData->toArray()
        ];
    }
    
    /**
     * Call Gemini AI for field capacity analysis
     */
    protected function callGeminiForAnalysis(array $data, Device $device): array
    {
        $prompt = $this->buildAnalysisPrompt($data, $device);
        
        Log::info("Sending analysis request to Gemini AI", [
            'device_id' => $device->id,
            'data_points' => $data['data_points']
        ]);
        
        $response = $this->gemini->generateContent($prompt, [
            'temperature' => 0.3, // Low temperature untuk consistency
            'maxOutputTokens' => 1024
        ]);
        
        if (!$response['success']) {
            return [
                'success' => false,
                'message' => 'Gemini API error: ' . ($response['error'] ?? 'Unknown error')
            ];
        }
        
        // Extract JSON from response
        $analysisJson = $this->gemini->extractJson($response['text']);
        
        if (!$analysisJson) {
            Log::error('Failed to parse Gemini response', [
                'raw_text' => $response['text']
            ]);
            
            return [
                'success' => false,
                'message' => 'Gagal mem-parse hasil analisis AI'
            ];
        }
        
        return [
            'success' => true,
            'analysis' => $analysisJson,
            'raw_response' => $response['text']
        ];
    }
    
    /**
     * Build optimized prompt for Gemini
     */
    protected function buildAnalysisPrompt(array $data, Device $device): string
    {
        $iteration = $device->ai_analysis_iteration;
        $jsonData = json_encode($data['hourly_readings'], JSON_PRETTY_PRINT);
        
        return <<<PROMPT
Kamu adalah ahli agronomi dan data scientist untuk sistem irigasi IoT AgriNex. Tugasmu menganalisis data sensor kelembaban tanah untuk menentukan Field Capacity (FC) yang akurat.

**Konteks:**
- Device: {$data['device_name']}
- User telah menyiram tanah hingga jenuh pada: {$data['saturation_timestamp']}
- Sudah berlalu {$data['analysis_hours']} jam sejak saturasi
- Iterasi analisis: #{$iteration} (0=first 24h, 1=48h, 2=72h)
- Kalibrasi sensor saat ini: Wet={$data['current_calibration']['soil_raw_wet']} ADC, Dry={$data['current_calibration']['soil_raw_dry']} ADC

**Data Sensor (Hourly Aggregated - {$data['data_points']} data points):**
```json
{$jsonData}
```

**Tugas Analisis:**
1. Identifikasi pola saturasi: Cari timestamp dimana soil_adc berada di nilai TERENDAH (tanah paling basah)
2. Deteksi drainase gravitasi: Cari titik stabilisasi 24-48 jam setelah saturasi dimana ADC naik lalu stabil
3. Tentukan Field Capacity: ADC saat tanah stabil setelah drainase gravitasi selesai
4. Deteksi Wilting Point: ADC saat tanah paling kering (nilai TERTINGGI) sebelum saturasi
5. Hitung confidence score berdasarkan:
   - Kejelasan pola saturasi (apakah ada drop ADC yang signifikan?)
   - Stabilitas setelah drainase (variance rendah = good)
   - Kecukupan data (24h=low, 48h=medium, 72h=high)

**Output (JSON only, no markdown):**
{
  "field_capacity_adc": <integer, ADC value saat field capacity>,
  "wilting_point_adc": <integer, ADC value saat wilting point>,
  "confidence_score": <float 0-100, tingkat kepercayaan analisis>,
  "saturation_detected_at": "<ISO8601 timestamp saat tanah jenuh terdeteksi>",
  "fc_detected_at": "<ISO8601 timestamp saat field capacity tercapai>",
  "analysis_quality": "<poor|fair|good|excellent>",
  "reasoning": "<penjelasan singkat 2-3 kalimat tentang hasil analisis>",
  "recommendations": [
    "<rekomendasi untuk improve accuracy jika confidence < 80%>"
  ]
}

PENTING:
- Jika data tidak cukup jelas atau pola tidak terdeteksi, set confidence_score < 60 dan berikan reasoning
- Field Capacity ADC harus LEBIH TINGGI dari saturasi point (karena tanah sudah drainage)
- Wilting Point ADC harus LEBIH TINGGI dari Field Capacity (tanah lebih kering)
- Jika iterasi = 0 (24h), confidence maksimal 70%. Untuk 48h → 85%, 72h → 95%+
PROMPT;
    }
    
    /**
     * Save analysis results to database
     */
    protected function saveAnalysisResults(Device $device, array $analysis): array
    {
        $confidenceScore = $analysis['confidence_score'] ?? 0;
        
        // Determine if we should auto-apply or need more data
        $shouldApply = $confidenceScore >= 70;
        
        if ($shouldApply) {
            // Apply the calibration
            $device->update([
                'ai_fc_raw_value' => $analysis['field_capacity_adc'],
                'ai_wp_raw_value' => $analysis['wilting_point_adc'] ?? null,
                'ai_confidence_score' => $confidenceScore,
                'ai_analysis_data' => json_encode($analysis),
                'ai_calibration_status' => 'completed',
                'ai_calibration_completed_at' => now(),
                'ai_analysis_iteration' => $device->ai_analysis_iteration + 1,
                
                // Auto-update actual calibration values
                'fc_raw_value' => $analysis['field_capacity_adc'],
                'wp_raw_value' => $analysis['wilting_point_adc'] ?? 2800,
                'fc_calibrated_at' => now(),
                'fc_calibration_status' => 'calibrated'
            ]);
            
            Log::info("AI Calibration completed for device {$device->id}", [
                'fc_adc' => $analysis['field_capacity_adc'],
                'confidence' => $confidenceScore
            ]);
            
            return [
                'success' => true,
                'message' => 'Kalibrasi berhasil! Field Capacity terdeteksi dengan confidence ' . round($confidenceScore) . '%',
                'status' => 'completed',
                'results' => $analysis
            ];
            
        } else {
            // Need more data - schedule next iteration
            $device->update([
                'ai_fc_raw_value' => $analysis['field_capacity_adc'],
                'ai_wp_raw_value' => $analysis['wilting_point_adc'] ?? null,
                'ai_confidence_score' => $confidenceScore,
                'ai_analysis_data' => json_encode($analysis),
                'ai_calibration_status' => 'waiting_24h', // Wait for more data
                'ai_analysis_iteration' => $device->ai_analysis_iteration + 1
            ]);
            
            $nextAnalysisHours = 24 * ($device->ai_analysis_iteration + 1);
            
            return [
                'success' => true,
                'message' => "Confidence rendah ({$confidenceScore}%). Menunggu {$nextAnalysisHours} jam untuk analisis lebih akurat.",
                'status' => 'needs_more_data',
                'next_analysis_at' => now()->addHours(24)->toIso8601String(),
                'results' => $analysis
            ];
        }
    }
    
    /**
     * Get calibration status for a device
     */
    public function getStatus(int $deviceId): array
    {
        $device = Device::findOrFail($deviceId);
        
        $status = [
            'device_id' => $device->id,
            'status' => $device->ai_calibration_status,
            'started_at' => $device->ai_calibration_started_at?->toIso8601String(),
            'saturation_at' => $device->ai_saturation_completed_at?->toIso8601String(),
            'completed_at' => $device->ai_calibration_completed_at?->toIso8601String(),
            'iteration' => $device->ai_analysis_iteration,
            'can_analyze' => false,
            'hours_remaining' => null
        ];
        
        // Calculate time remaining
        if ($device->ai_calibration_status === 'waiting_24h' && $device->ai_saturation_completed_at) {
            $hoursElapsed = now()->diffInHours($device->ai_saturation_completed_at);
            $nextAnalysisHour = 24 * ($device->ai_analysis_iteration + 1);
            
            if ($hoursElapsed >= $nextAnalysisHour) {
                $status['can_analyze'] = true;
            } else {
                $status['hours_remaining'] = $nextAnalysisHour - $hoursElapsed;
            }
        }
        
        // Add results if completed
        if ($device->ai_calibration_status === 'completed' && $device->ai_analysis_data) {
            $status['results'] = json_decode($device->ai_analysis_data, true);
            $status['fc_adc'] = $device->ai_fc_raw_value;
            $status['confidence'] = $device->ai_confidence_score;
        }
        
        return $status;
    }
}
