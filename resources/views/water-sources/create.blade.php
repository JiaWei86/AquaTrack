@extends('layouts.app')

@section('title', 'Add Water Source')
@section('page-title', 'Add Water Source')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header card-header-aqua">New Water Source</div>
        <div class="card-body">
            <form method="POST" action="{{ route('water-sources.store') }}">
                @csrf
                @include('water-sources._form')

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('water-sources.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection