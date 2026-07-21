<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    protected ReportRepositoryInterface $reportRepository;
    protected ReportService $reportService;

    public function __construct(ReportRepositoryInterface $reportRepository, ReportService $reportService)
    {
        $this->reportRepository = $reportRepository;
        $this->reportService = $reportService;
    }

    /**
     * Get preview summary for report filters
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'device_id' => 'nullable|exists:devices,id',
        ]);

        $filters = $this->reportService->normalizeFilters($validated);

        try {
            $summary = $this->reportRepository->getDashboardSummary($filters);
            
            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            \Log::error('Report preview failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed report data
     * GET /api/v1/reports/data
     */
    public function getData(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:sensor,weather,irrigation,usage,device_activity',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'device_id' => 'nullable|exists:devices,id',
            'limit' => 'nullable|integer|min:1|max:1000',
        ]);

        $filters = $this->reportService->normalizeFilters($validated);

        try {
            $data = match($validated['type']) {
                'sensor' => $this->reportRepository->getSensorDataReport($filters),
                'weather' => $this->reportRepository->getWeatherDataReport($filters),
                'irrigation' => $this->reportRepository->getIrrigationReport($filters),
                'usage' => $this->reportRepository->getWaterUsageSummary($filters),
                'device_activity' => $this->reportRepository->getDeviceActivityReport($filters),
            };
            
            return response()->json([
                'success' => true,
                'type' => $validated['type'],
                'data' => $data,
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            \Log::error('Get report data failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available report types
     * GET /api/v1/reports/types
     */
    public function getTypes()
    {
        return response()->json([
            'success' => true,
            'types' => $this->reportService->getAvailableReports()
        ]);
    }
}
