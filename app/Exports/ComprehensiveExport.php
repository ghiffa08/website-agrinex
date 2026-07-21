<?php

namespace App\Exports;

use App\Repositories\Contracts\ReportRepositoryInterface;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ComprehensiveExport implements WithMultipleSheets
{
    protected array $filters;
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(array $filters, ReportRepositoryInterface $reportRepository)
    {
        $this->filters = $filters;
        $this->reportRepository = $reportRepository;
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->filters, $this->reportRepository),
            new SensorDataExport($this->reportRepository->getSensorDataReport($this->filters)),
            new WeatherDataExport($this->reportRepository->getWeatherDataReport($this->filters)),
            new IrrigationExport($this->reportRepository->getIrrigationReport($this->filters)),
            new WaterUsageSummaryExport($this->reportRepository->getWaterUsageSummary($this->filters)),
        ];
    }
}
