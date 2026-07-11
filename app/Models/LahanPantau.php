<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LahanPantau extends Model
{
    protected $fillable = ['nama_lahan', 'lokasi', 'deskripsi', 'image_url'];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function nodes()
    {
        return $this->hasMany(Node::class);
    }
}
