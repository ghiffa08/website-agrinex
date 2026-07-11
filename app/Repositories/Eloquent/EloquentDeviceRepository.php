<?php

namespace App\Repositories\Eloquent;

use App\Models\Device;
use App\Models\Node;
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
        return Node::with('lahanPantau')->orderBy('node_id')->get();
    }

    public function findById($id)
    {
        return Device::find($id);
    }

    public function findNodeById($nodeId)
    {
        return Node::where('node_id', $nodeId)->first();
    }

    public function firstOrCreateNode(array $search, array $values)
    {
        return Node::firstOrCreate($search, $values);
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
