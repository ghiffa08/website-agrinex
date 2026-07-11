@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('header', 'Dashboard Overview')

@section('content')
<!-- Stats Cards Row (Stisla Style) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Nodes -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6 flex items-center">
        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Nodes</h4>
            <div class="text-2xl font-bold text-slate-800" id="total-nodes">{{ $stats['total_nodes'] }}</div>
            <p class="text-xs text-emerald-600 mt-1"><span class="font-medium">&uarr; {{ $stats['active_nodes'] }}</span> active</p>
        </div>
    </div>

    <!-- Experimental Plots -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6 flex items-center">
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mr-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Plots</h4>
            <div class="text-2xl font-bold text-slate-800">{{ $stats['total_plots'] }}</div>
            <p class="text-xs text-slate-400 mt-1">Experimental</p>
        </div>
    </div>

    <!-- Active Alerts -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6 flex items-center">
        <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mr-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">Alerts</h4>
            <div class="text-2xl font-bold text-slate-800" id="active-alerts">{{ $stats['active_alerts'] }}</div>
            <p class="text-xs text-red-500 mt-1">Needs attention</p>
        </div>
    </div>

    <!-- Ongoing Irrigation -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6 flex items-center">
        <div class="w-16 h-16 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center mr-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
            </svg>
        </div>
        <div>
            <h4 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1">Irrigation</h4>
            <div class="text-2xl font-bold text-slate-800" id="ongoing-irrigation">{{ $stats['ongoing_irrigation'] }}</div>
            <p class="text-xs text-sky-500 mt-1">Active now</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (Wider) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Sensor Nodes Status Table -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h5 class="font-bold text-slate-800 text-lg">Sensor Nodes Status</h5>
                <button onclick="refreshNodes()" class="text-xs font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100:bg-emerald-500/20 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Refresh
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                            <th class="px-6 py-3 border-b border-slate-100">Node ID</th>
                            <th class="px-6 py-3 border-b border-slate-100">Group</th>
                            <th class="px-6 py-3 border-b border-slate-100">Status</th>
                            <th class="px-6 py-3 border-b border-slate-100">Soil Moisture</th>
                            <th class="px-6 py-3 border-b border-slate-100">Temperature</th>
                            <th class="px-6 py-3 border-b border-slate-100">Last Update</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-100" id="nodes-table-body">
                        @forelse($nodesWithData as $node)
                        <tr class="hover:bg-slate-50:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                @if($node->id == 65)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Node {{ $node->id }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Node {{ $node->id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $node->group ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($node->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Offline
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                @if($node->latestReading && $node->latestReading->soil_pct !== null)
                                    @php
                                        $moisture = $node->latestReading->soil_pct;
                                        $color = $moisture < 30 ? 'text-red-600' : ($moisture < 60 ? 'text-amber-600' : 'text-emerald-600');
                                    @endphp
                                    <span class="{{ $color }}">{{ number_format($moisture, 1) }}%</span>
                                @else
                                    <span class="text-slate-400">--</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-rose-500">
                                @if($node->latestReading && $node->latestReading->temp_c !== null)
                                    {{ number_format($node->latestReading->temp_c, 1) }} °C
                                @else
                                    <span class="text-slate-400">--</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $node->lastCommunication ? \Carbon\Carbon::parse($node->lastCommunication)->diffForHumans() : 'Never' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                No sensor nodes found in the database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Soil Moisture Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h5 class="font-bold text-slate-800 text-lg">Soil Moisture Trend (24h)</h5>
                <select id="chart-node-select" onchange="updateChart()" class="text-sm border-slate-200 rounded-lg bg-slate-50 text-slate-700 focus:ring-emerald-500 focus:border-emerald-500 w-40">
                    <option value="">Select Node</option>
                    @foreach($nodes as $node)
                        <option value="{{ $node->id }}">Node {{ $node->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative h-72">
                <canvas id="soilMoistureChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        
        <!-- Weather Widget -->
        @if($weather)
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h5 class="font-bold text-slate-800">Current Weather</h5>
            </div>
            <div class="p-6 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-50 text-amber-500 mb-4">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h2 class="text-4xl font-bold text-slate-800 mb-1">{{ number_format($weather->temp_c ?? 0, 1) }}<span class="text-2xl text-slate-400">°C</span></h2>
                <p class="text-slate-500 mb-6">{{ number_format($weather->humidity_pct ?? 0, 0) }}% Humidity</p>
                
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-xs text-slate-500 block mb-1">Light Intensity</span>
                        <span class="font-semibold text-slate-800">{{ number_format($weather->light_lux ?? 0, 0) }} lux</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-xs text-slate-500 block mb-1">Wind Speed</span>
                        <span class="font-semibold text-slate-800">{{ number_format($weather->wind_speed ?? 0, 1) }} m/s</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 col-span-2">
                        <span class="text-xs text-slate-500 block mb-1">Power Status</span>
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-800">{{ number_format($weather->voltage_v ?? 0, 2) }}V</span>
                            <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">{{ number_format($weather->current_ma ?? 0, 0) }}mA</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-4 text-right">Updated {{ \Carbon\Carbon::parse($weather->received_at)->diffForHumans() }}</p>
            </div>
        </div>
        @endif
        
        <!-- Today's Irrigation -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h5 class="font-bold text-slate-800">Today's Irrigation</h5>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100">
                    @forelse($todayIrrigation as $event)
                    <li class="p-6 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">Session: {{ $event->sesi_id_irrigate }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-medium px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">{{ $event->node_sukses }} Success</span>
                                @if($event->node_gagal > 0)
                                <span class="text-xs font-medium px-2 py-0.5 rounded bg-red-50 text-red-600">{{ $event->node_gagal }} Failed</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-2">
                                {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} 
                                @if($event->waktu_akhir)
                                    &rarr; {{ \Carbon\Carbon::parse($event->waktu_akhir)->format('H:i') }}
                                @else
                                    (In Progress...)
                                @endif
                            </p>
                        </div>
                    </li>
                    @empty
                    <li class="p-8 text-center text-slate-500">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        No irrigation events today.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection

@stack('scripts')
<script>
    // Initialize Charts inside a DOMContentLoaded event just to be safe
    document.addEventListener('DOMContentLoaded', function() {
        let soilMoistureChart;
        
        // Soil Moisture Chart
        const ctxSoilMoisture = document.getElementById('soilMoistureChart');
        if (ctxSoilMoisture) {
            soilMoistureChart = new Chart(ctxSoilMoisture, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Soil Moisture (%)',
                        data: [],
                        borderColor: '#10b981', // Tailwind emerald-500
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        
        // Make updateChart globally available
        window.updateChart = function() {
            const nodeId = document.getElementById('chart-node-select').value;
            if (!nodeId) return;
            
            // IMPORTANT: Updated fetch URL to use /admin/dashboard
            fetch(`/admin/dashboard/chart-data?node_id=${nodeId}&hours=24`)
                .then(response => response.json())
                .then(data => {
                    soilMoistureChart.data.labels = data.labels;
                    soilMoistureChart.data.datasets[0].data = data.soil_moisture;
                    soilMoistureChart.update();
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }
        
        // Initial load
        const nodeSelect = document.getElementById('chart-node-select');
        if (nodeSelect && nodeSelect.options.length > 1) {
            nodeSelect.selectedIndex = 1;
            updateChart();
        }
        
        // Make refreshNodes globally available
        window.refreshNodes = function() {
            // IMPORTANT: Updated fetch URL to use /admin/dashboard
            fetch('/admin/dashboard/realtime-data')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-nodes').textContent = data.nodes.length;
                    document.getElementById('active-alerts').textContent = data.active_alerts;
                })
                .catch(error => console.error('Error refreshing data:', error));
        }
        
        setInterval(refreshNodes, 30000);
    });
</script>
