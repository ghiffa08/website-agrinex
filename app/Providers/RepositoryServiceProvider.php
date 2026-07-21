<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\DeviceRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentDeviceRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\SensorDataRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentSensorDataRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\WeatherDataRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentWeatherDataRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\SessionRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentSessionRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\IrrigationRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentIrrigationRepository::class
        );
        // JsonBackupRepository removed - table dropped in migration 2026_07_14_000900
        $this->app->bind(
            \App\Repositories\Contracts\LogRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\DashboardRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentDashboardRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentUserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MonitorRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentMonitorRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
