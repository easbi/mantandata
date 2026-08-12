{{-- =========================================================
     SIDEBAR
========================================================= --}}

<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="light">
    <div class="container-fluid">
        {{-- =================================================
             BRAND
        ================================================== --}}
        <div class="app-brand w-100 d-flex align-items-center">
            <a href="{{ route('dashboard') }}"
                class="navbar-brand d-flex align-items-center gap-3 text-reset text-decoration-none">
                <div class="app-logo">MA</div>
                <div>
                    <div class="app-name">Manajemen Anomali</div>
                    <div class="app-description">SE2026</div>
                </div>
            </a>
        </div>

        {{-- =================================================
             MENU – class "show" agar terbuka di desktop
        ================================================== --}}
        <div class="collapse navbar-collapse show" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3 w-100">

                {{-- DASHBOARD --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M4 4h6v6h-6z" />
                                <path d="M14 4h6v6h-6z" />
                                <path d="M4 14h6v6h-6z" />
                                <path d="M14 14h6v6h-6z" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                {{-- ANOMALI --}}
                <li class="nav-item">
                    <a href="{{ route('anomalies.index') }}"
                        class="nav-link {{ request()->routeIs('anomalies.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 9v4" />
                                <path d="M10.36 3.59L2.25 17.59a1.91 1.91 0 0 0 1.66 2.87h16.18a1.91 1.91 0 0 0 1.66-2.87L13.64 3.59a1.91 1.91 0 0 0-3.28 0z" />
                                <path d="M12 17h.01" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Anomali</span>
                    </a>
                </li>

                {{-- SECTION MANAJEMEN --}}
                <li class="nav-item">
                    <div class="sidebar-section">Manajemen</div>
                </li>

                {{-- IMPORT DATA --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 3v12" />
                                <path d="M8 11l4 4l4 -4" />
                                <path d="M5 21h14" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Import Data</span>
                    </a>
                </li>

                {{-- PETUGAS --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Petugas</span>
                    </a>
                </li>

                {{-- MONITORING --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 9 -9" />
                                <path d="M3 5v7h7" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Monitoring</span>
                    </a>
                </li>

                {{-- LOG AKTIVITAS --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M4 4h16v16h-16z" />
                                <path d="M8 8h8" />
                                <path d="M8 12h8" />
                                <path d="M8 16h5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Log Aktivitas</span>
                    </a>
                </li>

                {{-- SECTION SISTEM --}}
                <li class="nav-item">
                    <div class="sidebar-section">Sistem</div>
                </li>

                {{-- PENGATURAN --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M10.33 4.32c.43-1.76 2.92-1.76 3.35 0a1.72 1.72 0 0 0 2.57 1.07c1.54-.94 3.31.83 2.37 2.37a1.72 1.72 0 0 0 1.07 2.57c1.76.43 1.76 2.92 0 3.35a1.72 1.72 0 0 0-1.07 2.57c.94 1.54-.83 3.31-2.37 2.37a1.72 1.72 0 0 0-2.57 1.07c-.43 1.76-2.92 1.76-3.35 0a1.72 1.72 0 0 0-2.57-1.07c-1.54.94-3.31-.83-2.37-2.37a1.72 1.72 0 0 0-1.07-2.57c-1.76-.43-1.76-2.92 0-3.35a1.72 1.72 0 0 0 1.07-2.57c-.94-1.54.83-3.31 2.37-2.37a1.72 1.72 0 0 0 2.57-1.07z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Pengaturan</span>
                    </a>
                </li>

            </ul>

            {{-- USER CARD --}}
            @auth
                <div class="sidebar-user mt-auto">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <div class="flex-fill overflow-hidden">
                                    <div class="sidebar-user-name text-truncate">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="sidebar-user-email text-truncate">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="M10 8v-3a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-7a1 1 0 0 1-1-1v-3" />
                                            <path d="M15 12h-12" />
                                            <path d="M6 9l-3 3l3 3" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth

        </div>
    </div>
</aside>
