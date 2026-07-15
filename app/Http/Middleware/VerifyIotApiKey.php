<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyIotApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Bypass check if configured in .env (allows older ESP32 firmware to run without re-flashing)
        if (env('BYPASS_IOT_API_KEY', false) === true) {
            return $next($request);
        }

        // Mendapatkan API Key dari .env tanpa nilai default hardcoded
        $expectedApiKey = env('IOT_API_KEY');
        
        if (empty($expectedApiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Server Configuration Error: IOT_API_KEY not set.'
            ], 500);
        }

        $providedKey = $request->header('X-API-Key');

        if (!$providedKey || $providedKey !== $expectedApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing X-API-Key header.'
            ], 401);
        }

        return $next($request);
    }
}
