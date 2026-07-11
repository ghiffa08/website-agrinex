<?php

namespace App\Repositories\Eloquent;

use App\Models\SensorData;
use App\Models\SensorNodeData;
use App\Repositories\Contracts\SensorDataRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentSensorDataRepository implements SensorDataRepositoryInterface
{
    public function createSensorRecord(array $data)
    {
        return SensorData::create($data);
    }

    public function createSensorNodeRecord(array $data)
    {
        return SensorNodeData::create($data);
    }

    public function getLatestForNode($nodeId)
    {
        return SensorNodeData::where('node_id', $nodeId)
            ->latest('received_at')
            ->first();
    }

    public function getLatestForDevice($deviceId)
    {
        return SensorData::where('device_id', $deviceId)
            ->latest('recorded_at')
            ->first();
    }

    public function getLatestForDevices()
    {
        return SensorNodeData::select('node_id')
            ->selectRaw('MAX(received_at) as latest_reading')
            ->groupBy('node_id')
            ->get()
            ->map(function ($item) {
                return SensorNodeData::where('node_id', $item->node_id)
                    ->where('received_at', $item->latest_reading)
                    ->first();
            });
    }

    public function getHistory($filters, $limit)
    {
        $query = SensorNodeData::query();

        if (!empty($filters['sesi_id'])) {
            $query->where('sesi_id_getdata', $filters['sesi_id']);
        }

        if (!empty($filters['node_id'])) {
            $query->where('node_id', $filters['node_id']);
        }

        $orderBy = $filters['order_by'] ?? 'received_at';
        $orderDir = $filters['order_dir'] ?? 'desc';

        return $query->orderBy($orderBy, $orderDir)
            ->limit($limit)
            ->get();
    }

    public function getSensorDataHistory($filters, $limit)
    {
        $query = SensorData::query();

        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->where('recorded_at', '>=', $filters['start_date']);
        }

        $orderBy = $filters['order_by'] ?? 'recorded_at';
        $orderDir = $filters['order_dir'] ?? 'asc';

        return $query->orderBy($orderBy, $orderDir)
            ->limit($limit)
            ->get();
    }

    public function getStatistics($sesiId = null)
    {
        $query = SensorNodeData::query();

        if ($sesiId) {
            $query->where('sesi_id_getdata', $sesiId);
        }

        return [
            'total_readings' => $query->count(),
            'avg_temperature' => round($query->avg('temp_c'), 2),
            'avg_soil_moisture' => round($query->avg('soil_pct'), 2),
            'avg_voltage' => round($query->avg('voltage_v'), 2),
            'min_temperature' => $query->min('temp_c'),
            'max_temperature' => $query->max('temp_c'),
            'nodes_count' => $query->distinct('node_id')->count('node_id'),
        ];
    }
}
