<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Manajemen Anomali') }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    {{-- Tabler --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css">

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    {{-- Laravel/Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .navbar-vertical {
            border-right: 1px solid var(--tblr-border-color);
        }

        .app-brand {
            height: 64px;
            border-bottom: 1px solid var(--tblr-border-color);
        }

        .app-logo {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: var(--tblr-primary);
            color: #fff;

            font-size: 13px;
            font-weight: 800;
        }

        .app-name {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
        }

        .app-description {
            margin-top: 2px;
            font-size: 11px;
            color: var(--tblr-secondary);
        }

        .navbar-vertical .navbar-nav .nav-link {
            margin: 2px 10px;
            border-radius: 8px;
            font-weight: 500;
        }

        .navbar-vertical .navbar-nav .nav-link:hover {
            background: var(--tblr-bg-surface-secondary);
        }

        .navbar-vertical .navbar-nav .nav-link.active {
            background: var(--tblr-primary-lt);
            color: var(--tblr-primary);
            font-weight: 600;
        }

        .navbar-vertical .nav-link-icon {
            width: 24px;
            height: 24px;
        }

        .sidebar-section {
            padding: 18px 20px 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--tblr-secondary);
        }

        /* =====================================================
           MAIN
        ===================================================== */

        .page-wrapper {
            min-height: 100vh;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1030;

            min-height: 64px;
            border-bottom: 1px solid var(--tblr-border-color);

            background: var(--tblr-bg-surface);
        }

        .page-header {
            padding-top: 24px;
            padding-bottom: 0;
        }

        .page-pretitle {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .page-title {
            margin-top: 4px;
            font-weight: 700;
            letter-spacing: -.3px;
        }

        .page-body {
            padding-top: 24px;
        }

        /* =====================================================
           CARD
        ===================================================== */

        .card {
            border-color: var(--tblr-border-color);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
        }

        .card-title {
            font-weight: 650;
        }

        /* =====================================================
           USER
        ===================================================== */

        .sidebar-user {
            border-top: 1px solid var(--tblr-border-color);
            padding: 14px;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
        }

        .sidebar-user-email {
            font-size: 11px;
            color: var(--tblr-secondary);
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .app-footer {
            border-top: 1px solid var(--tblr-border-color);
            margin-top: 40px;
        }

        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 991.98px) {
            .app-brand {
                height: 56px;
            }

            .topbar {
                min-height: 56px;
            }
        }

        /* =====================================================
           PERBAIKAN CEPAT – SIDEBAR & LAYOUT
        ===================================================== */

        /* Pastikan sidebar dan konten berdampingan */
        .page {
            display: flex;
            flex-wrap: nowrap;
            min-height: 100vh;
        }

        .navbar-vertical {
            width: 280px;
            flex-shrink: 0;
            border-right: 1px solid var(--tblr-border-color);
            background: var(--tblr-bg-surface);
        }

        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Desktop: tampilkan menu selalu */
        @media (min-width: 992px) {
            .navbar-vertical .navbar-collapse {
                display: block !important;
                visibility: visible !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* Sembunyikan tombol toggle di sidebar (jika masih ada) */
            .navbar-vertical .navbar-toggler {
                display: none !important;
            }
        }

        /* Mobile: sidebar menjadi full-width */
        @media (max-width: 991.98px) {
            .page {
                flex-direction: column;
            }

            .navbar-vertical {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--tblr-border-color);
            }
        }
    </style>
</head>

<body>

    <div class="page">
        {{-- =====================================================
         SIDEBAR
    ====================================================== --}}
        @include('layouts.navigation')

        {{-- =====================================================
         PAGE WRAPPER
    ====================================================== --}}
        <div class="page-wrapper">

            {{-- =================================================
             TOPBAR
        ================================================== --}}
            <header class="navbar navbar-expand-md d-print-none topbar">

                <div class="container-xl">

                    {{-- Mobile sidebar button (offcanvas) --}}
                    <div class="navbar-nav d-lg-none">
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-label="Buka menu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>

                    {{-- Desktop title --}}
                    <div class="navbar-nav d-none d-md-flex">
                        <div class="nav-item">
                            <div class="fw-semibold">Manajemen Anomali</div>
                            <div class="text-secondary small">Monitoring dan tindak lanjut anomali</div>
                        </div>
                    </div>

                    {{-- Right side --}}
                    <div class="navbar-nav ms-auto">

                        {{-- Theme --}}
                        <div class="nav-item dropdown">
                            <button class="btn btn-icon btn-ghost-secondary" data-bs-toggle="dropdown"
                                aria-label="Tema">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3v2" />
                                    <path d="M12 19v2" />
                                    <path d="M4.22 4.22l1.42 1.42" />
                                    <path d="M18.36 18.36l1.42 1.42" />
                                    <path d="M3 12h2" />
                                    <path d="M19 12h2" />
                                    <path d="M4.22 19.78l1.42-1.42" />
                                    <path d="M18.36 5.64l1.42-1.42" />
                                    <circle cx="12" cy="12" r="4" />
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <button type="button" class="dropdown-item" onclick="setTheme('light')">☀️
                                    Light</button>
                                <button type="button" class="dropdown-item" onclick="setTheme('dark')">🌙 Dark</button>
                            </div>
                        </div>

                        {{-- User --}}
                        @auth
                            <div class="nav-item dropdown ms-2">
                                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                                    <span class="avatar avatar-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                    <div class="d-none d-xl-block ps-2">
                                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                        <div class="small text-secondary">{{ auth()->user()->email }}</div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <div class="dropdown-header">Akun</div>
                                    <div class="dropdown-item-text">
                                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                        <div class="text-secondary small">{{ auth()->user()->email }}</div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                                <path
                                                    d="M10 8v-3a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-7a1 1 0 0 1-1-1v-3" />
                                                <path d="M15 12h-12" />
                                                <path d="M6 9l-3 3l3 3" />
                                            </svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endauth

                    </div>

                </div>

            </header>

            {{-- =================================================
             PAGE HEADER
        ================================================== --}}
            @if (isset($header))
                <div class="page-header d-print-none">
                    <div class="container-xl">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="page-pretitle text-primary">Manajemen Anomali</div>
                                <h2 class="page-title">{{ $header }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- =================================================
             CONTENT
        ================================================== --}}
            <main class="page-body">
                <div class="container-xl">
                    {{ $slot }}
                </div>
            </main>

            {{-- =================================================
             FOOTER
        ================================================== --}}
            <footer class="app-footer py-4 d-print-none">
                <div class="container-xl">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-secondary small">Manajemen Anomali</div>
                        <div class="text-secondary small">SE2026 · {{ date('Y') }}</div>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    {{-- =========================================================
     SCRIPTS
========================================================= --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>

    <script>
        function setTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-bs-theme', savedTheme);
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
