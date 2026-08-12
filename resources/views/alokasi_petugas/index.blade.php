<x-app-layout>
    <x-slot name="header">{{ __('Master Alokasi Petugas') }}</x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div>
                            <h1 class="card-title">Master Alokasi Petugas</h1>
                            <p class="text-muted mb-0">Kelola data alokasi berdasarkan wilayah dan taskforce.</p>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row row-deck row-cards">
                <div class="col-12 col-xl-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Upload file alokasi</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('alokasi.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label" for="periode">Periode alokasi</label>
                                    <input id="periode" name="periode" type="date" value="{{ old('periode', now()->format('Y-m-d')) }}" class="form-control" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="file">File Excel / CSV</label>
                                    <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required class="form-control" />
                                    <span class="form-hint">Kolom minimal: assignment_id atau kode_wilayah. Jika hanya kode_wilayah diberikan, semua assignment di wilayah tersebut akan mengikuti master alokasi ini.</span>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Upload Master Alokasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Petunjuk</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">File ini akan menjadi master alokasi petugas. Setiap import anomali akan menggunakan data alokasi terakhir berdasarkan <strong>assignment_id</strong> dan <strong>periode</strong>.</p>
                            <p class="text-muted">Jika tidak ada alokasi khusus per assignment, sistem akan fallback ke alokasi berdasarkan <strong>kode_wilayah</strong> yang sama.</p>
                            <p class="text-muted">Jika <strong>periode</strong> sama dan <strong>assignment_id</strong> sama, baris akan diupdate.</p>
                            <p class="text-muted">Gunakan format header yang fleksibel: kolom akan dinormalisasi ke <code>snake_case</code>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Alokasi</h3>
                    <span class="text-muted">{{ $allocations->total() }} baris</span>
                </div>
                <div class="table-responsive">
                    <table id="allocationTable" class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>Kode Wilayah</th>
                                <th>Nama Wilayah</th>
                                <th>PPL</th>
                                <th>PML</th>
                                <th>Taskforce</th>
                                <th>Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allocations as $allocation)
                                <tr>
                                    <td>{{ $allocation->kode_wilayah ?? '-' }}</td>
                                    <td>{{ $allocation->nama_wilayah ?? '-' }}</td>
                                    <td>{{ $allocation->ppl_nama ? ($allocation->ppl_id ? $allocation->ppl_nama . ' (' . $allocation->ppl_id . ')' : $allocation->ppl_nama) : ($allocation->ppl_id ?? '-') }}</td>
                                    <td>{{ $allocation->pml_nama ? ($allocation->pml_id ? $allocation->pml_nama . ' (' . $allocation->pml_id . ')' : $allocation->pml_nama) : ($allocation->pml_id ?? '-') }}</td>
                                    <td>{{ $allocation->taskforce_nama ? ($allocation->taskforce_id ? $allocation->taskforce_nama . ' (' . $allocation->taskforce_id . ')' : $allocation->taskforce_nama) : ($allocation->taskforce_id ?? '-') }}</td>
                                    <td>{{ optional($allocation->periode)->format('Y-m-d') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data alokasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $allocations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#allocationTable').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: false,
                order: [[5, 'desc']],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        });
    </script>
@endpush
