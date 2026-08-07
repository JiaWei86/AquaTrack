@extends('layouts.app')

@section('title', 'Quality Readings')
@section('page-title', 'Water Quality Readings')
@section('page-subtitle', 'Review recorded water quality measurements')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Reading Records</h2>
        <a href="{{ route('quality-readings.create') }}" class="btn btn-primary">
            Add Reading
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($qualityReadings->isEmpty())
                <p class="text-muted mb-0">No quality readings have been recorded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Water Source</th>
                                <th>Source Type</th>
                                <th>Inspector</th>
                                <th>Result</th>
                                <th>Classification</th>
                                <th>Risk Level</th>
                                <th>Recorded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qualityReadings as $qualityReading)
                                @php
                                    $sourceType = optional($qualityReading->waterSource)->source_type;
                                    $usesWqi = in_array($sourceType, ['River', 'Lake', 'Reservoir', 'Well'], true);
                                @endphp
                                <tr>
                                    <td>{{ $qualityReading->id }}</td>
                                    <td>{{ optional($qualityReading->waterSource)->source_name ?? 'N/A' }}</td>
                                    <td>{{ $sourceType ?? 'N/A' }}</td>
                                    <td>{{ optional($qualityReading->inspector)->name ?? 'N/A' }}</td>
                                    <td>
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
                                    </td>
                                    <td>{{ $qualityReading->classification ?? 'N/A' }}</td>
                                    <td>{{ $qualityReading->status ?? 'N/A' }}</td>
                                    <td>{{ $qualityReading->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('quality-readings.show', $qualityReading) }}"
                                               class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="{{ route('quality-readings.edit', $qualityReading) }}"
                                               class="btn btn-sm btn-outline-secondary">Edit</a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                Delete
                                            </button>
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
