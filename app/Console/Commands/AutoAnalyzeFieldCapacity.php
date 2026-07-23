<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use App\Services\AI\FieldCapacityCalibrationService;
use Illuminate\Support\Facades\Log;

class AutoAnalyzeFieldCapacity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:analyze-field-capacity {--device-id= : Specific device ID to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-analyze field capacity for devices in waiting_24h status';

    protected FieldCapacityCalibrationService $calibrationService;

    public function __construct(FieldCapacityCalibrationService $calibrationService)
    {
        parent::__construct();
        $this->calibrationService = $calibrationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Starting AI Field Capacity Analysis...');
        
        // Get devices ready for analysis
        $query = Device::whereIn('ai_calibration_status', ['waiting_24h']);
        
        // Filter by specific device if provided
        if ($deviceId = $this->option('device-id')) {
            $query->where('id', $deviceId);
        }
        
        $devices = $query->get();
        
        if ($devices->isEmpty()) {
            $this->info('✅ No devices ready for analysis.');
            return 0;
        }
        
        $this->info("Found {$devices->count()} device(s) to analyze");
        
        $analyzed = 0;
        $skipped = 0;
        $failed = 0;
        
        foreach ($devices as $device) {
            $this->line("📊 Analyzing Device #{$device->id} ({$device->kode_perlakuan})...");
            
            // Check if ready (24h+ since saturation)
            if (!$this->calibrationService->isReadyForAnalysis($device)) {
                $hoursRemaining = 24 - now()->diffInHours($device->ai_saturation_completed_at);
                $this->warn("  ⏳ Skipped: Need {$hoursRemaining} more hours");
                $skipped++;
                continue;
            }
            
            try {
                $result = $this->calibrationService->analyzeFieldCapacity($device->id);
                
                if ($result['success']) {
                    if (isset($result['status']) && $result['status'] === 'completed') {
                        $this->info("  ✅ Completed! FC: {$result['results']['field_capacity_adc']} ADC, Confidence: {$result['results']['confidence_score']}%");
                        $analyzed++;
                    } else {
                        $this->warn("  ⚠️  Needs more data: {$result['message']}");
                        $skipped++;
                    }
                } else {
                    $this->error("  ❌ Failed: {$result['message']}");
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Exception: {$e->getMessage()}");
                Log::error("Auto-analysis failed for device {$device->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $failed++;
            }
        }
        
        $this->newLine();
        $this->info("📈 Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Analyzed & Completed', $analyzed],
                ['Skipped (not ready)', $skipped],
                ['Failed', $failed],
            ]
        );
        
        return 0;
    }
}
