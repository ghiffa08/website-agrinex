<?php

namespace App\Repositories\Contracts;

interface MonitorRepositoryInterface
{
    /**
     * Get database statistics
     */
    public function getDatabaseStats(): array;

    /**
     * Get latest sessions
     */
    public function getLatestSessions(): array;

    /**
     * Get today's statistics
     */
    public function getTodayStats(): array;

    /**
     * Get recent logs by type
     */
    public function getRecentLogs(string $type, int $limit): array;
}
