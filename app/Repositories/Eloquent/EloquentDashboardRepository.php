<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Models\IrrigationLog;
use App\Models\WeatherData;
use App\Models\DataSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Cache TTL constants (in seconds).
     *
     * Real-time data (devices, weather) uses short TTL as a safety net;
     * WebSocket push handles instant updates. Analytical data (usage,
     * schedule) changes infrequently and can be cached longer.
     */
    protected int $realtimeCacheTtl   = 15;   // 15s safety net (WebSocket is primary)
    protected int $analyticalCacheTtl = 600;  // 10 min for usage/schedule charts
    protected int $perNodeCacheTtl    = 30;   // 30s per-node cache (write-through)

    /**
     * Get a single device with latest sensor data.
     * Uses per-node write-through cache: the cache is warmed on telemetry
     * ingestion via invalidateNodeCache(), so dashboard reads are near-free.
     */
    public function getDevice(int $nodeId): ?array
    {
        return Cache::remember("dashboard_node_{$nodeId}", $this->perNodeCacheTtl, function () use ($nodeId) {
            return $this->buildNodeData($nodeId);
        });
    }

    /**
     * Invalidate caches for a specific node after telemetry write.
     * Called by TelemetryApiController to implement write-through caching.
     */
    public function invalidateNodeCache(int $nodeId): void
    {
        Cache::forget("dashboard_node_{$nodeId}");
        Cache::forget('dashboard_devices_repo');
    }

    /**
     * Build the device data array for a single device (uncached).
     */
    private function buildNodeData(int $deviceId): ?array
    {
        $device = DB::table('devices')->where('id', $deviceId)->first();
        if (!$device) return null;

        $sensor = DB::table('sensor_data')
            ->where('device_id', $deviceId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $log = DB::table('device_logs')
            ->where('device_id', $deviceId)
            ->orderBy('logged_at', 'desc')
            ->first();

        $lahanName = $device->lahan_pantau_id
            ? DB::table('lahan_pantaus')->where('id', $device->lahan_pantau_id)->value('nama_lahan')
            : null;

        $lastSeen = $sensor->recorded_at ?? $log->logged_at ?? null;
        $status   = $lastSeen ? $this->getConnectionStatus($lastSeen) : 'offline';

        return [
            'id'                    => $device->id,
            'device_id'             => $device->id,
            'name'                  => $device->name ?? "Device {$device->id}",
            'device_name'           => $device->name ?? "Device {$device->id}",
            'plot_number'           => $device->id,
            'location'              => $device->location ?? "Sensor Device {$device->id}",
            'treatment_description' => $device->description ?? 'Monitoring Optimal',
            'treatment_type'        => 'standard',
            'treatment_code'        => "T{$device->id}",
            'group'                 => null,
            'kode_perlakuan'       => "T{$device->id}",
            'lahan_pantau_id'      => $device->lahan_pantau_id ?? null,
            'lahan_pantau_name'    => $lahanName,

            'soil_moisture_pct'    => $sensor ? (float) $sensor->soil_moisture : null,
            'temperature_c'        => $sensor ? (float) $sensor->temperature   : null,
            'battery_voltage_v'    => $sensor ? (float) $sensor->voltage_v   : null,
            'battery_percentage'   => $sensor ? $this->calculateBatteryPercentage($sensor->voltage_v) : null,

            'air_temp_c'           => null,
            'air_humidity_pct'     => null,
            'light_lux'            => null,
            'water_height_cm'      => null,

            'signal_strength_rssi' => $log ? (float) ($log->rssi ?? null) : null,
            'signal_strength_pct'  => $log && isset($log->rssi)
                ? max(0, min(100, round((($log->rssi + 120) / 70) * 100)))
                : null,

            'connection_state'   => $status,
            'connection_status'  => $status,
            'valve_state'        => 'closed',
            'is_active'          => (bool) ($device->is_active ?? true),
            'status'             => $sensor ? 'normal' : 'no_data',
            'water_usage_today_l' => 0,
            'last_seen'          => $lastSeen,
            'recorded_at'        => $lastSeen,
            'waktu_update'       => $device->updated_at ?? null,
            'last_updated'       => $lastSeen
                ? Carbon::parse($lastSeen)->diffForHumans()
                : null,
        ];
    }

    /**
     * Get all devices with latest sensor data.
     * Fixed: was doing N+1 queries (1 per node). Now uses 2 bulk queries total.
     */
    public function getDevices(): array
    {
        return Cache::remember('dashboard_devices_repo', $this->realtimeCacheTtl, function () {
            $devices = DB::table('devices')->get();

            if ($devices->isEmpty()) {
                return [];
            }

            $deviceIds = $devices->pluck('id')->all();

            // Bulk fetch latest sensor data per device (one query)
            $latestSensor = DB::table('sensor_data as s')
                ->joinSub(
                    DB::table('sensor_data')
                        ->selectRaw('device_id, MAX(recorded_at) as max_at')
                        ->whereIn('device_id', $deviceIds)
                        ->groupBy('device_id'),
                    'latest',
                    fn ($join) => $join
                        ->on('s.device_id', '=', 'latest.device_id')
                        ->on('s.recorded_at', '=', 'latest.max_at')
                )
                ->whereIn('s.device_id', $deviceIds)
                ->get()
                ->keyBy('device_id');

            // Bulk fetch latest device logs per device (one query)
            $latestLog = DB::table('device_logs as l')
                ->joinSub(
                    DB::table('device_logs')
                        ->selectRaw('device_id, MAX(logged_at) as max_logged_at')
                        ->whereIn('device_id', $deviceIds)
                        ->groupBy('device_id'),
                    'latest',
                    fn ($join) => $join
                        ->on('l.device_id', '=', 'latest.device_id')
                        ->on('l.logged_at', '=', 'latest.max_logged_at')
                )
                ->whereIn('l.device_id', $deviceIds)
                ->get()
                ->keyBy('device_id');

            // Bulk fetch lahan pantau names (one query)
            $lahanIds = $devices->pluck('lahan_pantau_id')->filter()->unique()->all();
            $lahanNames = $lahanIds
                ? DB::table('lahan_pantaus')
                    ->whereIn('id', $lahanIds)
                    ->pluck('nama_lahan', 'id')
                : collect();

            return $devices->map(function ($device) use ($latestSensor, $latestLog, $lahanNames) {
                $sensor  = $latestSensor->get($device->id);
                $log     = $latestLog->get($device->id);
                $lastSeen = $sensor->recorded_at ?? $log->logged_at ?? null;
                $status   = $lastSeen ? $this->getConnectionStatus($lastSeen) : 'offline';

                return [
                    'id'                    => $device->id,
                    'device_id'             => $device->id,
                    'name'                  => $device->name ?? "Device {$device->id}",
                    'device_name'           => $device->name ?? "Device {$device->id}",
                    'plot_number'           => $device->id,
                    'location'              => $device->location ?? "Sensor Device {$device->id}",
                    'treatment_description' => $device->description ?? 'Monitoring Optimal',
                    'treatment_type'        => 'standard',
                    'treatment_code'        => "T{$device->id}",
                    'group'                 => null,
                    'kode_perlakuan'       => "T{$device->id}",
                    'lahan_pantau_id'      => $device->lahan_pantau_id ?? null,
                    'lahan_pantau_name'    => $lahanNames->get($device->lahan_pantau_id ?? null),

                    'soil_moisture_pct'    => $sensor ? (float) $sensor->soil_moisture : null,
                    'temperature_c'        => $sensor ? (float) $sensor->temperature   : null,
                    'battery_voltage_v'    => $sensor ? (float) $sensor->voltage_v   : null,
                    'battery_percentage'   => $sensor ? $this->calculateBatteryPercentage($sensor->voltage_v) : null,

                    // Unused fields set to null - not from this data source
                    'air_temp_c'           => null,
                    'air_humidity_pct'     => null,
                    'light_lux'            => null,
                    'water_height_cm'      => null,

                    'signal_strength_rssi' => $log ? (float) ($log->rssi ?? null) : null,
                    'signal_strength_pct'  => $log && isset($log->rssi)
                        ? max(0, min(100, round((($log->rssi + 120) / 70) * 100)))
                        : null,

                    'connection_state'   => $status,
                    'connection_status'  => $status,
                    'valve_state'        => 'closed',
                    'is_active'          => (bool) ($device->is_active ?? true),
                    'status'             => $sensor ? 'normal' : 'no_data',
                    'water_usage_today_l' => 0,
                    'last_seen'          => $lastSeen,
                    'recorded_at'        => $lastSeen,
                    'waktu_update'       => $device->updated_at ?? null,
                    'last_updated'       => $lastSeen
                        ? Carbon::parse($lastSeen)->diffForHumans()
                        : null,
                ];
            })->toArray();
        });
    }

    public function getTank(): array
    {
        return Cache::remember('dashboard_tank_repo', $this->realtimeCacheTtl, function () {
            $tankCapacity = 1000;
            $usedToday    = 0;

            if ($this->hasIrrigationLogsTable()) {
                $recentIrrigation = IrrigationLog::with('valveLogs')
                    ->where('status', 'completed')
                    ->orderBy('started_at', 'desc')
                    ->first();

                if ($recentIrrigation?->valveLogs) {
                    $usedToday = $recentIrrigation->valveLogs->sum('volume_ml') / 1000;
                }
            }

            $currentVolume = max(0, $tankCapacity - $usedToday);
            $percentage    = ($currentVolume / $tankCapacity) * 100;

            return [
                'id'                    => 1,
                'tank_name'             => 'Tangki Air Utama',
                'name'                  => 'Tangki Air Utama',
                'capacity'              => $tankCapacity,
                'capacity_liters'       => $tankCapacity,
                'current_volume_liters' => round($currentVolume, 2),
                'water_level_cm'        => round(($percentage / 100) * 150, 2),
                'percentage'            => round($percentage, 2),
                'status'                => $percentage > 70 ? 'normal' : ($percentage > 30 ? 'warning' : 'critical'),
                'updated_at'            => now(),
            ];
        });
    }

    public function getSchedule(): array
    {
        return Cache::remember('dashboard_schedule_repo', $this->analyticalCacheTtl, function () {
            if (!$this->hasIrrigationLogsTable()) {
                return [];
            }

            return IrrigationLog::whereDate('started_at', today())
                ->with('valveLogs')
                ->orderBy('started_at')
                ->get()
                ->map(fn ($s) => [
                    'id'               => $s->id,
                    'session_id'       => $s->session_id,
                    'start_time'       => $s->started_at,
                    'end_time'         => $s->ended_at,
                    'duration_minutes' => Carbon::parse($s->started_at)->diffInMinutes($s->ended_at),
                    'total_valves'     => $s->success_count + $s->failed_count,
                    'status'           => $s->status,
                    'total_volume_ml'  => $s->valveLogs->sum('volume_ml'),
                ])
                ->toArray();
        });
    }

    public function getUsage(): array
    {
        return Cache::remember('dashboard_usage_repo', $this->analyticalCacheTtl, function () {
            if (!$this->hasIrrigationLogsTable()) {
                return [];
            }
            $hasVolume = Schema::hasColumn('valve_logs', 'volume_ml');
            $volumeSql = $hasVolume ? 'SUM(v.volume_ml)' : '0';

            return DB::table('irrigation_logs as i')
                ->selectRaw("DATE(i.started_at) as date, COUNT(DISTINCT i.id) as sessions, COALESCE({$volumeSql}, 0) as total_volume")
                ->leftJoin('valve_logs as v', 'i.id', '=', 'v.irrigation_log_id')
                ->where('i.started_at', '>=', Carbon::now()->subDays(30))
                ->where('i.status', 'completed')
                ->groupByRaw('DATE(i.started_at)')
                ->orderByRaw('DATE(i.started_at) ASC')
                ->get()
                ->map(fn ($row) => [
                    'date'       => $row->date,
                    'usage_date' => $row->date,
                    'total_l'    => round($row->total_volume / 1000, 2),
                    'sessions'   => (int) $row->sessions,
                ])
                ->toArray();
        });
    }

    public function getUsageDaily(): array
    {
        return Cache::remember('dashboard_usage_daily_repo', $this->analyticalCacheTtl, function () {
            if (!$this->hasIrrigationLogsTable()) {
                return [];
            }
            $hasVolume = Schema::hasColumn('valve_logs', 'volume_ml');
            $volumeSql = $hasVolume ? 'SUM(v.volume_ml)' : '0';

            return DB::table('irrigation_logs as i')
                ->selectRaw("DATE_FORMAT(i.started_at, '%Y-%m-%d %H:00') as datetime, COUNT(DISTINCT i.id) as sessions, COALESCE({$volumeSql}, 0) as total_volume")
                ->leftJoin('valve_logs as v', 'i.id', '=', 'v.irrigation_log_id')
                ->where('i.started_at', '>=', Carbon::now()->subHours(24))
                ->where('i.status', 'completed')
                ->groupByRaw("DATE_FORMAT(i.started_at, '%Y-%m-%d %H:00')")
                ->orderByRaw("DATE_FORMAT(i.started_at, '%Y-%m-%d %H:00') ASC")
                ->get()
                ->map(fn ($row) => [
                    'hour'      => Carbon::parse($row->datetime)->format('H:00'),
                    'datetime'  => $row->datetime,
                    'total_l'   => round($row->total_volume / 1000, 2),
                    'sessions'  => (int) $row->sessions,
                ])
                ->toArray();
        });
    }

    public function getWeather(): ?array
    {
        return Cache::remember('dashboard_weather_repo', $this->realtimeCacheTtl, function () {
            $latestSession = DataSession::orderBy('started_at', 'desc')->first();

            if (!$latestSession) {
                return null;
            }

            $weatherData = WeatherData::where('data_session_id', $latestSession->id)->first();

            if (!$weatherData) {
                return null;
            }

            return [
                'temperature' => (float) $weatherData->temp_c,
                'temp'        => (float) $weatherData->temp_c,
                'humidity'    => (float) $weatherData->humidity_pct,
                'light'       => (float) $weatherData->light_lux,
                'light_pct'   => min(100, ($weatherData->light_lux / 100000) * 100),
                'rain'        => (float) $weatherData->rain_mm,
                'wind'        => (float) $weatherData->wind_speed_kmh,
                'wind_speed'  => (float) $weatherData->wind_speed_kmh,
                'pressure'    => (float) $weatherData->pressure_hpa,
                'timestamp'   => $latestSession->started_at,
                'session_id'  => $latestSession->session_id,
            ];
        });
    }

    private function calculateBatteryPercentage(mixed $voltage): float
    {
        if (!$voltage) {
            return 0.0;
        }
        $pct = (((float) $voltage - 3.3) / (4.2 - 3.3)) * 100;
        return (float) max(0, min(100, round($pct, 2)));
    }

    private function getConnectionStatus(string $lastSeen): string
    {
        $minutes = Carbon::parse($lastSeen)->diffInMinutes(now());
        if ($minutes < 5)  return 'online';
        if ($minutes < 15) return 'idle';
        return 'offline';
    }

    private function hasIrrigationLogsTable(): bool
    {
        return Cache::remember('has_irrigation_logs_table', 86400, fn () => Schema::hasTable('irrigation_logs'));
    }
}
