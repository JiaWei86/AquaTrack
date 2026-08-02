@extends('layouts.app')

@section('title', 'Edit Quality Reading')
@section('page-title', 'Edit Quality Reading')
@section('page-subtitle', 'Update water quality measurements')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="#" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="inspector_id" class="form-label">Inspector</label>
                        <select id="inspector_id" name="inspector_id" class="form-select">
                            <option value="">Select inspector</option>
                            @foreach ($inspectors as $inspector)
                                <option value="{{ $inspector->id }}" @selected($qualityReading->inspector_id === $inspector->id)>
                                    {{ $inspector->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="water_source_id" class="form-label">Water Source</label>
                        <select id="water_source_id" name="water_source_id" class="form-select">
                            <option value="">Select water source</option>
                            @foreach ($waterSources as $waterSource)
                                <option value="{{ $waterSource->id }}" @selected($qualityReading->water_source_id === $waterSource->id)>
                                    {{ $waterSource->source_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="ph" class="form-label">pH</label>
                        <input type="number" step="0.01" id="ph" name="ph" class="form-control" value="{{ $qualityReading->ph }}">
                    </div>

                    <div class="col-md-4">
                        <label for="temperature" class="form-label">Temperature</label>
                        <input type="number" step="0.01" id="temperature" name="temperature" class="form-control" value="{{ $qualityReading->temperature }}">
                    </div>

                    <div class="col-md-4">
                        <label for="turbidity" class="form-label">Turbidity</label>
                        <input type="number" step="0.01" id="turbidity" name="turbidity" class="form-control" value="{{ $qualityReading->turbidity }}">
                    </div>

                    <div class="col-md-4">
                        <label for="bacteria_count" class="form-label">Bacteria Count</label>
                        <input type="number" id="bacteria_count" name="bacteria_count" class="form-control" value="{{ $qualityReading->bacteria_count }}">
                    </div>

                    <div class="col-md-4">
                        <label for="dissolved_oxygen" class="form-label">Dissolved Oxygen</label>
                        <input type="number" step="0.01" id="dissolved_oxygen" name="dissolved_oxygen" class="form-control" value="{{ $qualityReading->dissolved_oxygen }}">
                    </div>

                    <div class="col-md-4">
                        <label for="conductivity" class="form-label">Conductivity</label>
                        <input type="number" step="0.01" id="conductivity" name="conductivity" class="form-control" value="{{ $qualityReading->conductivity }}">
                    </div>

                    <div class="col-12">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="4">{{ $qualityReading->remarks }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('quality-readings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="button" class="btn btn-primary" disabled>Update Reading</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
