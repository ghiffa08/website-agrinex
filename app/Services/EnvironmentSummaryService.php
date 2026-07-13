<?php

namespace App\Services;

use App\Models\SensorData;
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Environment Summary Service
 * Aggregate sensor data from all nodes + BMKG API data
 */
class EnvironmentSummaryService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Get environment summary (aggregated sensor data + BMKG weather)
     */
    public function getEnvironmentSummary(): array
    {
        return $this->cacheService->remember(
            'environment_summary',
            CacheService::TTL_SHORT, // 30 seconds
            fn() => $this->calculateEnvironmentSummary()
        );
    }

    /**
     * Calculate environment summary from all active nodes
     */
    private function calculateEnvironmentSummary(): array
    {
        // Get active devices
        $activeDeviceIds = Device::where('status', 'active')
            ->pluck('id')
            ->toArray();

        if (empty($activeDeviceIds)) {
            return $this->getEmptySummary();
        }

        // Get latest sensor reading from each device (last 15 minutes)
        $latestReadings = SensorData::whereIn('device_id', $activeDeviceIds)
            ->where('recorded_at', '>=', now()->subMinutes(15))
            ->select([
                'device_id',
                DB::raw('MAX(recorded_at) as latest_time'),
                DB::raw('AVG(soil_moisture) as avg_soil_moisture'),
                DB::raw('AVG(temperature) as avg_temperature'),
                DB::raw('AVG(humidity) as avg_humidity'),
                DB::raw('AVG(water_flow_rate) as avg_flow_rate'),
                DB::raw('AVG(battery_voltage) as avg_battery')
            ])
            ->groupBy('device_id')
            ->get();

        // Calculate overall averages
        $soilMoisture = $latestReadings->avg('avg_soil_moisture');
        $temperature = $latestReadings->avg('avg_temperature');
        $humidity = $latestReadings->avg('avg_humidity');
        $flowRate = $latestReadings->avg('avg_flow_rate');
        $battery = $latestReadings->avg('avg_battery');

        // Get BMKG weather data
        $bmkgData = $this->getBMKGWeather();

        return [
            // Aggregated sensor data from all nodes
            'sensor_aggregate' => [
                'soil_moisture' => round($soilMoisture ?? 0, 1),
                'temperature' => round($temperature ?? 0, 1),
                'humidity' => round($humidity ?? 0, 1),
                'flow_rate' => round($flowRate ?? 0, 2),
                'battery' => round($battery ?? 0, 2),
                'active_nodes' => $latestReadings->count(),
                'total_nodes' => count($activeDeviceIds),
            ],

            // BMKG external weather
            'bmkg_weather' => $bmkgData,

            // Combined metrics for cards
            'metrics' => [
                'soil_moisture' => [
                    'value' => round($soilMoisture ?? 0, 1),
                    'unit' => '%',
                    'source' => 'nodes',
                    'status' => $this->getSoilStatus($soilMoisture ?? 0),
                ],
                'temperature' => [
                    'value' => round($temperature ?? 0, 1),
                    'unit' => '°C',
                    'source' => 'nodes',
                    'status' => $this->getTemperatureStatus($temperature ?? 0),
                ],
                'humidity' => [
                    'value' => round($humidity ?? 0, 1),
                    'unit' => '%',
                    'source' => 'nodes',
                    'status' => $this->getHumidityStatus($humidity ?? 0),
                ],
                'external_temp' => [
                    'value' => $bmkgData['temperature'] ?? 0,
                    'unit' => '°C',
                    'source' => 'bmkg',
                    'status' => $bmkgData['weather'] ?? 'Unknown',
                ],
                'rainfall' => [
                    'value' => $bmkgData['rainfall'] ?? 0,
                    'unit' => 'mm',
                    'source' => 'bmkg',
                    'status' => $this->getRainfallStatus($bmkgData['rainfall'] ?? 0),
                ],
                'wind_speed' => [
                    'value' => $bmkgData['wind_speed'] ?? 0,
                    'unit' => 'km/h',
                    'source' => 'bmkg',
                    'status' => $this->getWindStatus($bmkgData['wind_speed'] ?? 0),
                ],
            ],

            'last_update' => now()->toIso8601String(),
        ];
    }

    /**
     * Get BMKG weather data
     */
    private function getBMKGWeather(): array
    {
        return $this->cacheService->remember(
            'bmkg_weather_data',
            CacheService::TTL_LONG, // 5 minutes
            fn() => $this->fetchBMKGWeather()
        );
    }

    /**
     * Fetch weather data from BMKG API
     */
    private function fetchBMKGWeather(): array
    {
        try {
            // BMKG API endpoint (example - adjust to your location)
            // Format: https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=XXXX
            $locationCode = config('services.bmkg.location_code', '501297'); // Default Jakarta

            $response = Http::timeout(10)
                ->get("https://api.bmkg.go.id/publik/prakiraan-cuaca", [
                    'adm4' => $locationCode
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Parse BMKG response (adjust based on actual API structure)
                return [
                    'temperature' => $data['data'][0]['cuaca'][0]['t'] ?? 28.0,
                    'humidity' => $data['data'][0]['cuaca'][0]['hu'] ?? 70.0,
                    'weather' => $data['data'][0]['cuaca'][0]['weather_desc'] ?? 'Cerah',
                    'rainfall' => $data['data'][0]['cuaca'][0]['tp'] ?? 0,
                    'wind_speed' => $data['data'][0]['cuaca'][0]['ws'] ?? 5.0,
                    'wind_direction' => $data['data'][0]['cuaca'][0]['wd_deg'] ?? 0,
                    'source' => 'BMKG',
                    'fetched_at' => now()->toIso8601String(),
                ];
            }

            // Fallback jika API gagal
            return $this->getDefaultBMKGData();

        } catch (\Exception $e) {
            Log::error('BMKG API fetch failed', [
                'error' => $e->getMessage()
            ]);
            
            return $this->getDefaultBMKGData();
        }
    }

    /**
     * Default BMKG data (fallback)
     */
    private function getDefaultBMKGData(): array
    {
        return [
            'temperature' => 28.0,
            'humidity' => 70.0,
            'weather' => 'Data tidak tersedia',
            'rainfall' => 0,
            'wind_speed' => 5.0,
            'wind_direction' => 0,
            'source' => 'default',
            'fetched_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Empty summary (no active devices)
     */
    private function getEmptySummary(): array
    {
        $bmkgData = $this->getBMKGWeather();

        return [
            'sensor_aggregate' => [
                'soil_moisture' => 0,
                'temperature' => 0,
                'humidity' => 0,
                'flow_rate' => 0,
                'battery' => 0,
                'active_nodes' => 0,
                'total_nodes' => 0,
            ],
            'bmkg_weather' => $bmkgData,
            'metrics' => [
                'soil_moisture' => ['value' => 0, 'unit' => '%', 'source' => 'nodes', 'status' => 'No Data'],
                'temperature' => ['value' => 0, 'unit' => '°C', 'source' => 'nodes', 'status' => 'No Data'],
                'humidity' => ['value' => 0, 'unit' => '%', 'source' => 'nodes', 'status' => 'No Data'],
                'external_temp' => ['value' => $bmkgData['temperature'], 'unit' => '°C', 'source' => 'bmkg', 'status' => $bmkgData['weather']],
                'rainfall' => ['value' => $bmkgData['rainfall'], 'unit' => 'mm', 'source' => 'bmkg', 'status' => 'No Rain'],
                'wind_speed' => ['value' => $bmkgData['wind_speed'], 'unit' => 'km/h', 'source' => 'bmkg', 'status' => 'Calm'],
            ],
            'last_update' => now()->toIso8601String(),
        ];
    }

    // Status helper methods
    private function getSoilStatus(float $value): string
    {
        if ($value >= 70) return 'Optimal';
        if ($value >= 50) return 'Baik';
        if ($value >= 30) return 'Rendah';
        return 'Kering';
    }

    private function getTemperatureStatus(float $value): string
    {
        if ($value >= 35) return 'Sangat Panas';
        if ($value >= 30) return 'Panas';
        if ($value >= 25) return 'Hangat';
        if ($value >= 20) return 'Sejuk';
        return 'Dingin';
    }

    private function getHumidityStatus(float $value): string
    {
        if ($value >= 80) return 'Sangat Lembab';
        if ($value >= 60) return 'Lembab';
        if ($value >= 40) return 'Sedang';
        return 'Kering';
    }

    private function getRainfallStatus(float $value): string
    {
        if ($value >= 50) return 'Hujan Lebat';
        if ($value >= 20) return 'Hujan Sedang';
        if ($value >= 5) return 'Hujan Ringan';
        if ($value > 0) return 'Gerimis';
        return 'Tidak Hujan';
    }

    private function getWindStatus(float $value): string
    {
        if ($value >= 40) return 'Kencang';
        if ($value >= 20) return 'Sedang';
        if ($value >= 10) return 'Lemah';
        return 'Tenang';
    }
}
