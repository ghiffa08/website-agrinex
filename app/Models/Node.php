<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $table = 'node';
    
    protected $fillable = [
        'node_id',
        'group',
        'kode_perlakuan',
        'lokasi',
        'keterangan',
        'fc_target',
        'threshold',
        'image_url',
        'lahan_pantau_id',
    ];
    
    protected $casts = [
        'waktu_dibuat' => 'datetime',
        'waktu_update' => 'datetime',
    ];
    
    // Disable Laravel's automatic timestamps since we use custom columns
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = 'waktu_update';
    
    /**
     * Get the latest sensor data for this node
     */
    public function latestSensorData()
    {
        return $this->hasOne(SensorNodeData::class, 'node_id', 'node_id')
            ->latest('received_at');
    }
    
    /**
     * Get all sensor data for this node
     */
    public function sensorData()
    {
        return $this->hasMany(SensorNodeData::class, 'node_id');
    }
    

    public function sensorWeatherData()
    {
        return $this->hasMany(SensorWeatherData::class, 'node_id');
    }
    
    /**
     * Get node activity logs
     */
    public function logs()
    {
        return $this->hasMany(NodeLog::class, 'node_id', 'node_id');
    }

    public function lahanPantau()
    {
        return $this->belongsTo(LahanPantau::class);
    }
}
