@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container py-5">
    <div class="card mx-auto" style="max-width: 420px;">
        <div class="card-header card-header-aqua">
            <i class="bi bi-droplet-fill"></i> Login to AquaTrack
        </div>
        <div class="card-body p-4">

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password"
                           class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="mt-4 text-center">
                <span>Don't have an account? </span>
                <a href="{{ route('register') }}">Register as Resident</a>
            </div>

        </div>
    </div>
</div>
@endsection