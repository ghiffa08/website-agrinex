<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Device Detail API Controller
 * Endpoint untuk detail device: sleep history, irrigation sessions, usage history, chart data
 */
class DeviceDetailController extends Controller
{
    protected DeviceService $deviceService;
    protected CacheService $cacheService;

    public function __construct(DeviceService $deviceService, CacheService $cacheService)
    {
        $this->deviceService = $deviceService;
        $this->cacheService = $cacheService;
    }

    /**
     * Get sleep history for device with time period filter
     * @param string $deviceId
     * @param Request $request - period: 'today', 'week', 'month' (default: 'week')
     */
    public function sleepHistory(string $deviceId, Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'week');
            
            $history = $this->deviceService->getSleepHistory($deviceId, $period);

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period,
                'history' => $history ?? []
            ]);
        } catch (\Exception $e) {
            \Log::error("DeviceDetailController::sleepHistory error: " . $e->getMessage());
            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period ?? 'week',
                'history' => []
            ]);
        }
    }

    /**
     * Get irrigation sessions with time period filter
     * @param string $deviceId
     * @param Request $request - period: 'today', 'week', 'month' (default: 'today')
     */
    public function irrigationSessions(string $deviceId, Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'today');
            
            $data = $this->deviceService->getIrrigationSessions($deviceId, $period);

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period,
                'sessions' => $data['sessions'] ?? [],
                'summary' => $data['summary'] ?? ['total_sessions' => 0]
            ]);
        } catch (\Exception $e) {
            \Log::error("DeviceDetailController::irrigationSessions error: " . $e->getMessage());
            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period ?? 'today',
                'sessions' => [],
                'summary' => ['total_sessions' => 0]
            ]);
        }
    }

    /**
     * Get usage history (last 7 days)
     */
    public function usageHistory(string $deviceId): JsonResponse
    {
        try {
            $data = $this->cacheService->remember(
                "device_usage_history_{$deviceId}",
                CacheService::TTL_MEDIUM,
                fn() => $this->deviceService->getUsageHistory($deviceId)
            );

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'history' => $data['history'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chart data (last 24 hours)
     */
    public function chartData(string $deviceId, Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'today');

            $data = $this->cacheService->remember(
                "device_chart_data_{$deviceId}_{$period}",
                CacheService::TTL_SHORT,
                fn() => $this->deviceService->getChartData($deviceId, $period)
            );

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period,
                'labels' => $data['labels'] ?? [],
                'datasets' => $data['datasets'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get battery history with voltage and percentage
     * @param string $deviceId
     * @param Request $request - period: 'today', 'week', 'month' (default: 'week')
     */
    public function batteryHistory(string $deviceId, Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'week');
            
            $data = $this->deviceService->getBatteryHistory($deviceId, $period);

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period,
                'history' => $data['history'] ?? [],
                'stats' => $data['stats'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error("DeviceDetailController::batteryHistory error: " . $e->getMessage());
            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'period' => $period ?? 'week',
                'history' => [],
                'stats' => null
            ]);
        }
    }
}
