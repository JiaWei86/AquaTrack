@extends('layouts.app')

@section('title', 'Submit Complaint')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Submit a Complaint</h2>

    {{-- Display validation errors returned by StoreComplaintRequest --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Notice: complaints are immutable once submitted --}}
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            Please review your complaint carefully before submitting.
            <strong>Once submitted, it cannot be edited</strong> — you may only
            delete it while it is still Pending.
        </div>
    </div>

    {{-- enctype="multipart/form-data" is required for file upload.
         onsubmit confirmation prevents accidental submission. --}}
    <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data"
          onsubmit="return confirm('Submit this complaint? It cannot be edited afterwards.');">
        @csrf {{-- CSRF protection: Laravel rejects the request without this token --}}

        <div class="mb-3">
            <label for="water_source_id" class="form-label">Water Source</label>
            <select name="water_source_id" id="water_source_id" class="form-select" required>
                <option value="">-- Select a water source --</option>
                @foreach ($waterSources as $source)
                    <option value="{{ $source->id }}"
                        {{ old('water_source_id') == $source->id ? 'selected' : '' }}>
                        {{ $source->source_name }} ({{ $source->location }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title') }}" maxlength="255" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control"
                      rows="5" maxlength="2000" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo (optional, JPG/PNG, max 2MB)</label>
            <input type="file" name="photo" id="photo" class="form-control"
                   accept=".jpg,.jpeg,.png">
            {{-- Note: the accept attribute is only a UX hint;
                 real File Upload Validation is enforced server-side in StoreComplaintRequest --}}
        </div>

        <button type="submit" class="btn btn-primary">Submit Complaint</button>
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection