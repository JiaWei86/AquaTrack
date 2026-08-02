@extends('layouts.app')

@section('title', 'Create Quality Reading')
@section('page-title', 'Create Quality Reading')
@section('page-subtitle', 'Record water quality measurements')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('quality-readings.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="inspector_id" class="form-label">Inspector</label>
                        <select id="inspector_id" name="inspector_id" class="form-select">
                            <option value="">Select inspector</option>
                            @foreach ($inspectors as $inspector)
                                <option value="{{ $inspector->id }}">{{ $inspector->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="water_source_id" class="form-label">Water Source</label>
                        <select id="water_source_id" name="water_source_id" class="form-select">
                            <option value="">Select water source</option>
                            @foreach ($waterSources as $waterSource)
                                <option
                                    value="{{ $waterSource->id }}"
                                    data-source-type="{{ strtolower($waterSource->source_type) }}">
                                    {{ $waterSource->source_name }} ({{ $waterSource->source_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 d-none" id="river-fields">
                        <h3 class="h6 mb-3">River / Lake Parameters</h3>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="river_ph" class="form-label">pH</label>
                                <input type="number" step="0.01" id="river_ph" name="ph" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="temperature" class="form-label">Temperature</label>
                                <input type="number" step="0.01" id="temperature" name="temperature" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="dissolved_oxygen" class="form-label">Dissolved Oxygen</label>
                                <input type="number" step="0.01" id="dissolved_oxygen" name="dissolved_oxygen" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="bod" class="form-label">BOD</label>
                                <input type="number" step="0.01" id="bod" name="bod" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="cod" class="form-label">COD</label>
                                <input type="number" step="0.01" id="cod" name="cod" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="suspended_solids" class="form-label">Suspended Solids</label>
                                <input type="number" step="0.01" id="suspended_solids" name="suspended_solids" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="ammoniacal_nitrogen" class="form-label">Ammoniacal Nitrogen</label>
                                <input type="number" step="0.01" id="ammoniacal_nitrogen" name="ammoniacal_nitrogen" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="well-fields">
                        <h3 class="h6 mb-3">Well Parameters</h3>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="well_ph" class="form-label">pH</label>
                                <input type="number" step="0.01" id="well_ph" name="ph" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="tds" class="form-label">TDS</label>
                                <input type="number" step="0.01" id="tds" name="tds" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="hardness" class="form-label">Hardness</label>
                                <input type="number" step="0.01" id="hardness" name="hardness" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="chloride" class="form-label">Chloride</label>
                                <input type="number" step="0.01" id="chloride" name="chloride" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="sulphate" class="form-label">Sulphate</label>
                                <input type="number" step="0.01" id="sulphate" name="sulphate" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="nitrate" class="form-label">Nitrate</label>
                                <input type="number" step="0.01" id="nitrate" name="nitrate" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="iron" class="form-label">Iron</label>
                                <input type="number" step="0.01" id="iron" name="iron" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="manganese" class="form-label">Manganese</label>
                                <input type="number" step="0.01" id="manganese" name="manganese" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="community-fields">
                        <h3 class="h6 mb-3">Community Tap Parameters</h3>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="community_ph" class="form-label">pH</label>
                                <input type="number" step="0.01" id="community_ph" name="ph" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="turbidity" class="form-label">Turbidity</label>
                                <input type="number" step="0.01" id="turbidity" name="turbidity" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="colour" class="form-label">Colour</label>
                                <input type="number" step="0.01" id="colour" name="colour" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="residual_chlorine" class="form-label">Residual Chlorine</label>
                                <input type="number" step="0.01" id="residual_chlorine" name="residual_chlorine" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="aluminium" class="form-label">Aluminium</label>
                                <input type="number" step="0.01" id="aluminium" name="aluminium" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="total_coliform" class="form-label">Total Coliform</label>
                                <select id="total_coliform" name="total_coliform" class="form-select">
                                    <option value="0">Not Detected</option>
                                    <option value="1">Detected</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="e_coli" class="form-label">E.coli</label>
                                <select id="e_coli" name="e_coli" class="form-select">
                                    <option value="0">Not Detected</option>
                                    <option value="1">Detected</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="4"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('quality-readings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        Save Reading
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const waterSourceSelect = document.getElementById('water_source_id');
        const riverFields = document.getElementById('river-fields');
        const wellFields = document.getElementById('well-fields');
        const communityFields = document.getElementById('community-fields');
        const fieldGroups = [riverFields, wellFields, communityFields];

        function setFieldGroupState(fieldGroup, isActive) {
            fieldGroup.classList.toggle('d-none', !isActive);

            fieldGroup
                .querySelectorAll('input, select, textarea')
                .forEach(function (field) {
                    field.disabled = !isActive;
                });
        }

        function toggleFieldGroups() {
            const selectedOption = waterSourceSelect.options[waterSourceSelect.selectedIndex];
            const sourceType = selectedOption.dataset.sourceType;
            let activeGroup = null;

            if (
                sourceType === 'river' ||
                sourceType === 'lake' ||
                sourceType === 'reservoir'
            ) {
                activeGroup = riverFields;
            }

            if (sourceType === 'well') {
                activeGroup = wellFields;
            }

            if (sourceType === 'community tap') {
                activeGroup = communityFields;
            }

            fieldGroups.forEach(function (fieldGroup) {
                setFieldGroupState(fieldGroup, fieldGroup === activeGroup);
            });
        }

        waterSourceSelect.addEventListener('change', toggleFieldGroups);
        toggleFieldGroups();
    });
</script>
@endsection
