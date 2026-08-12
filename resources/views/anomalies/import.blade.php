<x-app-layout>
    <x-slot name="header">{{ __('Import Excel Anomali') }}</x-slot>

    <div class="row row-cards">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import Excel Anomali</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Unggah file Excel/CSV untuk memproses data anomali.</p>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('anomalies.index') }}" class="btn btn-secondary mb-4">Lihat daftar kasus yang sudah diimpor</a>

                        <form action="{{ route('anomalies.import.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                            @csrf
                            <div class="col-12">
                                <label class="form-label" for="anomaly_type_id">Pilih Tipe Anomali</label>
                                <select id="anomaly_type_id" name="anomaly_type_id" class="form-select">
                                    <option value="">-- Buat baru / gunakan default --</option>
                                    @foreach ($anomalyTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->nama }} ({{ $type->kode }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="anomaly_type_kode">Kode Tipe Baru (opsional)</label>
                                <input id="anomaly_type_kode" name="anomaly_type_kode" type="text" placeholder="NON_RESP" class="form-control" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="anomaly_type_nama">Nama Tipe Baru (opsional)</label>
                                <input id="anomaly_type_nama" name="anomaly_type_nama" type="text" placeholder="Non Respon" class="form-control" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="tanggal_query">Tanggal Query</label>
                                <input id="tanggal_query" name="tanggal_query" type="date" class="form-control" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="file">File Excel / CSV</label>
                                <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required class="form-control" />
                                <span class="form-hint">Kolom yang disarankan: assignment_id, nks, id_responden, kode_wilayah.</span>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
