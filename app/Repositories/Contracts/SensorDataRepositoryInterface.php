<?php

namespace App\Repositories\Contracts;

interface SensorDataRepositoryInterface
{
    public function createSensorRecord(array $data);
    public function createSensorNodeRecord(array $data);
    public function getLatestForNode($nodeId);
    public function getLatestForDevice($deviceId);
    public function getLatestForDevices();
    public function getHistory($filters, $limit);
    public function getSensorDataHistory($filters, $limit);
    public function getStatistics($sesiId = null);
}
