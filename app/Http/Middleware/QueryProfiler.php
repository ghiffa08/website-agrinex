<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * QueryProfiler Middleware
 * 
 * Logs slow queries and per-request query statistics to help identify
 * performance bottlenecks in production. Only activates when
 * QUERY_PROFILER_ENABLED=true in .env.
 * 
 * Logs are written to storage/logs/query-profiler.log via the 'query-profiler' channel.
 */
class QueryProfiler
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.query_profiler_enabled', false)) {
            return $next($request);
        }

        $queries = [];
        $slowThresholdMs = config('app.query_profiler_slow_threshold', 100);

        DB::listen(function ($query) use (&$queries, $slowThresholdMs) {
            $entry = [
                'sql'      => $query->sql,
                'bindings' => $query->bindings,
                'time_ms'  => $query->time,
            ];

            $queries[] = $entry;

            // Log slow queries individually for immediate visibility
            if ($query->time >= $slowThresholdMs) {
                Log::channel('query-profiler')->warning('🐌 SLOW QUERY', [
                    'sql'      => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms'  => $query->time,
                ]);
            }
        });

        $response = $next($request);

        // Summarise per-request query stats
        $totalQueries = count($queries);
        $totalTimeMs  = array_sum(array_column($queries, 'time_ms'));
        $slowCount    = count(array_filter($queries, fn ($q) => $q['time_ms'] >= $slowThresholdMs));

        // Only log if there were queries
        if ($totalQueries > 0) {
            $logLevel = $slowCount > 0 ? 'warning' : ($totalQueries > 20 ? 'info' : 'debug');

            Log::channel('query-profiler')->{$logLevel}('📊 REQUEST PROFILE', [
                'method'        => $request->method(),
                'url'           => $request->fullUrl(),
                'total_queries' => $totalQueries,
                'total_time_ms' => round($totalTimeMs, 2),
                'slow_queries'  => $slowCount,
                'avg_time_ms'   => round($totalTimeMs / $totalQueries, 2),
            ]);
        }

        // Inject debug header in non-production environments
        if (config('app.debug')) {
            $response->headers->set('X-Query-Count', (string) $totalQueries);
            $response->headers->set('X-Query-Time-Ms', (string) round($totalTimeMs, 2));
        }

        return $response;
    }
}
