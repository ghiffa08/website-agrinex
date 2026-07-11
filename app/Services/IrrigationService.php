<?php

namespace App\Services;

use App\Repositories\Contracts\IrrigationRepositoryInterface;
use App\Repositories\Contracts\LogRepositoryInterface;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IrrigationService
{
    protected $irrigationRepo;
    protected $logRepo;
    protected $deviceRepo;

    public function __construct(
        IrrigationRepositoryInterface $irrigationRepo,
        LogRepositoryInterface $logRepo,
        DeviceRepositoryInterface $deviceRepo
    ) {
        $this->irrigationRepo = $irrigationRepo;
        $this->logRepo = $logRepo;
        $this->deviceRepo = $deviceRepo;
    }

    public function processIrrigationData(array $requestData)
    {
        $metadata = $requestData['metadata'];
        $data = $requestData['data'];

        $sesiId = $metadata['sesi_id_irrigate'];

        return DB::transaction(function () use ($data, $sesiId) {
            $insertedCounts = [];

            // 1. Insert irrigate_logs
            if (!empty($data['irrigate_logs'])) {
                foreach ($data['irrigate_logs'] as $log) {
                    $mappedLog = [
                        'sesi_id_irrigate' => $log['sesi_id_irrigate'] ?? $sesiId,
                        'waktu_mulai' => $log['waktu_mulai'] ?? now(),
                        'waktu_akhir' => $log['waktu_akhir'] ?? $log['waktu_selesai'] ?? null,
                        'node_sukses' => $log['node_sukses'] ?? 0,
                        'node_gagal' => $log['node_gagal'] ?? 0,
                        'valve_on_akhir' => $log['valve_on_akhir'] ?? 0,
                    ];
                    $this->irrigationRepo->createIrrigateLog($mappedLog);
                }
                $insertedCounts['irrigate_logs'] = count($data['irrigate_logs']);
            }

            // 2. Insert valve_logs
            if (!empty($data['valve_logs'])) {
                foreach ($data['valve_logs'] as $valve) {
                    $this->irrigationRepo->createValveLog(array_merge($valve, [
                        'sesi_id_irrigate' => $sesiId
                    ]));
                }
                $insertedCounts['valve_logs'] = count($data['valve_logs']);
            }

            // 3. Insert node_logs
            if (!empty($data['node_logs'])) {
                foreach ($data['node_logs'] as $nodeLog) {
                    $this->logRepo->createNodeLog($nodeLog);

                    // Auto-register master node if it doesn't exist
                    if (isset($nodeLog['node_id'])) {
                        $this->deviceRepo->firstOrCreateNode(
                            ['node_id' => $nodeLog['node_id']],
                            [
                                'group' => 'A',
                                'kode_perlakuan' => 'P' . $nodeLog['node_id'],
                                'lokasi' => 'Otomatis dari API Irigasi',
                                'keterangan' => 'Node ' . $nodeLog['node_id'] . ' didaftarkan otomatis'
                            ]
                        );
                    }
                }
                $insertedCounts['node_logs'] = count($data['node_logs']);
            }

            Log::info('Irrigation data inserted successfully', [
                'sesi_id' => $sesiId,
                'counts' => $insertedCounts
            ]);

            return [
                'sesi_id_irrigate' => $sesiId,
                'inserted_records' => $insertedCounts,
                'total_inserted' => array_sum($insertedCounts)
            ];
        });
    }

    public function getIrrigationData($sesiId = null, $limit = 100)
    {
        return $this->irrigationRepo->getHistory($sesiId ? ['sesi_id' => $sesiId] : [], $limit);
    }

    public function processValveOffData(array $requestData)
    {
        $metadata = $requestData['metadata'];
        $data = $requestData['data'];

        $nodeId = $metadata['node_id'];

        return DB::transaction(function () use ($data, $nodeId) {
            $insertedCounts = [];

            // Insert valve_logs for valve OFF events
            if (!empty($data['valve_logs'])) {
                foreach ($data['valve_logs'] as $valve) {
                    $this->irrigationRepo->createValveLog($valve);
                }
                $insertedCounts['valve_logs'] = count($data['valve_logs']);
            }

            // Auto-register master node if it doesn't exist
            if ($nodeId) {
                $this->deviceRepo->firstOrCreateNode(
                    ['node_id' => $nodeId],
                    [
                        'group' => 'A',
                        'kode_perlakuan' => 'P' . $nodeId,
                        'lokasi' => 'Otomatis dari API Valve Off',
                        'keterangan' => 'Node ' . $nodeId . ' didaftarkan otomatis'
                    ]
                );
            }

            Log::info('Valve OFF data inserted successfully', [
                'node_id' => $nodeId,
                'counts' => $insertedCounts
            ]);

            return [
                'node_id' => $nodeId,
                'inserted_records' => $insertedCounts,
                'total_inserted' => array_sum($insertedCounts)
            ];
        });
    }
}
