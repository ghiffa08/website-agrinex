<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IrrigateLog;
use Illuminate\Http\Request;

class IrrigateLogsController extends Controller
{
       public function index(Request $request)
       {
           // FIX N+1: Eager load relations untuk menghindari query berulang di view
           $query = IrrigateLog::with(['valveLogs', 'nodeLogs'])
               ->orderBy('waktu_mulai', 'desc');
        
           // Filter by date range
           if ($request->has('start_date') && $request->start_date != '') {
               $query->whereDate('waktu_mulai', '>=', $request->start_date);
           }
        
           if ($request->has('end_date') && $request->end_date != '') {
               $query->whereDate('waktu_mulai', '<=', $request->end_date);
           }
        
           $logs = $query->paginate(25);
        
           return view('admin.irrigate-logs.index', compact('logs'));
       }
    
    public function show($id)
    {
        $log = IrrigateLog::with(['valveLogs', 'nodeLogs'])->findOrFail($id);
        return view('admin.irrigate-logs.show', compact('log'));
    }
    
    public function edit($id)
    {
        $log = IrrigateLog::findOrFail($id);
        return view('admin.irrigate-logs.edit', compact('log'));
    }
    
    public function update(Request $request, $id)
    {
        $log = IrrigateLog::findOrFail($id);
        
        $validated = $request->validate([
            'node_sukses' => 'required|integer|min:0',
            'node_gagal' => 'required|integer|min:0',
            'valve_on_akhir' => 'required|integer|min:0',
        ]);
        
        $log->update($validated);
        
        return redirect()->route('admin.irrigate-logs.show', $id)
            ->with('success', 'Irrigate log updated successfully!');
    }
    
    public function destroy($id)
    {
        $log = IrrigateLog::findOrFail($id);
        $log->delete();
        
        return redirect()->route('admin.irrigate-logs.index')
            ->with('success', 'Irrigate log deleted successfully!');
    }
}
