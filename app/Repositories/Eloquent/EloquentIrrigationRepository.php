<?php

namespace App\Repositories\Eloquent;

use App\Models\IrrigationLog;
use App\Models\IrrigateLog;
use App\Models\ValveLog;
use App\Repositories\Contracts\IrrigationRepositoryInterface;
use Carbon\Carbon;

class EloquentIrrigationRepository implements IrrigationRepositoryInterface
{
    public function createIrrigationLog(array $data)
    {
        return IrrigationLog::create($data);
    }

    public function createIrrigateLog(array $data)
    {
        return IrrigateLog::create($data);
    }

    public function createValveLog(array $data)
    {
        return ValveLog::create($data);
    }

    public function getLatestCompleted()
    {
        return IrrigationLog::with('valveLogs')
            ->where('status', 'completed')
            ->orderBy('started_at', 'desc')
            ->first();
    }

    public function getHistory($filters, $limit)
    {
        $query = IrrigationLog::query();

        if (!empty($filters['start_date'])) {
            $query->where('started_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('valveLogs')
            ->orderBy('started_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
