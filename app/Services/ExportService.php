<?php

namespace App\Services;

use App\Models\SensorData;
use App\Models\WeatherData;
use App\Models\DeviceLog;
use App\Models\ValveLog;
use App\Models\IrrigationLog;
use Illuminate\Support\Facades\DB;

class ExportService
{
    protected $tables = [
        'sensor_data' => SensorData::class,
        'weather_data' => WeatherData::class,
        'device_logs' => DeviceLog::class,
        'valve_logs' => ValveLog::class,
        'irrigation_logs' => IrrigationLog::class,
    ];

    public function exportJson($table, $filters = [])
    {
        $data = $this->getData($table, $filters);
        return $data;
    }

    public function exportCsv($table, $filters = [])
    {
        $data = $this->getData($table, $filters);
        
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data to export'
            ], 404);
        }

        $filename = $table . '_' . date('Ymd_His') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $handle = fopen($filepath, 'w');

        // Write headers
        if ($table === 'all') {
            foreach ($data as $tableName => $tableData) {
                if (!empty($tableData)) {
                    fputcsv($handle, ["TABLE: $tableName"]);
                    fputcsv($handle, array_keys((array)$tableData[0]));
                    foreach ($tableData as $row) {
                        fputcsv($handle, (array)$row);
                    }
                    fputcsv($handle, []); // Empty line between tables
                }
            }
        } else {
            fputcsv($handle, array_keys((array)$data[0]));
            foreach ($data as $row) {
                fputcsv($handle, (array)$row);
            }
        }

        fclose($handle);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }

    public function exportSql($table, $filters = [])
    {
        $data = $this->getData($table, $filters);
        
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data to export'
            ], 404);
        }

        $filename = $table . '_' . date('Ymd_His') . '.sql';
        $filepath = storage_path('app/exports/' . $filename);

        // Ensure directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $sql = "-- AgriNex Data Export\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

        if ($table === 'all') {
            foreach ($data as $tableName => $tableData) {
                if (!empty($tableData)) {
                    $sql .= $this->generateInsertStatements($tableName, $tableData);
                }
            }
        } else {
            $sql .= $this->generateInsertStatements($table, $data);
        }

        file_put_contents($filepath, $sql);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }

    protected function getData($table, $filters = [])
    {
        if ($table === 'all') {
            $allData = [];
            foreach ($this->tables as $tableName => $model) {
                $allData[$tableName] = $this->getTableData($tableName, $filters);
            }
            return $allData;
        }

        return $this->getTableData($table, $filters);
    }

    protected function getTableData($table, $filters = [])
    {
        if (!isset($this->tables[$table])) {
            return [];
        }

        $model = $this->tables[$table];
        $query = $model::query();

        // Apply filters
        if (!empty($filters['sesi_id'])) {
            $sesiColumn = $this->getSesiColumnName($table);
            if ($sesiColumn) {
                $query->where($sesiColumn, $filters['sesi_id']);
            }
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('received_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('received_at', '<=', $filters['end_date']);
        }

        $limit = $filters['limit'] ?? 1000;
        
        return $query->limit($limit)->get()->toArray();
    }

    protected function getSesiColumnName($table)
    {
        $columnMap = [
            'getdata_logs' => 'sesi_id_getdata',
            'sensor_node_data' => 'sesi_id_getdata',
            'sensor_weather_data' => 'sesi_id_getdata',
            // 'irrigate_logs' => 'sesi_id_irrigate',
            // 'valve_logs' => 'sesi_id_irrigate',
            'node_logs' => 'sesi_id',
        ];

        return $columnMap[$table] ?? null;
    }

    protected function generateInsertStatements($table, $data)
    {
        $sql = "-- Table: $table\n";
        
        foreach ($data as $row) {
            $columns = array_keys($row);
            $values = array_values($row);
            
            $values = array_map(function($value) {
                if (is_null($value)) {
                    return 'NULL';
                }
                if (is_numeric($value)) {
                    return $value;
                }
                return "'" . addslashes($value) . "'";
            }, $values);

            $sql .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
        }

        $sql .= "\n";
        return $sql;
    }
}
