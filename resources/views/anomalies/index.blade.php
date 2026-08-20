<x-app-layout>

    <x-slot name="header">
        {{ __('Daftar Kasus Anomali') }}
    </x-slot>

    {{-- =========================================================
        DATATABLES CSS + CUSTOM STYLE
    ========================================================== --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css">

        <style>
            /* =====================================================
               VARIABLES & GLOBAL
            ===================================================== */

            :root {
                --spacing-xs: 4px;
                --spacing-sm: 8px;
                --spacing-md: 12px;
                --spacing-lg: 16px;
                --spacing-xl: 24px;
                --spacing-2xl: 32px;
            }

            /* =====================================================
               COLUMN TOGGLE CHECKBOX
            ===================================================== */

            .column-toggle {
                cursor: pointer;
            }

            .dropdown-menu {
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                border-radius: 12px;
                border: 1px solid var(--tblr-border-color);
            }

            /* =====================================================
               DATATABLE GENERAL
            ===================================================== */

            #anomaliTable {
                width: 100% !important;
            }

            .dt-container {
                font-size: 0.875rem;
            }

            /* =====================================================
               TABLE HEADER
            ===================================================== */

            #anomaliTable thead th {
                white-space: nowrap;
                background: var(--tblr-bg-surface-secondary);
                color: var(--tblr-secondary);
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                border-bottom: 1px solid var(--tblr-border-color);
                padding: 14px !important;
                vertical-align: middle;
            }

            /* =====================================================
               TABLE BODY
            ===================================================== */

            #anomaliTable tbody td {
                vertical-align: middle;
                font-size: 0.875rem;
                padding: 14px !important;
                border-bottom: 1px solid var(--tblr-border-color);
            }

            #anomaliTable tbody tr {
                transition: all 0.2s ease;
            }

            #anomaliTable tbody tr:hover {
                background-color: var(--tblr-bg-surface-secondary);
            }

            /* =====================================================
               ASSIGNMENT CELL
            ===================================================== */

            .assignment-cell {
                min-width: 200px;
                max-width: 280px;
            }

            .assignment-id {
                font-weight: 600;
                word-break: break-all;
                line-height: 1.4;
                color: var(--tblr-body-color);
                margin-bottom: 6px;
            }

            .detail-link {
                font-size: 0.8rem;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                transition: all 0.2s ease;
            }

            .detail-link:hover {
                text-decoration: underline;
            }

            .detail-link i {
                width: 14px;
                height: 14px;
            }

            /* =====================================================
               NAMA CELL
            ===================================================== */

            .nama-cell {
                min-width: 200px;
                max-width: 320px;
                white-space: normal;
            }

            /* =====================================================
               STATUS BADGE
            ===================================================== */

            .status-badge {
                white-space: nowrap;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 6px 10px !important;
                border-radius: 8px;
            }

            .badge.bg-secondary-lt {
                background: var(--tblr-secondary-lt) !important;
                color: var(--tblr-secondary) !important;
            }

            .badge.bg-blue-lt {
                background: var(--tblr-info-lt) !important;
                color: var(--tblr-info) !important;
            }

            .badge.bg-yellow-lt {
                background: var(--tblr-warning-lt) !important;
                color: var(--tblr-warning) !important;
            }

            .badge.bg-green-lt {
                background: var(--tblr-success-lt) !important;
                color: var(--tblr-success) !important;
            }

            /* =====================================================
               DATATABLES COMPONENTS
            ===================================================== */

            /* Search Input */
            .dt-search input {
                border: 1px solid var(--tblr-border-color) !important;
                border-radius: 8px !important;
                padding: 8px 12px !important;
                margin-left: 8px !important;
                background: var(--tblr-bg-surface);
                color: var(--tblr-body-color);
                font-size: 0.875rem;
                transition: all 0.2s ease;
            }

            .dt-search input:focus {
                border-color: var(--tblr-primary) !important;
                box-shadow: 0 0 0 3px rgba(86, 100, 234, 0.1) !important;
            }

            /* Length Menu Select */
            .dt-length select {
                border: 1px solid var(--tblr-border-color) !important;
                border-radius: 8px !important;
                padding: 8px 28px 8px 12px !important;
                background: var(--tblr-bg-surface);
                color: var(--tblr-body-color);
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .dt-length select:focus {
                border-color: var(--tblr-primary) !important;
                box-shadow: 0 0 0 3px rgba(86, 100, 234, 0.1) !important;
            }

            /* Pagination Buttons */
            .dt-paging-button {
                border-radius: 6px !important;
                border: 1px solid var(--tblr-border-color) !important;
                background: var(--tblr-bg-surface) !important;
                color: var(--tblr-body-color) !important;
                padding: 6px 10px !important;
                font-size: 0.875rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                margin: 0 2px;
            }

            .dt-paging-button:hover:not(.disabled) {
                background: var(--tblr-bg-surface-secondary) !important;
                border-color: var(--tblr-primary) !important;
            }

            .dt-paging-button.current {
                background: var(--tblr-primary) !important;
                color: #fff !important;
                border-color: var(--tblr-primary) !important;
                font-weight: 600;
            }

            /* Info Text */
            .dt-info {
                color: var(--tblr-secondary);
                font-size: 0.875rem;
            }

            /* DataTable Layout */
            .dt-layout-row {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                align-items: center;
                margin-bottom: 16px;
            }

            .dt-search,
            .dt-length {
                display: flex;
                align-items: center;
            }

            .dt-search label,
            .dt-length label {
                font-size: 0.875rem;
                font-weight: 500;
                margin-right: 6px;
                color: var(--tblr-body-color);
            }

            /* =====================================================
               LAYOUT CARDS
            ===================================================== */

            .card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 16px;
            }

            .card-header > div:first-child {
                flex: 1;
            }

            .card-title {
                font-size: 15px;
                font-weight: 700;
                margin-bottom: 4px;
                color: var(--tblr-body-color);
            }

            .card-header .text-muted {
                font-size: 12px;
            }

            /* =====================================================
               BUTTONS
            ===================================================== */

            .btn {
                border-radius: 8px;
                font-weight: 500;
                font-size: 0.875rem;
                padding: 8px 16px;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .btn-primary {
                box-shadow: 0 2px 6px rgba(86, 100, 234, 0.15);
            }

            .btn-primary:hover {
                box-shadow: 0 4px 12px rgba(86, 100, 234, 0.25);
            }

            .btn-list {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                align-items: center;
            }

            /* =====================================================
               FORM ELEMENTS
            ===================================================== */

            .form-label {
                font-size: 0.875rem;
                font-weight: 600;
                margin-bottom: 8px;
                color: var(--tblr-body-color);
            }

            .form-select {
                border: 1px solid var(--tblr-border-color) !important;
                border-radius: 8px !important;
                padding: 8px 12px !important;
                background: var(--tblr-bg-surface);
                color: var(--tblr-body-color);
                font-size: 0.875rem;
                transition: all 0.2s ease;
            }

            .form-select:focus {
                border-color: var(--tblr-primary) !important;
                box-shadow: 0 0 0 3px rgba(86, 100, 234, 0.1) !important;
            }

            /* =====================================================
               ALERT
            ===================================================== */

            .alert {
                border-radius: 8px;
                border: 1px solid var(--tblr-border-color);
                padding: 14px 16px;
                font-size: 0.875rem;
            }

            .alert-success {
                background: var(--tblr-success-lt);
                border-color: var(--tblr-success);
                color: var(--tblr-success);
            }

            .alert i {
                width: 18px;
                height: 18px;
            }

            /* =====================================================
               EMPTY STATE
            ===================================================== */

            .empty-state {
                text-align: center;
                padding: 48px 24px;
            }

            .empty-state-icon {
                font-size: 48px;
                margin-bottom: 16px;
                opacity: 0.5;
            }

            .empty-state-title {
                font-size: 15px;
                font-weight: 600;
                margin-bottom: 8px;
                color: var(--tblr-body-color);
            }

            .empty-state-text {
                font-size: 13px;
                color: var(--tblr-secondary);
            }

            /* =====================================================
               RESPONSIVE - TABLET/MOBILE
            ===================================================== */

            @media (max-width: 991.98px) {

                .card-body {
                    padding: 20px;
                }

                .card-header {
                    flex-direction: column;
                    align-items: flex-start !important;
                }

                .card-header > div:first-child {
                    width: 100%;
                }

                .btn-list {
                    width: 100%;
                }

                .btn-list .btn {
                    flex: 1;
                    justify-content: center;
                    min-height: 40px;
                }

                .dt-layout-row {
                    flex-direction: column;
                    align-items: stretch !important;
                    gap: 12px;
                }

                .dt-search,
                .dt-length {
                    width: 100%;
                }

                .dt-search input,
                .dt-length select {
                    width: 100%;
                }

                .assignment-cell {
                    min-width: 160px;
                }

                .nama-cell {
                    min-width: 160px;
                }

                #anomaliTable {
                    font-size: 0.8rem;
                }

                #anomaliTable thead th,
                #anomaliTable tbody td {
                    padding: 10px 8px !important;
                }

            }

            /* =====================================================
               RESPONSIVE - SMALL MOBILE
            ===================================================== */

            @media (max-width: 575.98px) {

                .card-title {
                    font-size: 14px;
                }

                .form-label {
                    font-size: 0.8rem;
                }

                .btn {
                    font-size: 0.8rem;
                    padding: 6px 12px;
                }

                .assignment-cell {
                    min-width: 140px;
                }

                .nama-cell {
                    min-width: 140px;
                }

                #anomaliTable {
                    font-size: 0.75rem;
                }

                #anomaliTable thead th,
                #anomaliTable tbody td {
                    padding: 8px 6px !important;
                }

                .status-badge {
                    padding: 4px 8px !important;
                    font-size: 0.7rem;
                }

            }

            /* =====================================================
               DARK MODE
            ===================================================== */

            [data-bs-theme="dark"] .btn-primary {
                box-shadow: 0 2px 6px rgba(86, 100, 234, 0.25);
            }

            [data-bs-theme="dark"] .btn-primary:hover {
                box-shadow: 0 4px 12px rgba(86, 100, 234, 0.35);
            }

        </style>
    @endpush


    <div class="row row-cards">

        {{-- =========================================================
            ACTIONS BAR
            (judul halaman sudah ditampilkan sekali lewat x-slot:header
            di layout, jadi di sini cukup deskripsi singkat + tombol aksi
            supaya tidak dobel dengan page-header)
        ========================================================== --}}
        <div class="col-12">

            <div class="card">

                <div
                    class="card-body
                            d-flex
                            flex-column
                            flex-md-row
                            align-items-start
                            align-items-md-center
                            justify-content-between
                            gap-3">

                    <div>

                        <p class="text-muted mb-0" style="font-size: 0.875rem;">
                            Lihat dan filter kasus aktif maupun tersembunyi.
                        </p>

                    </div>


                    <div class="btn-list">

                        {{-- =================================================
                            EXPORT BUTTON
                        ================================================== --}}
                        <form method="POST" action="{{ route('anomalies.export') }}" class="d-inline" id="exportForm">

                            @csrf

                            {{-- Bawa seluruh filter ke proses export --}}

                            @if (request('anomaly_type_id'))
                                <input type="hidden" name="anomaly_type_id" value="{{ request('anomaly_type_id') }}">
                            @endif

                            @if (request('status_penanganan'))
                                <input type="hidden" name="status_penanganan"
                                    value="{{ request('status_penanganan') }}">
                            @endif

                            @if (request('ppl_nama'))
                                <input type="hidden" name="ppl_nama" value="{{ request('ppl_nama') }}">
                            @endif

                            @if (request('pml_nama'))
                                <input type="hidden" name="pml_nama" value="{{ request('pml_nama') }}">
                            @endif

                            @if (request('taskforce_nama'))
                                <input type="hidden" name="taskforce_nama" value="{{ request('taskforce_nama') }}">
                            @endif

                            <button type="submit" class="btn btn-success">

                                <i class="ti ti-file-spreadsheet"></i>

                                Export Data Aktif

                            </button>

                        </form>


                        {{-- IMPORT BUTTON --}}

                        <a href="{{ route('anomalies.import') }}" class="btn btn-primary">

                            <i class="ti ti-file-import"></i>

                            Import Excel / CSV

                        </a>


                        {{-- DASHBOARD BUTTON --}}

                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">

                            <i class="ti ti-dashboard"></i>

                            Dashboard

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            FILTER SECTION
        ========================================================== --}}
        <div class="col-12">

            <div class="card">

                <div class="card-header">

                    <div>

                        <h3 class="card-title">
                            Filter Data
                        </h3>

                        <div class="text-muted" style="font-size: 0.875rem; margin-top: 4px;">
                            Gunakan filter untuk menampilkan kasus yang diperlukan.
                        </div>

                    </div>

                </div>


                <div class="card-body">

                    <form method="GET">

                        <div class="row g-3">

                            {{-- =================================================
                                TIPE ANOMALI
                            ================================================== --}}
                            <div class="col-12 col-md-6 col-lg-4">

                                <label class="form-label" for="anomaly_type_id">
                                    Filter Tipe Anomali
                                </label>

                                <select id="anomaly_type_id" name="anomaly_type_id" class="form-select">

                                    <option value="">
                                        Semua tipe
                                    </option>

                                    @foreach ($anomalyTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ request('anomaly_type_id') == $type->id ? 'selected' : '' }}>

                                            {{ $type->nama }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- =================================================
                                STATUS
                            ================================================== --}}
                            <div class="col-12 col-md-6 col-lg-4">

                                <label class="form-label" for="status_penanganan">
                                    Filter Status Penanganan
                                </label>

                                <select id="status_penanganan" name="status_penanganan" class="form-select">

                                    <option value="">
                                        Semua status
                                    </option>

                                    <option value="belum_ditangani"
                                        {{ request('status_penanganan') == 'belum_ditangani' ? 'selected' : '' }}>

                                        Belum ditangani

                                    </option>

                                    <option value="proses"
                                        {{ request('status_penanganan') == 'proses' ? 'selected' : '' }}>

                                        Proses

                                    </option>

                                    <option value="menunggu_konfirmasi"
                                        {{ request('status_penanganan') == 'menunggu_konfirmasi' ? 'selected' : '' }}>

                                        Menunggu konfirmasi

                                    </option>

                                    <option value="selesai"
                                        {{ request('status_penanganan') == 'selesai' ? 'selected' : '' }}>

                                        Selesai

                                    </option>

                                </select>

                            </div>


                            {{-- =================================================
                                PPL
                            ================================================== --}}
                            <div class="col-12 col-md-6 col-lg-4">

                                <label class="form-label" for="ppl_nama">
                                    Filter Nama PPL
                                </label>

                                <select id="ppl_nama" name="ppl_nama" class="form-select">

                                    <option value="">
                                        Semua PPL
                                    </option>

                                    @foreach ($pplOptions as $ppl)
                                        <option value="{{ $ppl }}"
                                            {{ request('ppl_nama') === $ppl ? 'selected' : '' }}>

                                            {{ $ppl }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- =================================================
                                PML
                            ================================================== --}}
                            <div class="col-12 col-md-6 col-lg-4">

                                <label class="form-label" for="pml_nama">
                                    Filter Nama PML
                                </label>

                                <select id="pml_nama" name="pml_nama" class="form-select">

                                    <option value="">
                                        Semua PML
                                    </option>

                                    @foreach ($pmlOptions as $pml)
                                        <option value="{{ $pml }}"
                                            {{ request('pml_nama') === $pml ? 'selected' : '' }}>

                                            {{ $pml }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- =================================================
                                TASK FORCE
                            ================================================== --}}
                            <div class="col-12 col-md-6 col-lg-4">

                                <label class="form-label" for="taskforce_nama">
                                    Filter Nama Taskforce
                                </label>

                                <select id="taskforce_nama" name="taskforce_nama" class="form-select">

                                    <option value="">
                                        Semua Taskforce
                                    </option>

                                    @foreach ($taskforceOptions as $taskforce)
                                        <option value="{{ $taskforce }}"
                                            {{ request('taskforce_nama') === $taskforce ? 'selected' : '' }}>

                                            {{ $taskforce }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- =================================================
                                BUTTON
                            ================================================== --}}
                            <div class="col-12 col-md-6 col-lg-4 d-flex align-items-end">

                                <button type="submit" class="btn btn-primary w-100">

                                    <i class="ti ti-filter"></i>

                                    Terapkan Filter

                                </button>

                            </div>

                        </div>

                    </form>


                    {{-- =====================================================
                        SUCCESS MESSAGE
                    ====================================================== --}}
                    @if (session('success'))
                        <div class="alert alert-success mt-4">

                            <i class="ti ti-check"></i>

                            {{ session('success') }}

                        </div>
                    @endif


                    {{-- =====================================================
                        SHOW HIDDEN TOGGLE
                    ====================================================== --}}
                    <div class="mt-4">

                        @if (($show ?? null) === 'hidden')
                            <a href="{{ route('anomalies.index', array_filter(request()->except('show'))) }}"
                                class="link-primary text-decoration-none"
                                style="font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">

                                <i class="ti ti-eye"></i>

                                Tampilkan hanya kasus aktif (run terbaru)

                            </a>
                        @else
                            <a href="{{ route('anomalies.index', array_merge(request()->all(), ['show' => 'hidden'])) }}"
                                class="link-primary text-decoration-none"
                                style="font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;">

                                <i class="ti ti-eye-off"></i>

                                Tampilkan kasus tersembunyi (tidak muncul di run terbaru)

                            </a>
                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            DATA TABLE SECTION
        ========================================================== --}}
        <div class="col-12">

            @if ($cases->isEmpty())

                {{-- =================================================
                    EMPTY STATE
                ================================================== --}}

                <div class="card">

                    <div class="card-body">

                        <div class="empty-state">

                            <div class="empty-state-icon">
                                <i class="ti ti-database-off"></i>
                            </div>

                            <div class="empty-state-title">
                                Belum ada data anomali
                            </div>

                            <div class="empty-state-text">
                                Silakan import file pertama Anda untuk memulai.
                            </div>

                        </div>

                    </div>

                </div>

            @else

                <div class="card">

                    <div class="card-header">

                        <div>
                            <h3 class="card-title">
                                Daftar Kasus Anomali
                            </h3>

                            <div class="text-muted" style="font-size: 0.875rem; margin-top: 4px;">
                                Menampilkan
                                <strong>{{ $cases->count() }}</strong>
                                kasus sesuai filter.
                            </div>
                        </div>

                        <div class="dropdown">

                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                <i class="ti ti-columns-3"></i>
                                Pilih Kolom

                            </button>

                            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 260px;">

                                <div class="fw-semibold mb-3" style="font-size: 0.875rem;">
                                    Tampilkan Kolom
                                </div>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="0"
                                        checked>

                                    <span class="form-check-label">
                                        ID SUBSLS
                                    </span>

                                </label>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="1"
                                        checked>

                                    <span class="form-check-label">
                                        Assignment
                                    </span>

                                </label>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="2"
                                        checked>

                                    <span class="form-check-label">
                                        Nama Usaha/Keluarga/Bangunan
                                    </span>

                                </label>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="3"
                                        checked>

                                    <span class="form-check-label">
                                        Tipe
                                    </span>

                                </label>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="4"
                                        checked>

                                    <span class="form-check-label">
                                        Status Penanganan
                                    </span>

                                </label>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="5">

                                    <span class="form-check-label">
                                        Terlihat di Run
                                    </span>

                                </label>

                                <label class="form-check mb-2">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="6">

                                    <span class="form-check-label">
                                        Times Seen
                                    </span>

                                </label>

                                <label class="form-check">

                                    <input class="form-check-input column-toggle" type="checkbox" data-column="7">

                                    <span class="form-check-label">
                                        Last Seen
                                    </span>

                                </label>

                            </div>

                        </div>

                    </div>


                    {{-- TABLE --}}

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table id="anomaliTable" class="table table-vcenter table-hover mb-0">

                                <thead>

                                    <tr>

                                        <th>
                                            ID SUBSLS
                                        </th>

                                        <th>
                                            Assignment
                                        </th>

                                        <th>
                                            Nama Usaha/Keluarga/Bangunan
                                        </th>

                                        <th>
                                            Tipe
                                        </th>

                                        <th>
                                            Status Penanganan
                                        </th>

                                        <th>
                                            Terlihat di Run
                                        </th>

                                        <th class="text-end">
                                            Times Seen
                                        </th>

                                        <th>
                                            Last Seen
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach ($cases as $case)
                                        @php

                                            /*
                                             * ==========================================
                                             * SNAPSHOT TERBARU
                                             * ==========================================
                                             */

                                            $latestSnapshot = $case->snapshots->last();

                                            $snapshotData = $latestSnapshot?->data_query ?? [];

                                            $namaUsaha = null;

                                            $idSubsls = null;

                                            /*
                                             * ==========================================
                                             * NORMALISASI DATA SNAPSHOT
                                             * ==========================================
                                             */

                                            foreach ($snapshotData as $key => $value) {
                                                $normalizedKey = strtolower(
                                                    preg_replace('/[^a-z0-9]+/', '_', trim((string) $key)),
                                                );

                                                /*
                                                 * NAMA
                                                 */

                                                if (
                                                    !empty($value) &&
                                                    in_array(
                                                        $normalizedKey,
                                                        [
                                                            'nama_usaha',
                                                            'nama_usaha_keluarga_bangunan',
                                                            'nama_keluarga',
                                                            'nama_bangunan',
                                                            'nama',
                                                            'usaha',
                                                            'keluarga',
                                                            'bangunan',
                                                            'nama_usaha_keluarga',
                                                            'nama_bangunan_keluarga',
                                                        ],
                                                        true,
                                                    )
                                                ) {
                                                    $namaUsaha = $value;
                                                }

                                                /*
                                                 * ID SUBSLS
                                                 */

                                                if (
                                                    !empty($value) &&
                                                    in_array(
                                                        $normalizedKey,
                                                        [
                                                            'kode_sls_subsls',
                                                            'kode_sls',
                                                            'kode_subsls',
                                                            'id_subsls',
                                                            'idsubsls',
                                                            'id_sub_sls',
                                                            'id_sls',
                                                            'subsls_id',
                                                            'subsl_id',
                                                            'sub_sls_id',
                                                            'kode_id_subsls',
                                                        ],
                                                        true,
                                                    )
                                                ) {
                                                    $idSubsls = $value;
                                                }
                                            }

                                            /*
                                             * ==========================================
                                             * STATUS STYLING
                                             * ==========================================
                                             */

                                            $status = $case->status_penanganan ?? null;

                                            $statusClass = match ($status) {
                                                'belum_ditangani' => 'bg-secondary-lt',

                                                'proses' => 'bg-blue-lt',

                                                'menunggu_konfirmasi' => 'bg-yellow-lt',

                                                'selesai' => 'bg-green-lt',

                                                default => 'bg-secondary-lt',
                                            };

                                            $statusLabel = match ($status) {
                                                'belum_ditangani' => 'Belum Ditangani',

                                                'proses' => 'Proses',

                                                'menunggu_konfirmasi' => 'Menunggu Konfirmasi',

                                                'selesai' => 'Selesai',

                                                default => '-',
                                            };

                                        @endphp


                                        <tr>

                                            {{-- =================================================
                                                ID SUBSLS
                                            ================================================== --}}

                                            <td>

                                                <span class="fw-semibold">

                                                    {{ $idSubsls ?? '-' }}

                                                </span>

                                            </td>


                                            {{-- =================================================
                                                ASSIGNMENT
                                            ================================================== --}}

                                            <td class="assignment-cell">

                                                <div class="assignment-id">

                                                    {{ $case->assignment_id ?? '-' }}

                                                </div>


                                                <div>

                                                    <a href="{{ route('anomalies.show', $case) }}"
                                                        class="link-primary detail-link">

                                                        <i class="ti ti-eye"></i>

                                                        Lihat detail case

                                                    </a>

                                                </div>

                                            </td>


                                            {{-- =================================================
                                                NAMA
                                            ================================================== --}}

                                            <td class="nama-cell">

                                                {{ $namaUsaha ?? '-' }}

                                            </td>


                                            {{-- =================================================
                                                TIPE
                                            ================================================== --}}

                                            <td>

                                                {{ optional($case->anomalyType)->nama ?? '-' }}

                                            </td>


                                            {{-- =================================================
                                                STATUS
                                            ================================================== --}}

                                            <td>

                                                <span
                                                    class="badge
                                                             status-badge
                                                             {{ $statusClass }}">

                                                    {{ $statusLabel }}

                                                </span>

                                            </td>


                                            {{-- =================================================
                                                RUN
                                            ================================================== --}}

                                            <td>

                                                {{ optional($case->latestRun)->id ?? '-' }}

                                            </td>


                                            {{-- =================================================
                                                TIMES SEEN
                                            ================================================== --}}

                                            <td class="text-end">

                                                {{ $case->times_seen ?? 0 }}

                                            </td>


                                            {{-- =================================================
                                                LAST SEEN
                                            ================================================== --}}

                                            <td class="text-nowrap">

                                                {{ $case->last_seen_at ? $case->last_seen_at->format('Y-m-d H:i') : '-' }}

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        DATATABLES JS
    ========================================================== --}}
    @push('scripts')
        <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const tableElement = document.querySelector('#anomaliTable');

                if (!tableElement) {
                    return;
                }

                const dataTable = new DataTable('#anomaliTable', {
                    paging: true,

                    pageLength: 10,

                    lengthMenu: [

                        [10, 25, 50, 100, -1],

                        [
                            '10',
                            '25',
                            '50',
                            '100',
                            'Semua'
                        ]

                    ],

                    searching: true,

                    ordering: true,

                    order: [
                        [7, 'desc']
                    ],

                    info: true,

                    responsive: true,

                    autoWidth: false,

                    language: {

                        search: 'Cari:',

                        lengthMenu: 'Tampilkan _MENU_ data',

                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                        infoEmpty: 'Tidak ada data',

                        zeroRecords: 'Data tidak ditemukan',

                        emptyTable: 'Tidak ada data yang tersedia',

                        paginate: {

                            first: 'Pertama',

                            last: 'Terakhir',

                            next: 'Berikutnya',

                            previous: 'Sebelumnya'

                        }

                    },

                    columnDefs: [

                        {
                            targets: [0, 1, 2, 3, 4, 5, 7],
                            className: 'text-start'
                        },

                        {
                            targets: [6],
                            className: 'text-end'
                        }

                    ]

                });

                document.querySelectorAll('.column-toggle')
                    .forEach(function(checkbox) {

                        checkbox.addEventListener('change', function() {

                            const columnIndex = parseInt(this.dataset.column);

                            dataTable.column(columnIndex).visible(this.checked);

                        });

                    });

            });
        </script>
    @endpush

</x-app-layout>
