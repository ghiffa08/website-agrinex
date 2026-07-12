<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ValveLog;
use App\Models\Node;
use Illuminate\Http\Request;

class ValveLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = ValveLog::with('node')->orderBy('waktu', 'desc');
        
        // Filter by node_id
        if ($request->has('node_id') && $request->node_id != '') {
            $query->where('node_id', $request->node_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by sesi_id
        if ($request->has('sesi_id') && $request->sesi_id != '') {
            $query->where('sesi_id_irrigate', $request->sesi_id);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('waktu', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('waktu', '<=', $request->end_date);
        }
        
        $logs = $query->simplePaginate(25);
        $nodes = Node::where('id', '!=', 65)->orderBy('id')->get();
        
        return view('admin.valve-logs.index', compact('logs', 'nodes'));
    }
    
    public function show($id)
    {
        $log = ValveLog::with('node')->findOrFail($id);
        return view('admin.valve-logs.show', compact('log'));
    }
    
    public function edit($id)
    {
        $log = ValveLog::with('node')->findOrFail($id);
        $nodes = Node::where('node_id', '!=', 65)->orderBy('node_id')->get();
        return view('admin.valve-logs.edit', compact('log', 'nodes'));
    }
    
    public function update(Request $request, $id)
    {
        $log = ValveLog::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:ON,OFF',
            'durasi_detik' => 'required|integer|min:0',
            'volume_air' => 'nullable|numeric|min:0',
            'rata_rata' => 'nullable|numeric|min:0',
            'pulse' => 'nullable|integer|min:0',
        ]);
        
        $log->update($validated);
        
        return redirect()->route('admin.valve-logs.show', $id)
            ->with('success', 'Valve log updated successfully!');
    }
    
    public function destroy($id)
    {
        $log = ValveLog::findOrFail($id);
        $log->delete();
        
        return redirect()->route('admin.valve-logs.index')
            ->with('success', 'Valve log deleted successfully!');
    }
}
