@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-5 mb-3">💧 Welcome to AquaTrack</h1>

    <p class="lead text-muted mb-4">
        A community water quality monitoring system<br>
        supporting SDG 6: Clean Water and Sanitation
    </p>

    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body py-4">
            <h5 class="mb-3">What you can do with AquaTrack</h5>
            <p class="mb-1">🚰 Report water quality complaints</p>
            <p class="mb-1">📍 Track water sources in your community</p>
            <p class="mb-1">🧪 View water quality readings</p>
            <p class="mb-0">🔔 Receive alerts on water issues</p>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('complaints.index') }}" class="btn btn-primary btn-lg">
            View Complaints
        </a>
    </div>
</div>
@endsection