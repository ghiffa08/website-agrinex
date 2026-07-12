<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NodeConfigController extends Controller
{
    /**
     * Get current global configuration for nodes
     */
    public function getConfig()
    {
        // Retrieve config from cache, defaulting to auto and OFF
        $config = Cache::get('node_config', [
            'mode' => 'auto',
            'valve' => 'OFF'
        ]);

        return response()->json($config);
    }

    /**
     * Update global configuration for nodes (called by Web/Mobile App)
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:auto,manual',
            'valve' => 'required|in:ON,OFF'
        ]);

        $config = [
            'mode' => $request->mode,
            'valve' => $request->valve
        ];

        // Store config in cache indefinitely until next update
        Cache::put('node_config', $config);

        return response()->json([
            'message' => 'Node configuration updated successfully',
            'data' => $config
        ]);
    }
}
