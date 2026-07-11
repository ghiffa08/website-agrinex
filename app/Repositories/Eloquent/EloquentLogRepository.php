<?php

namespace App\Repositories\Eloquent;

use App\Models\DeviceLog;
use App\Models\NodeLog;
use App\Repositories\Contracts\LogRepositoryInterface;

class EloquentLogRepository implements LogRepositoryInterface
{
    public function createDeviceLog(array $data)
    {
        return DeviceLog::create($data);
    }

    public function createNodeLog(array $data)
    {
        return NodeLog::create($data);
    }

    public function getLatestDeviceLogs()
    {
        return DeviceLog::with('device')
            ->orderBy('logged_at', 'desc')
            ->limit(50)
            ->get();
    }

    public function getLatestForNode($nodeId)
    {
        return NodeLog::where('node_id', $nodeId)
            ->latest('waktu')
            ->first();
    }

    public function getLatestLogsForDevices()
    {
        return DeviceLog::select('device_logs.*')
            ->from('device_logs')
            ->join(
                \DB::raw('(SELECT device_id, MAX(logged_at) as max_logged_at FROM device_logs GROUP BY device_id) as latest'),
                function ($join) {
                    $join->on('device_logs.device_id', '=', 'latest.device_id')
                        ->on('device_logs.logged_at', '=', 'latest.max_logged_at');
                }
            )
            ->get();
    }

    public function getLogsForDevice($deviceId, $filters, $limit)
    {
        $query = DeviceLog::where('device_id', $deviceId);

        if (!empty($filters['start_date'])) {
            $query->where('logged_at', '>=', $filters['start_date']);
        }

        return $query->orderBy('logged_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
