<?php

namespace App\Services;

use App\Repositories\Contracts\SessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ChartDataService
{
    protected $sessionRepo;

    public function __construct(SessionRepositoryInterface $sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    /**
     * Get sensor sessions based on requested timeframe/limit
     */
    public function getSessions($days, $limit)
    {
        $cacheKey = "chart_data_sessions_days_{$days}_limit_{$limit}";

        return Cache::remember($cacheKey, 300, function () use ($days, $limit) {
            if ($days) {
                $startTime = Carbon::now()->subDays($days);
                $sessions = $this->sessionRepo->getSessionsInRange($startTime);
            } elseif ($limit) {
                $sessions = $this->sessionRepo->getSessionsWithLimit($limit);
                $startTime = $sessions->first() ? Carbon::parse($sessions->first()->waktu_mulai) : Carbon::now();
            } else {
                $startTime = Carbon::now()->subDays(7);
                $sessions = $this->sessionRepo->getSessionsInRange($startTime);
            }

            return [
                'sessions' => $sessions,
                'start_time' => $startTime
            ];
        });
    }
}
