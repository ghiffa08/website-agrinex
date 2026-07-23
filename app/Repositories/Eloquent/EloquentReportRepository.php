<?php

namespace App\Repositories\Eloquent;

use App\Models\SensorData;
use App\Models\WeatherData;
use App\Models\IrrigationLog;
use App\Models\Device;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function getSensorDataReport(array $filters): array
    {
        $query = SensorData::with('device:id,name,lokasi');

        // Use whereBetween with timestamps for better index usage
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDateTime = Carbon::parse($filters['start_date'])->startOfDay();
            $endDateTime = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('recorded_at', [$startDateTime, $endDateTime]);
        } elseif (!empty($filters['start_date'])) {
            $query->where('recorded_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        } elseif (!empty($filters['end_date'])) {
            $query->where('recorded_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }
        
        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        $query->orderBy('recorded_at', 'desc');
        $limit = min($filters['limit'] ?? 1000, 5000); // Cap at 5000 for performance
        $query->limit($limit);

        return $query->get()->map(function ($item) {
            return [
                'timestamp' => $item->recorded_at,
                'device' => $item->device->name ?? '-',
                'location' => $item->device->lokasi ?? '-',
                'temperature_c' => $item->temperature ?? '-',
                'humidity_pct' => '-',
                'soil_moisture_pct' => $item->soil_moisture ?? '-',
                'light_lux' => '-',
                'water_height_cm' => '-',
                'battery_voltage_v' => $item->voltage_v ?? '-',
            ];
        })->toArray();
    }

    public function getWeatherDataReport(array $filters): array
    {
        $query = WeatherData::query();

        // Use whereBetween with timestamps for better index usage
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDateTime = Carbon::parse($filters['start_date'])->startOfDay();
            $endDateTime = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('recorded_at', [$startDateTime, $endDateTime]);
        } elseif (!empty($filters['start_date'])) {
            $query->where('recorded_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        } elseif (!empty($filters['end_date'])) {
            $query->where('recorded_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }

        $query->orderBy('recorded_at', 'desc');
        $limit = min($filters['limit'] ?? 1000, 5000); // Cap at 5000 for performance
        $query->limit($limit);

        return $query->get()->map(function ($item) {
            return [
                'timestamp' => $item->recorded_at,
                'location' => $item->location ?? '-',
                'temperature_c' => $item->temperature ?? '-',
                'humidity_pct' => $item->humidity_pct ?? '-',
                'rainfall_mm' => $item->rain_mm ?? '-',
                'wind_speed_ms' => '-',
                'wind_direction' => '-',
                'light_intensity_pct' => $item->light_lux ?? '-',
                'water_level_cm' => '-',
            ];
        })->toArray();
    }

    public function getIrrigationReport(array $filters): array
    {
        $query = IrrigationLog::with('device:id,name,lokasi');

        // Use whereBetween with timestamps for better index usage
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDateTime = Carbon::parse($filters['start_date'])->startOfDay();
            $endDateTime = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('started_at', [$startDateTime, $endDateTime]);
        } elseif (!empty($filters['start_date'])) {
            $query->where('started_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        } elseif (!empty($filters['end_date'])) {
            $query->where('started_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }
        
        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        $query->orderBy('started_at', 'desc');
        $limit = min($filters['limit'] ?? 1000, 5000); // Cap at 5000 for performance
        $query->limit($limit);

        return $query->get()->map(function ($item) {
            return [
                'start_time' => $item->started_at,
                'end_time' => $item->stopped_at ?? '-',
                'device' => $item->device->name ?? '-',
                'location' => $item->device->lokasi ?? '-',
                'water_used_liters' => $item->water_used_liters ?? 0,
                'duration_minutes' => $item->duration_minutes ?? 0,
                'mode' => $item->mode ?? 'auto',
                'status' => $item->stopped_at ? 'completed' : 'ongoing',
            ];
        })->toArray();
    }

    public function getDeviceActivityReport(array $filters): array
    {
        $startDateTime = !empty($filters['start_date']) 
            ? Carbon::parse($filters['start_date'])->startOfDay() 
            : null;
        $endDateTime = !empty($filters['end_date']) 
            ? Carbon::parse($filters['end_date'])->endOfDay() 
            : null;

        $query = Device::withCount([
            'sensorData' => function ($q) use ($startDateTime, $endDateTime) {
                if ($startDateTime && $endDateTime) {
                    $q->whereBetween('recorded_at', [$startDateTime, $endDateTime]);
                } elseif ($startDateTime) {
                    $q->where('recorded_at', '>=', $startDateTime);
                } elseif ($endDateTime) {
                    $q->where('recorded_at', '<=', $endDateTime);
                }
            },
            'irrigationLogs' => function ($q) use ($startDateTime, $endDateTime) {
                if ($startDateTime && $endDateTime) {
                    $q->whereBetween('started_at', [$startDateTime, $endDateTime]);
                } elseif ($startDateTime) {
                    $q->where('started_at', '>=', $startDateTime);
                } elseif ($endDateTime) {
                    $q->where('started_at', '<=', $endDateTime);
                }
            }
        ])->with(['sensorData' => function ($q) use ($startDateTime, $endDateTime) {
            if ($startDateTime && $endDateTime) {
                $q->whereBetween('recorded_at', [$startDateTime, $endDateTime]);
            } elseif ($startDateTime) {
                $q->where('recorded_at', '>=', $startDateTime);
            } elseif ($endDateTime) {
                $q->where('recorded_at', '<=', $endDateTime);
            }
            $q->latest('recorded_at')->limit(1);
        }]);

        if (!empty($filters['device_id'])) {
            $query->where('id', $filters['device_id']);
        }

        return $query->get()->map(function ($device) {
            $latestSensor = $device->sensorData->first();
            
            return [
                'device_name' => $device->name,
                'location' => $device->lokasi,
                'total_readings' => $device->sensor_data_count,
                'total_irrigations' => $device->irrigation_logs_count,
                'last_reading' => $latestSensor?->recorded_at,
                'last_temperature' => $latestSensor?->temperature,
                'last_soil_moisture' => $latestSensor?->soil_moisture,
                'last_battery' => $latestSensor?->voltage_v,
            ];
        })->toArray();
    }

    public function getWaterUsageSummary(array $filters): array
    {
        $query = IrrigationLog::with('device:id,name,lokasi')
            ->select(
                'device_id',
                DB::raw('COUNT(*) as total_sessions'),
                DB::raw('SUM(water_used_liters) as total_water_liters'),
                DB::raw('AVG(water_used_liters) as avg_water_per_session'),
                DB::raw('SUM(duration_minutes) as total_duration_minutes'),
                DB::raw('AVG(duration_minutes) as avg_duration_minutes')
            )
            ->groupBy('device_id');

        // Use whereBetween with timestamps for better index usage
        $startDateTime = !empty($filters['start_date']) 
            ? Carbon::parse($filters['start_date'])->startOfDay() 
            : null;
        $endDateTime = !empty($filters['end_date']) 
            ? Carbon::parse($filters['end_date'])->endOfDay() 
            : null;

        if ($startDateTime && $endDateTime) {
            $query->whereBetween('started_at', [$startDateTime, $endDateTime]);
        } elseif ($startDateTime) {
            $query->where('started_at', '>=', $startDateTime);
        } elseif ($endDateTime) {
            $query->where('started_at', '<=', $endDateTime);
        }
        
        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        return $query->get()->map(function ($item) {
            return [
                'device' => $item->device->name ?? '-',
                'location' => $item->device->lokasi ?? '-',
                'total_sessions' => $item->total_sessions,
                'total_water_liters' => round($item->total_water_liters, 2),
                'avg_water_per_session' => round($item->avg_water_per_session, 2),
                'total_duration_minutes' => round($item->total_duration_minutes, 0),
                'avg_duration_minutes' => round($item->avg_duration_minutes, 0),
            ];
        })->toArray();
    }

    public function getDashboardSummary(array $filters): array
    {
        $startDate = $filters['start_date'] ?? Carbon::now()->subDays(30)->toDateString();
        $endDate = $filters['end_date'] ?? Carbon::now()->toDateString();
        
        // Parse to datetime for better index usage
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        // Get all counts in fewer queries
        $totalDevices = Device::count();
        
        $activeDevices = Device::whereHas('sensorData', function ($q) use ($startDateTime, $endDateTime) {
            $q->whereBetween('recorded_at', [$startDateTime, $endDateTime]);
        })->count();

        // Combined sensor data stats
        $sensorStats = SensorData::whereBetween('recorded_at', [$startDateTime, $endDateTime])
            ->selectRaw('COUNT(*) as total_readings, AVG(temperature) as avg_temp, AVG(soil_moisture) as avg_soil_moisture')
            ->first();

        // Combined irrigation stats
        $irrigationStats = IrrigationLog::whereBetween('started_at', [$startDateTime, $endDateTime])
            ->selectRaw('COUNT(*) as total_sessions, SUM(water_used_liters) as total_water')
            ->first();

        return [
            'report_period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'devices' => [
                'total' => $totalDevices,
                'active' => $activeDevices,
            ],
            'readings' => [
                'total' => $sensorStats->total_readings ?? 0,
            ],
            'irrigation' => [
                'total_sessions' => $irrigationStats->total_sessions ?? 0,
                'total_water_liters' => round($irrigationStats->total_water ?? 0, 2),
            ],
            'environment' => [
                'avg_temperature_c' => round($sensorStats->avg_temp ?? 0, 1),
                'avg_humidity_pct' => 0,
                'avg_soil_moisture_pct' => round($sensorStats->avg_soil_moisture ?? 0, 1),
                'avg_light_lux' => 0,
            ],
        ];
    }
}
