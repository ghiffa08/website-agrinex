@extends('layouts.app')

@section('title', 'Node ' . $node->id . ' Details')

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('nodes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Nodes
            </a>
        </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-cpu"></i> Node {{ $node->id }}
                @php
                    // Fixed: Check if sensorNodeData exists and is not empty before accessing
                    $isOnline =
                        $latestSensorData &&
                        $latestSensorData->recorded_at &&
                        $latestSensorData->recorded_at->diffInHours(now()) < 1;
                @endphp
                <span class="node-badge {{ $isOnline ? 'online' : 'offline' }} ms-2">
                    <span class="status-dot {{ $isOnline ? 'online' : 'offline' }}"></span>
                    {{ $isOnline ? 'Online' : 'Offline' }}
                </span>
            </h4>
            <p class="text-muted mb-0">
                Group: {{ $node->group ?? 'N/A' }} |
                Treatment: {{ $node->kode_perlakuan ?? 'N/A' }} |
                Location: {{ $node->lokasi ?? 'N/A' }}
            </p>
        </div>
        <div>
            @can('role', ['admin', 'operator'])
                @include('nodes.partials.ai-calibration-modal')
                <a href="{{ route('nodes.edit', $node->id) }}" class="btn btn-primary ms-2">
                    <i class="bi bi-pencil"></i> Edit Node
                </a>
            @endcan
        </div>
    </div>        <!-- Current Status Cards -->
        @if ($sensorData->isNotEmpty())
            @php $latest = $sensorData->first(); @endphp
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-custom-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Temperature</p>
                                    <h3 class="mb-0">{{ number_format($latest->temperature ?? 0, 1) }}°C</h3>
                                    <small
                                        class="text-muted">{{ $latest->recorded_at ? $latest->recorded_at->diffForHumans() : 'N/A' }}</small>
                                </div>
                                <div class="stat-icon bg-danger">
                                    <i class="bi bi-thermometer-half"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-custom-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Soil Moisture</p>
                                    <h3 class="mb-0 {{ ($latest->soil_moisture ?? 0) < 30 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($latest->soil_moisture ?? 0, 1) }}%
                                    </h3>
                                    <small
                                        class="text-muted">{{ $latest->recorded_at ? $latest->recorded_at->diffForHumans() : 'N/A' }}</small>
                                </div>
                                <div class="stat-icon {{ ($latest->soil_moisture ?? 0) < 30 ? 'bg-danger' : 'bg-success' }}">
                                    <i class="bi bi-moisture"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-custom-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Voltage</p>
                                    <h3 class="mb-0 {{ ($latest->voltage_v ?? 0) < 3.0 ? 'text-warning' : 'text-success' }}">
                                        {{ number_format($latest->voltage_v ?? 0, 2) }}V
                                    </h3>
                                    <small
                                        class="text-muted">{{ $latest->recorded_at ? $latest->recorded_at->diffForHumans() : 'N/A' }}</small>
                                </div>
                                <div class="stat-icon {{ ($latest->voltage_v ?? 0) < 3.0 ? 'bg-warning' : 'bg-success' }}">
                                    <i class="bi bi-battery-charging"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-custom">
                        <div class="card-custom-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Current</p>
                                    <h3 class="mb-0">{{ number_format($latest->current_ma ?? 0, 0) }} mA</h3>
                                    <small
                                        class="text-muted">{{ $latest->recorded_at ? $latest->recorded_at->diffForHumans() : 'N/A' }}</small>
                                </div>
                                <div class="stat-icon bg-info">
                                    <i class="bi bi-lightning-charge"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>No sensor data available for this node yet.
            </div>
        @endif

        <div class="row g-3">
            <!-- 24-Hour Chart -->
            <div class="col-md-8">
                <div class="card-custom">
                    <div class="card-custom-header">
                        <h5 class="mb-0">24-Hour Sensor Data</h5>
                    </div>
                    <div class="card-custom-body">
                        @if ($sensorData->isNotEmpty())
                            <canvas id="sensorChart" height="80"></canvas>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-graph-up fs-1"></i>
                                <p class="mt-2">No chart data available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Readings -->
                <div class="card-custom mt-3">
                    <div class="card-custom-header">
                        <h5 class="mb-0">Recent Readings</h5>
                    </div>
                    <div class="card-custom-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Temperature</th>
                                        <th>Moisture</th>
                                        <th>Voltage</th>
                                        <th>Current</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sensorData->take(20) as $data)
                                        <tr>
                                            <td>{{ $data->recorded_at ? $data->recorded_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td>{{ number_format($data->temperature ?? 0, 1) }}°C</td>
                                            <td>
                                                <span
                                                    class="badge {{ ($data->soil_moisture ?? 0) < 30 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ number_format($data->soil_moisture ?? 0, 1) }}%
                                                </span>
                                            </td>
                                            <td>{{ number_format($data->voltage_v ?? 0, 2) }}V</td>
                                            <td>{{ number_format($data->current_ma ?? 0, 0) }} mA</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No recent readings</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Communication Logs -->
            <div class="col-md-4">
                <div class="card-custom">
                    <div class="card-custom-header">
                        <h5 class="mb-0">Communication Logs</h5>
                    </div>
                    <div class="card-custom-body">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>RSSI</th>
                                        <th>SNR</th>
                                        <th>Quality</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td><small>{{ $log->waktu_log ? $log->waktu_log->format('H:i:s') : 'N/A' }}</small>
                                            </td>
                                            <td><small>{{ $log->rssi ?? 'N/A' }} dBm</small></td>
                                            <td><small>{{ $log->snr ?? 'N/A' }} dB</small></td>
                                            <td>
                                                @php
                                                    $rssi = $log->rssi ?? -999;
                                                    if ($rssi > -70) {
                                                        $quality = 'EXCELLENT';
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($rssi > -85) {
                                                        $quality = 'GOOD';
                                                        $badgeClass = 'bg-primary';
                                                    } elseif ($rssi > -100) {
                                                        $quality = 'FAIR';
                                                        $badgeClass = 'bg-warning';
                                                    } else {
                                                        $quality = 'POOR';
                                                        $badgeClass = 'bg-danger';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $quality }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No logs available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('sensorChart');
                if (!ctx) return;

                const sensorData = @json($sensorData);

                if (!sensorData || sensorData.length === 0) {
                    return;
                }

                const labels = sensorData.map(d => {
                    const date = new Date(d.recorded_at);
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                });

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Temperature (°C)',
                                data: sensorData.map(d => d.temperature || 0),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                yAxisID: 'y',
                                },
                                {
                                label: 'Soil Moisture (%)',
                                data: sensorData.map(d => d.soil_moisture || 0),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Temperature (°C)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Soil Moisture (%)'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
