@extends('layouts.app')

@section('title', 'Edit Node ' . $node->id)

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('nodes.show', $node->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Node Details
        </a>
    </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-pencil-square"></i> Edit Node {{ $node->id }}
            </h4>
            <p class="text-muted mb-0">Update node information and configuration</p>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card-custom">
                <div class="card-custom-header">
                    <h5 class="mb-0">Node Information</h5>
                </div>
                <div class="card-custom-body">
                    <form action="{{ route('nodes.update', $node->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="node_id" class="form-label">Node ID *</label>
                            <input type="text" class="form-control" id="node_id" value="{{ $node->id }}" disabled>
                            <small class="text-muted">Node ID cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label">Group</label>
                            <input type="text" 
                                   class="form-control @error('group') is-invalid @enderror" 
                                   id="group" 
                                   name="group" 
                                   value="{{ old('group', $node->group) }}"
                                   placeholder="e.g., A, B, C">
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Group classification for this node</small>
                        </div>

                        <div class="mb-3">
                            <label for="kode_perlakuan" class="form-label">Treatment Code</label>
                            <input type="text" 
                                   class="form-control @error('kode_perlakuan') is-invalid @enderror" 
                                   id="kode_perlakuan" 
                                   name="kode_perlakuan" 
                                   value="{{ old('kode_perlakuan', $node->kode_perlakuan) }}"
                                   placeholder="e.g., T1, T2, Control">
                            @error('kode_perlakuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Treatment code or identifier</small>
                        </div>

                        <div class="mb-3">
                            <label for="lokasi" class="form-label">Location</label>
                            <input type="text" 
                                   class="form-control @error('lokasi') is-invalid @enderror" 
                                   id="lokasi" 
                                   name="lokasi" 
                                   value="{{ old('lokasi', $node->lokasi) }}"
                                   placeholder="e.g., Greenhouse A, Field 1, Plot 12">
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Physical location of the node</small>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Description / Notes</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="4"
                                      placeholder="Additional notes or description about this node...">{{ old('keterangan', $node->keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Any additional information or notes</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('nodes.show', $node->id) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Node Info Sidebar -->
        <div class="col-md-4">
            <!-- Current Status -->
            <div class="card-custom mb-3">
                <div class="card-custom-header">
                    <h5 class="mb-0">Current Status</h5>
                </div>
                <div class="card-custom-body">
                    @php
                        $latestData = $node->latestSensorData;
                        $isOnline = $latestData && 
                                    $latestData->recorded_at && 
                                    $latestData->recorded_at->diffInHours(now()) < 1;
                    @endphp
                    
                    <div class="mb-3">
                        <label class="text-muted small">Connection Status</label>
                        <div>
                            <span class="node-badge {{ $isOnline ? 'online' : 'offline' }}">
                                <span class="status-dot {{ $isOnline ? 'online' : 'offline' }}"></span>
                                {{ $isOnline ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    </div>

                    @if($latestData)
                        <div class="mb-3">
                            <label class="text-muted small">Last Communication</label>
                            <div>{{ $latestData->recorded_at->diffForHumans() }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Latest Temperature</label>
                            <div><strong>{{ number_format($latestData->temperature ?? 0, 1) }}°C</strong></div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small">Latest Moisture</label>
                            <div>
                                <strong class="{{ ($latestData->soil_moisture ?? 0) < 30 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($latestData->soil_moisture ?? 0, 1) }}%
                                </strong>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small">Battery Voltage</label>
                            <div>
                                <strong class="{{ ($latestData->voltage_v ?? 0) < 3.0 ? 'text-warning' : 'text-success' }}">
                                    {{ number_format($latestData->voltage_v ?? 0, 2) }}V
                                </strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-exclamation-circle fs-1"></i>
                            <p class="mt-2 mb-0">No sensor data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Node Info -->
            <div class="card-custom">
                <div class="card-custom-header">
                    <h5 class="mb-0">Node Information</h5>
                </div>
                <div class="card-custom-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="50%">Created</th>
                            <td>{{ $node->created_at ? $node->created_at->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $node->updated_at ? $node->updated_at->format('d M Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Readings</th>
                            <td>{{ $node->sensorNodeData()->count() ?? 0 }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Help Info -->
            <div class="alert alert-info mt-3">
                <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Editing Tips</h6>
                <small>
                    <ul class="mb-0 ps-3">
                        <li>Node ID is read-only and cannot be changed</li>
                        <li>All fields are optional except where marked with *</li>
                        <li>Changes will be saved immediately</li>
                        <li>Sensor data will not be affected</li>
                    </ul>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
