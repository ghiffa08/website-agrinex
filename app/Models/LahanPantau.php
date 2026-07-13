<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LahanPantau extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Updated: now uses devices table instead of legacy node table
    public function devices()
    {
        return $this->hasMany(Device::class, 'lahan_pantau_id');
    }
    
    // Alias for backward compatibility
    public function nodes()
    {
        return $this->devices();
    }
}
