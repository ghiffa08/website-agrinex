<?php

namespace App\Services;

use App\Models\SensorData;
use App\Models\WeatherData;
use App\Models\DeviceLog;
use App\Models\ValveLog;
use App\Models\IrrigationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupService
{
    public function cleanup($days = 90)
    {
        $cutoffDate = now()->subDays($days);
        
        Log::info('Starting cleanup', [
            'days' => $days,
            'cutoff_date' => $cutoffDate
        ]);

        return DB::transaction(function () use ($cutoffDate) {
            $result = [
                'cutoff_date' => $cutoffDate->toDateTimeString(),
                'deleted_counts' => []
            ];

            // Delete old sensor_data
            $deletedSensorData = SensorData::where('recorded_at', '<', $cutoffDate)->delete();
            $result['deleted_counts']['sensor_data'] = $deletedSensorData;

            // Delete old weather_data
            $deletedWeatherData = WeatherData::where('recorded_at', '<', $cutoffDate)->delete();
            $result['deleted_counts']['weather_data'] = $deletedWeatherData;

            // Delete old device_logs
            $deletedDeviceLogs = DeviceLog::where('logged_at', '<', $cutoffDate)->delete();
            $result['deleted_counts']['device_logs'] = $deletedDeviceLogs;

            // Delete old valve_logs (orphaned only, keep those with irrigation_logs)
            $deletedValveLogs = ValveLog::whereNull('irrigation_log_id')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            $result['deleted_counts']['valve_logs'] = $deletedValveLogs;

            $result['total_deleted'] = array_sum($result['deleted_counts']);

            Log::info('Cleanup completed', $result);

            return $result;
        });
    }

    public function cleanupOrphaned()
    {
        Log::info('Starting orphaned records cleanup');

        return DB::transaction(function () {
            $result = [
                'deleted_counts' => []
            ];

            // Delete sensor data without devices
            $deletedOrphanedSensorData = SensorData::whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('devices')
                    ->whereRaw('devices.id = sensor_data.device_id');
            })->delete();
            $result['deleted_counts']['orphaned_sensor_data'] = $deletedOrphanedSensorData;

            // Delete weather data without devices
            $deletedOrphanedWeatherData = WeatherData::whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('devices')
                    ->whereRaw('devices.id = weather_data.device_id');
            })->delete();
            $result['deleted_counts']['orphaned_weather_data'] = $deletedOrphanedWeatherData;

            // Delete device logs without devices
            $deletedOrphanedDeviceLogs = DeviceLog::whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('devices')
                    ->whereRaw('devices.id = device_logs.device_id');
            })->delete();
            $result['deleted_counts']['orphaned_device_logs'] = $deletedOrphanedDeviceLogs;

            // Delete valve logs without irrigation logs
            $deletedOrphanedValveLogs = ValveLog::whereNotNull('irrigation_log_id')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('irrigation_logs')
                        ->whereRaw('irrigation_logs.id = valve_logs.irrigation_log_id');
                })->delete();
            $result['deleted_counts']['orphaned_valve_logs'] = $deletedOrphanedValveLogs;

            $result['total_deleted'] = array_sum($result['deleted_counts']);

            Log::info('Orphaned records cleanup completed', $result);

            return $result;
        });
    }

    public function getStatistics()
    {
        return [
            'sensor_data' => SensorData::count(),
            'weather_data' => WeatherData::count(),
            'device_logs' => DeviceLog::count(),
            'valve_logs' => ValveLog::count(),
            'irrigation_logs' => IrrigationLog::count(),
            'oldest_sensor_data' => SensorData::oldest('recorded_at')->value('recorded_at'),
            'latest_sensor_data' => SensorData::latest('recorded_at')->value('recorded_at'),
        ];
    }
}
