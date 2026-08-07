@extends('layouts.app')

@section('title', 'Alert Details')
@section('page-title', 'Alert Details')
@section('page-subtitle', 'View water quality alert information')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('alerts.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Alert Summary</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Alert ID</dt>
                        <dd class="col-sm-7">{{ $alert->id }}</dd>

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
                        <dd class="col-sm-8">{{ optional($alert->qualityReading)->id ?? $alert->quality_reading_id ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Created At</dt>
                        <dd class="col-sm-8">{{ $alert->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Updated At</dt>
                        <dd class="col-sm-8">{{ $alert->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
