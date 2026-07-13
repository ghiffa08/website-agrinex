<?php

namespace App\Repositories\Eloquent;

use App\Models\DataSession;
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
        // Legacy method - now uses DataSession
        return DataSession::create([
            'session_id' => $data['sesi_id_getdata'] ?? null,
            'started_at' => $data['waktu_mulai'] ?? now(),
            'ended_at' => $data['waktu_selesai'] ?? null,
            'status' => 'completed',
        ]);
    }

    public function getLatestSession()
    {
        return DataSession::orderBy('started_at', 'desc')->first();
    }

    public function getSessionsInRange($startTime, $endTime = null)
    {
        $query = DataSession::where('started_at', '>=', $startTime);
        if ($endTime) {
            $query->where('started_at', '<=', $endTime);
        }
        return $query->with(['sensorData', 'weatherData'])
            ->orderBy('started_at', 'asc')
            ->get();
    }

    public function getSessionsWithLimit($limit)
    {
        return DataSession::with(['sensorData', 'weatherData'])
            ->orderBy('started_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();
    }
}
