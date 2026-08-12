<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaTrack - @yield('title', 'Water Quality Monitoring')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/aquatrack.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 {{ Auth::check() && Auth::user()->isAdministrator() ? 'admin-theme' : '' }}">

    {{-- ==================== NAVBAR ==================== --}}
    <nav class="navbar navbar-expand-lg navbar-aqua">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-droplet-fill"></i> AquaTrack
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navMenu" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                @auth
                    {{-- Left menu: modules --}}
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}"
                               href="{{ route('complaints.index') }}">
                                <i class="bi bi-megaphone"></i> Complaints
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('water-sources.*') ? 'active' : '' }}"
                               href="{{ url('/water-sources') }}">
                                <i class="bi bi-geo-alt"></i> Water Sources
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('quality-readings.*') ? 'active' : '' }}"
                               href="{{ url('/quality-readings') }}">
                                <i class="bi bi-clipboard-data"></i> Quality Readings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}"
                               href="{{ url('/alerts') }}">
                                <i class="bi bi-bell"></i> Alerts
                            </a>
                        </li>

                        {{-- Only administrators see User Management --}}
                        @if (Auth::user()->isAdministrator())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                   href="{{ url('/users') }}">
                                    <i class="bi bi-people"></i> Users
                                </a>
                            </li>
                        @endif
                    </ul>

                    {{-- Right menu: current user + logout --}}
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item me-lg-2">
                            @if (!Auth::user()->isAdministrator())
                                <a href="{{ route('profile') }}" class="navbar-text text-white text-decoration-none">
                                    <i class="bi bi-person-circle"></i>
                                    {{ Auth::user()->name }}
                                    <span class="badge bg-light text-dark ms-1">{{ Auth::user()->role }}</span>
                                </a>
                            @else
                                <span class="navbar-text text-white">
                                    <i class="bi bi-person-circle"></i>
                                    {{ Auth::user()->name }}
                                    <span class="badge bg-light text-dark ms-1">{{ Auth::user()->role }}</span>
                                </span>
                            @endif
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ==================== PAGE HEADER ==================== --}}
    @hasSection('page-title')
        <header class="page-header py-4 mb-4">
            <div class="container">
                <h1 class="h3 mb-0">@yield('page-title')</h1>
                @hasSection('page-subtitle')
                    <p class="text-muted mb-0 mt-1">@yield('page-subtitle')</p>
                @endif
            </div>
        </header>
    @endif

    {{-- ==================== MAIN CONTENT ==================== --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="footer-aqua mt-5 py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center text-md-start mb-2 mb-md-0">
                    <span class="fw-bold"><i class="bi bi-droplet-fill"></i> AquaTrack</span>
                </div>
                <div class="col-md-4 text-center mb-2 mb-md-0">
                    <small>Supporting SDG 6: Clean Water and Sanitation</small>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <small>&copy; {{ date('Y') }} AquaTrack Team</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ==================== ALERT NOTIFICATION TOAST ==================== --}}
    @if (session('alert_id'))
        @php
            $alertToastBgClass = session('alert_severity') === 'High' ? 'text-bg-danger' : 'text-bg-warning';
        @endphp
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="alertNotificationToast" class="toast align-items-center {{ $alertToastBgClass }} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="8000">
                <div class="d-flex">
                    <a href="{{ route('alerts.show', session('alert_id')) }}" class="toast-body text-decoration-none text-reset flex-grow-1">
                        {{ session('alert_message') }}
                    </a>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toastEl = document.getElementById('alertNotificationToast');

                if (toastEl) {
                    new bootstrap.Toast(toastEl).show();
                }
            });
        </script>
    @elseif (session('safe_status_message'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="safeStatusToast" class="toast align-items-center text-bg-success border-0" role="status" aria-live="polite" aria-atomic="true" data-bs-delay="6000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('safe_status_message') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toastEl = document.getElementById('safeStatusToast');

                if (toastEl) {
                    new bootstrap.Toast(toastEl).show();
                }
            });
        </script>
    @endif
</body>
</html>
