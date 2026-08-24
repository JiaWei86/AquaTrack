@extends('layouts.app')

@section('title', 'Complaint Details')

@section('content')
<div class="container py-4">

    @php
        $stages = ['Pending' => 1, 'Investigating' => 2, 'Resolved' => 3, 'Rejected' => 3];
        $current = $stages[$complaint->status] ?? 1;
        $isRejected = $complaint->status === 'Rejected';
    @endphp

    <a href="{{ route('complaints.index') }}" class="text-decoration-none d-inline-block mb-3">
        <i class="bi bi-arrow-left"></i> Back to complaints
    </a>

    <div class="card complaint-card">
        <div class="card-body p-4">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-chat-left-text" style="color: var(--aqua-main);"></i>
                        {{ $complaint->title }}
                    </h3>
                    <small class="text-muted">Reference No: #{{ $complaint->id }}</small>
                </div>
                <span class="badge {{ $complaint->state()->getBadgeClass() }} fs-6">
                    {{ $complaint->status }}
                </span>
            </div>

            {{-- Status progress --}}
            <div class="status-progress">
                <div class="status-step {{ $current >= 1 ? ($isRejected ? 'rejected' : 'done') : '' }}"></div>
                <div class="status-step {{ $current >= 2 ? ($isRejected ? 'rejected' : 'done') : '' }}"></div>
                <div class="status-step {{ $current >= 3 ? ($isRejected ? 'rejected' : 'done') : '' }}"></div>
            </div>
            <div class="status-labels mb-3">
                <span class="{{ $current >= 1 ? 'active' : '' }}">Submitted</span>
                <span class="{{ $current >= 2 ? 'active' : '' }}">Investigating</span>
                <span class="{{ $current >= 3 ? 'active' : '' }}">{{ $isRejected ? 'Rejected' : 'Resolved' }}</span>
            </div>

            {{-- Status message from the State object --}}
            <div class="alert alert-light border mb-4">
                <i class="bi bi-info-circle" style="color: var(--aqua-main);"></i>
                {{ $complaint->state()->getStatusMessage() }}
            </div>

            {{-- Details grid --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <small class="text-muted d-block"><i class="bi bi-person"></i> Resident</small>
                    <span>{{ $complaint->resident->name }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block"><i class="bi bi-geo-alt"></i> Water Source</small>
                    <span>{{ $complaint->waterSource->source_name }} ({{ $complaint->waterSource->location }})</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block"><i class="bi bi-calendar3"></i> Submitted</small>
                    <span>{{ $complaint->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <small class="text-muted d-block mb-1"><i class="bi bi-card-text"></i> Description</small>
                <p class="mb-0">{{ $complaint->description }}</p>
            </div>

            {{-- ============================================================
                 Web Service (Consumer): live water source details fetched
                 from the Water Source Management module's REST API
                 ============================================================ --}}
            @if ($waterSourceApiData)
                <div class="mb-4 p-3 rounded" style="background-color: var(--aqua-lighter);">
                    <small class="text-muted d-block mb-2">
                        <i class="bi bi-hdd-network"></i> Water Source Details
                        <span class="badge bg-secondary">via Web Service API</span>
                    </small>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <small class="text-muted">Type</small><br>
                            {{ $waterSourceApiData['source_type'] }}
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Location</small><br>
                            {{ $waterSourceApiData['location'] }}
                        </div>
                        <div class="col-md-5">
                            <small class="text-muted">Coordinates</small><br>
                            {{ $waterSourceApiData['latitude'] }}, {{ $waterSourceApiData['longitude'] }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Photo --}}
            @if ($complaint->photo)
                <div>
                    <small class="text-muted d-block mb-2"><i class="bi bi-image"></i> Photo Evidence</small>
                    <img src="{{ asset('storage/' . $complaint->photo) }}"
                         alt="Complaint photo" class="img-fluid rounded"
                         style="max-width: 400px; border: 1px solid #e3e8ee;">
                </div>
            @endif

        </div>
    </div>

    {{-- Admin action --}}
    @if (Auth::user()->isAdministrator())
        <div class="mt-3">
            <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Update Status
            </a>
        </div>
    @endif

</div>
@endsection