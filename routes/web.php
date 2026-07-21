<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TestConnectionController;
use App\Http\Controllers\Web\CleanupController;
use App\Http\Controllers\Web\AgriNexDashboardController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\NodesController;
use App\Http\Controllers\Web\IrrigationController;
use App\Http\Controllers\Web\AlertsController;
use App\Http\Controllers\Web\WeatherController;
use App\Http\Controllers\Web\ReportsController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Utility Pages (Accessible without login)

// Public Utility Pages (Accessible without login)
Route::get('/system-monitor', [DashboardController::class, 'monitor'])->name('monitor');
Route::get('/connection-test', [TestConnectionController::class, 'index'])->name('test-connection');
Route::get('/database-cleanup', [CleanupController::class, 'index'])->name('cleanup');
Route::post('/database-cleanup/execute', [CleanupController::class, 'execute'])->name('cleanup.execute');

// Hostinger / Shared Hosting Optimization Helper (Protected)
Route::get('/hostinger-optimize-artisan-route-99x', function () {
    // Validate admin key from header or query param
    $key = request()->header('X-Admin-Key') ?? request()->query('key');
    if ($key !== env('ADMIN_OPTIMIZE_KEY')) {
        abort(403, 'Unauthorized - Invalid admin key');
    }
    
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    
    return response()->json([
        'success' => true,
        'message' => 'Hostinger optimization complete: config, routes, and views cached successfully.',
        'timestamp' => now()->toDateTimeString()
    ]);
})->middleware('throttle:5,60')->name('admin.optimize');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Protected Routes - Require Authentication
Route::middleware(['auth'])->group(function () {
    
    // AgriNex Main Dashboard (with charts and real-time data)
    Route::get('/', [AgriNexDashboardController::class, 'index'])->name('agrinex.dashboard');
    Route::get('/devices', [AgriNexDashboardController::class, 'devices'])->name('agrinex.devices');
    Route::get('/node/{id}', [AgriNexDashboardController::class, 'nodeDetail'])->name('agrinex.node-detail');

    // Reports Routes - New Reporting System
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/devices', [ReportController::class, 'getDevices'])->name('reports.devices');
    Route::get('/reports/generate/{reportType}', [ReportController::class, 'generate'])->name('reports.generate');

    // Profile Routes
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::post('/profile/unlink-oauth', [ProfileController::class, 'unlinkOAuth'])->name('profile.unlink-oauth');
    Route::post('/profile/logout', [ProfileController::class, 'logout'])->name('profile.logout');
    Route::get('/profile/password-strength', [ProfileController::class, 'checkPasswordStrength'])->name('profile.password-strength');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update.put');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Admin Profile Routes
    Route::prefix('admin')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
        Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    });

    Route::middleware(['role'])->group(function () {

    // Simple Dashboard (Statistics only)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard API Routes
    Route::get('/admin/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
    Route::get('/admin/dashboard/realtime-data', [DashboardController::class, 'realtimeData'])->name('dashboard.realtime-data');

    // Nodes Management
    Route::get('/nodes', [NodesController::class, 'index'])->name('nodes.index');
    Route::get('/nodes/{id}', [NodesController::class, 'show'])->name('nodes.show');
    Route::get('/nodes/{id}/edit', [NodesController::class, 'edit'])->name('nodes.edit')->middleware(['role:admin,operator']);
    Route::put('/nodes/{id}', [NodesController::class, 'update'])->name('nodes.update')->middleware(['role:admin,operator']);

    // Irrigation Management
    Route::get('/irrigation', [IrrigationController::class, 'index'])->name('irrigation.index');
    Route::post('/irrigation/trigger', [IrrigationController::class, 'trigger'])->name('irrigation.trigger')->middleware(['role:admin,operator']);
    Route::get('/irrigation/history/{sesiId}', [IrrigationController::class, 'history'])->name('irrigation.history');

    // Alerts
    Route::get('/alerts', [AlertsController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{type}', [AlertsController::class, 'byType'])->name('alerts.by-type');

    // Weather
    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
    Route::get('/weather/history', [WeatherController::class, 'history'])->name('weather.history');
    Route::get('/weather/chart-data', [WeatherController::class, 'chartData'])->name('weather.chart-data');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/node/{nodeId}', [ReportsController::class, 'byNode'])->name('reports.by-node');
    Route::get('/reports/export/pdf', [ReportsController::class, 'exportPdf'])->name('reports.export.pdf');
    });

    // Settings Routes - Admin only
    Route::prefix('settings')->name('settings.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/users', [SettingsController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [SettingsController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [SettingsController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{id}/toggle-status', [SettingsController::class, 'toggleUserStatus'])->name('users.toggle-status');
    });

    // ESP32 Web Flasher Routes - Admin & Operator
    Route::prefix('admin/flasher')->name('flasher.')->middleware(['role:admin,operator'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FlasherController::class, 'index'])->name('index');
        Route::get('/firmware-list', [\App\Http\Controllers\Admin\FlasherController::class, 'firmwareList'])->name('firmware-list');
    });

    // Admin Data Management Routes removed - legacy system deprecated
    // Use AgriNex dashboard for device and sensor data management
});



