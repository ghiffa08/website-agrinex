<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Models\Device;
use App\Models\Node;
use App\Models\DataSession;
use App\Models\SensorData;
use App\Models\WeatherData;
use App\Models\IrrigationLog;
use App\Models\ValveLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Cache TTL in seconds for real-time data
     */
    protected $realtimeCacheTtl = 5;

    /**
     * Cache TTL in seconds for analytical/historical data
     */
    protected $analyticalCacheTtl = 300; // 5 minutes

    public function getDevices()
    {
        return Cache::remember('dashboard_devices_repo', $this->realtimeCacheTtl, function () {
            $nodes = DB::table('node')->get();

            if ($nodes->isEmpty()) {
                return [];
            }

            return $nodes->map(function ($node) {
                $sensorData = DB::table('sensor_node_data')
                    ->where('node_id', $node->node_id)
                    ->orderBy('received_at', 'desc')
                    ->first();

                $latestLog = DB::table('node_logs')
                    ->where('node_id', $node->node_id)
                    ->orderBy('waktu', 'desc')
                    ->first();

                $lastSeen = $sensorData->received_at ?? ($latestLog->waktu ?? null);
                $connectionStatus = $lastSeen ? $this->getConnectionStatus($lastSeen) : 'offline';

                return [
                    'id'              => $node->node_id,
                    'device_id'       => $node->node_id,
                    'device_name'     => "Node {$node->node_id}",
                    'plot_number'     => $node->node_id,
                    'location'        => $node->lokasi ?? "Sensor Node {$node->node_id}",
                    'treatment_description' => $node->keterangan ?? 'Monitoring Optimal',
                    'treatment_type'        => $node->group ?? 'standard',
                    'treatment_code'        => $node->kode_perlakuan ?? "T{$node->node_id}",
                    'group'                 => $node->group,
                    'kode_perlakuan'        => $node->kode_perlakuan,
                    
                    // Lahan Pantau 
                    'lahan_pantau_id'       => $node->lahan_pantau_id ?? null,
                    'lahan_pantau_name'     => isset($node->lahan_pantau_id) ? DB::table('lahan_pantaus')->where('id', $node->lahan_pantau_id)->value('nama_lahan') : null,

                    'soil_moisture_pct' => $sensorData ? (float) $sensorData->soil_pct  : null,
                    'temperature_c'     => $sensorData ? (float) $sensorData->temp_c     : null,
                    'soil_temp_c'       => $sensorData ? (float) $sensorData->temp_c     : null,
                    'air_temp_c'        => null,
                    'air_humidity_pct'  => null,
                    'light_lux'         => null,
                    'water_height_cm'   => null,
                    'battery_voltage'    => $sensorData ? (float) $sensorData->voltage_v : null,
                    'battery_voltage_v'  => $sensorData ? (float) $sensorData->voltage_v : null,
                    'battery_percentage' => $sensorData ? $this->calculateBatteryPercentage($sensorData->voltage_v) : null,
                    'signal_strength_rssi' => $latestLog ? (float) $latestLog->rssi_dbm : null,
                    'signal_strength_pct'  => $latestLog && $latestLog->rssi_dbm
                        ? max(0, min(100, round((($latestLog->rssi_dbm + 120) / 70) * 100)))
                        : null,
                    'connection_state'  => $connectionStatus,
                    'connection_status' => $connectionStatus,
                    'valve_state'       => 'closed',
                    'valve_status'      => 'closed',
                    'is_active'         => true,
                    'status'            => $sensorData ? 'normal' : 'no_data',
                    'water_usage_today_l' => 0,
                    'recorded_at'  => $lastSeen,
                    'last_seen'    => $lastSeen,
                    'waktu_update' => $node->waktu_update ?? null,
                ];
            })->toArray();
        });
    }

    public function getTank()
    {
        return Cache::remember('dashboard_tank_repo', $this->realtimeCacheTtl, function () {
            $tableExists = $this->hasIrrigationLogsTable();
            $recentIrrigation = null;
            if ($tableExists) {
                $recentIrrigation = IrrigationLog::with('valveLogs')
                    ->where('status', 'completed')
                    ->orderBy('started_at', 'desc')
                    ->first();
            }

            $tankCapacity = 1000; // liters
            $usedToday = 0;

            if ($recentIrrigation && $recentIrrigation->valveLogs) {
                $usedToday = $recentIrrigation->valveLogs->sum('volume_ml') / 1000;
            }

            $currentVolume = max(0, $tankCapacity - $usedToday);
            $percentage = ($currentVolume / $tankCapacity) * 100;
            $waterLevelCm = ($percentage / 100) * 150;

            return [
                'id' => 1,
                'tank_name' => 'Tangki Air Utama',
                'name' => 'Tangki Air Utama',
                'capacity' => $tankCapacity,
                'capacity_liters' => $tankCapacity,
                'current_volume_liters' => $currentVolume,
                'water_level_cm' => round($waterLevelCm, 2),
                'percentage' => round($percentage, 2),
                'status' => $percentage > 70 ? 'normal' : ($percentage > 30 ? 'warning' : 'critical'),
                'updated_at' => $recentIrrigation ? $recentIrrigation->started_at : now()
            ];
        });
    }

    public function getSchedule()
    {
        return Cache::remember('dashboard_schedule_repo', $this->analyticalCacheTtl, function () {
            if (!$this->hasIrrigationLogsTable()) {
                return [];
            }
            
            $todaySessions = IrrigationLog::whereDate('started_at', today())
                ->with('valveLogs')
                ->orderBy('started_at')
                ->get();
            
            return $todaySessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'start_time' => $session->started_at,
                    'end_time' => $session->ended_at,
                    'duration_minutes' => Carbon::parse($session->started_at)->diffInMinutes($session->ended_at),
                    'total_valves' => $session->success_count + $session->failed_count,
                    'status' => $session->status,
                    'total_volume_ml' => $session->valveLogs->sum('volume_ml')
                ];
            })->toArray();
        });
    }

    public function getUsage()
    {
        return Cache::remember('dashboard_usage_repo', $this->analyticalCacheTtl, function () {
            if (!$this->hasIrrigationLogsTable()) {
                return [];
            }
            
            $startDate = Carbon::now()->subDays(30);
            
            return IrrigationLog::where('started_at', '>=', $startDate)
                ->where('status', 'completed')
                ->with('valveLogs')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->started_at)->format('Y-m-d');
                })
                ->map(function ($daySessions, $date) {
                    $totalVolume = $daySessions->reduce(function ($carry, $session) {
                        return $carry + $session->valveLogs->sum('volume_ml');
                    }, 0);
                    
                    return [
                        'date' => $date,
                        'usage_date' => $date,
                        'total_l' => round($totalVolume / 1000, 2),
                        'liters' => round($totalVolume / 1000, 2),
                        'sessions' => $daySessions->count()
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    public function getUsageDaily()
    {
        return Cache::remember('dashboard_usage_daily_repo', $this->analyticalCacheTtl, function () {
            if (!$this->hasIrrigationLogsTable()) {
                return [];
            }
            
            $startTime = Carbon::now()->subHours(24);
            
            return IrrigationLog::where('started_at', '>=', $startTime)
                ->where('status', 'completed')
                ->with('valveLogs')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->started_at)->format('Y-m-d H:00');
                })
                ->map(function ($hourSessions, $datetime) {
                    $totalVolume = $hourSessions->reduce(function ($carry, $session) {
                        return $carry + $session->valveLogs->sum('volume_ml');
                    }, 0);
                    
                    $hour = Carbon::parse($datetime)->format('H:00');
                    
                    return [
                        'hour' => $hour,
                        'datetime' => $datetime,
                        'total_l' => round($totalVolume / 1000, 2),
                        'liters' => round($totalVolume / 1000, 2),
                        'sessions' => $hourSessions->count()
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    public function getWeather()
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
                'temp' => (float) $weatherData->temp_c,
                'humidity' => (float) $weatherData->humidity_pct,
                'light' => (float) $weatherData->light_lux,
                'light_pct' => min(100, ($weatherData->light_lux / 100000) * 100),
                'rain' => (float) $weatherData->rain_mm,
                'wind' => (float) $weatherData->wind_speed_kmh,
                'wind_speed' => (float) $weatherData->wind_speed_kmh,
                'pressure' => (float) $weatherData->pressure_hpa,
                'voltage' => null,
                'current' => null,
                'power' => null,
                'timestamp' => $latestSession->started_at,
                'session_id' => $latestSession->session_id
            ];
        });
    }

    private function calculateBatteryPercentage($voltage)
    {
        if (!$voltage) return 0;
        $minVoltage = 3.3;
        $maxVoltage = 4.2;
        $percentage = (($voltage - $minVoltage) / ($maxVoltage - $minVoltage)) * 100;
        return max(0, min(100, round($percentage, 2)));
    }
    
    private function getConnectionStatus($lastSeen)
    {
        $minutesAgo = Carbon::parse($lastSeen)->diffInMinutes(now());
        if ($minutesAgo < 5) return 'online';
        if ($minutesAgo < 15) return 'idle';
        return 'offline';
    }
    
    private function hasIrrigationLogsTable()
    {
        return Cache::remember('has_irrigation_logs_table', 60 * 60 * 24, function () {
            return Schema::hasTable('irrigation_logs');
        });
    }
}
