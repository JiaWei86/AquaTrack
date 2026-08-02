@extends('layouts.app')

@section('title', 'Quality Reading Details')
@section('page-title', 'Quality Reading Details')
@section('page-subtitle', 'View recorded water quality information')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('quality-readings.index') }}" class="btn btn-outline-secondary">Back</a>
        <a href="{{ route('quality-readings.edit', $qualityReading) }}" class="btn btn-primary">Edit</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Reading Summary</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">ID</dt>
                        <dd class="col-sm-7">{{ $qualityReading->id }}</dd>

                        <dt class="col-sm-5">Water Source</dt>
                        <dd class="col-sm-7">{{ optional($qualityReading->waterSource)->source_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Inspector</dt>
                        <dd class="col-sm-7">{{ optional($qualityReading->inspector)->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">{{ $qualityReading->status ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Classification</dt>
                        <dd class="col-sm-7">{{ $qualityReading->classification ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">Measurements</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">pH</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->ph }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Temperature</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->temperature }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Turbidity</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->turbidity }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Bacteria Count</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->bacteria_count }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Dissolved Oxygen</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->dissolved_oxygen }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Conductivity</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->conductivity }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">WQI</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->wqi ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">Remarks</div>
                <div class="card-body">
                    <p class="mb-0">{{ $qualityReading->remarks ?? 'No remarks provided.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
