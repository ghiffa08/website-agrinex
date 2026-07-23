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
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LahanPantauController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API v1
Route::post('/telemetry', [TelemetryApiController::class, 'store'])
    ->middleware(['throttle:120,1', 'iot.api']);
Route::get('/nodes/config', [\App\Http\Controllers\Api\NodeConfigController::class, 'getConfig'])
    ->middleware('throttle:60,1');
Route::post('/nodes/config', [\App\Http\Controllers\Api\NodeConfigController::class, 'updateConfig'])
    ->middleware(['throttle:10,1']);
Route::prefix('v1')->group(function () {
    
    // Data Ingestion Endpoints (NEW - for IoT devices)
    Route::prefix('ingest')->middleware(['throttle:60,1', 'iot.api'])->group(function () {
        Route::get('/health', [DataIngestionController::class, 'healthCheck']);
        Route::post('/sensor-data', [DataIngestionController::class, 'storeSensorData']);
        Route::post('/valve-on', [DataIngestionController::class, 'storeValveOn']);
        Route::post('/valve-off', [DataIngestionController::class, 'storeValveOff']);
    });
    
    // Dashboard Endpoints (NEW - for web dashboard)
    Route::prefix('dashboard')->middleware(['throttle:120,1'])->group(function () {
        Route::get('/poll', [DashboardPollingController::class, 'poll']);
        Route::get('/poll-status', [DashboardPollingController::class, 'pollStatus']);
        Route::get('/environment', [DashboardPollingController::class, 'environment']);
        Route::get('/status', [DashboardPollingController::class, 'status']); // deprecated
        Route::get('/devices', [DashboardApiController::class, 'getDevices']);
        Route::get('/tank', [DashboardApiController::class, 'getTank']);
        Route::get('/schedule', [DashboardApiController::class, 'getSchedule']);
        Route::get('/usage', [DashboardApiController::class, 'getUsage']);
        Route::get('/usage/daily', [DashboardApiController::class, 'getUsageDaily']);
        Route::get('/charts', [DashboardApiController::class, 'getChartData']);
        Route::get('/weather', [DashboardApiController::class, 'getWeather']);
        Route::get('/json-backup', [DashboardApiController::class, 'getJsonBackup']);
    });
    
    // Device Detail Endpoints (NEW - device-specific data)
    Route::prefix('devices/{deviceId}')
        ->middleware('throttle:120,1')
        ->group(function () {
            Route::get('/sleep-history', [DeviceDetailController::class, 'sleepHistory']);
            Route::get('/irrigation-sessions', [DeviceDetailController::class, 'irrigationSessions']);
            Route::get('/usage-history', [DeviceDetailController::class, 'usageHistory']);
            Route::get('/chart-data', [DeviceDetailController::class, 'chartData']);
            Route::get('/battery-history', [DeviceDetailController::class, 'batteryHistory']);
        });
    
    // Sensor Data Endpoints
    Route::prefix('sensor-data')->group(function () {
        Route::post('/', [SensorDataController::class, 'store'])
            ->middleware(['throttle:120,1', 'iot.api']);
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
    
    // Report API
    Route::prefix('reports')->group(function () {
        Route::get('/preview', [\App\Http\Controllers\Api\ReportApiController::class, 'preview']);
        Route::get('/data', [\App\Http\Controllers\Api\ReportApiController::class, 'getData']);
        Route::get('/types', [\App\Http\Controllers\Api\ReportApiController::class, 'getTypes']);
    });
    
    // Lahan Pantau API (without auth for now - will add after testing)
    Route::prefix('lahan-pantau')->group(function () {
        Route::get('/', [LahanPantauController::class, 'index']);
        Route::post('/', [LahanPantauController::class, 'store']);
        Route::get('/{id}', [LahanPantauController::class, 'show']);
        Route::put('/{id}', [LahanPantauController::class, 'update']);
        Route::delete('/{id}', [LahanPantauController::class, 'destroy']);
    });
    
    // AI Calibration API
    Route::prefix('devices/{deviceId}/ai-calibration')->group(function () {
        Route::post('/start', [\App\Http\Controllers\Api\AICalibrationController::class, 'start']);
        Route::post('/confirm-saturation', [\App\Http\Controllers\Api\AICalibrationController::class, 'confirmSaturation']);
        Route::post('/analyze', [\App\Http\Controllers\Api\AICalibrationController::class, 'analyze']);
        Route::get('/status', [\App\Http\Controllers\Api\AICalibrationController::class, 'status']);
        Route::post('/cancel', [\App\Http\Controllers\Api\AICalibrationController::class, 'cancel']);
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

// Mobile App API Routes
Route::prefix('v1/mobile')->group(function () {
    Route::post('/fcm-token', [MobileApiController::class, 'registerFcmToken'])->middleware('auth:sanctum');
    Route::post('/test-notification', [MobileApiController::class, 'testNotification'])->middleware('auth:sanctum');
    Route::get('/version', [MobileApiController::class, 'version']);
    Route::get('/health', [MobileApiController::class, 'health']);
    Route::post('/oauth-session', [MobileApiController::class, 'setOAuthSession']);
});

// Authentication API Routes (Strategy B - Token Based)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

