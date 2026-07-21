<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    use HasFactory;

    public $timestamps = false; // Table doesn't have created_at/updated_at

    protected $fillable = [
        'data_session_id',
        'device_id',
        'location',
        'temp_c',
        'humidity_pct',
        'pressure_hpa',
        'rainfall_mm',
        'wind_speed_kmh',
        'wind_direction',
        'uv_index',
        'weather_condition',
        'forecast_source',
        'water_level_cm',
        'recorded_at'
    ];

    public function session()
    {
        return $this->belongsTo(DataSession::class, 'data_session_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
