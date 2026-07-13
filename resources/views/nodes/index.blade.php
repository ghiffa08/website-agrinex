@extends('layouts.app')

@section('title', 'Sensor Nodes')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-cpu"></i> Sensor Nodes</h4>
            <p class="text-muted mb-0">Monitor and manage all sensor nodes</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card-custom">
                <div class="card-custom-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Nodes</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="bi bi-cpu"></i>
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
                            <p class="text-muted mb-1">Active Nodes</p>
                            <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="bi bi-check-circle"></i>
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
                            <p class="text-muted mb-1">Offline Nodes</p>
                            <h3 class="mb-0 text-danger">{{ $stats['offline'] }}</h3>
                        </div>
                        <div class="stat-icon bg-danger">
                            <i class="bi bi-exclamation-circle"></i>
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
                            <p class="text-muted mb-1">Alerts</p>
                            <h3 class="mb-0 text-warning">{{ $stats['alerts'] }}</h3>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="bi bi-bell"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nodes List -->
    <div class="card-custom">
        <div class="card-custom-header">
            <h5 class="mb-0">All Sensor Nodes</h5>
        </div>
        <div class="card-custom-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Node ID</th>
                            <th>Group</th>
                            <th>Treatment Code</th>
                            <th>Location</th>
                            <th>Latest Reading</th>
                            <th>Temperature</th>
                            <th>Soil Moisture</th>
                            <th>Voltage</th>
                            <th>Signal</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nodes as $node)
                        <tr>
                            <td><strong>{{ $node->id }}</strong></td>
                            <td>{{ $node->group ?? '-' }}</td>
                            <td>{{ $node->kode_perlakuan ?? '-' }}</td>
                            <td>{{ $node->lokasi ?? '-' }}</td>
                            <td>
                                @if($node->latestSensorData)
                                    <small>{{ $node->latestSensorData->recorded_at->format('d/m/Y H:i') }}</small>
                                @else
                                    <small class="text-muted">No data</small>
                                @endif
                            </td>
                            <td>
                                @if($node->latestSensorData)
                                    {{ number_format($node->latestSensorData->temperature, 1) }}°C
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($node->latestSensorData)
                                    <span class="badge {{ $node->latestSensorData->soil_moisture < 30 ? 'bg-danger' : 'bg-success' }}">
                                        {{ number_format($node->latestSensorData->soil_moisture, 1) }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($node->latestSensorData)
                                    <span class="badge {{ $node->latestSensorData->volt < 3.0 ? 'bg-warning' : 'bg-success' }}">
                                        {{ number_format($node->latestSensorData->volt, 2) }}V
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($node->logs->isNotEmpty())
                                    @php
                                        $latestLog = $node->logs->first();
                                        $signalQuality = $latestLog->signal_quality ?? 'UNKNOWN';
                                        $badgeClass = match($signalQuality) {
                                            'GOOD' => 'bg-success',
                                            'FAIR' => 'bg-warning',
                                            default => 'bg-danger'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $signalQuality }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $isOnline = $node->latestSensorData && 
                                                $node->latestSensorData->recorded_at->diffInHours(now()) < 1;
                                @endphp
                                <span class="node-badge {{ $isOnline ? 'online' : 'offline' }}">
                                    <span class="status-dot {{ $isOnline ? 'online' : 'offline' }}"></span>
                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('nodes.show', $node->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">No sensor nodes found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
