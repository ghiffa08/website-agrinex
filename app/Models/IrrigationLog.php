<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IrrigationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'device_id',
        'started_at',
        'finished_at',
        'duration_minutes',
        'status',
        'success_count',
        'failed_count',
        'notes'
    ];

    public function valveLogs()
    {
        return $this->hasMany(ValveLog::class);
    }
}
