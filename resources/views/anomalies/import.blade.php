<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel Anomali</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .card { max-width: 700px; margin: 0 auto; padding: 24px; border: 1px solid #ddd; border-radius: 8px; }
        .alert { padding: 12px 16px; margin-bottom: 16px; border-radius: 6px; }
        .alert-success { background: #eaf7ed; color: #1f6f3f; }
        .alert-error { background: #fdeaea; color: #a61b1b; }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, select, button { width: 100%; padding: 10px; margin-top: 6px; border-radius: 6px; border: 1px solid #bbb; }
        button { background: #2563eb; color: white; cursor: pointer; }
        small { color: #666; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Import Excel Anomali</h1>
        <p>Unggah file Excel/CSV untuk memproses data anomali.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <p><a href="{{ route('anomalies.index') }}">Lihat daftar kasus yang sudah diimpor</a></p>

        <form action="{{ route('anomalies.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="anomaly_type_id">Pilih Tipe Anomali</label>
            <select name="anomaly_type_id" id="anomaly_type_id">
                <option value="">-- Buat baru / gunakan default --</option>
                @foreach ($anomalyTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->nama }} ({{ $type->kode }})</option>
                @endforeach
            </select>

            <label for="anomaly_type_kode">Kode Tipe Baru (opsional)</label>
            <input type="text" name="anomaly_type_kode" id="anomaly_type_kode" placeholder="NON_RESP">

            <label for="anomaly_type_nama">Nama Tipe Baru (opsional)</label>
            <input type="text" name="anomaly_type_nama" id="anomaly_type_nama" placeholder="Non Respon">

            <label for="tanggal_query">Tanggal Query</label>
            <input type="date" name="tanggal_query" id="tanggal_query">

            <label for="file">File Excel / CSV</label>
            <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required>
            <small>Kolom yang disarankan: assignment_id, nks, id_responden, kode_wilayah</small>

            <button type="submit" style="margin-top: 16px;">Import</button>
        </form>
    </div>
</body>
</html>
