<?php

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    /**
     * Get all devices with their latest sensor data and connection status.
     *
     * @return array
     */
    public function getDevices();

    /**
     * Get a specific device with its latest sensor data and connection status.
     *
     * @param int $nodeId
     * @return array|null
     */
    public function getDevice(int $nodeId): ?array;

    /**
     * Invalidate cached data for a specific node.
     * Should be called after telemetry ingestion to implement write-through caching.
     *
     * @param int $nodeId
     * @return void
     */
    public function invalidateNodeCache(int $nodeId): void;


    /**
     * Get water tank information.
     *
     * @return array
     */
    public function getTank();

    /**
     * Get irrigation schedule for today.
     *
     * @return array
     */
    public function getSchedule();

    /**
     * Get 30-day water usage history.
     *
     * @return array
     */
    public function getUsage();

    /**
     * Get 24-hour water usage history (hourly).
     *
     * @return array
     */
    public function getUsageDaily();

    /**
     * Get current weather from the latest sensor reading.
     *
     * @return array|null
     */
    public function getWeather();
}
