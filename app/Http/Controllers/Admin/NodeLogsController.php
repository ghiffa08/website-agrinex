<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NodeLog;
use App\Models\Node;
use Illuminate\Http\Request;

class NodeLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = NodeLog::with('node')->orderBy('waktu', 'desc');
        
        // Filter by node_id
        if ($request->has('node_id') && $request->node_id != '') {
            $query->where('node_id', $request->node_id);
        }
        
        // Filter by type_sesi
        if ($request->has('type_sesi') && $request->type_sesi != '') {
            $query->where('type_sesi', $request->type_sesi);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('waktu', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('waktu', '<=', $request->end_date);
        }
        
        $logs = $query->simplePaginate(25);
        $nodes = Node::orderBy('id')->get();
        
        return view('admin.node-logs.index', compact('logs', 'nodes'));
    }
    
    public function show($id)
    {
        $log = NodeLog::with('node')->findOrFail($id);
        return view('admin.node-logs.show', compact('log'));
    }
    
    public function edit($id)
    {
        $log = NodeLog::with('node')->findOrFail($id);
        $nodes = Node::orderBy('node_id')->get();
        return view('admin.node-logs.edit', compact('log', 'nodes'));
    }
    
    public function update(Request $request, $id)
    {
        $log = NodeLog::findOrFail($id);
        
        $validated = $request->validate([
            'rssi_dbm' => 'nullable|numeric',
            'snr_db' => 'nullable|numeric',
            'signal_quality' => 'nullable|string|max:20',
            'status' => 'required|in:Aktif,Non Aktif',
            'keterangan' => 'nullable|string',
        ]);
        
        $log->update($validated);
        
        return redirect()->route('admin.node-logs.show', $id)
            ->with('success', 'Node log updated successfully!');
    }
    
    public function destroy($id)
    {
        $log = NodeLog::findOrFail($id);
        $log->delete();
        
        return redirect()->route('admin.node-logs.index')
            ->with('success', 'Node log deleted successfully!');
    }
}
