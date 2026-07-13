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
     * Get irrigation sessions for a specific device with period filter
     * @param string $period 'today', 'week', 'month'
     */
    public function getIrrigationSessions(int|string $deviceId, string $period = 'today'): array
    {
        try {
            $hasTable = \Illuminate\Support\Facades\Schema::hasTable('irrigation_logs');
            if (!$hasTable) {
                return ['sessions' => [], 'summary' => ['total_sessions' => 0]];
            }

            $hasValveLogs = \Illuminate\Support\Facades\Schema::hasTable('valve_logs');
            if (!$hasValveLogs) {
                return ['sessions' => [], 'summary' => ['total_sessions' => 0]];
            }

            $dateRange = $this->getDateRange($period);
            
            $sessions = \Illuminate\Support\Facades\DB::table('valve_logs')
                ->join('irrigation_logs', 'valve_logs.irrigation_log_id', '=', 'irrigation_logs.id')
                ->where('valve_logs.device_id', $deviceId)
                ->whereBetween('irrigation_logs.started_at', [$dateRange['start'], $dateRange['end']])
                ->orderBy('irrigation_logs.started_at', 'desc')
                ->limit(50)
                ->select('irrigation_logs.id as session_id', 'irrigation_logs.started_at', 'irrigation_logs.ended_at', 'irrigation_logs.status', 'valve_logs.valve_status')
                ->get()
                ->toArray();

            return [
                'sessions' => $sessions,
                'summary'  => ['total_sessions' => count($sessions)],
            ];
        } catch (\Exception $e) {
            \Log::error("Error fetching irrigation sessions for device {$deviceId}: " . $e->getMessage());
            return ['sessions' => [], 'summary' => ['total_sessions' => 0]];
        }
    }

    /**
     * Get usage history for a specific device with period filter
     * @param string $period 'today', 'week', 'month'
     */
    public function getUsageHistory(int|string $deviceId, string $period = 'week'): array
    {
        $hasTable = \Illuminate\Support\Facades\Schema::hasTable('irrigation_logs');
        if (!$hasTable) {
            return ['history' => []];
        }

        $dateRange = $this->getDateRange($period);
        $hasVolume = \Illuminate\Support\Facades\Schema::hasColumn('valve_logs', 'volume_ml');
        $volumeSql = $hasVolume ? 'SUM(valve_logs.volume_ml)' : '0';

        $history = \Illuminate\Support\Facades\DB::table('valve_logs')
            ->join('irrigation_logs', 'valve_logs.irrigation_log_id', '=', 'irrigation_logs.id')
            ->where('valve_logs.device_id', $deviceId)
            ->whereBetween('irrigation_logs.started_at', [$dateRange['start'], $dateRange['end']])
            ->groupByRaw('DATE(irrigation_logs.started_at)')
            ->orderByRaw('DATE(irrigation_logs.started_at) DESC')
            ->selectRaw('DATE(irrigation_logs.started_at) as date, COUNT(valve_logs.id) as count, ' . $volumeSql . ' as total_volume_ml')
            ->get()
            ->toArray();

        return [
            'history' => $history,
        ];
    }

    /**
     * Get sleep history for device with period filter
     * Tracks when device enters/exits sleep mode
     * @param string $period 'today', 'week', 'month'
     */
    public function getSleepHistory(int|string $deviceId, string $period = 'week'): array
    {
        try {
            $dateRange = $this->getDateRange($period);
            
            // Get sleep data from adaptive_sleep_duration field in sensor_data
            $readings = SensorData::where('device_id', $deviceId)
                ->whereBetween('recorded_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('adaptive_sleep_duration')
                ->where('adaptive_sleep_duration', '>', 0)
                ->orderBy('recorded_at', 'desc')
                ->select('recorded_at', 'adaptive_sleep_duration', 'voltage_v', 'battery_pct')
                ->get();

            $history = [];

            foreach ($readings as $reading) {
                $sleepDurationSeconds = (int) $reading->adaptive_sleep_duration;
                $sleepDurationMinutes = round($sleepDurationSeconds / 60);
                
                $wakeTime = Carbon::parse($reading->recorded_at);
                $sleepTime = $wakeTime->copy()->subSeconds($sleepDurationSeconds);

                $history[] = [
                    'sleep_start' => $sleepTime->format('Y-m-d H:i:s'),
                    'sleep_end' => $wakeTime->format('Y-m-d H:i:s'),
                    'duration_minutes' => $sleepDurationMinutes,
                    'duration_formatted' => $this->formatDuration($sleepDurationMinutes),
                    'battery_voltage' => $reading->voltage_v ? (float) $reading->voltage_v : null,
                    'battery_pct' => $reading->battery_pct ? (int) $reading->battery_pct : null,
                ];
            }

            return $history;
        } catch (\Exception $e) {
            \Log::error("Error fetching sleep history for device {$deviceId}: " . $e->getMessage());
            return [];
        }
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
                    ->orderBy('id', 'asc')  // Order by node_id (id) ascending
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

    /**
     * Get battery history with voltage and percentage
     * @param string $period 'today', 'week', 'month'
     */
    public function getBatteryHistory(int|string $deviceId, string $period = 'week'): array
    {
        try {
            $dateRange = $this->getDateRange($period);
            
            // Get battery data from sensor_data table with new fields
            $readings = SensorData::where('device_id', $deviceId)
                ->whereBetween('recorded_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('voltage_v')
                ->orderBy('recorded_at', 'desc')
                ->select('recorded_at', 'voltage_v', 'battery_pct', 'current_ma', 'power_mw')
                ->get();

            $history = [];
            $voltages = [];

            foreach ($readings as $reading) {
                $voltage = (float) $reading->voltage_v;
                $percentage = $reading->battery_pct ? (int) $reading->battery_pct : $this->calculateBatteryPercentage($voltage);
                
                $history[] = [
                    'recorded_at' => $reading->recorded_at,
                    'voltage' => round($voltage, 2),
                    'percentage' => $percentage,
                    'current_ma' => $reading->current_ma ? round((float) $reading->current_ma, 2) : null,
                    'power_mw' => $reading->power_mw ? round((float) $reading->power_mw, 2) : null,
                    'status' => $this->getBatteryStatus($percentage),
                    'timestamp' => Carbon::parse($reading->recorded_at)->format('Y-m-d H:i:s'),
                ];

                $voltages[] = $voltage;
            }

            // Calculate stats
            $stats = null;
            if (count($voltages) > 0) {
                $stats = [
                    'avg_voltage' => round(array_sum($voltages) / count($voltages), 2),
                    'min_voltage' => round(min($voltages), 2),
                    'max_voltage' => round(max($voltages), 2),
                    'avg_percentage' => $this->calculateBatteryPercentage(array_sum($voltages) / count($voltages)),
                    'readings_count' => count($voltages),
                ];
            }

            return [
                'history' => $history,
                'stats' => $stats,
            ];
        } catch (\Exception $e) {
            \Log::error("Error fetching battery history for device {$deviceId}: " . $e->getMessage());
            return [
                'history' => [],
                'stats' => null,
            ];
        }
    }

    /**
     * Calculate battery percentage from voltage
     * LiPo battery: 3.0V (0%) to 4.2V (100%)
     */
    private function calculateBatteryPercentage(float $voltage): int
    {
        $minVoltage = 3.0;
        $maxVoltage = 4.2;
        
        if ($voltage <= $minVoltage) return 0;
        if ($voltage >= $maxVoltage) return 100;
        
        $percentage = (($voltage - $minVoltage) / ($maxVoltage - $minVoltage)) * 100;
        return (int) round($percentage);
    }

    /**
     * Get battery status label based on percentage
     */
    private function getBatteryStatus(int $percentage): string
    {
        if ($percentage >= 80) return 'Baik';
        if ($percentage >= 50) return 'Cukup';
        if ($percentage >= 20) return 'Rendah';
        return 'Kritis';
    }

    /**
     * Get date range based on period
     */
    private function getDateRange(string $period): array
    {
        $now = Carbon::now();
        
        return match($period) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'week' => [
                'start' => $now->copy()->subDays(7)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'month' => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            default => [
                'start' => $now->copy()->subDays(7)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }
}
