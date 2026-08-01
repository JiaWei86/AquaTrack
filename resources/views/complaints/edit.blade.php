@extends('layouts.app')

@section('title', 'Update Complaint Status')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Update Complaint Status</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $complaint->title }}</h5>
            <p class="text-muted mb-1">
                Submitted by {{ $complaint->resident->name }} ·
                {{ $complaint->waterSource->source_name }}
            </p>
            <p class="mb-0">{{ $complaint->description }}</p>
        </div>
    </div>

    <form action="{{ route('complaints.update', $complaint) }}" method="POST">
        @csrf
        @method('PUT') {{-- Resource route expects PUT/PATCH for update --}}

        <div class="mb-3">
            <label for="status" class="form-label">
                Current status: <strong>{{ $complaint->status }}</strong>
            </label>

            @if (empty($allowedStatuses))
                {{-- State Pattern: final states (Resolved / Rejected) allow no transitions --}}
                <div class="alert alert-info mb-0">
                    This complaint is in a final state and can no longer be updated.
                </div>
            @else
                {{-- State Pattern: only the transitions permitted by the current
                     state are shown - the options come from the state object,
                     not hard-coded here --}}
                <select name="status" id="status" class="form-select" required>
                    @foreach ($allowedStatuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        @if (!empty($allowedStatuses))
            <button type="submit" class="btn btn-primary">Update Status</button>
        @endif
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection