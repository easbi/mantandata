<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Terapkan tema tersimpan sedini mungkin, sebelum CSS dimuat,
         supaya tidak ada flash / delay saat sidebar & body ikut berubah warna --}}
    <script>
        (function () {
            var saved = localStorage.getItem('theme');
            if (saved) {
                document.documentElement.setAttribute('data-bs-theme', saved);
            }
        })();
    </script>

    <title>{{ config('app.name', 'Manajemen Anomali') }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    {{-- Tabler --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css">

    {{-- DataTables --}}
    <link rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    {{-- Laravel / Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        /* =====================================================
           RESET & GLOBAL
        ===================================================== */

        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            height: 100%;
        }

        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--tblr-bg-surface-secondary);
            color: var(--tblr-body-color);
            line-height: 1.5;
        }

        /* =====================================================
           MAIN PAGE
        ===================================================== */

        .page {
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .navbar-vertical {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;

            width: 260px;
            min-width: 260px;
            max-width: 260px;

            z-index: 1050;

            border-right: 1px solid var(--tblr-border-color);
            background: var(--tblr-bg-surface);

            overflow-y: auto;
            overflow-x: hidden;

            display: flex;
            flex-direction: column;
        }

        .navbar-vertical::-webkit-scrollbar {
            width: 6px;
        }

        .navbar-vertical::-webkit-scrollbar-track {
            background: transparent;
        }

        .navbar-vertical::-webkit-scrollbar-thumb {
            background: var(--tblr-border-color);
            border-radius: 3px;
        }

        .navbar-vertical::-webkit-scrollbar-thumb:hover {
            background: var(--tblr-secondary);
        }

        /* App Brand */
        .app-brand {
            height: 70px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--tblr-border-color);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .app-logo {
            width: 42px;
            height: 42px;
            min-width: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 12px;
            background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-info));
            color: #fff;

            font-size: 16px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(86, 100, 234, 0.2);
        }

        .app-brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .app-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
            color: var(--tblr-body-color);
        }

        .app-description {
            font-size: 10px;
            color: var(--tblr-secondary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Navigation */
        .navbar-vertical .navbar-nav {
            padding: 12px 8px;
            flex: 1;
            overflow-y: auto;
        }

        .navbar-vertical .navbar-nav .nav-link {
            margin: 4px 0;
            padding: 10px 12px !important;
            border-radius: 8px;
            font-weight: 500;
            font-size: 13px;
            color: var(--tblr-body-color);
            transition: all 0.2s ease;
            position: relative;
        }

        .navbar-vertical .navbar-nav .nav-link:hover {
            background: var(--tblr-bg-surface-secondary);
            padding-left: 14px !important;
        }

        .navbar-vertical .navbar-nav .nav-link.active {
            background: var(--tblr-primary-lt);
            color: var(--tblr-primary);
            font-weight: 600;
            box-shadow: inset -3px 0 0 var(--tblr-primary);
        }

        .navbar-vertical .navbar-nav .nav-link-icon {
            width: 22px;
            height: 22px;
            margin-right: 10px;
        }

        /* Sidebar Section Headers */
        .sidebar-section {
            padding: 20px 16px 8px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--tblr-secondary);
            margin-top: 8px;
        }

        .sidebar-section:first-child {
            margin-top: 0;
        }

        /* Sidebar User */
        .sidebar-user {
            border-top: 1px solid var(--tblr-border-color);
            padding: 16px;
            margin-top: auto;
            flex-shrink: 0;
            background: var(--tblr-bg-surface-secondary);
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .sidebar-user-email {
            font-size: 11px;
            color: var(--tblr-secondary);
        }

        /* =====================================================
           SIDEBAR BACKDROP (mobile)
        ===================================================== */

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1045; /* di bawah sidebar (1050), di atas konten */
        }

        @media (max-width: 991.98px) {
            .sidebar-backdrop.show {
                display: block;
            }
        }

        /* =====================================================
           PAGE WRAPPER
        ===================================================== */

        .page-wrapper {
            width: calc(100% - 260px);
            min-width: 0;
            min-height: 100vh;

            margin-left: 260px;

            display: flex;
            flex-direction: column;
        }

        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {
            position: fixed;

            top: 0;
            right: 0;

            width: calc(100% - 260px);
            height: 70px;

            z-index: 1030;

            border-bottom: 1px solid var(--tblr-border-color);
            background: var(--tblr-bg-surface);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .topbar .container-xl {
            padding-left: 32px;
            padding-right: 32px;
            height: 100%;
            display: flex;
            align-items: center;
        }

        /* =====================================================
           PAGE HEADER (bukan fixed lagi — ikut scroll bersama konten)
        ===================================================== */

        .page-header {
            padding: 0 0 20px 0;
            margin-bottom: 24px;

            border-bottom: 1px solid var(--tblr-border-color);
        }

        .page-pretitle {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--tblr-primary);
            margin-bottom: 4px;
        }

        .page-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.4px;
            color: var(--tblr-body-color);
        }

        /* =====================================================
           PAGE BODY
           (padding-top hanya perlu menyisakan ruang untuk topbar
           yang fixed, karena page-header sudah bukan fixed lagi)
        ===================================================== */

        .page-body {
            padding-top: 102px; /* 70px tinggi topbar + 32px jarak */
            padding-bottom: 32px;
            padding-left: 32px;
            padding-right: 32px;
            flex: 1;
            overflow-y: auto;
        }

        .page-body::-webkit-scrollbar {
            width: 8px;
        }

        .page-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .page-body::-webkit-scrollbar-thumb {
            background: var(--tblr-border-color);
            border-radius: 4px;
        }

        .page-body::-webkit-scrollbar-thumb:hover {
            background: var(--tblr-secondary);
        }

        /* =====================================================
           CARD
        ===================================================== */

        .card {
            border: 1px solid var(--tblr-border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: var(--tblr-border-color);
        }

        .card-header {
            border-bottom: 1px solid var(--tblr-border-color);
            padding: 20px 24px;
            background: var(--tblr-bg-surface);
        }

        .card-body {
            padding: 24px;
        }

        .card-footer {
            border-top: 1px solid var(--tblr-border-color);
            padding: 20px 24px;
            background: var(--tblr-bg-surface-secondary);
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 0;
            color: var(--tblr-body-color);
        }

        /* =====================================================
           FOOTER
        ===================================================== */

        .app-footer {
            background: var(--tblr-bg-surface);
            border-top: 1px solid var(--tblr-border-color);
            padding: 20px 32px;
            margin-top: auto;
            flex-shrink: 0;
        }

        .app-footer .text-secondary {
            font-size: 12px;
        }

        /* =====================================================
           BUTTONS & COMPONENTS
        ===================================================== */

        .btn {
            border-radius: 8px;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            box-shadow: 0 2px 6px rgba(86, 100, 234, 0.15);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(86, 100, 234, 0.25);
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            background: var(--tblr-bg-surface-secondary);
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid var(--tblr-border-color);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .dropdown-item {
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        /* =====================================================
           RESPONSIVE - DESKTOP
        ===================================================== */

        @media (min-width: 992px) {

            .navbar-vertical .navbar-collapse {
                display: block !important;
                visibility: visible !important;
                height: auto !important;
                overflow: visible !important;
            }

            .navbar-vertical .navbar-toggler {
                display: none !important;
            }
        }

        /* =====================================================
           RESPONSIVE - TABLET / MOBILE
        ===================================================== */

        @media (max-width: 991.98px) {

            .page {
                display: block;
                min-height: 100vh;
            }

            .navbar-vertical {
                position: fixed !important;

                top: 0 !important;
                left: 0 !important;
                bottom: 0 !important;

                width: 260px !important;
                min-width: 260px !important;
                max-width: 260px !important;

                z-index: 1050 !important;

                margin: 0 !important;

                border: 0 !important;
                border-right: 1px solid var(--tblr-border-color) !important;

                overflow-y: auto !important;
                overflow-x: hidden !important;

                background: var(--tblr-bg-surface);

                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .navbar-vertical.show {
                transform: translateX(0);
            }

            /* Tabler menyembunyikan .navbar-collapse di bawah breakpoint lg
               secara bawaan (mode navbar horizontal biasa). Karena di sini
               kita pakai sidebar off-canvas custom, menu di dalamnya harus
               tetap dipaksa tampil begitu panel .navbar-vertical terbuka. */
            .navbar-vertical .navbar-collapse {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                visibility: visible !important;
            }

            .navbar-vertical .navbar-nav {
                display: flex !important;
                flex-direction: column !important;
            }

            .page-wrapper {
                width: 100% !important;
                min-height: 100vh;

                margin-left: 0 !important;

                padding-top: 0 !important;
            }

            .topbar {
                position: fixed !important;

                top: 0 !important;
                left: 0 !important;
                right: 0 !important;

                width: 100% !important;
                height: 64px !important;

                z-index: 1040 !important;
            }

            .topbar .container-xl {
                padding-left: 16px;
                padding-right: 16px;
            }

            .page-header {
                padding: 0 0 16px 0;
                margin-bottom: 16px;
            }

            .page-body {
                padding-top: 80px !important; /* 64px tinggi topbar mobile + 16px jarak */
                padding-bottom: 24px;
                padding-left: 16px;
                padding-right: 16px;
            }

            .app-brand {
                height: 64px;
                padding: 12px 16px;
            }

            .app-logo {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }

            .page-title {
                font-size: 20px;
            }

            .app-footer {
                padding: 16px;
            }
        }

        /* =====================================================
           RESPONSIVE - SMALL MOBILE
        ===================================================== */

        @media (max-width: 575.98px) {

            .navbar-vertical {
                width: 240px !important;
                min-width: 240px !important;
                max-width: 240px !important;
            }

            .page-title {
                font-size: 18px;
            }

            .page-pretitle {
                font-size: 9px;
            }

            .container-xl {
                padding-left: 16px;
                padding-right: 16px;
            }

            .card-body {
                padding: 16px;
            }

            .app-name {
                font-size: 12px;
            }

            .app-description {
                font-size: 9px;
            }
        }

        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            .navbar-vertical,
            .topbar,
            .page-header,
            .app-footer,
            .sidebar-backdrop {
                position: static !important;
                display: none !important;
            }

            .page {
                display: block !important;
            }

            .page-wrapper {
                width: 100% !important;
                margin-left: 0 !important;
                padding-top: 0 !important;
            }

            .page-body {
                padding-top: 0 !important;
            }
        }

        /* =====================================================
           DARK MODE ADJUSTMENTS
        ===================================================== */

        [data-bs-theme="dark"] .app-logo {
            box-shadow: 0 4px 12px rgba(86, 100, 234, 0.15);
        }

        [data-bs-theme="dark"] .card {
            background: var(--tblr-bg-surface);
        }

        [data-bs-theme="dark"] .btn-primary {
            box-shadow: 0 2px 6px rgba(86, 100, 234, 0.25);
        }

        [data-bs-theme="dark"] .dropdown-menu {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.24);
        }
    </style>
</head>

<body>

    <div class="page">

        {{-- =====================================================
             BACKDROP UNTUK SIDEBAR MOBILE
             (klik di luar sidebar akan menutupnya)
        ====================================================== --}}
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

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

                    {{-- Mobile sidebar button --}}
                    <div class="navbar-nav d-lg-none">
                        <button
                            class="navbar-toggler"
                            type="button"
                            onclick="toggleSidebar()"
                            aria-label="Buka menu">

                            <span class="navbar-toggler-icon"></span>

                        </button>
                    </div>


                    {{-- =================================================
                         Judul halaman sudah ditampilkan di page-header
                         (lihat @if (isset($header)) di bawah), jadi topbar
                         cukup berisi kontrol (toggle sidebar, tema, user)
                         supaya judul tidak tampil dobel.
                    ================================================== --}}


                    {{-- Right side --}}
                    <div class="navbar-nav ms-auto" style="gap: 8px;">

                        {{-- Theme --}}
                        <div class="nav-item dropdown">

                            <button
                                type="button"
                                class="btn btn-icon btn-ghost-secondary"
                                data-bs-toggle="dropdown"
                                aria-label="Tema">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M12 3v2" />
                                    <path d="M12 19v2" />
                                    <path d="M4.22 4.22l1.42 1.42" />
                                    <path d="M18.36 18.36l1.42-1.42" />
                                    <path d="M3 12h2" />
                                    <path d="M19 12h2" />
                                    <path d="M4.22 19.78l1.42-1.42" />
                                    <path d="M18.36 5.64l-1.42-1.42" />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="4" />

                                </svg>

                            </button>

                            <div class="dropdown-menu dropdown-menu-end">

                                <button
                                    type="button"
                                    class="dropdown-item"
                                    onclick="setTheme('light')">

                                    ☀️ Light

                                </button>

                                <button
                                    type="button"
                                    class="dropdown-item"
                                    onclick="setTheme('dark')">

                                    🌙 Dark

                                </button>

                            </div>

                        </div>


                        {{-- User --}}
                        @auth

                            <div class="nav-item dropdown ms-2">

                                <a
                                    href="#"
                                    class="nav-link d-flex lh-1 text-reset p-0"
                                    data-bs-toggle="dropdown"
                                    style="gap: 10px;">

                                    <span class="avatar avatar-sm bg-primary text-white fw-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>

                                    <div class="d-none d-xl-flex flex-column">

                                        <div class="fw-semibold" style="font-size: 13px;">
                                            {{ auth()->user()->name }}
                                        </div>

                                        <div class="small text-secondary" style="font-size: 11px;">
                                            {{ auth()->user()->email }}
                                        </div>

                                    </div>

                                </a>

                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                                    <div class="dropdown-header">
                                        Profil Akun
                                    </div>

                                    <div class="dropdown-item-text">

                                        <div class="fw-semibold" style="font-size: 13px;">
                                            {{ auth()->user()->name }}
                                        </div>

                                        <div class="text-secondary small" style="font-size: 11px; margin-top: 4px;">
                                            {{ auth()->user()->email }}
                                        </div>

                                    </div>

                                    <div class="dropdown-divider"></div>

                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="dropdown-item text-danger">

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="me-2">

                                                <path d="M10 8v-3a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-7a1 1 0 0 1-1-1v-3" />
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
                 CONTENT
                 (judul halaman sekarang di sini, bukan fixed lagi,
                 jadi ikut ter-scroll bersama konten — hanya topbar
                 di atas yang tetap menempel)
            ================================================== --}}
            <main class="page-body">

                <div class="container-xl">

                    @if (isset($header))

                        <div class="page-header d-print-none">

                            <div class="page-pretitle">
                                Manajemen Anomali
                            </div>

                            <h2 class="page-title">
                                {{ $header }}
                            </h2>

                        </div>

                    @endif

                    {{ $slot }}

                </div>

            </main>


            {{-- =================================================
                 FOOTER
            ================================================== --}}
            <footer class="app-footer d-print-none">

                <div class="d-flex justify-content-between align-items-center">

                    <div class="text-secondary">
                        Manajemen Anomali SE2026
                    </div>

                    <div class="text-secondary">
                        © {{ date('Y') }} BPS Kota Padang Panjang
                    </div>

                </div>

            </footer>

        </div>

    </div>


    {{-- =========================================================
         SCRIPTS
    ========================================================== --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js"></script>

    <script>
        /* =====================================================
           THEME
        ===================================================== */

        function setTheme(theme) {
            document.documentElement.setAttribute(
                'data-bs-theme',
                theme
            );

            localStorage.setItem('theme', theme);
        }

        /* Catatan: pembacaan tema awal sudah dilakukan di <head>
           (lihat inline script paling atas) supaya tidak ada flash
           dan sidebar langsung mengikuti tema sejak render pertama. */


        /* =====================================================
           SIDEBAR TOGGLE (MOBILE)
        ===================================================== */

        function toggleSidebar() {
            document.querySelector('.navbar-vertical')?.classList.toggle('show');
            document.getElementById('sidebarBackdrop')?.classList.toggle('show');
        }

        function closeSidebar() {
            document.querySelector('.navbar-vertical')?.classList.remove('show');
            document.getElementById('sidebarBackdrop')?.classList.remove('show');
        }

        // Tutup sidebar otomatis kalau layar dibesarkan ke ukuran desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });

        // Tutup sidebar otomatis setiap kali link menu di sidebar diklik (mobile)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.navbar-vertical .nav-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });
        });
    </script>

    @stack('scripts')

</body>

</html>
