<x-app-layout>
    <x-slot name="header">{{ __('Daftar Kasus Anomali') }}</x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div>
                        <h3 class="card-title">Daftar Kasus Anomali</h3>
                        <p class="text-muted mb-0">Lihat dan filter kasus aktif maupun tersembunyi.</p>
                    </div>
                    <div class="btn-list">
                        <a href="{{ route('anomalies.import') }}" class="btn btn-primary">Import Excel / CSV</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label" for="anomaly_type_id">Filter tipe anomali</label>
                            <select id="anomaly_type_id" name="anomaly_type_id" class="form-select">
                                <option value="">Semua tipe</option>
                                @foreach ($anomalyTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('anomaly_type_id') == $type->id ? 'selected' : '' }}>{{ $type->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label" for="status_penanganan">Filter status penanganan</label>
                            <select id="status_penanganan" name="status_penanganan" class="form-select">
                                <option value="">Semua status</option>
                                <option value="belum_ditangani" {{ request('status_penanganan') == 'belum_ditangani' ? 'selected' : '' }}>Belum ditangani</option>
                                <option value="proses" {{ request('status_penanganan') == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="menunggu_konfirmasi" {{ request('status_penanganan') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu konfirmasi</option>
                                <option value="selesai" {{ request('status_penanganan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label" for="ppl_nama">Filter nama PPL</label>
                            <select id="ppl_nama" name="ppl_nama" class="form-select">
                                <option value="">Semua PPL</option>
                                @foreach ($pplOptions as $ppl)
                                    <option value="{{ $ppl }}" {{ request('ppl_nama') === $ppl ? 'selected' : '' }}>{{ $ppl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label" for="pml_nama">Filter nama PML</label>
                            <select id="pml_nama" name="pml_nama" class="form-select">
                                <option value="">Semua PML</option>
                                @foreach ($pmlOptions as $pml)
                                    <option value="{{ $pml }}" {{ request('pml_nama') === $pml ? 'selected' : '' }}>{{ $pml }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <label class="form-label" for="taskforce_nama">Filter nama Taskforce</label>
                            <select id="taskforce_nama" name="taskforce_nama" class="form-select">
                                <option value="">Semua Taskforce</option>
                                @foreach ($taskforceOptions as $taskforce)
                                    <option value="{{ $taskforce }}" {{ request('taskforce_nama') === $taskforce ? 'selected' : '' }}>{{ $taskforce }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Terapkan filter</button>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="alert alert-success mt-4">{{ session('success') }}</div>
                    @endif

                    <div class="mt-4 text-muted">
                        @if (($show ?? null) === 'hidden')
                            <a href="{{ route('anomalies.index', array_filter(request()->except('show'))) }}">Tampilkan hanya kasus aktif (run terbaru)</a>
                        @else
                            <a href="{{ route('anomalies.index', array_merge(request()->all(), ['show' => 'hidden'])) }}">Tampilkan kasus tersembunyi (tidak muncul di run terbaru)</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            @if ($cases->isEmpty())
                <div class="card">
                    <div class="card-body text-center text-muted">Belum ada data anomali. Silakan import file pertama Anda.</div>
                </div>
            @else
                <div class="card">
                    <div class="table-responsive">
                        <table id="anomaliTable" class="table card-table table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>IDSUBSLS</th>
                                    <th>Assignment</th>
                                    <th>Nama Usaha/Keluarga/Bangunan</th>
                                    <th>Tipe</th>
                                    <th>Status Penanganan</th>
                                    <th>Terlihat di Run</th>
                                    <th class="text-end">Times Seen</th>
                                    <th class="text-end">Last Seen</th>
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
                                            <div class="text-muted text-truncate mt-1">
                                                <a href="{{ route('anomalies.show', $case) }}" class="link-secondary">Lihat detail case</a>
                                            </div>
                                        </td>
                                        <td>{{ $namaUsaha ?? '-' }}</td>
                                        <td>{{ optional($case->anomalyType)->nama ?? '-' }}</td>
                                        <td>{{ str_replace('_', ' ', $case->status_penanganan ?? '-') }}</td>
                                        <td>{{ optional($case->latestRun)->id ?? '-' }}</td>
                                        <td class="text-end">{{ $case->times_seen ?? 0 }}</td>
                                        <td class="text-end">{{ $case->last_seen_at ? $case->last_seen_at->format('Y-m-d') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

@push('scripts')
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
@endpush
