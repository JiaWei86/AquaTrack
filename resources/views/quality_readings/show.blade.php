@extends('layouts.app')

@section('title', 'Quality Reading Details')
@section('page-title', 'Quality Reading Details')
@section('page-subtitle', 'View recorded water quality information')

@section('content')
@php
    $sourceType = optional($qualityReading->waterSource)->source_type;
    $usesDoeWqi = in_array($sourceType, ['River', 'Lake', 'Reservoir'], true);
    $usesGroundwaterWqi = $sourceType === 'Well';
    $usesComplianceAssessment = $sourceType === 'Community Tap';
    $canManageQualityReadings = auth()->user() && (auth()->user()->isAdministrator() || auth()->user()->isInspector());
@endphp

<div class="container">
    <div class="d-flex justify-content-end gap-2 mb-3">
        <a href="{{ route('quality-readings.index') }}" class="btn btn-outline-secondary">Back</a>
        @if ($canManageQualityReadings)
            <a href="{{ route('quality-readings.edit', $qualityReading) }}" class="btn btn-primary">Edit</a>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">Reading Summary</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">ID</dt>
                        <dd class="col-sm-7">QR-{{ $qualityReading->id }}</dd>

                        <dt class="col-sm-5">Water Source</dt>
                        <dd class="col-sm-7">{{ optional($qualityReading->waterSource)->source_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Source Type</dt>
                        <dd class="col-sm-7">{{ $sourceType ?? 'N/A' }}</dd>

                        @if ($canManageQualityReadings)
                            <dt class="col-sm-5">Inspector</dt>
                            <dd class="col-sm-7">{{ optional($qualityReading->inspector)->name ?? 'N/A' }}</dd>
                        @endif

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
                        @if ($usesDoeWqi)
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">pH</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->ph ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Temperature</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->temperature ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Dissolved Oxygen</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->dissolved_oxygen ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">BOD</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->bod ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">COD</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->cod ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Suspended Solids</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->suspended_solids ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Ammoniacal Nitrogen</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->ammoniacal_nitrogen ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">WQI</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->wqi ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @elseif ($usesGroundwaterWqi)
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">pH</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->ph ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">TDS</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->tds ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Hardness</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->hardness ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Chloride</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->chloride ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Sulphate</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->sulphate ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Nitrate</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->nitrate ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Iron</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->iron ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Manganese</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->manganese ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">WQI</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->wqi ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @elseif ($usesComplianceAssessment)
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">pH</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->ph ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Turbidity</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->turbidity ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Colour</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->colour ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Residual Chlorine</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->residual_chlorine ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Aluminium</div>
                                    <div class="fs-5 fw-semibold">{{ $qualityReading->aluminium ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Total Coliform</div>
                                    <div class="fs-5 fw-semibold">
                                        @if ($qualityReading->total_coliform === null)
                                            N/A
                                        @elseif ($qualityReading->total_coliform)
                                            Detected
                                        @else
                                            Not Detected
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">E.coli</div>
                                    <div class="fs-5 fw-semibold">
                                        @if ($qualityReading->e_coli === null)
                                            N/A
                                        @elseif ($qualityReading->e_coli)
                                            Detected
                                        @else
                                            Not Detected
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12">
                                <p class="text-muted mb-0">No measurement fields are available for this water source type.</p>
                            </div>
                        @endif

                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Classification</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->classification ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Status</div>
                                <div class="fs-5 fw-semibold">{{ $qualityReading->status ?? 'N/A' }}</div>
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
