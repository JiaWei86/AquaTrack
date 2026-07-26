@extends('layouts.app')

@section('title', 'Complaint Details')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Complaint #{{ $complaint->id }}</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $complaint->title }}</h5>

            <p class="mb-1"><strong>Resident:</strong> {{ $complaint->resident->name }}</p>
            <p class="mb-1"><strong>Water Source:</strong>
                {{ $complaint->waterSource->source_name }} ({{ $complaint->waterSource->location }})
            </p>
            <p class="mb-1"><strong>Status:</strong>
                <span class="badge badge-aqua">{{ $complaint->status }}</span>
            </p>
            <p class="mb-3"><strong>Submitted:</strong> {{ $complaint->created_at->format('d M Y, h:i A') }}</p>

            <p><strong>Description:</strong></p>
            {{-- Blade escaping prevents XSS even if the description contains <script> tags --}}
            <p>{{ $complaint->description }}</p>

            @if ($complaint->photo)
                <p><strong>Photo:</strong></p>
                <img src="{{ asset('storage/' . $complaint->photo) }}"
                     alt="Complaint photo" class="img-fluid rounded" style="max-width: 400px;">
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary">Back to List</a>

        @if (Auth::user()->isAdministrator())
            <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-primary">Update Status</a>
        @endif
    </div>
</div>
@endsection