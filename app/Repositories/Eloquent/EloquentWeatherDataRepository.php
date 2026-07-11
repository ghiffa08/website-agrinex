<?php

namespace App\Repositories\Eloquent;

use App\Models\WeatherData;
use App\Models\SensorWeatherData;
use App\Repositories\Contracts\WeatherDataRepositoryInterface;

class EloquentWeatherDataRepository implements WeatherDataRepositoryInterface
{
    public function createWeatherRecord(array $data)
    {
        return WeatherData::create($data);
    }

    public function createSensorWeatherRecord(array $data)
    {
        return SensorWeatherData::create($data);
    }

    public function getLatest($sessionId = null)
    {
        if ($sessionId) {
            return WeatherData::where('data_session_id', $sessionId)->first();
        }
        return WeatherData::latest('recorded_at')->first();
    }
}
