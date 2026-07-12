<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function session()
    {
        return $this->belongsTo(DataSession::class, 'data_session_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
