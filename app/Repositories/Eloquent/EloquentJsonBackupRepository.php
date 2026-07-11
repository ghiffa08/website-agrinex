<?php

namespace App\Repositories\Eloquent;

use App\Models\JsonBackup;
use App\Repositories\Contracts\JsonBackupRepositoryInterface;

class EloquentJsonBackupRepository implements JsonBackupRepositoryInterface
{
    public function createBackup(array $data)
    {
        return JsonBackup::create($data);
    }

    public function getBackups($filters, $limit)
    {
        $query = JsonBackup::query();

        if (!empty($filters['sesi_id'])) {
            $query->where('sesi_id_getdata', $filters['sesi_id']);
        }

        return $query->orderBy('backup_timestamp', 'desc')
            ->limit($limit)
            ->get();
    }
}
