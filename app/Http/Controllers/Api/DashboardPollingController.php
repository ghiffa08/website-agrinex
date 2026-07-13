<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\SensorDataService;
use App\Services\CacheService;
use App\Services\EnvironmentSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardPollingController extends Controller
{
    protected DeviceService $deviceService;
    protected SensorDataService $sensorDataService;
    protected CacheService $cacheService;
    protected EnvironmentSummaryService $environmentService;

    public function __construct(
        DeviceService $deviceService,
        SensorDataService $sensorDataService,
        CacheService $cacheService,
        EnvironmentSummaryService $environmentService
    ) {
        $this->deviceService = $deviceService;
        $this->sensorDataService = $sensorDataService;
        $this->cacheService = $cacheService;
        $this->environmentService = $environmentService;
    }

    /**
     * Polling endpoint untuk dashboard
     * GET /api/v1/dashboard/poll
     */
    public function poll(Request $request)
    {
        try {
            $lastClientUpdate = (int) $request->query('last_update', 0);
            $serverLastUpdate = $this->cacheService->getDashboardLastUpdate();

            // Check if there are changes
            if ($lastClientUpdate >= $serverLastUpdate) {
                return response()->json([
                    'success' => true,
                    'has_changes' => false,
                    'last_update' => $serverLastUpdate,
                ]);
            }

            // Ada perubahan, kirim data lengkap
            $devicesData = $this->deviceService->getAllDevicesWithLatestData();
            $environmentSummary = $this->environmentService->getEnvironmentSummary();

            return response()->json([
                'success' => true,
                'has_changes' => true,
                'last_update' => $serverLastUpdate ?: now()->timestamp,
                'data' => [
                    'devices' => $devicesData,
                    'environment' => $environmentSummary,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard polling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Polling failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Polling ringan untuk status devices saja
     * GET /api/v1/dashboard/poll-status
     */
    public function pollStatus(Request $request)
    {
        try {
            $lastClientUpdate = (int) $request->query('last_update', 0);
            $serverLastUpdate = $this->cacheService->getDashboardLastUpdate();

            if ($lastClientUpdate >= $serverLastUpdate) {
                return response()->json([
                    'success' => true,
                    'has_changes' => false,
                    'last_update' => $serverLastUpdate,
                ]);
            }

            // Hanya kirim status singkat tanpa data sensor lengkap
            $devices = $this->cacheService->remember(
                'dashboard_status_only',
                CacheService::TTL_SHORT,
                fn() => $this->deviceService->getDevicesStatusOnly()
            );

            return response()->json([
                'success' => true,
                'has_changes' => true,
                'last_update' => $serverLastUpdate ?: now()->timestamp,
                'data' => [
                    'devices' => $devices,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Status polling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Status polling failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get environment summary (aggregated sensor + BMKG weather)
     * GET /api/v1/dashboard/environment
     */
    public function environment(Request $request)
    {
        try {
            $summary = $this->environmentService->getEnvironmentSummary();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);

        } catch (\Exception $e) {
            Log::error('Environment summary error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch environment summary: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Status endpoint (deprecated - use poll instead)
     * GET /api/v1/dashboard/status
     */
    public function status()
    {
        return response()->json([
            'success' => true,
            'message' => 'This endpoint is deprecated. Use /api/v1/dashboard/poll instead.',
            'timestamp' => now()->timestamp,
        ]);
    }
}
