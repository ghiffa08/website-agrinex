<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'started_at',
        'ended_at',
        'status',
        'success_count',
        'failed_count',
        'notes'
    ];

    protected $casts = [
        'session_id' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'success_count' => 'integer',
        'failed_count' => 'integer',
    ];

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }
}
