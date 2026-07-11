<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    /**
     * Get irrigation sessions for a specific device
     */
    public function getIrrigationSessions($deviceId): JsonResponse
    {
        // Mock data - implement when irrigate_logs table available
        return response()->json([
            'success' => true,
            'data' => [],
            'note' => 'irrigate_logs table not available'
        ]);
    }

    /**
     * Get usage history for a specific device
     */
    public function getUsageHistory($deviceId): JsonResponse
    {
        // Mock data - implement when irrigate_logs table available
        return response()->json([
            'success' => true,
            'data' => [],
            'note' => 'irrigate_logs table not available'
        ]);
    }

    /**
     * Get chart history data for a specific device
     */
    public function getChartData($deviceId): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Cache::remember("chart_data_{$deviceId}", 5, function () use ($deviceId) {
                $data = \App\Models\SensorNodeData::where('node_id', $deviceId)
                            ->orderBy('received_at', 'desc')
                            ->take(100)
                            ->get()
                            ->reverse()
                            ->values();
                
                $labels = [];
                $temperature = [];
                $soilMoisture = [];
                
                foreach ($data as $item) {
                    $time = \Carbon\Carbon::parse($item->received_at)->format('H:i');
                    $labels[] = $time;
                    $temperature[] = (float) $item->temp_c;
                    $soilMoisture[] = (float) $item->soil_pct;
                }

                return [
                    'success' => true,
                    'labels' => $labels,
                    'datasets' => [
                        'temperature' => $temperature,
                        'soil_moisture' => $soilMoisture,
                    ]
                ];
            });

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data: ' . $e->getMessage()
            ], 500);
        }
    }
}
