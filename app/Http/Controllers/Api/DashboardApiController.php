<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JsonBackup;
use Illuminate\Http\Request;
use App\Services\ChartDataService;
use App\Http\Resources\ChartDataResource;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardApiController extends Controller
{
    protected $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Get all devices with latest sensor data
     * GET /api/v1/dashboard/devices
     */
    public function getDevices()
    {
        try {
            $devices = $this->dashboardRepository->getDevices();

            if (empty($devices)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No nodes found'
                ]);
            }

            return response()->json([
                'success' => true,
                'data'    => $devices,
                'session_info' => [
                    'total_nodes' => count($devices),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching devices: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get water tank information
     * GET /api/v1/dashboard/tank
     */
    public function getTank()
    {
        try {
            $tank = $this->dashboardRepository->getTank();

            return response()->json([
                'success' => true,
                'data' => $tank
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => null,
                    'tank_name' => 'Tangki Air Utama',
                    'name' => 'Tangki Air Utama',
                    'capacity' => 0,
                    'capacity_liters' => 0,
                    'current_volume_liters' => 0,
                    'water_level_cm' => 0,
                    'percentage' => 0,
                    'status' => 'no_data',
                    'updated_at' => null
                ],
                'note' => 'No data available'
            ]);
        }
    }
    
    /**
     * Get irrigation schedule
     * GET /api/v1/dashboard/schedule
     */
    public function getSchedule()
    {
        try {
            $schedule = $this->dashboardRepository->getSchedule();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => today()->format('Y-m-d'),
                    'total_sessions' => count($schedule),
                    'sessions' => $schedule
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => today()->format('Y-m-d'),
                    'total_sessions' => 0,
                    'sessions' => []
                ],
                'note' => 'No data available: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get 30-day water usage history
     * GET /api/v1/dashboard/usage
     */
    public function getUsage()
    {
        try {
            $usage = $this->dashboardRepository->getUsage();
            
            return response()->json([
                'success' => true,
                'data' => $usage,
                'summary' => [
                    'total_days' => count($usage),
                    'total_usage_l' => array_sum(array_column($usage, 'total_l')),
                    'average_usage_l' => count($usage) > 0 ? round(array_sum(array_column($usage, 'total_l')) / count($usage), 2) : 0,
                    'period' => '30 days'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => [],
                'summary' => [
                    'total_days' => 0,
                    'total_usage_l' => 0,
                    'average_usage_l' => 0,
                    'period' => '30 days'
                ],
                'note' => 'No data available: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get 24-hour water usage history (hourly)
     * GET /api/v1/dashboard/usage/daily
     */
    public function getUsageDaily()
    {
        try {
            $usage = $this->dashboardRepository->getUsageDaily();
            
            return response()->json([
                'success' => true,
                'data' => $usage,
                'summary' => [
                    'total_hours' => count($usage),
                    'total_usage_l' => array_sum(array_column($usage, 'total_l')),
                    'average_usage_l' => count($usage) > 0 ? round(array_sum(array_column($usage, 'total_l')) / count($usage), 2) : 0,
                    'period' => '24 hours'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => [],
                'summary' => [
                    'total_hours' => 0,
                    'total_usage_l' => 0,
                    'average_usage_l' => 0,
                    'period' => '24 hours'
                ],
                'note' => 'No data available: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get chart data for environmental monitoring
     * GET /api/v1/dashboard/charts
     */
    public function getChartData(Request $request, ChartDataService $chartService)
    {
        try {
            $type = $request->input('type', 'all');
            $days = $request->input('days', 7);
            $limit = $request->input('limit', null);
            
            $result = $chartService->getSessions($days, $limit);
            
            $formattedData = (new ChartDataResource($result['sessions']))
                                ->setType($type)
                                ->resolve();
            
            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'meta' => [
                    'total_points' => $result['sessions']->count(),
                    'time_range_days' => $days ?? ($limit ? 'limited' : 7),
                    'start_time' => $result['start_time']->format('Y-m-d H:i:s'),
                    'end_time' => now()->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get current weather from latest sensor reading
     * GET /api/v1/dashboard/weather
     */
    public function getWeather()
    {
        try {
            $weather = $this->dashboardRepository->getWeather();
            
            if (!$weather) {
                return response()->json([
                    'success' => false,
                    'message' => 'No weather data available'
                ], 200); // 200 to prevent console errors
            }
            
            return response()->json([
                'success' => true,
                'data' => $weather
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching weather: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get JSON backup history
     * GET /api/v1/dashboard/json-backup
     */
    public function getJsonBackup(Request $request)
    {
        try {
            $query = JsonBackup::orderBy('backup_timestamp', 'desc');
            
            // Filter by sesi_id_getdata if provided
            if ($request->has('sesi_id_getdata')) {
                $query->where('sesi_id_getdata', $request->sesi_id_getdata);
            }
            
            // Filter by date range if provided
            if ($request->has('date_from')) {
                $query->whereDate('backup_timestamp', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('backup_timestamp', '<=', $request->date_to);
            }
            
            // Limit results
            $limit = min($request->get('limit', 50), 200);
            $backups = $query->limit($limit)->get();
            
            return response()->json([
                'success' => true,
                'data' => $backups,
                'metadata' => [
                    'total_records' => $backups->count(),
                    'limit' => $limit,
                    'filters_applied' => [
                        'sesi_id_getdata' => $request->sesi_id_getdata,
                        'date_from' => $request->date_from,
                        'date_to' => $request->date_to,
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching JSON backup: ' . $e->getMessage()
            ], 500);
        }
    }
}
