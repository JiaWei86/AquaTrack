@php $source = $waterSource ?? null; @endphp

<div class="mb-3">
    <label for="source_name" class="form-label">Source Name</label>
    <input type="text" name="source_name" id="source_name"
           class="form-control @error('source_name') is-invalid @enderror"
           value="{{ old('source_name', $source->source_name ?? '') }}">
    @error('source_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="source_type" class="form-label">Source Type</label>
    <select name="source_type" id="source_type"
            class="form-select @error('source_type') is-invalid @enderror">
        <option value="">-- Select Type --</option>
        @foreach (['River', 'Lake', 'Reservoir', 'Well', 'Community Tap'] as $type)
            <option value="{{ $type }}" @selected(old('source_type', $source->source_type ?? '') === $type)>
                {{ $type }}
            </option>
        @endforeach
    </select>
    @error('source_type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="location" class="form-label">Location</label>
    <input type="text" name="location" id="location"
           class="form-control @error('location') is-invalid @enderror"
           value="{{ old('location', $source->location ?? '') }}">
    @error('location')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="latitude" class="form-label">Latitude</label>
        <input type="number" step="any" name="latitude" id="latitude"
               class="form-control @error('latitude') is-invalid @enderror"
               value="{{ old('latitude', $source->latitude ?? '') }}">
        @error('latitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="longitude" class="form-label">Longitude</label>
        <input type="number" step="any" name="longitude" id="longitude"
               class="form-control @error('longitude') is-invalid @enderror"
               value="{{ old('longitude', $source->longitude ?? '') }}">
        @error('longitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea name="notes" id="notes" rows="3"
              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $source->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>