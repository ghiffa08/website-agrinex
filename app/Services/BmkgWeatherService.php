<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class BmkgWeatherService
{
    /**
     * Fetch and parse weather forecast from BMKG
     */
    public function getForecast()
    {
        try {
            $response = Http::get('https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=35.78.11.1001');
            $raw = $response->json();

            // Attempt to normalize BMKG response into flat entries array
            $entries = [];

            // Case A: BMKG v2 style: data -> [ { cuaca: [ [entry,...], [entry,...] ] } ]
            if (isset($raw['data'][0]['cuaca']) && is_array($raw['data'][0]['cuaca'])) {
                foreach ($raw['data'][0]['cuaca'] as $block) {
                    if (is_array($block)) {
                        foreach ($block as $entry) {
                            if (!is_array($entry)) continue;
                            $entries[] = [
                                'local_datetime' => $entry['local_datetime'] ?? ($entry['datetime'] ?? null),
                                't' => $entry['t'] ?? $entry['temperature_c'] ?? null,
                                'humidity' => $entry['hu'] ?? $entry['h'] ?? $entry['humidity'] ?? null,
                                'rain' => $entry['tp'] ?? $entry['rain'] ?? null,
                                'weather_desc' => $entry['weather_desc'] ?? ($entry['weather'] ?? null),
                                'weather_icon' => $entry['image'] ?? ($entry['weather_icon'] ?? null),
                                'wind_speed_ms' => $entry['ws'] ?? $entry['wind_speed_ms'] ?? null,
                                'wind_dir_cardinal' => $entry['wd'] ?? ($entry['wind_dir_cardinal'] ?? null),
                                'tcc' => $entry['tcc'] ?? null,
                            ];
                        }
                    }
                }
            }

            // Case B: Some proxies return entries directly
            if (empty($entries)) {
                if (is_array($raw) && count($raw) && array_values($raw) === $raw) {
                    // top-level array
                    $entries = $raw;
                } elseif (isset($raw['entries']) && is_array($raw['entries'])) {
                    $entries = $raw['entries'];
                } elseif (isset($raw['data']) && is_array($raw['data']) && array_values($raw['data']) === $raw['data']) {
                    // If data is already an array of entries, try to flatten
                    $possible = [];
                    foreach ($raw['data'] as $d) {
                        if (is_array($d)) $possible[] = $d;
                    }
                    if ($possible) $entries = $possible;
                }
            }

            // Return normalized entries or raw data
            if (!empty($entries)) {
                return ['entries' => $entries];
            }

            return $raw;
        } catch (Exception $e) {
            throw new Exception('Failed to fetch BMKG data: ' . $e->getMessage());
        }
    }
}
