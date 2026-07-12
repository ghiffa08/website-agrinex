<?php

namespace App\Services\Admin;

use App\Models\GetdataLog;

class GetdataLogsService
{
    /**
     * Get paginated logs with applied filters
     * FIX N+1: Eager load relations untuk menghindari query berulang
     */
    public function getPaginatedLogs(array $filters, int $perPage = 25)
    {
        $query = GetdataLog::with(['sensorNodeData', 'sensorWeatherData', 'nodeLogs'])
            ->orderBy('waktu_mulai', 'desc');
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->whereDate('waktu_mulai', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->whereDate('waktu_mulai', '<=', $filters['end_date']);
        }
        
        return $query->paginate($perPage);
    }
    
    /**
     * Get single log with relationships eager loaded
     */
    public function getLogWithRelations($id)
    {
        return GetdataLog::with(['sensorNodeData', 'sensorWeatherData', 'nodeLogs'])->findOrFail($id);
    }
    
    /**
     * Update log record
     */
    public function updateLog($id, array $data)
    {
        $log = GetdataLog::findOrFail($id);
        $log->update($data);
        return $log;
    }
    
    /**
     * Delete log record
     */
    public function deleteLog($id)
    {
        $log = GetdataLog::findOrFail($id);
        return $log->delete();
    }
}
