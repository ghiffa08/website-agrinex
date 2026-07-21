<?php

namespace App\Exports;

use App\Repositories\Contracts\ReportRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SummarySheet implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $filters;
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(array $filters, ReportRepositoryInterface $reportRepository)
    {
        $this->filters = $filters;
        $this->reportRepository = $reportRepository;
    }

    public function array(): array
    {
        $summary = $this->reportRepository->getDashboardSummary($this->filters);
        $deviceActivity = $this->reportRepository->getDeviceActivityReport($this->filters);
        $waterUsage = $this->reportRepository->getWaterUsageSummary($this->filters);

        $rows = [];
        
        // Report Header
        $rows[] = ['AgriNex SmartDrip - Laporan Komprehensif'];
        $rows[] = ['Tanggal Generate', now()->format('d F Y H:i:s')];
        $rows[] = ['Periode', ($this->filters['start_date'] ?? '-') . ' s/d ' . ($this->filters['end_date'] ?? '-')];
        $rows[] = []; // Empty row
        
        // Summary Statistics
        $rows[] = ['RINGKASAN STATISTIK'];
        $rows[] = ['Total Devices', $summary['total_devices'] ?? 0];
        $rows[] = ['Devices Aktif', $summary['active_devices'] ?? 0];
        $rows[] = ['Total Sesi Irigasi', $summary['total_irrigation_sessions'] ?? 0];
        $rows[] = ['Total Pembacaan Sensor', $summary['total_sensor_readings'] ?? 0];
        $rows[] = ['Total Data Cuaca', $summary['total_weather_data'] ?? 0];
        $rows[] = []; // Empty row
        
        // Water Usage Summary
        $rows[] = ['RINGKASAN PENGGUNAAN AIR'];
        $rows[] = ['Device', 'Total Volume (L)', 'Rata-rata per Sesi (L)', 'Jumlah Sesi'];
        foreach ($waterUsage as $usage) {
            $rows[] = [
                $usage['device_name'] ?? '-',
                $usage['total_volume_l'] ?? 0,
                $usage['avg_volume_per_session_l'] ?? 0,
                $usage['session_count'] ?? 0,
            ];
        }
        $rows[] = []; // Empty row
        
        // Device Activity
        $rows[] = ['AKTIVITAS DEVICE'];
        $rows[] = ['Device', 'Status', 'Last Active', 'Total Readings'];
        foreach ($deviceActivity as $device) {
            $rows[] = [
                $device['name'] ?? '-',
                $device['status'] ?? '-',
                $device['last_active'] ?? '-',
                $device['total_readings'] ?? 0,
            ];
        }
        
        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10b981'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'e5e7eb'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}
