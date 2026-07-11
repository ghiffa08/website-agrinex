<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DataSession;
use App\Models\IrrigationLog;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SensorData;
use App\Models\WeatherData;
use App\Models\ValveLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        // Get all devices (formerly nodes)
        $nodes = Device::all();
        
        // Statistics
        $stats = [
            'total_nodes' => $nodes->count(),
            'active_nodes' => $this->getActiveNodes(),
            'total_plots' => $nodes->whereNotNull('kode_perlakuan')->count(),
            'active_alerts' => $this->getActiveAlerts(),
            'ongoing_irrigation' => IrrigationLog::whereNull('ended_at')->count(),
        ];

        // Get latest sensor readings for each device
        $nodesWithData = $nodes->map(function ($node) {
            $latestData = SensorData::where('device_id', $node->id)
                ->latest('recorded_at')
                ->first();
            
            $latestLog = DeviceLog::where('device_id', $node->id)
                ->latest('logged_at')
                ->first();
            
            $node->latestReading = $latestData;
            $node->lastCommunication = $latestLog?->logged_at;
            $node->is_active = $latestLog && Carbon::parse($latestLog->logged_at)->gt(Carbon::now()->subHours(24));
            
            return $node;
        });

        // Get latest weather data (Assuming Device ID 65 is still weather)
        $weather = WeatherData::where('device_id', 65)
            ->latest('recorded_at')
            ->first();

        // Get recent alerts (from node logs with status issues)
        $recentAlerts = $this->getRecentAlerts();

        // Get today's irrigation events
        $todayIrrigation = IrrigationLog::whereDate('started_at', Carbon::today())
            ->latest('started_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'nodes',
            'nodesWithData',
            'weather',
            'recentAlerts',
            'todayIrrigation'
        ));
    }

    /**
     * Get chart data for specific node
     */
    public function chartData(Request $request)
    {
        $nodeId = $request->input('node_id');
        $hours = $request->input('hours', 24);
        
        $startTime = Carbon::now()->subHours($hours);
        
        // Get sensor node data
        $sensorData = SensorData::where('device_id', $nodeId)
            ->where('recorded_at', '>=', $startTime)
            ->orderBy('recorded_at')
            ->get();
        
        // Get weather data
        $weatherData = WeatherData::where('device_id', 65)
            ->where('recorded_at', '>=', $startTime)
            ->orderBy('recorded_at')
            ->get();
        
        $labels = $sensorData->pluck('recorded_at')->map(function ($date) {
            return Carbon::parse($date)->format('H:i');
        });
        
        return response()->json([
            'labels' => $labels,
            'soil_moisture' => $sensorData->pluck('soil_moisture'),
            'soil_temperature' => $sensorData->pluck('temperature'),
            'air_temperature' => $weatherData->pluck('temp_c'),
            'air_humidity' => $weatherData->pluck('humidity_pct'),
        ]);
    }

    /**
     * Get realtime data for dashboard refresh
     */
    public function realtimeData()
    {
        $nodes = Device::all()->map(function ($node) {
            $latestData = SensorData::where('device_id', $node->id)
                ->latest('recorded_at')
                ->first();
            
            return [
                'node_id' => $node->id,
                'node_code' => $node->id,
                'soil_moisture' => $latestData?->soil_moisture,
                'temperature' => $latestData?->temperature,
                'last_reading' => $latestData?->recorded_at,
            ];
        });

        return response()->json([
            'nodes' => $nodes,
            'active_alerts' => $this->getActiveAlerts(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Show the monitor page
     */
    public function monitor()
    {
        return view('monitor');
    }

    /**
     * Get count of active nodes (communicated in last 24 hours)
     */
    private function getActiveNodes()
    {
        return DeviceLog::where('is_active', true)
            ->where('logged_at', '>=', Carbon::now()->subHours(24))
            ->distinct('device_id')
            ->count();
    }

    /**
     * Get active alerts from node logs
     */
    private function getActiveAlerts()
    {
        return DeviceLog::where('logged_at', '>=', Carbon::now()->subHours(24))
            ->where('is_active', false)
            ->orWhereRaw("LOWER(remarks) LIKE '%error%'")
            ->orWhereRaw("LOWER(remarks) LIKE '%gagal%'")
            ->count();
    }

    /**
     * Get recent alerts
     */
    private function getRecentAlerts()
    {
        $alerts = [];
        
        // Check for nodes with communication issues
        $failedNodes = DeviceLog::where('logged_at', '>=', Carbon::now()->subHours(24))
            ->where('is_active', false)
            ->latest('logged_at')
            ->limit(5)
            ->get();
        
        foreach ($failedNodes as $log) {
            $alerts[] = (object)[
                'severity' => 'warning',
                'message' => "Device {$log->device_id} communication failed",
                'timestamp' => $log->logged_at,
            ];
        }
        
        // Check for low soil moisture
        $lowMoisture = SensorData::where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->where('soil_moisture', '<', 30)
            ->latest('recorded_at')
            ->limit(3)
            ->get();
        
        foreach ($lowMoisture as $reading) {
            $alerts[] = (object)[
                'severity' => 'critical',
                'message' => "Low soil moisture on Device {$reading->device_id} ({$reading->soil_moisture}%)",
                'timestamp' => $reading->recorded_at,
            ];
        }
        
        // Sort by timestamp
        usort($alerts, function ($a, $b) {
            return Carbon::parse($b->timestamp)->timestamp - Carbon::parse($a->timestamp)->timestamp;
        });
        
        return array_slice($alerts, 0, 5);
    }
}
