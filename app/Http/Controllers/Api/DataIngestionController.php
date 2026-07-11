<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SensorDataService;
use App\Services\IrrigationService;
use App\Repositories\Contracts\JsonBackupRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DataIngestionController extends Controller
{
    protected $sensorDataService;
    protected $irrigationService;
    protected $backupRepo;

    public function __construct(
        SensorDataService $sensorDataService,
        IrrigationService $irrigationService,
        JsonBackupRepositoryInterface $backupRepo
    ) {
        $this->sensorDataService = $sensorDataService;
        $this->irrigationService = $irrigationService;
        $this->backupRepo = $backupRepo;
    }

    /**
     * POST Sensor Data (getdata)
     * Endpoint untuk menerima data sensor dari Raspberry Pi
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSensorData(Request $request)
    {
        try {
            // Validate request structure
            $validator = Validator::make($request->all(), [
                'metadata' => 'required|array',
                'metadata.sesi_id_getdata' => 'required|integer',
                'metadata.timestamp' => 'required|date',
                'data' => 'required|array',
                'data.getdata_logs' => 'array',
                'data.sensor_weather_data' => 'array',
                'data.sensor_node_data' => 'array',
                'data.node_logs' => 'array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'timestamp' => now()->toDateTimeString(),
                ], 422);
            }

            // Backup JSON request to json_backup table
            $requestData = $request->all();
            $jsonString = json_encode($requestData);
            $dataSizeKb = round(strlen($jsonString) / 1024, 2);

            // Calculate statistics from request
            $statistics = $requestData['statistics'] ?? [];
            $recordsByTable = $statistics['records_by_table'] ?? [];
            
            $this->backupRepo->createBackup([
                'sesi_id_getdata' => $request->input('metadata.sesi_id_getdata'),
                'json_data' => $requestData,
                'data_size_kb' => $dataSizeKb,
                'total_records' => $statistics['total_records'] ?? 0,
                'node_completeness' => $statistics['node_status']['completeness_percentage'] ?? '0.00%',
                'getdata_logs_count' => $recordsByTable['getdata_logs'] ?? 0,
                'sensor_weather_count' => $recordsByTable['sensor_weather_data'] ?? 0,
                'sensor_node_count' => $recordsByTable['sensor_node_data'] ?? 0,
                'backup_timestamp' => now()
            ]);

            // Process sensor data
            $result = $this->sensorDataService->processSensorData($requestData);

            Log::info('Sensor data ingestion successful', [
                'sesi_id' => $request->input('metadata.sesi_id_getdata'),
                'result' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sensor data processed successfully',
                'data' => $result,
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Sensor data ingestion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Processing error: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 500);
        }
    }

    /**
     * POST Valve ON Data (irrigate)
     * Endpoint untuk menerima data valve ON dan irigasi
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeValveOn(Request $request)
    {
        try {
            // Validate request structure
            $validator = Validator::make($request->all(), [
                'metadata' => 'required|array',
                'metadata.sesi_id_irrigate' => 'required|integer',
                'metadata.timestamp' => 'required|date',
                'data' => 'required|array',
                'data.irrigate_logs' => 'array',
                'data.valve_logs' => 'array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'timestamp' => now()->toDateTimeString(),
                ], 422);
            }

            // Backup JSON request to json_backup table
            $requestData = $request->all();
            $jsonString = json_encode($requestData);
            $dataSizeKb = round(strlen($jsonString) / 1024, 2);

            // Calculate statistics from request
            $statistics = $requestData['statistics'] ?? [];
            $recordsByTable = $statistics['records_by_table'] ?? [];
            
            $backup = $this->backupRepo->createBackup([
                'sesi_id_getdata' => $request->input('metadata.sesi_id_irrigate'),
                'json_data' => $requestData,
                'data_size_kb' => $dataSizeKb,
                'total_records' => $statistics['total_records'] ?? 0,
                'node_completeness' => $statistics['irrigation_summary']['nodes_irrigated'] ?? 0 . ' nodes',
                'getdata_logs_count' => 0,
                'sensor_weather_count' => 0,
                'sensor_node_count' => $recordsByTable['valve_logs'] ?? 0,
                'backup_timestamp' => now()
            ]);

            // Process irrigation data
            $result = $this->irrigationService->processIrrigationData($requestData);
            
            // Add backup info to result
            $result['backup_info'] = [
                'json_backup_id' => $backup->id,
                'data_size_kb' => $dataSizeKb,
                'backup_timestamp' => $backup->backup_timestamp->toIso8601String()
            ];

            Log::info('Valve ON data ingestion successful', [
                'sesi_id' => $request->input('metadata.sesi_id_irrigate'),
                'result' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Valve ON data processed successfully',
                'data' => $result,
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Valve ON data ingestion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Processing error: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 500);
        }
    }

    /**
     * POST Valve OFF Data (valve logs)
     * Endpoint untuk menerima data valve OFF
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeValveOff(Request $request)
    {
        try {
            // Validate request structure
            $validator = Validator::make($request->all(), [
                'metadata' => 'required|array',
                'metadata.node_id' => 'required|integer',
                'metadata.timestamp' => 'required|date',
                'data' => 'required|array',
                'data.valve_logs' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'timestamp' => now()->toDateTimeString(),
                ], 422);
            }

            // Backup JSON request to json_backup table
            $requestData = $request->all();
            $jsonString = json_encode($requestData);
            $dataSizeKb = round(strlen($jsonString) / 1024, 2);

            // Calculate statistics from request
            $statistics = $requestData['statistics'] ?? [];
            $sessions = $statistics['sessions'] ?? [];
            
            $backup = $this->backupRepo->createBackup([
                'sesi_id_getdata' => $sessions[0] ?? $request->input('metadata.node_id'),
                'json_data' => $requestData,
                'data_size_kb' => $dataSizeKb,
                'total_records' => $statistics['total_records'] ?? 0,
                'node_completeness' => 'Node ' . $request->input('metadata.node_id'),
                'getdata_logs_count' => 0,
                'sensor_weather_count' => 0,
                'sensor_node_count' => $statistics['total_records'] ?? 0,
                'backup_timestamp' => now()
            ]);

            // Process valve OFF data
            $result = $this->irrigationService->processValveOffData($requestData);
            
            // Add backup info to result
            $result['backup_info'] = [
                'json_backup_id' => $backup->id,
                'data_size_kb' => $dataSizeKb,
                'backup_timestamp' => $backup->backup_timestamp->toIso8601String()
            ];

            Log::info('Valve OFF data ingestion successful', [
                'node_id' => $request->input('metadata.node_id'),
                'result' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Valve OFF data processed successfully',
                'data' => $result,
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Valve OFF data ingestion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Processing error: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 500);
        }
    }

    /**
     * Health Check
     * Endpoint untuk mengecek status API
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function healthCheck()
    {
        return response()->json([
            'success' => true,
            'message' => 'AgriNex Data Ingestion API is running',
            'version' => '2.0',
            'timestamp' => now()->toDateTimeString(),
            'endpoints' => [
                'sensor_data' => '/api/v1/ingest/sensor-data',
                'valve_on' => '/api/v1/ingest/valve-on',
                'valve_off' => '/api/v1/ingest/valve-off',
            ]
        ]);
    }
}
