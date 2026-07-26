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

{{-- ============ Resident view: friendly progress cards ============ --}}
    <div class="row g-3">
        @foreach ($complaints as $complaint)
            @php
                // Map each status to a progress stage (State Pattern reflected in UI)
                $stages = ['Pending' => 1, 'Investigating' => 2, 'Resolved' => 3, 'Rejected' => 3];
                $current = $stages[$complaint->status] ?? 1;
                $isRejected = $complaint->status === 'Rejected';
            @endphp
            <div class="col-12">
                <div class="card complaint-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h5 class="mb-1">
                                    <i class="bi bi-chat-left-text" style="color: var(--aqua-main);"></i>
                                    {{ $complaint->title }}
                                </h5>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> {{ $complaint->waterSource->source_name }}
                                    &nbsp;·&nbsp;
                                    <i class="bi bi-calendar3"></i> {{ $complaint->created_at->format('d M Y') }}
                                </small>
                            </div>
                            <span class="badge {{ $complaint->state()->getBadgeClass() }}">
                                {{ $complaint->status }}
                            </span>
                        </div>

                        {{-- Status progress bar --}}
                        <div class="status-progress">
                            <div class="status-step {{ $current >= 1 ? ($isRejected ? 'rejected' : 'done') : '' }}"></div>
                            <div class="status-step {{ $current >= 2 ? ($isRejected ? 'rejected' : 'done') : '' }}"></div>
                            <div class="status-step {{ $current >= 3 ? ($isRejected ? 'rejected' : 'done') : '' }}"></div>
                        </div>
                        <div class="status-labels">
                            <span class="{{ $current >= 1 ? 'active' : '' }}">Submitted</span>
                            <span class="{{ $current >= 2 ? 'active' : '' }}">Investigating</span>
                            <span class="{{ $current >= 3 ? 'active' : '' }}">
                                {{ $isRejected ? 'Rejected' : 'Resolved' }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('complaints.show', $complaint) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @if ($complaint->canBeDeletedBy(Auth::user()))
                                <form action="{{ route('complaints.destroy', $complaint) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this complaint?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
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