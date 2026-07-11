<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Rate limiter for pseudo-realtime polling
        RateLimiter::for('polling', function (Request $request) {
            return Limit::perMinute(600)->by($request->ip());
        });
        
        // Rate limiter for IoT Devices pushing telemetry
        RateLimiter::for('telemetry', function (Request $request) {
            return Limit::perMinute(300)->by($request->ip());
        });
    }
}
