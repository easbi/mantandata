<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anomali</title>

    <style>
        body {
                font-family: Inter, system-ui, sans-serif;
                margin: 40px;
                line-height: 1.5;
                background: #eef2ff;
            }

            .card {
                max-width: 1200px;
                margin: 0 auto;
                padding: 28px;
                border-radius: 24px;
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
                border: 1px solid rgba(148, 163, 184, 0.16);
            }

            .alert {
                padding: 14px 18px;
                margin-bottom: 20px;
                border-radius: 16px;
            }

            .alert-success {
                background: #ecfdf5;
                color: #166534;
                border: 1px solid #d1fae5;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 20px;
            }

            .actions a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 170px;
                padding: 12px 16px;
                background: #3730a3;
                color: white;
                text-decoration: none;
                border-radius: 14px;
                font-weight: 600;
                transition: transform 0.15s ease, background-color 0.15s ease;
            }

            .actions a:hover {
                background: #312e81;
                transform: translateY(-1px);
            }

            .actions a.secondary {
                background: #475569;
            }

            table.dataTable thead th {
                background-color: #e2e8f0;
                color: #0f172a;
                font-weight: 700;
                border-bottom: 0;
            }

            table.dataTable tbody tr {
                background: white;
            }

            table.dataTable tbody tr:hover {
                background-color: #f8fafc;
            }

            .filter-box {
                margin-bottom: 20px;
                border: 1px solid rgba(148, 163, 184, 0.32);
                border-radius: 18px;
                padding: 20px;
                background: #ffffff;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
            }

            .filter-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 16px;
                align-items: end;
            }

            .filter-item {
                display: grid;
                gap: 8px;
            }

            .filter-item label {
                font-size: 0.95rem;
                color: #334155;
            }

            .filter-item input,
            .filter-item select {
                width: 100%;
                min-height: 42px;
                padding: 10px 12px;
                border-radius: 12px;
                border: 1px solid #cbd5e1;
                background: #f8fafc;
                color: #0f172a;
                font-size: 0.95rem;
            }

            .filter-actions {
                margin-top: 18px;
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 12px 18px;
                border-radius: 999px;
                border: none;
                font-weight: 700;
                cursor: pointer;
                transition: transform 0.15s ease, opacity 0.15s ease;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            .btn.primary {
                background: #4338ca;
                color: #fff;
            }

            .btn.secondary {
                background: #e2e8f0;
                color: #0f172a;
            }

            .link-detail {
                font-size: 0.9rem;
                color: #4338ca;
                text-decoration: none;
            }

            .link-detail:hover {
                text-decoration: underline;
            }

            .toggle-link {
                font-size: 0.95rem;
                text-decoration: none;
                color: #4338ca;
        .toggle-link:hover {
            text-decoration: underline;
        }
    </style>

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="card">
        <h1>Daftar Kasus Anomali</h1>

        <div class="actions">
            <a href="{{ route('anomalies.import') }}">Import Excel / CSV</a>
            <a href="{{ route('dashboard') }}" class="secondary">Kembali ke Dashboard</a>
        </div>

        <form method="GET" class="filter-box">
            <div class="filter-grid">
                <div class="filter-item">
                    <label for="anomaly_type_id">Filter tipe anomali</label>
                    <select name="anomaly_type_id" id="anomaly_type_id">
                        <option value="">Semua tipe</option>
                        @foreach ($anomalyTypes as $type)
                            <option value="{{ $type->id }}" {{ request('anomaly_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="status_penanganan">Filter status penanganan</label>
                    <select name="status_penanganan" id="status_penanganan">
                        <option value="">Semua status</option>
                        <option value="belum_ditangani" {{ request('status_penanganan') == 'belum_ditangani' ? 'selected' : '' }}>
                            Belum ditangani
                        </option>
                        <option value="proses" {{ request('status_penanganan') == 'proses' ? 'selected' : '' }}>
                            Proses
                        </option>
                        <option value="menunggu_konfirmasi" {{ request('status_penanganan') == 'menunggu_konfirmasi' ? 'selected' : '' }}>
                            Menunggu konfirmasi
                        </option>
                        <option value="selesai" {{ request('status_penanganan') == 'selesai' ? 'selected' : '' }}>
                            Selesai
                        </option>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="ppl_nama">Filter nama PPL</label>
                    <select name="ppl_nama" id="ppl_nama">
                        <option value="">Semua PPL</option>
                        @foreach ($pplOptions as $ppl)
                            <option value="{{ $ppl }}" {{ request('ppl_nama') === $ppl ? 'selected' : '' }}>{{ $ppl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="pml_nama">Filter nama PML</label>
                    <select name="pml_nama" id="pml_nama">
                        <option value="">Semua PML</option>
                        @foreach ($pmlOptions as $pml)
                            <option value="{{ $pml }}" {{ request('pml_nama') === $pml ? 'selected' : '' }}>{{ $pml }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="taskforce_nama">Filter nama Taskforce</label>
                    <select name="taskforce_nama" id="taskforce_nama">
                        <option value="">Semua Taskforce</option>
                        @foreach ($taskforceOptions as $taskforce)
                            <option value="{{ $taskforce }}" {{ request('taskforce_nama') === $taskforce ? 'selected' : '' }}>{{ $taskforce }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn primary">Terapkan filter</button>
                <a href="{{ route('anomalies.index') }}" class="btn secondary">Reset filter</a>
            </div>

            @if(request()->has('show'))
                <input type="hidden" name="show" value="{{ request('show') }}">
            @endif
        </form>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div style="margin-bottom:12px;">
            @if (($show ?? null) === 'hidden')
                <a class="toggle-link" href="{{ route('anomalies.index', array_filter(request()->except('show'))) }}">
                    Tampilkan hanya kasus aktif (run terbaru)
                </a>
            @else
                <a class="toggle-link" href="{{ route('anomalies.index', array_merge(request()->all(), ['show' => 'hidden'])) }}">
                    Tampilkan kasus tersembunyi (tidak muncul di run terbaru)
                </a>
            @endif
        </div>

        @if ($cases->isEmpty())
            <p>Belum ada data anomali. Silakan import file pertama Anda.</p>
        @else
            <table id="anomaliTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>IDSUBSLS</th>
                        <th>Assignment</th>
                        <th>Nama Usaha/Keluarga/Bangunan</th>
                        <th>Tipe</th>
                        <th>Status Penanganan</th>
                        <th>Terlihat di Run</th>
                        <th>Times Seen</th>
                        <th>Last Seen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cases as $case)
                        @php
                            $latestSnapshot = $case->snapshots->last();
                            $snapshotData = $latestSnapshot?->data_query ?? [];
                            $namaUsaha = null;
                            $idSubsls = null;

                            foreach ($snapshotData as $key => $value) {
                                $normalizedKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim((string) $key)));

                                if (!empty($value) && in_array($normalizedKey, [
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
                                ], true)) {
                                    $namaUsaha = $value;
                                }

                                if (!empty($value) && in_array($normalizedKey, [
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
                                ], true)) {
                                    $idSubsls = $value;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $idSubsls ?? '-' }}</td>
                            <td>
                                {{ $case->assignment_id ?? '-' }}
                                <div style="margin-top: 4px;">
                                    <a href="{{ route('anomalies.show', $case) }}" class="link-detail">
                                        Lihat detail case
                                    </a>
                                </div>
                            </td>
                            <td>{{ $namaUsaha ?? '-' }}</td>
                            <td>{{ optional($case->anomalyType)->nama ?? '-' }}</td>
                            <td>{{ str_replace('_', ' ', $case->status_penanganan ?? '-') }}</td>
                            <td>{{ optional($case->latestRun)->id ?? '-' }}</td>
                            <td>{{ $case->times_seen ?? 0 }}</td>
                            <td>{{ $case->last_seen_at ? $case->last_seen_at->format('Y-m-d') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#anomaliTable').DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[7, 'desc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
</body>
</html>
