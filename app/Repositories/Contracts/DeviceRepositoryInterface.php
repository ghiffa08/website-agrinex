<?php

namespace App\Repositories\Contracts;

interface DeviceRepositoryInterface
{
    public function allDevices();
    public function allNodes();
    public function findById($id);
    public function findNodeById($nodeId);
    public function firstOrCreateNode(array $search, array $values);
    public function firstOrCreateDevice(array $search, array $values);
    public function update($id, array $data);
}
