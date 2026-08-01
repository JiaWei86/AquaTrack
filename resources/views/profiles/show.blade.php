@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')
@section('page-subtitle', 'View basic account details')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="h4 mb-1">{{ $user->name }}</h2>
                </div>
                <span class="badge bg-primary-subtle text-primary">{{ $user->status }}</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="form-control bg-light">{{ $user->email }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <div class="form-control bg-light">{{ $user->phone ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">State</label>
                    <div class="form-control bg-light">{{ $user->state ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Edit Profile
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
