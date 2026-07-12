<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrrigateLog extends Model
{
    protected $table = 'irrigate_logs';

    // Primary key sesuai dengan database
    protected $primaryKey = 'id';
    
    // Disable timestamps karena tidak ada created_at/updated_at
    public $timestamps = false;
    
    // Field yang bisa diisi - sesuai dengan struktur database
    protected $fillable = [
        'id',               // Allow manual ID assignment
        'sesi_id_irrigate',
        'waktu_mulai',
        'waktu_akhir',      // bukan waktu_selesai
        'node_sukses',      // bukan valve_sukses
        'node_gagal',       // bukan valve_gagal
        'valve_on_akhir',
    ];

    protected $casts = [
        'sesi_id_irrigate' => 'integer',
        'node_sukses' => 'integer',
        'node_gagal' => 'integer',
        'valve_on_akhir' => 'integer',
        'waktu_mulai' => 'datetime',
        'waktu_akhir' => 'datetime',
    ];

    /**
     * Get valve logs for this session
     */
    public function valveLogs()
    {
        // FIX: valve_logs uses irrigation_log_id, not sesi_id_irrigate
        return $this->hasMany(ValveLog::class, 'irrigation_log_id', 'id');
    }

    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id', 'id');
    }

    /**
     * Get node logs for this session
     */
    public function nodeLogs()
    {
        return $this->hasMany(NodeLog::class, 'sesi_id', 'sesi_id_irrigate');
    }

    /**
     * Scope for successful sessions
     * Session sukses = waktu_akhir tidak null DAN tidak ada node gagal
     */
    public function scopeSuccessful($query)
    {
        return $query->whereNotNull('waktu_akhir')
                     ->where('node_gagal', 0);
    }

    /**
     * Scope for failed sessions
     * Session gagal = waktu_akhir null ATAU ada node gagal
     */
    public function scopeFailed($query)
    {
        return $query->where(function($q) {
            $q->whereNull('waktu_akhir')
              ->orWhere('node_gagal', '>', 0);
        });
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        $totalNodes = $this->node_sukses + $this->node_gagal;
        if ($totalNodes == 0) return 0;
        return round(($this->node_sukses / $totalNodes) * 100, 2);
    }

    /**
     * Get session duration in seconds
     */
    public function getDurationAttribute()
    {
        if (!$this->waktu_mulai || !$this->waktu_akhir) return 0;
        return $this->waktu_akhir->diffInSeconds($this->waktu_mulai);
    }
    
    /**
     * Check if session is completed
     */
    public function isCompleted()
    {
        return $this->waktu_akhir !== null;
    }
    
    /**
     * Check if session has failures
     */
    public function hasFailures()
    {
        return $this->node_gagal > 0;
    }
}
