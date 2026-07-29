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
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ $waterSource->complaints()->count() }}</h3>
                    <small class="text-muted">Complaints</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ $waterSource->qualityReadings()->count() }}</h3>
                    <small class="text-muted">Quality Readings</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-0">{{ $waterSource->alerts()->count() }}</h3>
                    <small class="text-muted">Alerts</small>
                </div>
            </div>
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