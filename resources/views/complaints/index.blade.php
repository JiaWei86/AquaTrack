@extends('layouts.app')

@section('title', 'Complaints')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ Auth::user()->isResident() ? 'My Complaints' : 'Complaints' }}</h2>

        {{-- Only residents see the submit button --}}
        @if (Auth::user()->isResident())
            <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Complaint
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($complaints->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1" style="color: var(--aqua-main);"></i>
                <p class="text-muted mt-2 mb-3">You haven't reported any water issues yet.</p>
                @if (Auth::user()->isResident())
                    <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                        Report Your First Issue
                    </a>
                @endif
            </div>
        </div>
    @elseif (Auth::user()->isResident())

        {{-- ============ Resident view: friendly cards ============ --}}
        <div class="row g-3">
            @foreach ($complaints as $complaint)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="mb-1">{{ $complaint->title }}</h5>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ $complaint->waterSource->source_name }}
                                    &nbsp;·&nbsp;
                                    <i class="bi bi-calendar3"></i> {{ $complaint->created_at->format('d M Y') }}
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge
                                    @switch($complaint->status)
                                        @case('Pending') bg-warning text-dark @break
                                        @case('Investigating') badge-aqua @break
                                        @case('Resolved') bg-success @break
                                        @case('Rejected') bg-danger @break
                                    @endswitch">
                                    {{ $complaint->status }}
                                </span>
                                <a href="{{ route('complaints.show', $complaint) }}"
                                   class="btn btn-sm btn-outline-primary">View</a>
                                @if ($complaint->status === 'Pending')
                                    <form action="{{ route('complaints.destroy', $complaint) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this complaint?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">{{ $complaints->links() }}</div>

    @else

        {{-- ============ Admin / Inspector view: data table ============ --}}
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Resident</th>
                    <th>Water Source</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($complaints as $complaint)
                    <tr>
                        <td>{{ $complaint->id }}</td>
                        {{-- Blade {{ }} auto-escapes output, preventing XSS --}}
                        <td>{{ $complaint->resident->name }}</td>
                        <td>{{ $complaint->waterSource->source_name }}</td>
                        <td>{{ $complaint->title }}</td>
                        <td>
                            <span class="badge
                                @switch($complaint->status)
                                    @case('Pending') bg-warning text-dark @break
                                    @case('Investigating') badge-aqua @break
                                    @case('Resolved') bg-success @break
                                    @case('Rejected') bg-danger @break
                                @endswitch">
                                {{ $complaint->status }}
                            </span>
                        </td>
                        <td>{{ $complaint->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('complaints.show', $complaint) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                            @if (Auth::user()->isAdministrator())
                                <a href="{{ route('complaints.edit', $complaint) }}"
                                   class="btn btn-sm btn-outline-secondary">Update Status</a>
                                <form action="{{ route('complaints.destroy', $complaint) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this complaint?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $complaints->links() }}

    @endif
</div>
@endsection