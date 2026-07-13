<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'data_session_id',
        'device_id',
        'voltage_v',
        'battery_pct',
        'current_ma',
        'power_mw',
        'temperature',
        'soil_moisture',
        'flow_rate',
        'total_volume_l',
        'soil_adc',
        'ai_valve_decision',
        'adaptive_sleep_duration',
        'rssi',
        'recorded_at',
    ];

    public function session()
    {
        return $this->belongsTo(DataSession::class, 'data_session_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function getSoilPctAttribute()
    {
        return $this->soil_moisture;
    }

    public function getTempCAttribute()
    {
        return $this->temperature;
    }
}
