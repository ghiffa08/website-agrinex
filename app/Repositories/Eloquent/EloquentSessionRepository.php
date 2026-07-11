<?php

namespace App\Repositories\Eloquent;

use App\Models\DataSession;
use App\Models\GetdataLog;
use App\Repositories\Contracts\SessionRepositoryInterface;

class EloquentSessionRepository implements SessionRepositoryInterface
{
    public function findOrCreateSession($sessionId, array $defaults = [])
    {
        return DataSession::firstOrCreate(
            ['session_id' => $sessionId],
            $defaults
        );
    }

    public function createGetdataLog(array $data)
    {
        return GetdataLog::create($data);
    }

    public function getLatestSession()
    {
        return DataSession::orderBy('started_at', 'desc')->first();
    }

    public function getSessionsInRange($startTime, $endTime = null)
    {
        $query = GetdataLog::where('waktu_mulai', '>=', $startTime);
        if ($endTime) {
            $query->where('waktu_mulai', '<=', $endTime);
        }
        return $query->with(['sensorWeatherData', 'sensorNodeData'])
            ->orderBy('waktu_mulai', 'asc')
            ->get();
    }

    public function getSessionsWithLimit($limit)
    {
        return GetdataLog::with(['sensorWeatherData', 'sensorNodeData'])
            ->orderBy('waktu_mulai', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();
    }
}
