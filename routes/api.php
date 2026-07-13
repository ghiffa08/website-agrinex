<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\IrrigationController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\DataIngestionController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\TelemetryApiController;
use App\Http\Controllers\Api\DashboardPollingController;
use App\Http\Controllers\Api\DeviceDetailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API v1
Route::post('/telemetry', [TelemetryApiController::class, 'store']);
Route::get('/nodes/config', [\App\Http\Controllers\Api\NodeConfigController::class, 'getConfig']);
Route::post('/nodes/config', [\App\Http\Controllers\Api\NodeConfigController::class, 'updateConfig']);
Route::prefix('v1')->group(function () {
    
    // Data Ingestion Endpoints (NEW - for IoT devices)
    Route::prefix('ingest')->middleware(['throttle:60,1', 'iot.api'])->group(function () {
        Route::get('/health', [DataIngestionController::class, 'healthCheck']);
        Route::post('/sensor-data', [DataIngestionController::class, 'storeSensorData']);
        Route::post('/valve-on', [DataIngestionController::class, 'storeValveOn']);
        Route::post('/valve-off', [DataIngestionController::class, 'storeValveOff']);
    });
    
    // Dashboard Endpoints (NEW - for web dashboard)
    Route::prefix('dashboard')->group(function () {
        Route::get('/poll', [DashboardPollingController::class, 'poll']);
        Route::get('/poll-status', [DashboardPollingController::class, 'status']);
        Route::get('/devices', [DashboardApiController::class, 'getDevices']);
        Route::get('/tank', [DashboardApiController::class, 'getTank']);
        Route::get('/schedule', [DashboardApiController::class, 'getSchedule']);
        Route::get('/usage', [DashboardApiController::class, 'getUsage']);
        Route::get('/usage/daily', [DashboardApiController::class, 'getUsageDaily']);
        Route::get('/charts', [DashboardApiController::class, 'getChartData']);
        Route::get('/weather', [DashboardApiController::class, 'getWeather']);
        Route::get('/json-backup', [DashboardApiController::class, 'getJsonBackup']);
    });
    
    // Device detail endpoints
    Route::prefix('devices/{deviceId}')->group(function () {
        Route::get('/sleep-history', [DeviceDetailController::class, 'sleepHistory']);
        Route::get('/irrigation/sessions', [DeviceDetailController::class, 'irrigationSessions']);
        Route::get('/usage-history', [DeviceDetailController::class, 'usageHistory']);
        Route::get('/chart-data', [DeviceDetailController::class, 'chartData']);
    });
    
    // Sensor Data Endpoints
    Route::prefix('sensor-data')->group(function () {
        Route::post('/', [SensorDataController::class, 'store']);
        Route::get('/', [SensorDataController::class, 'index']);
        Route::get('/statistics', [SensorDataController::class, 'statistics']);
        Route::get('/latest', [SensorDataController::class, 'latest']);
    });
    
    // Irrigation Endpoints
    Route::prefix('irrigation')->group(function () {
        Route::post('/', [IrrigationController::class, 'store']);
        Route::get('/', [IrrigationController::class, 'index']);
        Route::get('/statistics', [IrrigationController::class, 'statistics']);
    });
    
    // Export Endpoints
    Route::prefix('export')->group(function () {
        Route::get('/', [ExportController::class, 'export']);
        Route::get('/download/{filename}', [ExportController::class, 'download']);
        Route::get('/list', [ExportController::class, 'list']);
    });
    
    // Monitor Endpoints
    Route::prefix('monitor')->group(function () {
        Route::get('/stats', [MonitorController::class, 'getStats']);
        Route::get('/logs', [MonitorController::class, 'getLogs']);
        Route::get('/health', [MonitorController::class, 'health']);
        Route::get('/nodes', [MonitorController::class, 'nodes']);
    });
});

// BMKG Weather Proxy (untuk bypass CORS)
Route::get('/bmkg/forecast', [WeatherController::class, 'getForecast']);



// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'AgriNex API',
        'version' => '2.0',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// API Documentation
Route::get('/docs', function () {
    return response()->json([
        'service' => 'AgriNex API',
        'version' => '2.0',
        'description' => 'Smart Agriculture IoT Data Collection API',
        'endpoints' => [
            'dashboard' => [
                'GET /api/v1/dashboard/devices' => 'Get all devices with latest sensor data',
                'GET /api/v1/dashboard/tank' => 'Get water tank information',
                'GET /api/v1/dashboard/schedule' => 'Get irrigation schedule',
                'GET /api/v1/dashboard/usage' => 'Get 30-day water usage history',
                'GET /api/v1/dashboard/usage/daily' => 'Get 24-hour hourly usage',
                'GET /api/v1/dashboard/charts' => 'Get chart data (temperature, humidity, etc)',
                'GET /api/v1/dashboard/weather' => 'Get current weather from sensors',
            ],
            'sensor_data' => [
                'POST /api/v1/sensor-data' => 'Submit sensor data',
                'GET /api/v1/sensor-data' => 'Get sensor data',
                'GET /api/v1/sensor-data/statistics' => 'Get statistics',
                'GET /api/v1/sensor-data/latest' => 'Get latest readings',
            ],
            'irrigation' => [
                'POST /api/v1/irrigation' => 'Submit irrigation data',
                'GET /api/v1/irrigation' => 'Get irrigation logs',
                'GET /api/v1/irrigation/statistics' => 'Get irrigation statistics',
            ],
            'export' => [
                'GET /api/v1/export' => 'Export data (format=json|csv|sql)',
                'GET /api/v1/export/list' => 'List available exports',
                'GET /api/v1/export/download/{filename}' => 'Download export file',
            ],
            'monitor' => [
                'GET /api/v1/monitor/stats' => 'Get system statistics',
                'GET /api/v1/monitor/logs' => 'Get recent logs',
                'GET /api/v1/monitor/health' => 'System health check',
                'GET /api/v1/monitor/nodes' => 'Get node status',
            ],
            'legacy' => [
                'POST /api/index.php' => 'Legacy sensor data endpoint',
                'POST /api/api_getdata.php' => 'Legacy getdata endpoint',
                'POST /api/api_irrigate.php' => 'Legacy irrigation endpoint',
                'GET /api/export_data.php' => 'Legacy export endpoint',
            ]
        ],
        'documentation_url' => url('/api/docs'),
        'support' => 'agrinex@example.com'
    ]);
});

