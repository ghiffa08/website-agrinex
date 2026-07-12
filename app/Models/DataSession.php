<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSession extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }
}
