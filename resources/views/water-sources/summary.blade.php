@extends('layouts.app')

@php
    $typeLabels = [
        'complaints'       => 'Complaints',
        'quality-readings' => 'Quality Readings',
        'alerts'           => 'Alerts',
    ];
    $typeLabel = $typeLabels[$type] ?? ucfirst($type);
@endphp

@section('title', $waterSource->source_name . ' — ' . $typeLabel)
@section('page-title', $typeLabel)
@section('page-subtitle', $waterSource->source_name)

@section('content')
<div class="container py-4">
    <div class="card mb-4">
        <div class="card-header card-header-aqua">{{ $waterSource->source_name }}</div>
        <div class="card-body">
            <p class="mb-0 text-muted">{{ $waterSource->source_type }} &middot; {{ $waterSource->location }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header card-header-aqua">{{ $typeLabel }}</div>
        <div class="card-body">
            @if ($items->isEmpty())
                <p class="text-muted mb-0">No {{ strtolower($typeLabel) }} recorded for this water source yet.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($items as $item)
                        <li class="list-group-item">
                            @if ($type === 'complaints')
                                <a href="{{ route('complaints.show', $item) }}">{{ $item->title }}</a>
                                <small class="text-muted d-block">
                                    {{ $item->created_at->format('d M Y, H:i') }} &middot; {{ $item->status }}
                                </small>
                            @elseif ($type === 'quality-readings')
                                <a href="{{ route('quality-readings.show', $item) }}">
                                    Reading — {{ $item->sample_date?->format('d M Y') ?? $item->created_at->format('d M Y') }}
                                </a>
                                <small class="text-muted d-block">Status: {{ $item->status }}</small>
                            @else
                                <a href="{{ route('alerts.show', $item) }}">{{ $item->message }}</a>
                                <small class="text-muted d-block">
                                    {{ $item->created_at->format('d M Y, H:i') }} &middot;
                                    Severity: {{ $item->severity }} &middot; Status: {{ $item->status }}
                                </small>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <a href="{{ route('water-sources.show', $waterSource) }}" class="btn btn-outline-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Back to {{ $waterSource->source_name }}
    </a>
</div>
@endsection