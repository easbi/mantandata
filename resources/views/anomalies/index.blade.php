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
            .column-toggle {
                cursor: pointer;
            }

            .dropdown-menu {
                box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
            }

            #anomaliTable {
                width: 100% !important;
            }

            #anomaliTable thead th {
                white-space: nowrap;
                background: #f8fafc;
                color: #4b5563;
                font-size: .78rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .3px;
                border-bottom: 1px solid #e5e7eb;
            }

            #anomaliTable tbody td {
                vertical-align: middle;
                font-size: .875rem;
            }

            #anomaliTable tbody tr:hover {
                background-color: #f8fafc;
            }

            /* =====================================================
                       ASSIGNMENT
                    ====================================================== */

            .assignment-cell {
                min-width: 220px;
                max-width: 300px;
            }

            .assignment-id {
                font-weight: 600;
                word-break: break-all;
                line-height: 1.4;
            }

            .detail-link {
                font-size: .8rem;
                text-decoration: none;
            }

            .detail-link:hover {
                text-decoration: underline;
            }

            /* =====================================================
                       NAMA USAHA
                    ====================================================== */

            .nama-cell {
                min-width: 220px;
                max-width: 350px;
                white-space: normal;
            }

            /* =====================================================
                       STATUS
                    ====================================================== */

            .status-badge {
                white-space: nowrap;
                font-size: .75rem;
            }

            /* =====================================================
                       DATATABLES
                    ====================================================== */

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
                color: #fff !important;
                border-color: #206bc4 !important;
            }

            .dt-info {
                color: #6b7280;
            }

            /* =====================================================
                       MOBILE
                    ====================================================== */

            @media (max-width: 768px) {

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

                .assignment-cell {
                    min-width: 180px;
                }

                .nama-cell {
                    min-width: 180px;
                }

            }
        </style>
    @endpush


    <div class="row row-cards">

        {{-- =========================================================
            HEADER
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

                        <h3 class="card-title mb-1">
                            Daftar Kasus Anomali
                        </h3>

                        <p class="text-muted mb-0">
                            Lihat dan filter kasus aktif maupun tersembunyi.
                        </p>

                    </div>


                    <div class="btn-list">

                        {{-- =================================================
                            EXPORT
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

                                <i class="ti ti-file-spreadsheet me-1"></i>

                                Export Data Aktif

                            </button>

                        </form>


                        {{-- IMPORT --}}

                        <a href="{{ route('anomalies.import') }}" class="btn btn-primary">

                            <i class="ti ti-file-import me-1"></i>

                            Import Excel / CSV

                        </a>


                        {{-- DASHBOARD --}}

                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">

                            <i class="ti ti-dashboard me-1"></i>

                            Dashboard

                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            FILTER
        ========================================================== --}}
        <div class="col-12">

            <div class="card">

                <div class="card-header">

                    <div>

                        <h3 class="card-title mb-1">
                            Filter Data
                        </h3>

                        <div class="text-muted small">
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
                            <div class="col-12 col-md-6 col-xl-4">

                                <label class="form-label" for="anomaly_type_id">

                                    Filter tipe anomali

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
                            <div class="col-12 col-md-6 col-xl-4">

                                <label class="form-label" for="status_penanganan">

                                    Filter status penanganan

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
                            <div class="col-12 col-md-6 col-xl-4">

                                <label class="form-label" for="ppl_nama">

                                    Filter nama PPL

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
                            <div class="col-12 col-md-6 col-xl-4">

                                <label class="form-label" for="pml_nama">

                                    Filter nama PML

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
                            <div class="col-12 col-md-6 col-xl-4">

                                <label class="form-label" for="taskforce_nama">

                                    Filter nama Taskforce

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
                            <div class="col-12 col-md-6 col-xl-4 d-flex align-items-end">

                                <button type="submit" class="btn btn-primary w-100">

                                    <i class="ti ti-filter me-1"></i>

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

                            <i class="ti ti-check me-1"></i>

                            {{ session('success') }}

                        </div>
                    @endif


                    {{-- =====================================================
                        SHOW HIDDEN
                    ====================================================== --}}
                    <div class="mt-4">

                        @if (($show ?? null) === 'hidden')
                            <a href="{{ route('anomalies.index', array_filter(request()->except('show'))) }}"
                                class="text-decoration-none">

                                <i class="ti ti-eye me-1"></i>

                                Tampilkan hanya kasus aktif
                                (run terbaru)

                            </a>
                        @else
                            <a href="{{ route('anomalies.index', array_merge(request()->all(), ['show' => 'hidden'])) }}"
                                class="text-decoration-none">

                                <i class="ti ti-eye-off me-1"></i>

                                Tampilkan kasus tersembunyi
                                (tidak muncul di run terbaru)

                            </a>
                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            DATA TABLE
        ========================================================== --}}
        <div class="col-12">

            @if ($cases->isEmpty())

                {{-- =================================================
                    EMPTY
                ================================================== --}}

                <div class="card">

                    <div class="card-body text-center text-muted py-5">

                        <i class="ti ti-database-off fs-2 d-block mb-2"></i>

                        <div class="fw-semibold">
                            Belum ada data anomali
                        </div>

                        <div class="small mt-1">
                            Silakan import file pertama Anda.
                        </div>

                    </div>

                </div>
            @else
                <div class="card">
                    <div class="card-header">

                        <div>
                            <h3 class="card-title mb-1">
                                Daftar Kasus Anomali
                            </h3>

                            <div class="text-muted small">
                                Menampilkan
                                <strong>{{ $cases->count() }}</strong>
                                kasus sesuai filter.
                            </div>
                        </div>

                        <div class="dropdown">

                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">

                                <i class="ti ti-columns-3 me-1"></i>
                                Pilih Kolom

                            </button>

                            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 240px;">

                                <div class="fw-semibold mb-2">
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

                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="anomaliTable" class="table table-vcenter table-hover">

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
                                             * STATUS
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


                                                <div class="mt-1">

                                                    <a href="{{ route('anomalies.show', $case) }}"
                                                        class="link-primary detail-link">

                                                        <i class="ti ti-eye me-1"></i>

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
            document.addEventListener(
                'DOMContentLoaded',
                function() {

                    const tableElement =
                        document.querySelector('#anomaliTable');


                    /*
                     * ================================================
                     * CEK TABLE
                     * ================================================
                     */

                    if (!tableElement) {
                        return;
                    }


                    /*
                     * ================================================
                     * DATATABLE
                     * ================================================
                     */

                    const dataTable = new DataTable(
                        '#anomaliTable', {
                            paging: true,

                            pageLength: 10,

                            lengthMenu: [

                                [10, 25, 50, 100, -1],

                                [
                                    10,
                                    25,
                                    50,
                                    100,
                                    'Semua'
                                ]

                            ],


                            /*
                             * SEARCH
                             */

                            searching: true,


                            /*
                             * SORTING
                             */

                            ordering: true,

                            order: [
                                [7, 'desc']
                            ],


                            /*
                             * INFO
                             */

                            info: true,


                            /*
                             * RESPONSIVE
                             */

                            responsive: true,


                            /*
                             * WIDTH
                             */

                            autoWidth: false,


                            /*
                             * LANGUAGE
                             */

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


                            /*
                             * COLUMN ALIGNMENT
                             */

                            columnDefs: [

                                {
                                    targets: [
                                        0,
                                        1,
                                        2,
                                        3,
                                        4,
                                        5,
                                        7
                                    ],

                                    className: 'text-start'
                                },

                                {
                                    targets: [6],

                                    className: 'text-end'
                                }

                            ]

                        }
                    );
                    document.querySelectorAll('.column-toggle')
                        .forEach(function(checkbox) {

                            checkbox.addEventListener(
                                'change',
                                function() {

                                    const columnIndex =
                                        parseInt(this.dataset.column);

                                    dataTable
                                        .column(columnIndex)
                                        .visible(this.checked);

                                }
                            );

                        });

                }
            );
        </script>
    @endpush

</x-app-layout>
