<?php

namespace App\Repositories\Contracts;

interface WeatherDataRepositoryInterface
{
    public function createWeatherRecord(array $data);
    public function createSensorWeatherRecord(array $data);
    public function getLatest($sessionId = null);
}
