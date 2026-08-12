@extends('layouts.app')

@section('title', 'Edit Quality Reading')
@section('page-title', 'Edit Quality Reading')
@section('page-subtitle', 'Update water quality measurements')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('quality-readings.update', $qualityReading) }}" method="POST">
                @csrf
                @method('PUT')
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <p class="mb-2">Please correct the following errors:</p>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="inspector_id" class="form-label">Inspector</label>
                        @if (auth()->user()->isInspector())
                            <input type="text" id="inspector_id" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            <input type="hidden" name="inspector_id" value="{{ auth()->id() }}">
                        @else
                            <select id="inspector_id" name="inspector_id" class="form-select @error('inspector_id') is-invalid @enderror">
                                @foreach ($inspectors as $inspector)
                                    <option value="{{ $inspector->id }}" @selected(old('inspector_id', $qualityReading->inspector_id) == $inspector->id)>
                                        {{ $inspector->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('inspector_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="water_source_id" class="form-label">Water Source</label>
                        @if (auth()->user()->isInspector())
                            <input type="text" id="water_source_id" class="form-control"
                                   value="{{ optional($qualityReading->waterSource)->source_name }} ({{ optional($qualityReading->waterSource)->source_type }})"
                                   data-source-type="{{ strtolower(optional($qualityReading->waterSource)->source_type ?? '') }}"
                                   readonly disabled>
                            <input type="hidden" name="water_source_id" value="{{ $qualityReading->water_source_id }}"
                                   data-source-type="{{ strtolower(optional($qualityReading->waterSource)->source_type ?? '') }}">
                        @else
                            <select id="water_source_id" name="water_source_id" class="form-select @error('water_source_id') is-invalid @enderror">
                                @foreach ($waterSources as $waterSource)
                                    <option
                                        value="{{ $waterSource->id }}"
                                        data-source-type="{{ strtolower($waterSource->source_type) }}"
                                        @selected(old('water_source_id', $qualityReading->water_source_id) == $waterSource->id)>
                                        {{ $waterSource->source_name }} ({{ $waterSource->source_type }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('water_source_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-none" id="river-fields">
                        <h3 class="h6 mb-3">River / Lake Parameters</h3>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="river_ph" class="form-label">pH</label>
                                <input type="number" step="0.01" id="river_ph" name="ph" class="form-control @error('ph') is-invalid @enderror" value="{{ old('ph', $qualityReading->ph) }}">
                                @error('ph')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="temperature" class="form-label">Temperature</label>
                                <input type="number" step="0.01" id="temperature" name="temperature" class="form-control @error('temperature') is-invalid @enderror" value="{{ old('temperature', $qualityReading->temperature) }}">
                                @error('temperature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="dissolved_oxygen" class="form-label">Dissolved Oxygen</label>
                                <input type="number" step="0.01" id="dissolved_oxygen" name="dissolved_oxygen" class="form-control @error('dissolved_oxygen') is-invalid @enderror" value="{{ old('dissolved_oxygen', $qualityReading->dissolved_oxygen) }}">
                                @error('dissolved_oxygen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="bod" class="form-label">BOD</label>
                                <input type="number" step="0.01" id="bod" name="bod" class="form-control @error('bod') is-invalid @enderror" value="{{ old('bod', $qualityReading->bod) }}">
                                @error('bod')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="cod" class="form-label">COD</label>
                                <input type="number" step="0.01" id="cod" name="cod" class="form-control @error('cod') is-invalid @enderror" value="{{ old('cod', $qualityReading->cod) }}">
                                @error('cod')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="suspended_solids" class="form-label">Suspended Solids</label>
                                <input type="number" step="0.01" id="suspended_solids" name="suspended_solids" class="form-control @error('suspended_solids') is-invalid @enderror" value="{{ old('suspended_solids', $qualityReading->suspended_solids) }}">
                                @error('suspended_solids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="ammoniacal_nitrogen" class="form-label">Ammoniacal Nitrogen</label>
                                <input type="number" step="0.01" id="ammoniacal_nitrogen" name="ammoniacal_nitrogen" class="form-control @error('ammoniacal_nitrogen') is-invalid @enderror" value="{{ old('ammoniacal_nitrogen', $qualityReading->ammoniacal_nitrogen) }}">
                                @error('ammoniacal_nitrogen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="well-fields">
                        <h3 class="h6 mb-3">Well Parameters</h3>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="well_ph" class="form-label">pH</label>
                                <input type="number" step="0.01" id="well_ph" name="ph" class="form-control @error('ph') is-invalid @enderror" value="{{ old('ph', $qualityReading->ph) }}">
                                @error('ph')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="tds" class="form-label">TDS</label>
                                <input type="number" step="0.01" id="tds" name="tds" class="form-control @error('tds') is-invalid @enderror" value="{{ old('tds', $qualityReading->tds) }}">
                                @error('tds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="hardness" class="form-label">Hardness</label>
                                <input type="number" step="0.01" id="hardness" name="hardness" class="form-control @error('hardness') is-invalid @enderror" value="{{ old('hardness', $qualityReading->hardness) }}">
                                @error('hardness')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="chloride" class="form-label">Chloride</label>
                                <input type="number" step="0.01" id="chloride" name="chloride" class="form-control @error('chloride') is-invalid @enderror" value="{{ old('chloride', $qualityReading->chloride) }}">
                                @error('chloride')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="sulphate" class="form-label">Sulphate</label>
                                <input type="number" step="0.01" id="sulphate" name="sulphate" class="form-control @error('sulphate') is-invalid @enderror" value="{{ old('sulphate', $qualityReading->sulphate) }}">
                                @error('sulphate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="nitrate" class="form-label">Nitrate</label>
                                <input type="number" step="0.01" id="nitrate" name="nitrate" class="form-control @error('nitrate') is-invalid @enderror" value="{{ old('nitrate', $qualityReading->nitrate) }}">
                                @error('nitrate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="iron" class="form-label">Iron</label>
                                <input type="number" step="0.01" id="iron" name="iron" class="form-control @error('iron') is-invalid @enderror" value="{{ old('iron', $qualityReading->iron) }}">
                                @error('iron')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="manganese" class="form-label">Manganese</label>
                                <input type="number" step="0.01" id="manganese" name="manganese" class="form-control @error('manganese') is-invalid @enderror" value="{{ old('manganese', $qualityReading->manganese) }}">
                                @error('manganese')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="community-fields">
                        <h3 class="h6 mb-3">Community Tap Parameters</h3>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="community_ph" class="form-label">pH</label>
                                <input type="number" step="0.01" id="community_ph" name="ph" class="form-control @error('ph') is-invalid @enderror" value="{{ old('ph', $qualityReading->ph) }}">
                                @error('ph')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="turbidity" class="form-label">Turbidity</label>
                                <input type="number" step="0.01" id="turbidity" name="turbidity" class="form-control @error('turbidity') is-invalid @enderror" value="{{ old('turbidity', $qualityReading->turbidity) }}">
                                @error('turbidity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="colour" class="form-label">Colour</label>
                                <input type="number" step="0.01" id="colour" name="colour" class="form-control @error('colour') is-invalid @enderror" value="{{ old('colour', $qualityReading->colour) }}">
                                @error('colour')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="residual_chlorine" class="form-label">Residual Chlorine</label>
                                <input type="number" step="0.01" id="residual_chlorine" name="residual_chlorine" class="form-control @error('residual_chlorine') is-invalid @enderror" value="{{ old('residual_chlorine', $qualityReading->residual_chlorine) }}">
                                @error('residual_chlorine')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="aluminium" class="form-label">Aluminium</label>
                                <input type="number" step="0.01" id="aluminium" name="aluminium" class="form-control @error('aluminium') is-invalid @enderror" value="{{ old('aluminium', $qualityReading->aluminium) }}">
                                @error('aluminium')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="total_coliform" class="form-label">Total Coliform</label>
                                <select id="total_coliform" name="total_coliform" class="form-select @error('total_coliform') is-invalid @enderror">
                                    <option value="0" @selected((string) old('total_coliform', (int) $qualityReading->total_coliform) === '0')>Not Detected</option>
                                    <option value="1" @selected((string) old('total_coliform', (int) $qualityReading->total_coliform) === '1')>Detected</option>
                                </select>
                                @error('total_coliform')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="e_coli" class="form-label">E.coli</label>
                                <select id="e_coli" name="e_coli" class="form-select @error('e_coli') is-invalid @enderror">
                                    <option value="0" @selected((string) old('e_coli', (int) $qualityReading->e_coli) === '0')>Not Detected</option>
                                    <option value="1" @selected((string) old('e_coli', (int) $qualityReading->e_coli) === '1')>Detected</option>
                                </select>
                                @error('e_coli')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea id="remarks" name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="4">{{ old('remarks', $qualityReading->remarks) }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('quality-readings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Reading</button>
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
            const sourceType = waterSourceSelect.tagName === 'SELECT'
                ? waterSourceSelect.options[waterSourceSelect.selectedIndex].dataset.sourceType
                : waterSourceSelect.dataset.sourceType;
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
