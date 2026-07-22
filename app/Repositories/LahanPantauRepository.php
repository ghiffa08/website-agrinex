<?php

namespace App\Repositories;

use App\Models\LahanPantau;
use App\Models\Device;
use App\Repositories\Contracts\LahanPantauRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LahanPantauRepository implements LahanPantauRepositoryInterface
{
    public function getAll()
    {
        return LahanPantau::withCount('devices')
            ->orderBy('nama_lahan', 'asc')
            ->get()
            ->map(function ($lahan) {
                return [
                    'id' => $lahan->id,
                    'nama_lahan' => $lahan->nama_lahan,
                    'lokasi' => $lahan->lokasi,
                    'deskripsi' => $lahan->deskripsi,
                    'image_url' => $lahan->image_url,
                    'total_devices' => $lahan->devices_count,
                    'created_at' => $lahan->created_at?->format('d M Y'),
                ];
            });
    }

    public function getById(int $id)
    {
        return LahanPantau::findOrFail($id);
    }

    public function create(array $data)
    {
        return LahanPantau::create([
            'nama_lahan' => $data['nama_lahan'],
            'lokasi' => $data['lokasi'] ?? null,
            'deskripsi' => $data['deskripsi'] ?? null,
            'image_url' => $data['image_url'] ?? null,
        ]);
    }

    public function update(int $id, array $data)
    {
        $lahan = $this->getById($id);
        
        $lahan->update([
            'nama_lahan' => $data['nama_lahan'] ?? $lahan->nama_lahan,
            'lokasi' => $data['lokasi'] ?? $lahan->lokasi,
            'deskripsi' => $data['deskripsi'] ?? $lahan->deskripsi,
            'image_url' => $data['image_url'] ?? $lahan->image_url,
        ]);

        return $lahan->fresh();
    }

    public function delete(int $id)
    {
        $lahan = $this->getById($id);
        
        // Unassign devices before deleting
        Device::where('lahan_pantau_id', $id)->update(['lahan_pantau_id' => null]);
        
        return $lahan->delete();
    }

    public function getWithDevices(int $id)
    {
        $lahan = LahanPantau::with(['devices' => function ($query) {
            $query->select('id', 'name', 'location', 'lokasi', 'is_active', 'lahan_pantau_id', 'created_at')
                  ->orderBy('name', 'asc');
        }])->findOrFail($id);

        return [
            'id' => $lahan->id,
            'nama_lahan' => $lahan->nama_lahan,
            'lokasi' => $lahan->lokasi,
            'deskripsi' => $lahan->deskripsi,
            'image_url' => $lahan->image_url,
            'created_at' => $lahan->created_at?->format('d M Y H:i'),
            'devices' => $lahan->devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'location' => $device->location ?? $device->lokasi,
                    'is_active' => $device->is_active,
                    'status' => $device->is_active ? 'online' : 'offline',
                    'registered_at' => $device->created_at?->format('d M Y'),
                ];
            }),
            'total_devices' => $lahan->devices->count(),
        ];
    }
}
