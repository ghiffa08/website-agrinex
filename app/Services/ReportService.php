<?php

namespace App\Services;

use App\Repositories\Contracts\ReportRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Generate Excel report for sensor data
     */
    public function generateSensorDataExcel(array $filters)
    {
        $data = $this->reportRepository->getSensorDataReport($filters);
        $filename = 'sensor_data_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\SensorDataExport($data),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Generate Excel report for weather data
     */
    public function generateWeatherDataExcel(array $filters)
    {
        $data = $this->reportRepository->getWeatherDataReport($filters);
        $filename = 'weather_data_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\WeatherDataExport($data),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Generate Excel report for irrigation logs
     */
    public function generateIrrigationExcel(array $filters)
    {
        $data = $this->reportRepository->getIrrigationReport($filters);
        $filename = 'irrigation_report_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\IrrigationExport($data),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Generate Excel report for water usage summary
     */
    public function generateWaterUsageExcel(array $filters)
    {
        $data = $this->reportRepository->getWaterUsageSummary($filters);
        $filename = 'water_usage_summary_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\WaterUsageSummaryExport($data),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Generate comprehensive PDF report
     */
    public function generateComprehensivePdf(array $filters)
    {
        $summary = $this->reportRepository->getDashboardSummary($filters);
        $deviceActivity = $this->reportRepository->getDeviceActivityReport($filters);
        $waterUsage = $this->reportRepository->getWaterUsageSummary($filters);
        
        // Recent sensor data (last 50)
        $recentSensors = $this->reportRepository->getSensorDataReport(array_merge($filters, ['limit' => 50]));
        
        // Recent irrigation (last 30)
        $recentIrrigation = $this->reportRepository->getIrrigationReport(array_merge($filters, ['limit' => 30]));

        $data = [
            'report_title' => 'AgriNex SmartDrip - Laporan Komprehensif',
            'generated_at' => now()->format('d F Y H:i:s'),
            'summary' => $summary,
            'device_activity' => $deviceActivity,
            'water_usage' => $waterUsage,
            'recent_sensors' => $recentSensors,
            'recent_irrigation' => $recentIrrigation,
            'filters' => $filters,
        ];

        $pdf = Pdf::loadView('reports.comprehensive', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'comprehensive_report_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate irrigation summary PDF report
     */
    public function generateIrrigationPdf(array $filters)
    {
        $irrigation = $this->reportRepository->getIrrigationReport($filters);
        $waterUsage = $this->reportRepository->getWaterUsageSummary($filters);

        $data = [
            'report_title' => 'Laporan Irigasi',
            'generated_at' => now()->format('d F Y H:i:s'),
            'irrigation_logs' => $irrigation,
            'water_usage_summary' => $waterUsage,
            'filters' => $filters,
        ];

        $pdf = Pdf::loadView('reports.irrigation', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'irrigation_report_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate comprehensive Excel report (all data in separate sheets)
     */
    public function generateComprehensiveExcel(array $filters)
    {
        $filename = 'comprehensive_report_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new \App\Exports\ComprehensiveExport($filters, $this->reportRepository),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Validate and normalize filter parameters
     */
    public function normalizeFilters(array $filters): array
    {
        // Default date range: last 30 days
        $startDate = $filters['start_date'] ?? Carbon::now()->subDays(30)->toDateString();
        $endDate = $filters['end_date'] ?? Carbon::now()->toDateString();

        // Validate dates
        try {
            $startDate = Carbon::parse($startDate)->toDateString();
            $endDate = Carbon::parse($endDate)->toDateString();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->subDays(30)->toDateString();
            $endDate = Carbon::now()->toDateString();
        }

        // Ensure start <= end
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'device_id' => $filters['device_id'] ?? null,
            'limit' => min($filters['limit'] ?? 1000, 10000), // Max 10k records
        ];
    }

    /**
     * Get available report types
     */
    public function getAvailableReports(): array
    {
        return [
            [
                'id' => 'sensor_data_excel',
                'name' => 'Data Sensor (Excel)',
                'description' => 'Laporan lengkap data sensor dari semua device',
                'format' => 'xlsx',
                'icon' => 'chart-line',
            ],
            [
                'id' => 'weather_data_excel',
                'name' => 'Data Cuaca (Excel)',
                'description' => 'Laporan data cuaca dan kondisi lingkungan',
                'format' => 'xlsx',
                'icon' => 'cloud-sun',
            ],
            [
                'id' => 'irrigation_excel',
                'name' => 'Log Irigasi (Excel)',
                'description' => 'Riwayat lengkap sesi irigasi dan penggunaan air',
                'format' => 'xlsx',
                'icon' => 'droplet',
            ],
            [
                'id' => 'water_usage_excel',
                'name' => 'Ringkasan Penggunaan Air (Excel)',
                'description' => 'Statistik penggunaan air per device',
                'format' => 'xlsx',
                'icon' => 'database',
            ],
            [
                'id' => 'comprehensive_pdf',
                'name' => 'Laporan Komprehensif (PDF)',
                'description' => 'Laporan lengkap semua aspek sistem dalam format PDF',
                'format' => 'pdf',
                'icon' => 'file-text',
            ],
            [
                'id' => 'irrigation_pdf',
                'name' => 'Laporan Irigasi (PDF)',
                'description' => 'Laporan fokus irigasi dan penggunaan air dalam format PDF',
                'format' => 'pdf',
                'icon' => 'file-chart',
            ],
        ];
    }
}
