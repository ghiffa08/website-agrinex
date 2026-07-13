<?php

namespace App\Services;

use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Repositories\Contracts\SensorDataRepositoryInterface;
use App\Repositories\Contracts\WeatherDataRepositoryInterface;
use App\Repositories\Contracts\SessionRepositoryInterface;
use App\Repositories\Contracts\LogRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorDataService
{
    protected $deviceRepo;
    protected $sensorRepo;
    protected $weatherRepo;
    protected $sessionRepo;
    protected $logRepo;
    protected CacheService $cacheService;

    public function __construct(
        DeviceRepositoryInterface $deviceRepo,
        SensorDataRepositoryInterface $sensorRepo,
        WeatherDataRepositoryInterface $weatherRepo,
        SessionRepositoryInterface $sessionRepo,
        LogRepositoryInterface $logRepo,
        CacheService $cacheService
    ) {
        $this->deviceRepo = $deviceRepo;
        $this->sensorRepo = $sensorRepo;
        $this->weatherRepo = $weatherRepo;
        $this->sessionRepo = $sessionRepo;
        $this->logRepo = $logRepo;
        $this->cacheService = $cacheService;
    }

    public function processSensorData(array $requestData)
    {
        $metadata = $requestData['metadata'];
        $data = $requestData['data'];
        $statistics = $requestData['statistics'];
        
        $sesiId = $metadata['sesi_id_getdata'];

        return DB::transaction(function () use ($data, $sesiId, $statistics) {
            $insertedCounts = [];

            // 1. Insert getdata_logs
            if (!empty($data['getdata_logs'])) {
                foreach ($data['getdata_logs'] as $log) {
                    $this->sessionRepo->createGetdataLog(array_merge($log, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));

                    // Auto-register master node if it doesn't exist
                    if (isset($log['node_id'])) {
                        $this->deviceRepo->firstOrCreateNode(
                            ['node_id' => $log['node_id']],
                            [
                                'group' => 'A',
                                'kode_perlakuan' => 'P' . $log['node_id'],
                                'lokasi' => 'Otomatis dari API',
                                'keterangan' => 'Node ' . $log['node_id'] . ' didaftarkan otomatis'
                            ]
                        );
                    }
                }
                $insertedCounts['getdata_logs'] = count($data['getdata_logs']);
            }

            // 2. Insert weather_data
            if (!empty($data['sensor_weather_data'])) {
                foreach ($data['sensor_weather_data'] as $weather) {
                    $this->weatherRepo->createWeatherRecord(array_merge($weather, [
                        'data_session_id' => $sesiId, // Map from legacy sesi_id_getdata
                        // Map legacy fields to new fields
                        'temp_c' => $weather['temp_dht'] ?? null,
                        'humidity_pct' => $weather['humidity'] ?? null,
                        'light_lux' => $weather['light'] ?? null,
                        'voltage_v' => $weather['voltage'] ?? null,
                        'current_ma' => $weather['arus'] ?? null,
                        'power_mw' => $weather['power'] ?? null,
                    ]));
                }
                $insertedCounts['weather_data'] = count($data['sensor_weather_data']);
            }

            // 3. Insert sensor_data
            if (!empty($data['sensor_node_data'])) {
                foreach ($data['sensor_node_data'] as $node) {
                    $this->sensorRepo->createSensorRecord(array_merge($node, [
                        'data_session_id' => $sesiId, // Map from legacy sesi_id_getdata
                        'device_id' => $node['node_id'] ?? null,
                        'soil_moisture' => $node['soil_pct'] ?? null,
                    ]));

                    // Auto-register master node if it doesn't exist
                    $this->deviceRepo->firstOrCreateNode(
                        ['node_id' => $node['node_id']],
                        [
                            'group' => 'A',
                            'kode_perlakuan' => 'P' . $node['node_id'],
                            'lokasi' => 'Otomatis dari API',
                            'keterangan' => 'Node ' . $node['node_id'] . ' didaftarkan otomatis'
                        ]
                    );
                }
                $insertedCounts['sensor_data'] = count($data['sensor_node_data']);
            }

            // 4. Insert node_logs
            if (!empty($data['node_logs'])) {
                foreach ($data['node_logs'] as $nodeLog) {
                    $this->logRepo->createNodeLog($nodeLog);
                }
                $insertedCounts['node_logs'] = count($data['node_logs']);
            }

            Log::info('Data inserted successfully', [
                'sesi_id' => $sesiId,
                'counts' => $insertedCounts
            ]);

            return [
                'sesi_id_getdata' => $sesiId,
                'inserted_records' => $insertedCounts,
                'total_inserted' => array_sum($insertedCounts),
                'node_completeness' => $statistics['node_status']['completeness_percentage'] ?? 'N/A'
            ];
        });
    }

    public function getSensorData($filters = [])
    {
        return $this->sensorRepo->getHistory($filters, $filters['limit'] ?? 100);
    }

    public function getStatistics($sesiId = null)
    {
        return $this->sensorRepo->getStatistics($sesiId);
    }

    public function getLatestReadings($nodeId = null)
    {
        if ($nodeId) {
            return $this->cacheService->remember(
                "sensor_latest_{$nodeId}",
                CacheService::TTL_SHORT,
                fn() => $this->sensorRepo->getLatestForNode($nodeId)
            );
        }
        return $this->cacheService->remember(
            'sensor_latest_all',
            CacheService::TTL_SHORT,
            fn() => $this->sensorRepo->getLatestForDevices()
        );
    }

    public function getLatestWeatherData()
    {
        return $this->cacheService->remember(
            CacheService::KEY_DASHBOARD_WEATHER,
            CacheService::TTL_SHORT,
            fn() => $this->weatherRepo->getLatestWeatherData()
        );
    }
}