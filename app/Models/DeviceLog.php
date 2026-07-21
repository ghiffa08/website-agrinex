<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'log_type',
        'message',
        'severity',
        'data',
        'logged_at'
    ];

    public $timestamps = false;

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
