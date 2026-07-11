<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Repositories\Contracts\SensorDataRepositoryInterface;
use App\Repositories\Contracts\LogRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NodesController extends Controller
{
    protected $deviceRepo;
    protected $sensorRepo;
    protected $logRepo;

    public function __construct(
        DeviceRepositoryInterface $deviceRepo,
        SensorDataRepositoryInterface $sensorRepo,
        LogRepositoryInterface $logRepo
    ) {
        $this->deviceRepo = $deviceRepo;
        $this->sensorRepo = $sensorRepo;
        $this->logRepo = $logRepo;
    }

    public function index()
    {
        // Get all devices
        $nodes = $this->deviceRepo->allDevices();

        // Get latest log for each device
        $latestLogs = $this->logRepo->getLatestLogsForDevices();

        // Attach latest log to each node
        foreach ($nodes as $node) {
            $node->latestLog = $latestLogs->firstWhere('device_id', $node->id);
            $node->is_online = $node->latestLog && 
                               $node->latestLog->is_active &&
                               now()->diffInHours($node->latestLog->logged_at) < 24;
            $node->latestSensorData = $node->sensorData->first();
        }

        // Calculate statistics based on latest logs only
        $stats = [
            'total' => $nodes->count(),
            'active' => $latestLogs->where('is_active', true)->count(),
            'online' => $latestLogs->where('is_active', true)->count(),
            'offline' => $latestLogs->where('is_active', false)->count(),
            'alerts' => $latestLogs->whereNotIn('signal_quality', ['Good', 'Excellent'])->count(),
        ];

        return view('nodes.index', compact('nodes', 'stats'));
    }

    public function show($id)
    {
        $node = $this->deviceRepo->findById($id);

        if (!$node) {
            abort(404, 'Node not found');
        }

        // Get sensor data for the last 24 hours
        $sensorData = $this->sensorRepo->getSensorDataHistory([
            'device_id' => $id,
            'start_date' => now()->subDay()
        ], 1000);

        // Get communication logs for the last 7 days
        $logs = $this->logRepo->getLogsForDevice($id, [
            'start_date' => now()->subDays(7)
        ], 50);

        $latestSensorData = $this->sensorRepo->getLatestForDevice($id);

        // Prepare chart data
        $chartData = [
            'labels' => $sensorData->pluck('recorded_at')->map(function ($date) {
                return Carbon::parse($date)->format('H:i');
            }),
            'temperature' => $sensorData->pluck('temperature'),
            'moisture' => $sensorData->pluck('soil_moisture'),
        ];

        // Calculate statistics with default values if no data
        $stats = [
            'avg_temperature' => $sensorData->count() > 0 ? round($sensorData->avg('temperature'), 1) : 0,
            'avg_moisture' => $sensorData->count() > 0 ? round($sensorData->avg('soil_moisture'), 1) : 0,
            'min_temperature' => $sensorData->count() > 0 ? round($sensorData->min('temperature'), 1) : 0,
            'max_temperature' => $sensorData->count() > 0 ? round($sensorData->max('temperature'), 1) : 0,
            'min_moisture' => $sensorData->count() > 0 ? round($sensorData->min('soil_moisture'), 1) : 0,
            'max_moisture' => $sensorData->count() > 0 ? round($sensorData->max('soil_moisture'), 1) : 0,
            'avg_rssi' => $logs->count() > 0 ? round($logs->avg('rssi_dbm'), 1) : 0,
            'total_readings' => $sensorData->count(),
        ];

        return view('nodes.show', compact('node', 'sensorData', 'logs', 'chartData', 'stats', 'latestSensorData'));
    }

    /**
     * Show the form for editing the specified node
     */
    public function edit($id)
    {
        $node = $this->deviceRepo->findById($id);
        
        if (!$node) {
            abort(404, 'Node not found');
        }

        return view('nodes.edit', compact('node'));
    }

    /**
     * Update the specified node in storage
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group' => 'nullable|string|max:50',
            'kode_perlakuan' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $this->deviceRepo->update($id, $validated);

        return redirect()
            ->route('nodes.show', $id)
            ->with('success', 'Device information updated successfully!');
    }
}
