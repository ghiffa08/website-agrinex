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
}
