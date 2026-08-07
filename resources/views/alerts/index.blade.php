@extends('layouts.app')

@section('title', 'Alerts')
@section('page-title', 'Water Quality Alerts')
@section('page-subtitle', 'Review and resolve water quality alerts')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($alerts->isEmpty())
                <p class="text-muted mb-0">No alerts have been recorded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Water Source</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Triggered Reading ID</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alerts as $alert)
                                <tr>
                                    <td>{{ $alert->id }}</td>
                                    <td>{{ optional($alert->waterSource)->source_name ?? 'N/A' }}</td>
                                    <td>
                                        <span @class([
                                            'badge',
                                            'bg-secondary' => $alert->severity === 'Low',
                                            'bg-warning text-dark' => $alert->severity === 'Medium',
                                            'bg-danger' => $alert->severity === 'High',
                                        ])>
                                            {{ $alert->severity }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($alert->status === 'Active')
                                            <span class="badge bg-danger">Active</span>
                                        @else
                                            <span class="badge bg-success">Resolved</span>
                                        @endif
                                    </td>
                                    <td>{{ $alert->message }}</td>
                                    <td>{{ optional($alert->qualityReading)->id ?? $alert->quality_reading_id ?? 'N/A' }}</td>
                                    <td>{{ $alert->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('alerts.show', $alert) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>

                                            @if ($alert->status === 'Active')
                                                <form action="{{ route('alerts.update', $alert) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        Resolve
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-success align-self-center">Resolved</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
