<?php
$repo = app(\App\Repositories\Contracts\ReportRepositoryInterface::class);
echo 'Sensor: ' . count($repo->getSensorDataReport([])) . "\n";
echo 'DeviceActivity: ' . count($repo->getDeviceActivityReport([])) . "\n";
echo 'WaterUsage: ' . count($repo->getWaterUsageSummary([])) . "\n";
echo 'Irrigation: ' . count($repo->getIrrigationReport([])) . "\n";
echo "SUCCESS\n";
