@extends('layouts.app')

@section('title', 'Water Sources')
@section('page-title', 'Water Sources')
@section('page-subtitle', 'Manage monitored water sources')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('water-sources.index') }}" class="d-flex gap-2">
            <select name="source_type" class="form-select" onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach (['River', 'Lake', 'Reservoir', 'Well', 'Community Tap'] as $type)
                    <option value="{{ $type }}" @selected(request('source_type') === $type)>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </form>

        @if (Auth::user()->isAdministrator())
            <a href="{{ route('water-sources.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Water Source
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            @if ($waterSources->isEmpty())
                <p class="text-muted mb-0">No water sources found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                @foreach ([
                                    'id'          => 'ID',
                                    'source_name' => 'Name',
                                    'source_type' => 'Type',
                                    'location'    => 'Location',
                                    'coordinates' => 'Coordinates',
                                ] as $column => $label)
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => ($activeSort === $column && $direction === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}"
                                        class="text-decoration-none text-reset d-inline-flex align-items-center gap-1">
                                            {{ $label }}
                                            @if ($activeSort === $column)
                                                <i class="bi bi-arrow-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                                            @else
                                                <i class="bi bi-arrow-down-up text-muted small"></i>
                                            @endif
                                        </a>
                                    </th>
                                @endforeach
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($waterSources as $source)
                                <tr>
                                    <td>{{ $source->id }}</td>
                                    <td>{{ $source->source_name }}</td>
                                    <td><span class="badge badge-aqua">{{ $source->source_type }}</span></td>
                                    <td>{{ $source->location }}</td>
                                    <td class="text-muted small">
                                        {{ $source->latitude }}, {{ $source->longitude }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('water-sources.show', $source) }}"
                                           class="btn btn-sm btn-outline-primary">View</a>
                                        @if (Auth::user()->isAdministrator())
                                            <a href="{{ route('water-sources.edit', $source) }}"
                                               class="btn btn-sm btn-outline-secondary">Edit</a>
                                            <form action="{{ route('water-sources.destroy', $source) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this water source?');">
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
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        {{ $waterSources->links() }}
    </div>
</div>
@endsection