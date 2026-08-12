<x-app-layout>
    <x-slot name="header">
        Dashboard Anomali
    </x-slot>

    {{-- =========================
        CDN DATATABLES
    ========================== --}}
    @push('styles')
        <link rel="stylesheet"
              href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css">

        <style>
            /* =========================
               GLOBAL DASHBOARD
            ========================== */

            .dashboard-title {
                font-weight: 700;
                letter-spacing: -0.3px;
            }

            .dashboard-subtitle {
                font-size: 0.875rem;
                color: #6b7280;
            }

            /* =========================
               STAT CARD
            ========================== */

            .stat-card {
                border: 1px solid #e5e7eb;
                transition: all .2s ease;
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
            }

            .stat-label {
                font-size: .8rem;
                color: #6b7280;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .3px;
            }

            .stat-value {
                font-size: 1.75rem;
                font-weight: 700;
                line-height: 1.2;
            }

            .stat-icon {
                width: 42px;
                height: 42px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
            }

            .icon-primary {
                background: #e8f1ff;
                color: #206bc4;
            }

            .icon-warning {
                background: #fff4d6;
                color: #d99a00;
            }

            .icon-info {
                background: #e5f6fb;
                color: #0b7285;
            }

            .icon-success {
                background: #e8f7ee;
                color: #2b8a3e;
            }

            /* =========================
               FILTER
            ========================== */

            .filter-card {
                border: 1px solid #e5e7eb;
            }

            .filter-title {
                font-weight: 600;
            }

            .form-select {
                min-height: 40px;
            }

            /* =========================
               STATUS CARD
            ========================== */

            .status-item {
                border: 1px solid #edf0f3;
                border-radius: 10px;
                padding: 14px 16px;
                background: #fff;
                transition: all .2s ease;
            }

            .status-item:hover {
                border-color: #d9dee5;
                box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
            }

            .status-label {
                font-size: .85rem;
                color: #6b7280;
            }

            .status-number {
                font-size: 1.5rem;
                font-weight: 700;
            }

            /* =========================
               ANOMALI TYPE
            ========================== */

            .anomaly-item {
                padding: 12px 0;
                border-bottom: 1px solid #edf0f3;
            }

            .anomaly-item:last-child {
                border-bottom: 0;
            }

            .anomaly-name {
                font-weight: 500;
                font-size: .9rem;
            }

            .anomaly-count {
                font-weight: 700;
                font-size: .9rem;
            }

            .progress {
                background: #eef1f4;
            }

            /* =========================
               TASK FORCE TABLE
            ========================== */

            #taskforceTable {
                width: 100% !important;
            }

            #taskforceTable thead th {
                background: #f8fafc;
                color: #4b5563;
                font-size: .78rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .3px;
                white-space: nowrap;
                border-bottom: 1px solid #e5e7eb;
            }

            #taskforceTable tbody td {
                vertical-align: middle;
                font-size: .875rem;
            }

            #taskforceTable tbody tr:hover {
                background-color: #f8fafc;
            }

            #taskforceTable td:first-child {
                font-weight: 500;
            }

            .number-cell {
                font-weight: 600;
                text-align: right;
            }

            /* DataTables */
            .dt-container {
                font-size: .875rem;
            }

            .dt-search input {
                border: 1px solid #d9dee5 !important;
                border-radius: 7px !important;
                padding: 7px 10px !important;
                margin-left: 5px !important;
            }

            .dt-length select {
                border: 1px solid #d9dee5 !important;
                border-radius: 7px !important;
                padding: 6px 28px 6px 8px !important;
            }

            .dt-paging-button {
                border-radius: 6px !important;
            }

            .dt-paging-button.current {
                background: #206bc4 !important;
                color: white !important;
                border-color: #206bc4 !important;
            }

            .dt-info {
                color: #6b7280;
            }

            /* =========================
               RESPONSIVE
            ========================== */

            @media (max-width: 768px) {

                .stat-value {
                    font-size: 1.5rem;
                }

                .card-header {
                    padding: 1rem;
                }

                .card-body {
                    padding: 1rem;
                }

                .dt-layout-row {
                    flex-direction: column;
                    gap: 10px;
                    align-items: flex-start !important;
                }

                .dt-search {
                    width: 100%;
                }

                .dt-search input {
                    width: 100%;
                    margin-left: 0 !important;
                }
            }
        </style>
    @endpush


    <div class="row row-cards">

        {{-- =====================================================
            HEADER
        ====================================================== --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row
                                align-items-md-center
                                justify-content-between gap-3">

                        <div>
                            <h2 class="dashboard-title mb-1">
                                Dashboard Anomali
                            </h2>

                            <div class="dashboard-subtitle">
                                Ringkasan kasus aktif, status penanganan,
                                dan monitoring Task Force.
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('anomalies.import') }}"
                               class="btn btn-primary">
                                <i class="ti ti-file-import me-1"></i>
                                Import File
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        {{-- =====================================================
            FILTER
        ====================================================== --}}
        <div class="col-12">
            <div class="card filter-card">

                <div class="card-header">
                    <div>
                        <h3 class="card-title filter-title mb-1">
                            Filter Dashboard
                        </h3>

                        <div class="text-muted small">
                            Gunakan filter untuk melihat data sesuai kebutuhan.
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <form method="GET">

                        <div class="row g-3">

                            {{-- Jenis Anomali --}}
                            <div class="col-12 col-md-4">

                                <label class="form-label fw-semibold"
                                       for="anomaly_type_id">
                                    Jenis Anomali
                                </label>

                                <select id="anomaly_type_id"
                                        name="anomaly_type_id"
                                        class="form-select">

                                    <option value="">
                                        Semua jenis
                                    </option>

                                    @foreach ($anomalyTypes as $type)

                                        <option value="{{ $type->id }}"
                                            {{ $anomalyTypeId == $type->id ? 'selected' : '' }}>

                                            {{ $type->nama }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- Run --}}
                            <div class="col-12 col-md-4">

                                <label class="form-label fw-semibold"
                                       for="run_id">

                                    Periode / Run

                                </label>

                                <select id="run_id"
                                        name="run_id"
                                        class="form-select">

                                    <option value="">
                                        Run terbaru
                                    </option>

                                    @foreach ($runOptions as $run)

                                        <option value="{{ $run->id }}"
                                            {{ (int) $runId === $run->id ? 'selected' : '' }}>

                                            {{ $run->tanggal_query
                                                ? $run->tanggal_query->format('Y-m-d')
                                                : $run->created_at->format('Y-m-d') }}

                                            -
                                            {{ $run->anomalyType->nama ?? 'Unknown' }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- Wilayah --}}
                            <div class="col-12 col-md-4">

                                <label class="form-label fw-semibold"
                                       for="kode_wilayah">

                                    Wilayah

                                </label>

                                <select id="kode_wilayah"
                                        name="kode_wilayah"
                                        class="form-select">

                                    <option value="">
                                        Semua wilayah
                                    </option>

                                    @foreach ($wilayahOptions as $wilayah)

                                        <option value="{{ $wilayah }}"
                                            {{ $kodeWilayah === $wilayah ? 'selected' : '' }}>

                                            {{ $wilayah }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-12 d-flex justify-content-end">

                                <button type="submit"
                                        class="btn btn-primary">

                                    <i class="ti ti-filter me-1"></i>
                                    Terapkan Filter

                                </button>

                            </div>

                        </div>

                    </form>

                </div>
            </div>
        </div>


        {{-- =====================================================
            STATISTIK UTAMA
        ====================================================== --}}
        <div class="col-12">

            <div class="row row-cards">

                {{-- Total --}}
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        align-items-center
                                        justify-content-between">

                                <div>
                                    <div class="stat-label">
                                        Total Anomali Aktif
                                    </div>

                                    <div class="stat-value mt-2">
                                        {{ number_format($totalActive) }}
                                    </div>
                                </div>

                                <div class="stat-icon icon-primary">
                                    <i class="ti ti-alert-circle"></i>
                                </div>

                            </div>

                            <div class="text-muted small mt-3">
                                Kasus yang muncul pada run terbaru.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Baru --}}
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        align-items-center
                                        justify-content-between">

                                <div>

                                    <div class="stat-label">
                                        Anomali Baru
                                    </div>

                                    <div class="stat-value mt-2">
                                        {{ number_format($newCasesCount) }}
                                    </div>

                                </div>

                                <div class="stat-icon icon-warning">
                                    <i class="ti ti-alert-triangle"></i>
                                </div>

                            </div>

                            <div class="text-muted small mt-3">
                                Pertama kali muncul pada run terbaru.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Proses --}}
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        align-items-center
                                        justify-content-between">

                                <div>

                                    <div class="stat-label">
                                        Sedang Diproses
                                    </div>

                                    <div class="stat-value mt-2">
                                        {{ number_format($statusCounts['proses'] ?? 0) }}
                                    </div>

                                </div>

                                <div class="stat-icon icon-info">
                                    <i class="ti ti-loader"></i>
                                </div>

                            </div>

                            <div class="text-muted small mt-3">
                                Kasus yang sedang ditangani.
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Selesai --}}
                <div class="col-12 col-sm-6 col-xl-3">

                    <div class="card stat-card h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        align-items-center
                                        justify-content-between">

                                <div>

                                    <div class="stat-label">
                                        Selesai
                                    </div>

                                    <div class="stat-value mt-2">
                                        {{ number_format($statusCounts['selesai'] ?? 0) }}
                                    </div>

                                </div>

                                <div class="stat-icon icon-success">
                                    <i class="ti ti-circle-check"></i>
                                </div>

                            </div>

                            <div class="text-muted small mt-3">
                                Kasus yang telah selesai ditangani.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            STATUS + ANOMALI PER JENIS
        ====================================================== --}}

        <div class="col-12 col-lg-6">

            <div class="card h-100">

                <div class="card-header">

                    <div>
                        <h3 class="card-title mb-1">
                            Status Penanganan
                        </h3>

                        <div class="text-muted small">
                            Distribusi status kasus aktif.
                        </div>
                    </div>

                </div>


                <div class="card-body">

                    <div class="row g-3">

                        {{-- Belum --}}
                        <div class="col-12 col-sm-6">

                            <div class="status-item">

                                <div class="status-label">
                                    Belum Ditindaklanjuti
                                </div>

                                <div class="status-number mt-1">
                                    {{ number_format($statusCounts['belum_ditangani'] ?? 0) }}
                                </div>

                            </div>

                        </div>


                        {{-- Proses --}}
                        <div class="col-12 col-sm-6">

                            <div class="status-item">

                                <div class="status-label">
                                    Sedang Diproses
                                </div>

                                <div class="status-number mt-1">
                                    {{ number_format($statusCounts['proses'] ?? 0) }}
                                </div>

                            </div>

                        </div>


                        {{-- Menunggu --}}
                        <div class="col-12 col-sm-6">

                            <div class="status-item">

                                <div class="status-label">
                                    Menunggu Konfirmasi
                                </div>

                                <div class="status-number mt-1">
                                    {{ number_format($statusCounts['menunggu_konfirmasi'] ?? 0) }}
                                </div>

                            </div>

                        </div>


                        {{-- Selesai --}}
                        <div class="col-12 col-sm-6">

                            <div class="status-item">

                                <div class="status-label">
                                    Selesai
                                </div>

                                <div class="status-number mt-1">
                                    {{ number_format($statusCounts['selesai'] ?? 0) }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            ANOMALI PER JENIS
        ====================================================== --}}

        <div class="col-12 col-lg-6">

            <div class="card h-100">

                <div class="card-header">

                    <div class="w-100 d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <h3 class="card-title mb-1">
                                Anomali per Jenis
                            </h3>

                            <div class="text-muted small">
                                Jumlah kasus berdasarkan jenis anomali.
                            </div>

                        </div>

                        <span class="badge bg-secondary-lt">
                            {{ $anomalyTypeCounts->count() }} jenis
                        </span>

                    </div>

                </div>


                <div class="card-body">

                    @php
                        $maxCount = $anomalyTypeCounts->max('total') ?: 1;
                    @endphp


                    @if ($anomalyTypeCounts->isEmpty())

                        <div class="empty">

                            <div class="empty-icon">
                                <i class="ti ti-database-off"></i>
                            </div>

                            <p class="empty-title">
                                Tidak ada data
                            </p>

                            <p class="empty-subtitle text-muted">
                                Tidak ada kasus aktif untuk filter yang dipilih.
                            </p>

                        </div>

                    @else

                        @foreach ($anomalyTypeCounts as $count)

                            <div class="anomaly-item">

                                <div class="d-flex
                                            justify-content-between
                                            align-items-center
                                            mb-2">

                                    <div class="anomaly-name">
                                        {{ $count->nama }}
                                    </div>

                                    <div class="anomaly-count">
                                        {{ number_format($count->total) }}
                                    </div>

                                </div>


                                <div class="progress"
                                     style="height: 7px;">

                                    <div class="progress-bar"
                                         role="progressbar"
                                         style="
                                            width:
                                            {{ $maxCount > 0
                                                ? round(($count->total / $maxCount) * 100, 2)
                                                : 0
                                            }}%;
                                         ">
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @endif

                </div>

            </div>

        </div>


        {{-- =====================================================
            MONITORING TASK FORCE
        ====================================================== --}}

        <div class="col-12">

            <div class="card">

                <div class="card-header">

                    <div class="w-100 d-flex
                                flex-column flex-md-row
                                justify-content-between
                                align-items-md-center
                                gap-2">

                        <div>

                            <h3 class="card-title mb-1">
                                Monitoring Task Force
                            </h3>

                            <div class="text-muted small">
                                Status penanganan kasus berdasarkan Task Force.
                            </div>

                        </div>

                        <span class="badge bg-primary-lt">
                            Total Grup:
                            {{ $taskforceStats->count() }}
                        </span>

                    </div>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table id="taskforceTable"
                               class="table table-vcenter table-hover">

                            <thead>

                                <tr>

                                    <th>
                                        Nama Task Force
                                    </th>

                                    <th class="text-end">
                                        Total
                                    </th>

                                    <th class="text-end">
                                        Belum
                                    </th>

                                    <th class="text-end">
                                        Proses
                                    </th>

                                    <th class="text-end">
                                        Selesai
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($taskforceStats as $task)

                                    <tr>

                                        <td>
                                            {{ $task->taskforce_nama }}
                                        </td>

                                        <td class="number-cell">
                                            {{ number_format($task->total) }}
                                        </td>

                                        <td class="number-cell">
                                            {{ number_format($task->belum) }}
                                        </td>

                                        <td class="number-cell">
                                            {{ number_format($task->proses) }}
                                        </td>

                                        <td class="number-cell">
                                            {{ number_format($task->selesai) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="5"
                                            class="text-center text-muted py-5">

                                            <i class="ti ti-database-off fs-2 d-block mb-2"></i>

                                            Tidak ditemukan data Task Force.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
        DATATABLES JS VIA SRC
    ====================================================== --}}

    @push('scripts')

        {{-- jQuery --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        {{-- DataTables --}}
        <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>


        <script>

            document.addEventListener('DOMContentLoaded', function () {

                new DataTable('#taskforceTable', {

                    paging: true,

                    pageLength: 10,

                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'Semua']
                    ],

                    searching: true,

                    ordering: true,

                    order: [
                        [1, 'desc']
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
                            targets: 0,
                            className: 'text-start'
                        },

                        {
                            targets: [1, 2, 3, 4],
                            className: 'text-end'
                        }

                    ]

                });

            });

        </script>

    @endpush

</x-app-layout>
