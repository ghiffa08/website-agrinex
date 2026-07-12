<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    /**
     * Display weather dashboard
     */
    public function index()
    {
        // Get latest weather data (Device 65)
        $latestWeather = WeatherData::where('device_id', 65)
            ->latest('recorded_at')
            ->first();

        // Get weather data for last 24 hours
        $weatherData24h = WeatherData::where('device_id', 65)
            ->where('recorded_at', '>=', now()->subDay())
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Get weather data for last 7 days
        $weatherData7d = WeatherData::where('device_id', 65)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Calculate statistics
        $stats = [
            'current_temp' => $latestWeather->temp_c ?? 0,
            'current_humidity' => $latestWeather->humidity_pct ?? 0,
            'avg_temp_24h' => $weatherData24h->avg('temp_c'),
            'avg_humidity_24h' => $weatherData24h->avg('humidity_pct'),
            'max_light_24h' => $weatherData24h->max('light_lux'),
            'total_readings' => WeatherData::where('device_id', 65)->count(),
        ];

        // Check rain status
        $stats['rain_status'] = $latestWeather && $latestWeather->rain_pct > 0 ? 'Raining' : 'No Rain';

        return view('weather.index', compact('latestWeather', 'weatherData24h', 'weatherData7d', 'stats'));
    }

    /**
     * Get weather history
     */
    public function history(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(7));
        $endDate = $request->input('end_date', now());

        $weatherHistory = WeatherData::where('device_id', 65)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'desc')
            ->simplePaginate(50);

        return view('weather.history', compact('weatherHistory', 'startDate', 'endDate'));
    }

    /**
     * Get weather data for charts (API endpoint)
     */
    public function chartData(Request $request)
    {
        $hours = $request->input('hours', 24);

        $data = WeatherData::where('device_id', 65)
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json([
            'labels' => $data->pluck('recorded_at')->map(function($date) {
                return $date->format('H:i');
            }),
            'temperature' => $data->pluck('temp_c'),
            'humidity' => $data->pluck('humidity_pct'),
            'light' => $data->pluck('light_lux'),
            'wind' => $data->pluck('wind_speed'),
            'rain' => $data->pluck('rain_pct'),
        ]);
    }
}
