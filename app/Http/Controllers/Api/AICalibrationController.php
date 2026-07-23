<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\FieldCapacityCalibrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AICalibrationController extends Controller
{
    protected FieldCapacityCalibrationService $calibrationService;
    
    public function __construct(FieldCapacityCalibrationService $calibrationService)
    {
        $this->calibrationService = $calibrationService;
    }
    
    /**
     * Start AI calibration process
     * POST /api/v1/devices/{deviceId}/ai-calibration/start
     */
    public function start($deviceId)
    {
        try {
            $result = $this->calibrationService->startCalibration($deviceId);
            
            return response()->json($result, $result['success'] ? 200 : 400);
            
        } catch (\Exception $e) {
            Log::error("Failed to start AI calibration for device {$deviceId}", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * User confirms soil saturation is complete
     * POST /api/v1/devices/{deviceId}/ai-calibration/confirm-saturation
     */
    public function confirmSaturation($deviceId)
    {
        try {
            $result = $this->calibrationService->confirmSaturation($deviceId);
            
            return response()->json($result, $result['success'] ? 200 : 400);
            
        } catch (\Exception $e) {
            Log::error("Failed to confirm saturation for device {$deviceId}", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi saturasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Trigger AI analysis (can be called manually or via cron)
     * POST /api/v1/devices/{deviceId}/ai-calibration/analyze
     */
    public function analyze($deviceId, Request $request)
    {
        try {
            $force = $request->boolean('force', false);
            
            $result = $this->calibrationService->analyzeFieldCapacity($deviceId, $force);
            
            return response()->json($result, $result['success'] ? 200 : 400);
            
        } catch (\Exception $e) {
            Log::error("Failed to analyze field capacity for device {$deviceId}", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal analisis: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get calibration status
     * GET /api/v1/devices/{deviceId}/ai-calibration/status
     */
    public function status($deviceId)
    {
        try {
            $status = $this->calibrationService->getStatus($deviceId);
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to get calibration status for device {$deviceId}", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancel ongoing calibration
     * POST /api/v1/devices/{deviceId}/ai-calibration/cancel
     */
    public function cancel($deviceId)
    {
        try {
            $device = \App\Models\Device::findOrFail($deviceId);
            
            $device->update([
                'ai_calibration_status' => 'idle',
                'ai_calibration_started_at' => null,
                'ai_saturation_completed_at' => null
            ]);
            
            Log::info("AI Calibration cancelled for device {$deviceId}");
            
            return response()->json([
                'success' => true,
                'message' => 'Kalibrasi dibatalkan'
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to cancel calibration for device {$deviceId}", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
