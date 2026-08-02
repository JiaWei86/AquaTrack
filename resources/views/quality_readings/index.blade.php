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
                                <th>Inspector</th>
                                <th>pH</th>
                                <th>Temperature</th>
                                <th>Turbidity</th>
                                <th>WQI</th>
                                <th>Classification</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qualityReadings as $qualityReading)
                                <tr>
                                    <td>{{ $qualityReading->id }}</td>
                                    <td>{{ optional($qualityReading->waterSource)->source_name ?? 'N/A' }}</td>
                                    <td>{{ optional($qualityReading->inspector)->name ?? 'N/A' }}</td>
                                    <td>{{ $qualityReading->ph }}</td>
                                    <td>{{ $qualityReading->temperature }}</td>
                                    <td>{{ $qualityReading->turbidity }}</td>
                                    <td>{{ $qualityReading->wqi ?? 'N/A' }}</td>
                                    <td>{{ $qualityReading->classification ?? 'N/A' }}</td>
                                    <td>{{ $qualityReading->status ?? 'N/A' }}</td>
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
