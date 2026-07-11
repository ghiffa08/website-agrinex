<?php

namespace App\Repositories\Contracts;

interface LogRepositoryInterface
{
    public function createDeviceLog(array $data);
    public function createNodeLog(array $data);
    public function getLatestDeviceLogs();
    public function getLatestForNode($nodeId);
    public function getLatestLogsForDevices();
    public function getLogsForDevice($deviceId, $filters, $limit);
}
