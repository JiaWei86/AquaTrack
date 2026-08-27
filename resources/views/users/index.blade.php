@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('page-subtitle', 'Inspectors and residents overview')

@section('content')
@php
    $nextInspectorDirection = fn (string $column) => ($inspectorSort === $column && $inspectorDirection === 'asc') ? 'desc' : 'asc';
    $nextResidentDirection = fn (string $column) => ($residentSort === $column && $residentDirection === 'asc') ? 'desc' : 'asc';
@endphp
<div class="container py-4">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="h5 mb-1">Inspectors</h3>
                    <p class="text-muted mb-0">Administrators can create and remove inspectors.</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Create Inspector
                </a>
            </div>
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        @foreach (['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'status' => 'Status', 'role' => 'Role', 'latest_quality_reading' => 'Latest Quality Reading'] as $column => $label)
                            <th>
                                <a href="{{ route('users.index', array_merge(request()->only(['resident_sort', 'resident_direction']), ['inspector_sort' => $column, 'inspector_direction' => $nextInspectorDirection($column)])) }}"
                                   class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                    {{ $label }}
                                    @if ($activeInspectorSort === $column)
                                        <i class="bi bi-arrow-{{ $inspectorDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted small"></i>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inspectors as $inspector)
                        <tr>
                            <td>{{ $inspector->name }}</td>
                            <td>{{ $inspector->email }}</td>
                            <td>{{ $inspector->phone ?? '-' }}</td>
                            <td>{{ $inspector->status }}</td>
                            <td>{{ $inspector->role }}</td>
                            @php($latestReading = $latestQualityReadings->get($inspector->id))
                            <td>
                                @if ($latestReading)
                                    <div>{{ $latestReading['water_source']['source_name'] ?? 'Unknown water source' }}</div>
                                    <small class="text-muted">
                                        {{ $latestReading['status'] ?? 'N/A' }}
                                        | {{ $latestReading['sample_date'] ?? 'No sample date' }}
                                    </small>
                                @else
                                    <span class="text-muted">No readings</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('users.destroy', $inspector) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this inspector?');">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <h3 class="h5">Residents</h3>
            <p class="text-muted">Residents are registered through the public register page.</p>
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        @foreach (['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'state' => 'State', 'status' => 'Status'] as $column => $label)
                            <th>
                                <a href="{{ route('users.index', array_merge(request()->only(['inspector_sort', 'inspector_direction']), ['resident_sort' => $column, 'resident_direction' => $nextResidentDirection($column)])) }}"
                                   class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                    {{ $label }}
                                    @if ($activeResidentSort === $column)
                                        <i class="bi bi-arrow-{{ $residentDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-muted small"></i>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($residents as $resident)
                        <tr>
                            <td>
                                <a href="/profiles/{{ $resident->id }}" class="text-decoration-none fw-semibold text-primary">
                                    {{ $resident->name }}
                                </a>
                            </td>
                            <td>{{ $resident->email }}</td>
                            <td>{{ $resident->phone ?? '-' }}</td>
                            <td>{{ $resident->state ?? '-' }}</td>
                            <td>{{ $resident->status }}</td>
                            <td class="text-end">
                                <form action="{{ route('users.update.status', $resident) }}" method="POST" class="d-inline-flex align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm me-2" style="width: 130px;">
                                        @foreach(['Active', 'Inactive'] as $status)
                                            <option value="{{ $status }}" {{ $resident->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection