<?php

namespace App\Services;

use App\Models\SensorData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DeviceService
{
    /**
     * Get chart data (last 100 readings) for a specific node.
     */
    public function getChartData(int|string $deviceId): array
    {
        return Cache::remember("chart_data_{$deviceId}", 30, function () use ($deviceId) {
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
        });
    }

    /**
     * Get irrigation sessions for a specific device.
     */
    public function getIrrigationSessions(int|string $deviceId): array
    {
        return Cache::remember("irrigation_sessions_{$deviceId}", 300, function () use ($deviceId) {
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
        });
    }

    /**
     * Get usage history (last 7 days) for a specific device.
     */
    public function getUsageHistory(int|string $deviceId): array
    {
        return Cache::remember("usage_history_{$deviceId}", 300, function () use ($deviceId) {
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
                ->selectRaw("DATE(irrigation_logs.started_at) as date, COUNT(valve_logs.id) as count, {$volumeSql} as total_volume_ml")
                ->get()
                ->toArray();

            return [
                'history' => $history,
            ];
        });
    }

    /**
     * Get all devices with latest sensor data (for polling)
     */
    public function getAllDevicesWithLatestData(): array
    {
        return Cache::remember('dashboard_devices_repo', 60, function () {
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
        });
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
