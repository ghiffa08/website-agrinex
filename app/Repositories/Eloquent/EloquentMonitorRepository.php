<?php

namespace App\Repositories\Eloquent;

use App\Models\DataSession;
use App\Models\DeviceLog;
use App\Models\IrrigationLog;
use App\Models\SensorData;
use App\Models\WeatherData;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EloquentMonitorRepository implements MonitorRepositoryInterface
{
    /**
     * Get database statistics
     */
    public function getDatabaseStats(): array
    {
        return Cache::remember('monitor:database_stats', 300, function () {
            return [
                'getdata_logs' => DataSession::count(),
                'irrigate_logs' => IrrigationLog::count(),
                'sensor_node_data' => SensorData::count(),
                'sensor_weather_data' => WeatherData::count(),
                'node_logs' => DeviceLog::count(),
            ];
        });
    }

    /**
     * Get latest sessions
     */
    public function getLatestSessions(): array
    {
        return Cache::remember('monitor:latest_sessions', 60, function () {
            return [
                'getdata' => DataSession::latest()->first(),
                'irrigate' => IrrigationLog::latest()->first(),
            ];
        });
    }

    /**
     * Get today's statistics using whereBetween for better index usage
     */
    public function getTodayStats(): array
    {
        $cacheKey = 'monitor:today_stats:' . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 300, function () {
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();

            return [
                'getdata_sessions' => DataSession::whereBetween('started_at', [$startOfDay, $endOfDay])->count(),
                'irrigate_sessions' => IrrigationLog::whereBetween('started_at', [$startOfDay, $endOfDay])->count(),
                'sensor_readings' => SensorData::whereBetween('recorded_at', [$startOfDay, $endOfDay])->count(),
            ];
        });
    }

    /**
     * Get recent logs by type
     */
    public function getRecentLogs(string $type, int $limit): array
    {
        $cacheKey = "monitor:recent_logs:{$type}:{$limit}";
        
        return Cache::remember($cacheKey, 60, function () use ($type, $limit) {
            $logs = [];

            if ($type === 'getdata' || $type === 'all') {
                $logs['getdata'] = DataSession::with(['sensorData'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($session) {
                        return [
                            'sesi_id_getdata' => $session->session_id,
                            'status' => $session->status,
                            'created_at' => $session->created_at,
                            'node_sukses' => $session->success_count,
                            'jumlah_node' => $session->success_count + $session->failed_count,
                        ];
                    });
            }

            if ($type === 'irrigate' || $type === 'all') {
                $logs['irrigate'] = IrrigationLog::with(['valveLogs'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($session) {
                        return [
                            'sesi_id_irrigate' => $session->session_id,
                            'status' => $session->status,
                            'created_at' => $session->created_at,
                            'duration_minutes' => $session->duration_minutes,
                        ];
                    });
            }

            return $logs;
        });
    }
}
