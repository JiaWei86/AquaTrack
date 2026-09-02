@extends('layouts.app')

@section('title', 'Alert Details')
@section('page-title', 'Alert Details')
@section('page-subtitle', 'View water quality alert information')

@section('content')
@php
    $canResolveAlerts = auth()->user()
        && (auth()->user()->isAdministrator() || auth()->user()->isInspector());
@endphp
<div class="container">
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
        <a href="{{ route('alerts.index') }}" class="btn btn-outline-secondary">Back</a>

        @if ($alert->status === 'Active' && $canResolveAlerts)
            <form action="{{ route('alerts.update', $alert) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">Resolve</button>
            </form>
        @elseif ($alert->status === 'Resolved')
            <span class="badge bg-success">Resolved</span>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Alert Summary</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Alert ID</dt>
                        <dd class="col-sm-7">AL-{{ $alert->id }}</dd>

                        <dt class="col-sm-5">Water Source</dt>
                        <dd class="col-sm-7">{{ optional($alert->waterSource)->source_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Severity</dt>
                        <dd class="col-sm-7">{{ $alert->severity ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">{{ $alert->status ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">Alert Information</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Message</dt>
                        <dd class="col-sm-8">{{ $alert->message ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Triggered Reading ID</dt>
                        @php $triggeredReadingId = optional($alert->qualityReading)->id ?? $alert->quality_reading_id; @endphp
                        <dd class="col-sm-8">{{ $triggeredReadingId !== null ? 'QR-' . $triggeredReadingId : 'N/A' }}</dd>

                        <dt class="col-sm-4">Created At</dt>
                        <dd class="col-sm-8">{{ $alert->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Updated At</dt>
                        <dd class="col-sm-8">{{ $alert->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    @php
        $waterSource = $alert->waterSource;
        $hasCoordinates = $waterSource && $waterSource->latitude !== null && $waterSource->longitude !== null;
    @endphp

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Water Source Location</div>
                <div class="card-body">
                    @if ($hasCoordinates)
                        <div id="water-source-map"
                             class="rounded"
                             style="width: 100%; height: 380px;"
                             data-lat="{{ $waterSource->latitude }}"
                             data-lng="{{ $waterSource->longitude }}"
                             data-name="{{ $waterSource->source_name }}"></div>

                        <div class="mt-3">
                            <div class="fw-semibold">{{ $waterSource->source_name }}</div>
                            <div class="text-muted small">
                                Coordinates: {{ number_format((float) $waterSource->latitude, 6) }}, {{ number_format((float) $waterSource->longitude, 6) }}
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Location coordinates are not available for this water source.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if ($hasCoordinates)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('water-source-map');

            if (!mapEl) {
                return;
            }

            const latitude = parseFloat(mapEl.dataset.lat);
            const longitude = parseFloat(mapEl.dataset.lng);

            if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
                return;
            }

            const map = L.map('water-source-map').setView([latitude, longitude], 15);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const popupContent = document.createElement('div');
            popupContent.textContent = mapEl.dataset.name;

            L.marker([latitude, longitude])
                .addTo(map)
                .bindPopup(popupContent)
                .openPopup();
        });
    </script>
@endif
@endsection
