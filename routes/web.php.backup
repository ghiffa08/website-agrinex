<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TestConnectionController;
use App\Http\Controllers\Web\CleanupController;
use App\Http\Controllers\Web\AgriNexDashboardController;
// Admin controllers removed - legacy tables dropped
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\NodesController;
use App\Http\Controllers\Web\IrrigationController;
use App\Http\Controllers\Web\AlertsController;
use App\Http\Controllers\Web\WeatherController;
use App\Http\Controllers\Web\ReportsController;

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

// Hostinger / Shared Hosting Optimization Helper
Route::get('/hostinger-optimize-artisan-route-99x', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    return 'Hostinger optimization complete: config, routes, and views cached successfully.';
});

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

    // Reports Routes
    Route::get('/reports', [\App\Http\Controllers\Web\ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/export', [\App\Http\Controllers\Web\ReportsController::class, 'export'])->name('reports.export');

    // Profile Routes
    Route::put('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/link-oauth', [\App\Http\Controllers\Web\ProfileController::class, 'linkOAuth'])->name('profile.link-oauth');
    Route::post('/profile/unlink-oauth', [\App\Http\Controllers\Web\ProfileController::class, 'unlinkOAuth'])->name('profile.unlink-oauth');
    Route::post('/profile/logout', [\App\Http\Controllers\Web\ProfileController::class, 'logout'])->name('profile.logout');
    Route::get('/profile/password-strength', [\App\Http\Controllers\Web\ProfileController::class, 'checkPasswordStrength'])->name('profile.password-strength');
    Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/logout', [\App\Http\Controllers\Web\ProfileController::class, 'logout'])->name('profile.logout');

    // Admin Profile Routes
    Route::prefix('admin')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'index'])->name('admin.profile.index');
        Route::put('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('admin.profile.update');
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

    // Admin Data Management Routes removed - legacy system deprecated
    // Use AgriNex dashboard for device and sensor data management
});



