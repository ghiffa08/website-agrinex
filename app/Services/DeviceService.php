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
            $hasValveLogs = \Illuminate\Support\Facades\Schema::hasTable('valve_logs');
            
            $sessions = [];
            if ($hasTable && $hasValveLogs) {
                $dateRange = $this->getDateRange($period);
                
                $hasVolume = \Illuminate\Support\Facades\Schema::hasColumn('valve_logs', 'volume_ml');
                
                $query = \Illuminate\Support\Facades\DB::table('valve_logs')
                    ->join('irrigation_logs', 'valve_logs.irrigation_log_id', '=', 'irrigation_logs.id')
                    ->where('valve_logs.device_id', $deviceId)
                    ->whereBetween('irrigation_logs.started_at', [$dateRange['start'], $dateRange['end']])
                    ->orderBy('irrigation_logs.started_at', 'desc')
                    ->limit(50);
                    
                if ($hasVolume) {
                    $query->select('irrigation_logs.id as session_id', 'irrigation_logs.started_at', 'irrigation_logs.ended_at', 'valve_logs.valve_status', 'valve_logs.volume_ml');
                } else {
                    $query->select('irrigation_logs.id as session_id', 'irrigation_logs.started_at', 'irrigation_logs.ended_at', 'valve_logs.valve_status');
                }
                
                $sessions = $query->get()
                    ->map(fn($row) => [
                        'id' => $row->session_id,
                        'session_id' => $row->session_id,
                        'started_at' => $row->started_at,
                        'ended_at' => $row->ended_at,
                        'status' => $row->ended_at ? 'completed' : 'active',
                        'valve_status' => $row->valve_status,
                        'index' => 'Sesi',
                        'session' => 'Sesi',
                        'time' => Carbon::parse($row->started_at)->format('H:i') . ($row->ended_at ? ' - ' . Carbon::parse($row->ended_at)->format('H:i') : ''),
                        'planned_l' => round(($row->volume_ml ?? 5000) / 1000, 1),
                        'planned_volume_l' => round(($row->volume_ml ?? 5000) / 1000, 1),
                        'actual_l' => round(($row->volume_ml ?? 4800) / 1000, 1),
                        'actual_volume_l' => round(($row->volume_ml ?? 4800) / 1000, 1),
                    ])
                    ->toArray();
            }

            // Fallback to Mock Data if empty
            if (empty($sessions)) {
                $sessions = $this->generateMockIrrigationSessions($deviceId, $period);
            }

            // Calculate summary
            $totalPlanned = 0.0;
            $totalActual = 0.0;
            foreach ($sessions as $s) {
                $totalPlanned += (float)$s['planned_l'];
                $totalActual += (float)$s['actual_l'];
            }
            $efficiencyPct = $totalPlanned > 0 ? (int) round(($totalActual / $totalPlanned) * 100) : 0;

            return [
                'sessions' => $sessions,
                'summary'  => [
                    'total_sessions' => count($sessions),
                    'total_planned_l' => round($totalPlanned, 1),
                    'total_actual_l' => round($totalActual, 1),
                    'efficiency_pct' => $efficiencyPct,
                ],
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
        try {
            $hasTable = \Illuminate\Support\Facades\Schema::hasTable('irrigation_logs');
            $hasValveLogs = \Illuminate\Support\Facades\Schema::hasTable('valve_logs');
            
            $history = [];
            
            if ($hasTable && $hasValveLogs) {
                $dateRange = $this->getDateRange($period);
                $hasVolume = \Illuminate\Support\Facades\Schema::hasColumn('valve_logs', 'volume_ml');
                $volumeSql = $hasVolume ? 'SUM(valve_logs.volume_ml)' : '0';

                $history = \Illuminate\Support\Facades\DB::table('valve_logs')
                    ->join('irrigation_logs', 'valve_logs.irrigation_log_id', '=', 'irrigation_logs.id')
                    ->where('valve_logs.device_id', $deviceId)
                    ->whereBetween('irrigation_logs.started_at', [$dateRange['start'], $dateRange['end']])
                    ->groupByRaw('DATE(irrigation_logs.started_at)')
                    ->orderByRaw('DATE(irrigation_logs.started_at) DESC')
                    ->selectRaw('DATE(irrigation_logs.started_at) as date, COUNT(valve_logs.id) as sessions, ' . $volumeSql . ' as total_volume_ml')
                    ->get()
                    ->map(fn($row) => [
                        'date' => $row->date,
                        'day' => Carbon::parse($row->date)->translatedFormat('d M'),
                        'sessions' => $row->sessions,
                        'session_count' => $row->sessions,
                        'total_l' => round($row->total_volume_ml / 1000, 2),
                        'volume_l' => round($row->total_volume_ml / 1000, 2),
                    ])
                    ->toArray();
            }

            // Fallback to Mock Data if empty
            if (empty($history)) {
                $history = $this->generateMockUsageHistory($deviceId, $period);
            }

            return [
                'history' => $history,
            ];
        } catch (\Exception $e) {
            \Log::error("Error fetching usage history for device {$deviceId}: " . $e->getMessage());
            return ['history' => []];
        }
    }

    /**
     * Generate realistic mock irrigation sessions
     */
    private function generateMockIrrigationSessions(int|string $deviceId, string $period): array
    {
        $sessions = [];
        $now = Carbon::now();
        
        if ($period === 'today') {
            // Generate 3 sessions for today
            $times = [
                ['start' => '07:00:00', 'end' => '07:15:00', 'planned' => 8.5, 'actual' => 8.2],
                ['start' => '12:00:00', 'end' => '12:12:00', 'planned' => 10.0, 'actual' => 9.8],
                ['start' => '16:30:00', 'end' => '16:45:00', 'planned' => 8.5, 'actual' => 8.7],
            ];
            
            foreach ($times as $idx => $t) {
                $start = Carbon::parse($now->format('Y-m-d') . ' ' . $t['start']);
                $end = Carbon::parse($now->format('Y-m-d') . ' ' . $t['end']);
                
                if ($start->isPast()) {
                    $sessions[] = [
                        'id' => $idx + 1,
                        'index' => 'Sesi ' . ($idx + 1),
                        'session' => 'Sesi ' . ($idx + 1),
                        'time' => $start->format('H:i') . ' - ' . $end->format('H:i'),
                        'start_time' => $start->format('Y-m-d H:i:s'),
                        'ended_at' => $end->format('Y-m-d H:i:s'),
                        'status' => 'completed',
                        'valve_status' => 'OFF',
                        'planned_l' => $t['planned'],
                        'planned_volume_l' => $t['planned'],
                        'actual_l' => $t['actual'],
                        'actual_volume_l' => $t['actual'],
                    ];
                }
            }
        } elseif ($period === 'week') {
            // Generate for the last 7 days
            $idCounter = 1;
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $times = [
                    ['start' => '07:00:00', 'end' => '07:15:00', 'planned' => 8.5, 'actual' => 8.2],
                    ['start' => '12:00:00', 'end' => '12:12:00', 'planned' => 10.0, 'actual' => 9.8],
                    ['start' => '16:30:00', 'end' => '16:45:00', 'planned' => 8.5, 'actual' => 8.7],
                ];
                
                foreach ($times as $idx => $t) {
                    $start = Carbon::parse($date->format('Y-m-d') . ' ' . $t['start']);
                    $end = Carbon::parse($date->format('Y-m-d') . ' ' . $t['end']);
                    
                    if ($start->isPast()) {
                        $dayLabel = $date->translatedFormat('D H:i'); // e.g. "Sen 07:00"
                        $sessions[] = [
                            'id' => $idCounter++,
                            'index' => $dayLabel,
                            'session' => 'Sesi ' . ($idx + 1),
                            'time' => $start->format('H:i') . ' - ' . $end->format('H:i'),
                            'start_time' => $start->format('Y-m-d H:i:s'),
                            'ended_at' => $end->format('Y-m-d H:i:s'),
                            'status' => 'completed',
                            'valve_status' => 'OFF',
                            'planned_l' => $t['planned'],
                            'planned_volume_l' => $t['planned'],
                            'actual_l' => $t['actual'],
                            'actual_volume_l' => $t['actual'],
                        ];
                    }
                }
            }
        } else {
            // month
            // Generate for the last 30 days
            $idCounter = 1;
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $times = [
                    ['start' => '07:00:00', 'end' => '07:15:00', 'planned' => 8.5, 'actual' => 8.2],
                    ['start' => '16:30:00', 'end' => '16:45:00', 'planned' => 8.5, 'actual' => 8.7],
                ];
                
                foreach ($times as $idx => $t) {
                    $start = Carbon::parse($date->format('Y-m-d') . ' ' . $t['start']);
                    $end = Carbon::parse($date->format('Y-m-d') . ' ' . $t['end']);
                    
                    if ($start->isPast()) {
                        $dayLabel = $date->format('d/m H:i'); // e.g. "14/07 07:00"
                        $sessions[] = [
                            'id' => $idCounter++,
                            'index' => $dayLabel,
                            'session' => 'Sesi ' . ($idx + 1),
                            'time' => $start->format('H:i') . ' - ' . $end->format('H:i'),
                            'start_time' => $start->format('Y-m-d H:i:s'),
                            'ended_at' => $end->format('Y-m-d H:i:s'),
                            'status' => 'completed',
                            'valve_status' => 'OFF',
                            'planned_l' => $t['planned'],
                            'planned_volume_l' => $t['planned'],
                            'actual_l' => $t['actual'],
                            'actual_volume_l' => $t['actual'],
                        ];
                    }
                }
            }
        }
        
        return $sessions;
    }

    /**
     * Generate realistic mock water usage history
     */
    private function generateMockUsageHistory(int|string $deviceId, string $period): array
    {
        $history = [];
        $now = Carbon::now();
        $days = $period === 'month' ? 30 : 7;
        
        for ($i = 0; $i < $days; $i++) {
            $date = $now->copy()->subDays($i);
            
            $sessionsCount = rand(2, 3);
            $totalVolume = $sessionsCount * rand(8, 10) + (rand(0, 9) / 10.0);
            
            $history[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->translatedFormat('d M'),
                'sessions' => $sessionsCount,
                'session_count' => $sessionsCount,
                'total_l' => $totalVolume,
                'volume_l' => $totalVolume,
            ];
        }
        
        return $history;
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
                    'sleep_start_human' => $sleepTime->diffForHumans(),
                    'sleep_end' => $wakeTime->format('Y-m-d H:i:s'),
                    'sleep_end_human' => $wakeTime->diffForHumans(),
                    'duration_minutes' => $sleepDurationMinutes,
                    'duration_formatted' => $this->formatDuration($sleepDurationMinutes),
                    'battery_voltage' => $reading->voltage_v ? (float) $reading->voltage_v : null,
                    'battery_pct' => $reading->battery_pct ? (int) $reading->battery_pct : null,
                ];
            }

            // Fallback to Mock Data if empty
            if (empty($history)) {
                $history = $this->generateMockSleepHistory($deviceId, $period);
            }

            return $history;
        } catch (\Exception $e) {
            \Log::error("Error fetching sleep history for device {$deviceId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate realistic mock sleep history records
     */
    private function generateMockSleepHistory(int|string $deviceId, string $period): array
    {
        $history = [];
        $now = Carbon::now();
        
        $recordsCount = 8;
        if ($period === 'today') {
            $recordsCount = 5;
        } elseif ($period === 'month') {
            $recordsCount = 20;
        }

        for ($i = 0; $i < $recordsCount; $i++) {
            if ($period === 'today') {
                $wakeTime = $now->copy()->subHours($i * 3 + rand(0, 1))->subMinutes(rand(0, 59));
            } else {
                $wakeTime = $now->copy()->subHours($i * 12 + rand(0, 5))->subMinutes(rand(0, 59));
            }
            
            if ($wakeTime->isFuture()) {
                continue;
            }

            $durations = [120, 300, 600];
            $sleepDurationSeconds = $durations[array_rand($durations)];
            $sleepDurationMinutes = round($sleepDurationSeconds / 60);
            $sleepTime = $wakeTime->copy()->subSeconds($sleepDurationSeconds);

            $batteryPct = max(20, min(100, 85 - $i * 2 + rand(-2, 2)));
            $batteryVoltage = round(3.5 + ($batteryPct / 100) * 0.7, 2);

            $history[] = [
                'sleep_start' => $sleepTime->format('Y-m-d H:i:s'),
                'sleep_start_human' => $sleepTime->diffForHumans(),
                'sleep_end' => $wakeTime->format('Y-m-d H:i:s'),
                'sleep_end_human' => $wakeTime->diffForHumans(),
                'duration_minutes' => $sleepDurationMinutes,
                'duration_formatted' => $this->formatDuration($sleepDurationMinutes),
                'battery_voltage' => $batteryVoltage,
                'battery_pct' => $batteryPct,
            ];
        }

        return $history;
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
                    ->orderBy('id', 'asc')  // Order by device id ascending
                    ->get();

                $result = [];
                foreach ($devices as $device) {
                    $latestData = SensorData::where('device_id', $device->id)
                        ->orderBy('recorded_at', 'desc')
                        ->first();

                    $result[] = [
                        'id' => $device->id,
                        'name' => 'Device ' . $device->id,
                        'location' => $device->lokasi ?? 'Unknown',
                        'group' => $device->group,
                        'kode_perlakuan' => $device->kode_perlakuan,
                        'is_active' => true, // Assume all devices in DB are active
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
            ->select('id', 'group', 'kode_perlakuan', 'lokasi')
            ->get();

        $result = [];
        foreach ($devices as $device) {
            $hasRecentData = SensorData::where('device_id', $device->id)
                ->where('recorded_at', '>=', Carbon::now()->subMinutes(10))
                ->exists();

            $result[] = [
                'id' => $device->id,
                'name' => 'Device ' . $device->id,
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
