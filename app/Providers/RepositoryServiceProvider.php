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
        $this->app->bind(
            \App\Repositories\Contracts\JsonBackupRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentJsonBackupRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LogRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\DashboardRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentDashboardRepository::class
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
