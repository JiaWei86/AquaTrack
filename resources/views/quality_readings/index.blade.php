@extends('layouts.app')

@section('title', 'Quality Readings')
@section('page-title', 'Water Quality Readings')
@section('page-subtitle', 'Review recorded water quality measurements')

@section('content')
@php
    $canManageQualityReadings = auth()->user() && (auth()->user()->isAdministrator() || auth()->user()->isInspector());
    $isAdministrator = auth()->user() && auth()->user()->isAdministrator();
    $nextSortDirection = fn (string $column) => ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
@endphp
<div class="container">
    <div class="d-flex justify-content-end align-items-center mb-3">
        @if ($canManageQualityReadings)
            <a href="{{ route('quality-readings.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Reading
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($qualityReadings->isEmpty())
                <p class="text-muted mb-0">No quality readings have been recorded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'id', 'direction' => $nextSortDirection('id')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Reading ID
                                        @if ($activeSort === 'id')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'water_source', 'direction' => $nextSortDirection('water_source')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Water Source
                                        @if ($activeSort === 'water_source')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'source_type', 'direction' => $nextSortDirection('source_type')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Source Type
                                        @if ($activeSort === 'source_type')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                @if ($canManageQualityReadings)
                                    <th class="text-nowrap">
                                        <a href="{{ route('quality-readings.index', ['sort' => 'inspector', 'direction' => $nextSortDirection('inspector')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                            Inspector
                                            @if ($activeSort === 'inspector')
                                                <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="bi bi-arrow-down-up text-muted small"></i>
                                            @endif
                                        </a>
                                    </th>
                                @endif
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'result', 'direction' => $nextSortDirection('result')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Result
                                        @if ($activeSort === 'result')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'classification', 'direction' => $nextSortDirection('classification')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Classification
                                        @if ($activeSort === 'classification')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'status', 'direction' => $nextSortDirection('status')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Risk Level
                                        @if ($activeSort === 'status')
                                            <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a href="{{ route('quality-readings.index', ['sort' => 'created_at', 'direction' => $nextSortDirection('created_at')]) }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                        Recorded At
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
                            @foreach ($qualityReadings as $qualityReading)
                                @php
                                    $sourceType = optional($qualityReading->waterSource)->source_type;
                                    $usesWqi = in_array($sourceType, ['River', 'Lake', 'Reservoir', 'Well'], true);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>QR-{{ $qualityReading->id }}</td>
                                    <td>{{ optional($qualityReading->waterSource)->source_name ?? 'N/A' }}</td>
                                    <td>{{ $sourceType ?? 'N/A' }}</td>
                                    @if ($canManageQualityReadings)
                                        <td>{{ optional($qualityReading->inspector)->name ?? 'N/A' }}</td>
                                    @endif
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            @if ($usesWqi)
                                                WQI:
                                                {{ $qualityReading->wqi !== null ? number_format((float) $qualityReading->wqi, 2) : 'N/A' }}
                                            @elseif ($sourceType === 'Community Tap')
                                                @if ($qualityReading->compliance_percentage !== null)
                                                    Compliance: {{ number_format((float) $qualityReading->compliance_percentage, 2) }}%
                                                @else
                                                    {{ $qualityReading->classification ?? 'N/A' }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if ($qualityReading->classification)
                                            <span @class([
                                                'badge',
                                                'bg-success' => $qualityReading->classification === 'Clean',
                                                'bg-warning text-dark' => $qualityReading->classification === 'Slightly Polluted',
                                                'bg-danger' => in_array($qualityReading->classification, ['Polluted', 'Non-Compliant'], true),
                                            ])>
                                                {{ $qualityReading->classification }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($qualityReading->status)
                                            <span @class([
                                                'badge fw-semibold px-2 py-1',
                                                'bg-success' => $qualityReading->status === 'Safe',
                                                'bg-warning text-dark' => $qualityReading->status === 'Warning',
                                                'bg-danger' => $qualityReading->status === 'Critical',
                                            ])>
                                                {{ $qualityReading->status }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $qualityReading->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('quality-readings.show', $qualityReading) }}"
                                               class="btn btn-sm btn-outline-primary">View</a>
                                            @if ($canManageQualityReadings)
                                                <a href="{{ route('quality-readings.edit', $qualityReading) }}"
                                                   class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @endif
                                            @if ($isAdministrator)
                                                <form action="{{ route('quality-readings.destroy', $qualityReading) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this quality reading?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
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