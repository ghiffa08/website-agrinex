<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\SensorData;

class MonitorController extends Controller
{
    public function __construct(
        protected MonitorRepositoryInterface $monitorRepo
    ) {}

    /**
     * Get API statistics
     * GET /api/v1/monitor/stats
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = [
                'database' => [
                    'status' => 'connected',
                    'type' => config('database.default'),
                    'name' => config('database.connections.mysql.database')
                ],
                'tables' => $this->monitorRepo->getDatabaseStats(),
                'latest_sessions' => $this->monitorRepo->getLatestSessions(),
                'today' => $this->monitorRepo->getTodayStats(),
                'server' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'timezone' => config('app.timezone'),
                    'environment' => config('app.env'),
                ]
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Get recent logs
     * GET /api/v1/monitor/logs
     */
    public function getLogs(Request $request): JsonResponse
    {
        try {
            $type = $request->query('type', 'all'); // getdata, irrigate, all
            $limit = (int) $request->query('limit', 50);

            $logs = $this->monitorRepo->getRecentLogs($type, $limit);

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve logs',
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Check system health
     * GET /api/v1/monitor/health
     */
    public function health(): JsonResponse
    {
        try {
            $health = [
                'status' => 'healthy',
                'checks' => []
            ];

            // Database check
            try {
                DB::connection()->getPdo();
                $health['checks']['database'] = [
                    'status' => 'ok',
                    'message' => 'Database connection successful'
                ];
            } catch (\Exception $e) {
                $health['status'] = 'unhealthy';
                $health['checks']['database'] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }

            // Storage check
            $storagePath = storage_path('app');
            $health['checks']['storage'] = [
                'status' => is_writable($storagePath) ? 'ok' : 'error',
                'writable' => is_writable($storagePath),
                'path' => $storagePath
            ];

            // Logs check
            $logsPath = storage_path('logs');
            $health['checks']['logs'] = [
                'status' => is_writable($logsPath) ? 'ok' : 'error',
                'writable' => is_writable($logsPath),
                'path' => $logsPath
            ];

            return response()->json([
                'success' => true,
                'health' => $health,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Health check failed: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Get node status
     * GET /api/v1/monitor/nodes
     */
    public function nodes(Request $request): JsonResponse
    {
        try {
            $sesiId = $request->query('sesi_id');
            
            $query = SensorData::select('device_id as node_id')
                ->selectRaw('MAX(recorded_at) as last_seen')
                ->selectRaw('COUNT(*) as total_readings')
                ->selectRaw('AVG(voltage_v) as avg_voltage')
                ->selectRaw('AVG(temp_c) as avg_temperature')
                ->selectRaw('AVG(soil_pct) as avg_soil_moisture')
                ->groupBy('device_id')
                ->orderBy('device_id');

            if ($sesiId) {
                // If filtering by session ID, we need to join with data_sessions to find by session_id 
                // OR filter by data_session_id directly. The front end might still send the string session_id.
                // Assuming it sends the string `session_id`, let's do a whereHas:
                $query->whereHas('dataSession', function($q) use ($sesiId) {
                    $q->where('session_id', $sesiId);
                });
            }

            $nodes = $query->get();

            return response()->json([
                'success' => true,
                'nodes' => $nodes,
                'count' => count($nodes),
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve node status: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }
}
