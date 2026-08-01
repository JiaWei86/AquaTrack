@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Admin Dashboard</h2>
            <p class="text-muted mb-0">System overview</p>
        </div>
    </div>

    {{-- Statistics cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['totalComplaints'] }}</h3>
                    <small class="text-muted">Total Complaints</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0 text-warning">{{ $stats['pendingComplaints'] }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0 text-success">{{ $stats['resolvedComplaints'] }}</h3>
                    <small class="text-muted">Resolved</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['totalUsers'] }}</h3>
                    <small class="text-muted">Users</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['totalWaterSources'] }}</h3>
                    <small class="text-muted">Water Sources</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent complaints --}}
    <div class="card">
        <div class="card-header card-header-aqua">Recent Complaints</div>
        <div class="card-body">
            @if ($recentComplaints->isEmpty())
                <p class="text-muted mb-0">No complaints yet.</p>
            @else
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Resident</th>
                            <th>Water Source</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentComplaints as $complaint)
                            <tr>
                                <td>{{ $complaint->resident->name }}</td>
                                <td>{{ $complaint->waterSource->name }}</td>
                                <td>{{ $complaint->title }}</td>
                                <td><span class="badge badge-aqua">{{ $complaint->status }}</span></td>
                                <td>
                                    <a href="{{ route('complaints.show', $complaint) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
