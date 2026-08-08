@extends('layouts.app')

@section('title', $waterSource->source_name)
@section('page-title', $waterSource->source_name)
@section('page-subtitle', $waterSource->source_type)

@section('content')
<div class="container py-4">
    <div class="card mb-4">
        <div class="card-header card-header-aqua">Details</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Type</dt>
                <dd class="col-sm-9"><span class="badge badge-aqua">{{ $waterSource->source_type }}</span></dd>

                <dt class="col-sm-3">Location</dt>
                <dd class="col-sm-9">{{ $waterSource->location }}</dd>

                <dt class="col-sm-3">Coordinates</dt>
                <dd class="col-sm-9">{{ $waterSource->latitude }}, {{ $waterSource->longitude }}</dd>

                <dt class="col-sm-3">Notes</dt>
                <dd class="col-sm-9">{{ $waterSource->notes ?: '—' }}</dd>

                <dt class="col-sm-3">Added</dt>
                <dd class="col-sm-9">{{ $waterSource->created_at->format('d M Y, H:i') }}</dd>
            </dl>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($summary as $stat)
            <div class="col-6 col-md">
                <a href="{{ $stat['link'] }}" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <h3 class="mb-0">{{ $stat['value'] }}</h3>
                            <small class="text-muted">{{ $stat['label'] }}</small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <div class="card mb-4">
        <div class="card-header card-header-aqua">
            Complaint Statistics
            <small class="fw-normal">(from Complaint Management)</small>
        </div>
        <div class="card-body">
            @if ($complaintStatisticsError)
                <div class="alert alert-warning mb-0" role="alert">
                    {{ $complaintStatisticsError }}
                </div>
            @elseif ($complaintStatistics)
                <div class="row g-3 text-center">
                    <div class="col-6 col-md">
                        <h4 class="mb-0">{{ $complaintStatistics['totalComplaints'] ?? 0 }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-6 col-md">
                        <h4 class="mb-0">{{ $complaintStatistics['pending'] ?? 0 }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-6 col-md">
                        <h4 class="mb-0">{{ $complaintStatistics['investigating'] ?? 0 }}</h4>
                        <small class="text-muted">Investigating</small>
                    </div>
                    <div class="col-6 col-md">
                        <h4 class="mb-0">{{ $complaintStatistics['resolved'] ?? 0 }}</h4>
                        <small class="text-muted">Resolved</small>
                    </div>
                    <div class="col-6 col-md">
                        <h4 class="mb-0">{{ $complaintStatistics['rejected'] ?? 0 }}</h4>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('water-sources.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
    @if (Auth::user()->isAdministrator())
        <a href="{{ route('water-sources.edit', $waterSource) }}" class="btn btn-primary">Edit</a>
    @endif
</div>
@endsection
