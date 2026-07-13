<?php

namespace App\Services;

use App\Models\SensorData;
use Carbon\Carbon;

class DeviceService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get chart data (last 100 readings) for a specific node.
     */
    public function getChartData(int|string $deviceId): array
    {
        return $this->cacheService->remember(
            "chart_data_{$deviceId}", 
            CacheService::TTL_SHORT, 
            function () use ($deviceId) {
                $rows = SensorData::where('device_id', $deviceId)
                    ->orderBy('recorded_at', 'desc')
                    ->limit(100)
                    ->get(['recorded_at', 'temperature', 'soil_moisture'])
                    ->sortBy('recorded_at')
                    ->values();

                $labels       = [];
                $temperature  = [];
                $soilMoisture = [];

                foreach ($rows as $row) {
                    $labels[]       = Carbon::parse($row->recorded_at)->format('H:i');
                    $temperature[]  = (float) $row->temperature;
                    $soilMoisture[] = (float) $row->soil_moisture;
                }

                return [
                    'labels'   => $labels,
                    'datasets' => [
                        'temperature'   => $temperature,
                        'soil_moisture' => $soilMoisture,
                    ],
                ];
            }
        );
    }

    /**
     * Get irrigation sessions for a specific device.
     */
    public function getIrrigationSessions(int|string $deviceId): array
    {
        return $this->cacheService->remember(
            "irrigation_sessions_{$deviceId}", 
            CacheService::TTL_MEDIUM, 
            function () use ($deviceId) {
                $hasTable = \Illuminate\Support\Facades\Schema::hasTable('irrigation_logs');
                if (!$hasTable) {
                    return ['sessions' => [], 'summary' => null];
                }

                $sessions = \Illuminate\Support\Facades\DB::table('valve_logs')
                    ->join('irrigation_logs', 'valve_logs.irrigation_log_id', '=', 'irrigation_logs.id')
                    ->where('valve_logs.device_id', $deviceId)
                    ->orderBy('irrigation_logs.started_at', 'desc')
                    ->limit(20)
                    ->select('irrigation_logs.id as session_id', 'irrigation_logs.started_at', 'irrigation_logs.ended_at', 'irrigation_logs.status', 'valve_logs.valve_status')
                    ->get()
                    ->toArray();

                return [
                    'sessions' => $sessions,
                    'summary'  => ['total_sessions' => count($sessions)],
                ];
            }
        );
    }

    /**
     * Get usage history (last 7 days) for a specific device.
     */
    public function getUsageHistory(int|string $deviceId): array
    {
        return $this->cacheService->remember(
            "usage_history_{$deviceId}", 
            CacheService::TTL_MEDIUM, 
            function () use ($deviceId) {
                $hasTable = \Illuminate\Support\Facades\Schema::hasTable('irrigation_logs');
                if (!$hasTable) {
                    return ['history' => []];
                }

                $hasVolume = \Illuminate\Support\Facades\Schema::hasColumn('valve_logs', 'volume_ml');
                $volumeSql = $hasVolume ? 'SUM(valve_logs.volume_ml)' : '0';

                $history = \Illuminate\Support\Facades\DB::table('valve_logs')
                    ->join('irrigation_logs', 'valve_logs.irrigation_log_id', '=', 'irrigation_logs.id')
                    ->where('valve_logs.device_id', $deviceId)
                    ->where('irrigation_logs.started_at', '>=', Carbon::now()->subDays(7))
                    ->groupByRaw('DATE(irrigation_logs.started_at)')
                    ->orderByRaw('DATE(irrigation_logs.started_at) ASC')
                    ->selectRaw('DATE(irrigation_logs.started_at) as date, COUNT(valve_logs.id) as count, ' . $volumeSql . ' as total_volume_ml')
                    ->get()
                    ->toArray();

                return [
                    'history' => $history,
                ];
            }
        );
    }

    /**
     * Get sleep history for device (last 7 days)
     * Tracks when device enters/exits sleep mode
     */
    public function getSleepHistory(int|string $deviceId): array
    {
        return $this->cacheService->remember(
            "sleep_history_{$deviceId}",
            CacheService::TTL_MEDIUM,
            function () use ($deviceId) {
                // Get sleep/wake events from sensor data
                // Assumption: device is "sleeping" when there's a gap > 15 minutes between readings
                $allReadings = SensorData::where('device_id', $deviceId)
                    ->where('recorded_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('recorded_at', 'asc')
                    ->get(['recorded_at', 'battery_voltage']);

                $history = [];
                $prevReading = null;

                foreach ($allReadings as $reading) {
                    $currentTime = Carbon::parse($reading->recorded_at);

                    if ($prevReading) {
                        $prevTime = Carbon::parse($prevReading->recorded_at);
                        $gapMinutes = $prevTime->diffInMinutes($currentTime);

                        // If gap > 15 minutes, device was sleeping
                        if ($gapMinutes > 15) {
                            $durationMinutes = $gapMinutes;

                            $history[] = [
                                'sleep_start' => $prevTime->format('Y-m-d H:i:s'),
                                'sleep_end' => $currentTime->format('Y-m-d H:i:s'),
                                'duration_minutes' => $durationMinutes,
                                'duration_formatted' => $this->formatDuration($durationMinutes),
                                'battery_before' => $prevReading->battery_voltage ? (float) $prevReading->battery_voltage : null,
                                'battery_after' => $reading->battery_voltage ? (float) $reading->battery_voltage : null,
                            ];
                        }
                    }

                    $prevReading = $reading;
                }

                return array_reverse($history); // Most recent first
            }
        );
    }

    /**
     * Format duration in human-readable format
     */
    private function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} jam";
        }

        return "{$hours} jam {$remainingMinutes} menit";
    }

    /**
     * Get all devices with latest sensor data (for polling)
     */
    public function getAllDevicesWithLatestData(): array
    {
        return $this->cacheService->remember(
            CacheService::KEY_DASHBOARD_DEVICES, 
            CacheService::TTL_SHORT, 
            function () {
                $devices = \Illuminate\Support\Facades\DB::table('devices')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();

                $result = [];
                foreach ($devices as $device) {
                    $latestData = SensorData::where('device_id', $device->id)
                        ->orderBy('recorded_at', 'desc')
                        ->first();

                    $result[] = [
                        'id' => $device->id,
                        'name' => $device->name,
                        'location' => $device->location ?? 'Unknown',
                        'is_active' => (bool) $device->is_active,
                        'latest_data' => $latestData ? [
                            'temperature' => (float) $latestData->temperature,
                            'soil_moisture' => (float) $latestData->soil_moisture,
                            'humidity' => (float) ($latestData->humidity ?? 0),
                            'recorded_at' => $latestData->recorded_at,
                        ] : null,
                    ];
                }

                return $result;
            }
        );
    }

    /**
     * Get devices status only (lightweight polling)
     */
    public function getDevicesStatusOnly(): array
    {
        $devices = \Illuminate\Support\Facades\DB::table('devices')
            ->where('is_active', true)
            ->select('id', 'name', 'is_active')
            ->get();

        $result = [];
        foreach ($devices as $device) {
            $hasRecentData = SensorData::where('device_id', $device->id)
                ->where('recorded_at', '>=', Carbon::now()->subMinutes(10))
                ->exists();

            $result[] = [
                'id' => $device->id,
                'name' => $device->name,
                'online' => $hasRecentData,
            ];
        }

        return $result;
    }
}
