<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display reports page
     * GET /reports
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get report type filter
        $reportType = $request->get('type', 'all');
        
        return view('reports', [
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => $reportType,
            'pageTitle' => 'Laporan - AgriNex',
            'pageDescription' => 'Laporan dan analisis data irigasi'
        ]);
    }
    
    /**
     * Export report data
     * POST /reports/export
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:irrigation,sensor,weather,usage,summary,comprehensive',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,csv,excel',
            'device_id' => 'nullable|exists:devices,id'
        ]);
        
        $filters = [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'device_id' => $validated['device_id'] ?? null
        ];
        
        $reportService = app(\App\Services\ReportService::class);
        
        try {
            switch ($validated['type']) {
                case 'sensor':
                    if ($validated['format'] === 'excel' || $validated['format'] === 'csv') {
                        return $reportService->generateSensorDataExcel($filters);
                    }
                    break;
                    
                case 'weather':
                    if ($validated['format'] === 'excel' || $validated['format'] === 'csv') {
                        return $reportService->generateWeatherDataExcel($filters);
                    }
                    break;
                    
                case 'irrigation':
                    if ($validated['format'] === 'excel' || $validated['format'] === 'csv') {
                        return $reportService->generateIrrigationExcel($filters);
                    }
                    break;
                    
                case 'usage':
                    if ($validated['format'] === 'excel' || $validated['format'] === 'csv') {
                        return $reportService->generateWaterUsageExcel($filters);
                    }
                    break;
                    
                case 'summary':
                case 'comprehensive':
                    if ($validated['format'] === 'pdf') {
                        return $reportService->generateComprehensivePdf($filters);
                    } elseif ($validated['format'] === 'excel') {
                        return $reportService->generateComprehensiveExcel($filters);
                    }
                    break;
            }
            
            return back()->with('error', 'Format tidak didukung untuk tipe laporan ini');
            
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor laporan: ' . $e->getMessage());
        }
    }
}
