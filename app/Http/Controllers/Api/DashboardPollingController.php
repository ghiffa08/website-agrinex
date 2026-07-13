<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use App\Services\SensorDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardPollingController extends Controller
{
    protected $deviceService;
    protected $sensorDataService;

    public function __construct(
        DeviceService $deviceService,
        SensorDataService $sensorDataService
    ) {
        $this->deviceService = $deviceService;
        $this->sensorDataService = $sensorDataService;
    }

    /**
     * Polling endpoint untuk dashboard
     * GET /api/v1/dashboard/poll
     */
    public function poll(Request $request)
    {
        try {
            $lastClientUpdate = $request->query('last_update', 0);
            $serverLastUpdate = Cache::get('dashboard_last_update', 0);

            // Jika tidak ada perubahan, kirim status 304 Not Modified
            if ($lastClientUpdate >= $serverLastUpdate && $serverLastUpdate > 0) {
                return response()->json([
                    'success' => true,
                    'has_changes' => false,
                    'last_update' => $serverLastUpdate,
                ], 200);
            }

            // Ada perubahan, kirim data lengkap
            $devicesData = $this->deviceService->getAllDevicesWithLatestData();
            $weatherData = $this->sensorDataService->getLatestWeatherData();

            return response()->json([
                'success' => true,
                'has_changes' => true,
                'last_update' => $serverLastUpdate ?: now()->timestamp,
                'data' => [
                    'devices' => $devicesData,
                    'weather' => $weatherData,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Polling failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Polling ringan untuk status devices saja
     * GET /api/v1/dashboard/poll-status
     */
    public function pollStatus(Request $request)
    {
        try {
            $lastClientUpdate = $request->query('last_update', 0);
            $serverLastUpdate = Cache::get('dashboard_last_update', 0);

            if ($lastClientUpdate >= $serverLastUpdate && $serverLastUpdate > 0) {
                return response()->json([
                    'success' => true,
                    'has_changes' => false,
                    'last_update' => $serverLastUpdate,
                ], 200);
            }

            // Hanya kirim status singkat tanpa data sensor lengkap
            $devices = Cache::remember('dashboard_status_only', 60, function () {
                return $this->deviceService->getDevicesStatusOnly();
            });

            return response()->json([
                'success' => true,
                'has_changes' => true,
                'last_update' => $serverLastUpdate ?: now()->timestamp,
                'data' => [
                    'devices' => $devices,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Status polling failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
