<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DeviceLog;
use App\Models\SensorData;
use App\Models\Device;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    /**
     * Display alerts dashboard
     */
    public function index()
    {
        // Get communication failures (bad signal quality)
        $commFailures = DeviceLog::whereNotIn('signal_quality', ['Good', 'Excellent'])
            ->orWhere('rssi_dbm', '<', -120)
            ->with('device')
            ->latest('logged_at')
            ->limit(50)
            ->get();

        // Get low soil moisture alerts (< 30%)
        $lowMoisture = SensorData::where('soil_moisture', '<', 30)
            ->where('recorded_at', '>=', now()->subDay())
            ->with('device')
            ->latest('recorded_at')
            ->limit(50)
            ->get();

        // Get high temperature alerts (> 35°C)
        $highTemp = SensorData::where('temperature', '>', 35)
            ->where('recorded_at', '>=', now()->subDay())
            ->with('device')
            ->latest('recorded_at')
            ->limit(50)
            ->get();

        // Get low voltage alerts (< 3.0V)
        $lowVoltage = SensorData::where('voltage_v', '<', 3.0)
            ->where('recorded_at', '>=', now()->subDay())
            ->with('device')
            ->latest('recorded_at')
            ->limit(50)
            ->get();

        // Get offline nodes (no data in last 2 hours)
        $offlineNodes = Device::whereDoesntHave('sensorData', function($query) {
            $query->where('recorded_at', '>=', now()->subHours(2));
        })->get();

        // Statistics
        $stats = [
            'critical' => $lowMoisture->count() + $offlineNodes->count(),
            'warning' => $highTemp->count() + $lowVoltage->count(),
            'info' => $commFailures->count(),
            'total' => $commFailures->count() + $lowMoisture->count() + $highTemp->count() + $lowVoltage->count() + $offlineNodes->count(),
        ];

        return view('alerts.index', compact(
            'commFailures', 
            'lowMoisture', 
            'highTemp', 
            'lowVoltage', 
            'offlineNodes',
            'stats'
        ));
    }

    /**
     * Get alerts by type
     */
    public function byType($type)
    {
        $alerts = collect();
        $title = '';

        switch($type) {
            case 'communication':
                $alerts = DeviceLog::whereNotIn('signal_quality', ['Good', 'Excellent'])
                    ->orWhere('rssi_dbm', '<', -120)
                    ->with('device')
                    ->latest('logged_at')
                    ->paginate(50);
                $title = 'Communication Issues';
                break;
            
            case 'moisture':
                $alerts = SensorData::where('soil_moisture', '<', 30)
                    ->where('recorded_at', '>=', now()->subDay())
                    ->with('device')
                    ->latest('recorded_at')
                    ->paginate(50);
                $title = 'Low Soil Moisture Alerts';
                break;
            
            case 'temperature':
                $alerts = SensorData::where('temperature', '>', 35)
                    ->where('recorded_at', '>=', now()->subDay())
                    ->with('device')
                    ->latest('recorded_at')
                    ->paginate(50);
                $title = 'High Temperature Alerts';
                break;
            
            case 'voltage':
                $alerts = SensorData::where('voltage_v', '<', 3.0)
                    ->where('recorded_at', '>=', now()->subDay())
                    ->with('device')
                    ->latest('recorded_at')
                    ->paginate(50);
                $title = 'Low Voltage Alerts';
                break;
        }

        return view('alerts.by-type', compact('alerts', 'title', 'type'));
    }
}
