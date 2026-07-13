<?php

namespace App\Repositories\Eloquent;

use App\Models\Device;
use App\Repositories\Contracts\DeviceRepositoryInterface;

class EloquentDeviceRepository implements DeviceRepositoryInterface
{
    public function allDevices()
    {
        return Device::with(['sensorData' => function($query) {
            $query->latest('recorded_at')->limit(1);
        }])->orderBy('id')->get();
    }

    public function allNodes()
    {
        // Legacy method - now uses devices table
        return Device::with('lahanPantau')->orderBy('node_id')->get();
    }

    public function findById($id)
    {
        return Device::find($id);
    }

    public function findNodeById($nodeId)
    {
        // Legacy method - now uses devices table
        return Device::where('node_id', $nodeId)->first();
    }

    public function firstOrCreateNode(array $search, array $values)
    {
        // Legacy method - now uses devices table
        return Device::firstOrCreate($search, $values);
    }

    public function firstOrCreateDevice(array $search, array $values)
    {
        return Device::firstOrCreate($search, $values);
    }

    public function update($id, array $data)
    {
        $device = Device::findOrFail($id);
        $device->update($data);
        return $device;
    }
}
