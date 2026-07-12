<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SensorNodeData;
use App\Models\Node;
use Illuminate\Http\Request;

class SensorNodeDataController extends Controller
{
    public function index(Request $request)
    {
        $query = SensorNodeData::with('node')->orderBy('received_at', 'desc');
        
        // Filter by node_id
        if ($request->has('node_id') && $request->node_id != '') {
            $query->where('node_id', $request->node_id);
        }
        
        // Filter by sesi_id
        if ($request->has('sesi_id') && $request->sesi_id != '') {
            $query->where('sesi_id_getdata', $request->sesi_id);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('received_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('received_at', '<=', $request->end_date);
        }
        
        $sensorData = $query->simplePaginate(25);
        $nodes = Node::where('id', '!=', 65)->orderBy('id')->get();
        
        return view('admin.sensor-node-data.index', compact('sensorData', 'nodes'));
    }
    
    public function show($id)
    {
        $data = SensorNodeData::with('node')->findOrFail($id);
        return view('admin.sensor-node-data.show', compact('data'));
    }
    
    public function edit($id)
    {
        $data = SensorNodeData::with('node')->findOrFail($id);
        $nodes = Node::orderBy('node_id')->get();
        return view('admin.sensor-node-data.edit', compact('data', 'nodes'));
    }
    
    public function update(Request $request, $id)
    {
        $data = SensorNodeData::findOrFail($id);
        
        $validated = $request->validate([
            'voltage_v' => 'nullable|numeric|min:0',
            'current_ma' => 'nullable|numeric|min:0',
            'power_mw' => 'nullable|numeric|min:0',
            'temp_c' => 'nullable|numeric',
            'soil_pct' => 'nullable|numeric|min:0|max:100',
            'soil_adc' => 'nullable|integer|min:0',
        ]);
        
        $data->update($validated);
        
        return redirect()->route('admin.sensor-node-data.show', $id)
            ->with('success', 'Sensor node data updated successfully!');
    }
    
    public function destroy($id)
    {
        $data = SensorNodeData::findOrFail($id);
        $data->delete();
        
        return redirect()->route('admin.sensor-node-data.index')
            ->with('success', 'Sensor node data deleted successfully!');
    }
}
