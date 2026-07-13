<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;

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
     * Get sleep history for device (last 7 days)
     */
    public function sleepHistory(string $deviceId): JsonResponse
    {
        try {
            $history = $this->cacheService->remember(
                "device_sleep_history_{$deviceId}",
                CacheService::TTL_MEDIUM,
                fn() => $this->deviceService->getSleepHistory($deviceId)
            );

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get irrigation sessions (today)
     */
    public function irrigationSessions(string $deviceId): JsonResponse
    {
        try {
            $data = $this->cacheService->remember(
                "device_irrigation_sessions_{$deviceId}",
                CacheService::TTL_MEDIUM,
                fn() => $this->deviceService->getIrrigationSessions($deviceId)
            );

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
                'sessions' => $data['sessions'] ?? [],
                'summary' => $data['summary'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
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
    public function chartData(string $deviceId): JsonResponse
    {
        try {
            $data = $this->cacheService->remember(
                "device_chart_data_{$deviceId}",
                CacheService::TTL_SHORT,
                fn() => $this->deviceService->getChartData($deviceId)
            );

            return response()->json([
                'success' => true,
                'device_id' => $deviceId,
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
}
