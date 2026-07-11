<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }

    public function logs()
    {
        return $this->hasMany(DeviceLog::class);
    }

    public function lahanPantau()
    {
        return $this->belongsTo(LahanPantau::class);
    }
}
