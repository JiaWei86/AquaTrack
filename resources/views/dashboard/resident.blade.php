@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container py-4">

    {{-- Hero welcome section --}}
    <div class="card mb-4">
        <div class="card-body p-4 p-md-5 text-center">
            <h2 class="mb-2">Hi, {{ Auth::user()->name }} 👋</h2>
            <p class="text-muted mb-4">
                Notice a problem with your water supply?<br>
                Report it here and we'll look into it.
            </p>
            <a href="{{ route('complaints.create') }}" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-megaphone"></i> Report a Water Issue
            </a>
        </div>
    </div>

    {{-- Quick action cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('complaints.index') }}" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <i class="bi bi-list-check fs-1" style="color: var(--aqua-main);"></i>
                        <h5 class="mt-2 mb-1">My Complaints</h5>
                        <small class="text-muted">Track the status of your reports</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ url('/water-sources') }}" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <i class="bi bi-geo-alt fs-1" style="color: var(--aqua-main);"></i>
                        <h5 class="mt-2 mb-1">Water Sources</h5>
                        <small class="text-muted">View water sources in your area</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ url('/alerts') }}" class="text-decoration-none">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <i class="bi bi-bell fs-1" style="color: var(--aqua-main);"></i>
                        <h5 class="mt-2 mb-1">Alerts</h5>
                        <small class="text-muted">Latest water quality alerts</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- My complaint progress --}}
    <div class="card">
        <div class="card-header card-header-aqua">My Complaint Progress</div>
        <div class="card-body">
            @if ($myRecent->isEmpty())
                <p class="text-muted mb-0">
                    You haven't submitted any complaints yet.
                    <a href="{{ route('complaints.create') }}">Report your first issue</a>.
                </p>
            @else
                <div class="row text-center mb-3">
                    <div class="col-4">
                        <h4 class="mb-0">{{ $stats['myTotal'] }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-warning">{{ $stats['myPending'] }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-4">
                        <h4 class="mb-0 text-success">{{ $stats['myResolved'] }}</h4>
                        <small class="text-muted">Resolved</small>
                    </div>
                </div>

                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @foreach ($myRecent as $complaint)
                            <tr>
                                <td>
                                    <strong>{{ $complaint->title }}</strong><br>
                                    <small class="text-muted">
                                        {{ $complaint->waterSource->name }} ·
                                        {{ $complaint->created_at->format('d M Y') }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    <span class="badge badge-aqua">{{ $complaint->status }}</span>
                                    <a href="{{ route('complaints.show', $complaint) }}"
                                       class="btn btn-sm btn-outline-primary ms-2">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
