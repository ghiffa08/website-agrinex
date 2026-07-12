<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SensorDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SensorDataController extends Controller
{
    protected $sensorService;

    public function __construct(SensorDataService $sensorService)
    {
        $this->sensorService = $sensorService;
    }

    /**
     * Receive sensor data from Raspberry Pi
     * POST /api/v1/sensor-data
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request
            Log::info('Received sensor data', [
                'size' => strlen($request->getContent()),
                'ip' => $request->ip(),
                'sesi_id' => $request->header('X-Sesi-ID'),
                'data_size' => $request->header('X-Data-Size')
            ]);

            // Validate request structure
            $validator = Validator::make($request->all(), [
                'metadata' => 'required|array',
                'metadata.sesi_id_getdata' => 'required|integer|min:1',
                'metadata.timestamp' => 'required',
                'metadata.source' => 'required|string',
                'data' => 'required|array',
                'data.getdata_logs' => 'nullable|array',
                'data.sensor_weather_data' => 'nullable|array',
                'data.sensor_node_data' => 'nullable|array',
                'data.node_logs' => 'nullable|array',
                'statistics' => 'required|array',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'timestamp' => now()->toDateTimeString(),
                    'server' => 'Laravel AgriNex API'
                ], 422);
            }

            // Process data through service
            $result = $this->sensorService->processSensorData(
                $request->all()
            );

            \Illuminate\Support\Facades\Cache::forget('dashboard_devices_repo');
            \Illuminate\Support\Facades\Cache::forget('dashboard_weather_repo');
            broadcast(new \App\Events\DashboardDataUpdated());

            Log::info('Data processed successfully', [
                'sesi_id' => $result['sesi_id_getdata'],
                'total_records' => $result['total_inserted']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data received and saved successfully',
                'data' => $result,
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
                'server' => 'Laravel AgriNex API'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Sensor data processing failed', [
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
     * Get sensor data
     * GET /api/v1/sensor-data
     */
    public function index(Request $request)
    {
        try {
            $sesiId = $request->query('sesi_id');
            $nodeId = $request->query('node_id');
            $limit = $request->query('limit', 100);
            $orderBy = $request->query('order_by', 'received_at');
            $orderDir = $request->query('order_dir', 'desc');

            $data = $this->sensorService->getSensorData([
                'sesi_id' => $sesiId,
                'node_id' => $nodeId,
                'limit' => $limit,
                'order_by' => $orderBy,
                'order_dir' => $orderDir
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => count($data),
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve sensor data', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Get sensor data statistics
     * GET /api/v1/sensor-data/statistics
     */
    public function statistics(Request $request)
    {
        try {
            $sesiId = $request->query('sesi_id');
            $stats = $this->sensorService->getStatistics($sesiId);

            return response()->json([
                'success' => true,
                'statistics' => $stats,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Get latest sensor readings
     * GET /api/v1/sensor-data/latest
     */
    public function latest(Request $request)
    {
        try {
            $nodeId = $request->query('node_id');
            $latest = $this->sensorService->getLatestReadings($nodeId);

            return response()->json([
                'success' => true,
                'data' => $latest,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve latest data: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }
}
