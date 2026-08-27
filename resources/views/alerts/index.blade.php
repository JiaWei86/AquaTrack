@extends('layouts.app')

@section('title', 'Alerts')
@section('page-title', 'Water Quality Alerts')
@section('page-subtitle', 'Review and resolve water quality alerts')

@section('content')
@php
    $nextSortDirection = fn (string $column) => ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
@endphp
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
                                <th class="text-nowrap">No.</th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'id', 'direction' => $nextSortDirection('id')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Alert ID
                                        @if ($activeSort === 'id')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'water_source', 'direction' => $nextSortDirection('water_source')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Water Source
                                        @if ($activeSort === 'water_source')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'severity', 'direction' => $nextSortDirection('severity')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Severity
                                        @if ($activeSort === 'severity')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'status', 'direction' => $nextSortDirection('status')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Status
                                        @if ($activeSort === 'status')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'message', 'direction' => $nextSortDirection('message')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Message
                                        @if ($activeSort === 'message')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'quality_reading', 'direction' => $nextSortDirection('quality_reading')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Triggered Reading
                                        @if ($activeSort === 'quality_reading')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('alerts.index', ['sort' => 'created_at', 'direction' => $nextSortDirection('created_at')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Created At
                                        @if ($activeSort === 'created_at')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alerts as $alert)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>AL-{{ $alert->id }}</td>
                                    <td>
                                        @if ($alert->waterSource)
                                            <div>{{ $alert->waterSource->source_name }}</div>
                                            <div class="small text-muted">{{ $alert->waterSource->source_type }}</div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
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
                                    <td>
                                        @if ($alert->qualityReading)
                                            <a href="{{ route('quality-readings.show', $alert->qualityReading) }}">
                                                QR-{{ $alert->qualityReading->id }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
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