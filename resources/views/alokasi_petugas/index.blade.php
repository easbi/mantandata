<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Alokasi Petugas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f8fafc; }
        .card { max-width: 1200px; margin: 0 auto; padding: 24px; background: #fff; border: 1px solid #ddd; border-radius: 10px; }
        .alert { padding: 12px 16px; margin-bottom: 16px; border-radius: 6px; }
        .alert-success { background: #eaf7ed; color: #1f6f3f; }
        .alert-error { background: #fdeaea; color: #a61b1b; }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, select, button { width: 100%; padding: 10px; margin-top: 6px; border-radius: 6px; border: 1px solid #bbb; }
        button { background: #2563eb; color: white; cursor: pointer; }
        .grid { display: grid; gap: 20px; grid-template-columns: 1fr 1fr; }
        .table-wrapper { overflow-x: auto; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #f1f5f9; }
        .pagination { margin-top: 16px; }
        .pagination a { margin-right: 8px; text-decoration: none; color: #2563eb; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Master Alokasi Petugas</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="grid">
            <div>
                <h2>Upload file alokasi</h2>
                <form action="{{ route('alokasi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="periode">Periode alokasi</label>
                    <input type="date" name="periode" id="periode" value="{{ old('periode', now()->format('Y-m-d')) }}">

                    <label for="file">File Excel / CSV</label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required>
                    <small>Kolom minimal: assignment_id atau kode_wilayah. Jika hanya kode_wilayah diberikan, semua assignment di wilayah tersebut akan mengikuti master alokasi ini.</small>

                    <button type="submit" style="margin-top: 16px;">Upload Master Alokasi</button>
                </form>
            </div>

            <div>
                <h2>Petunjuk</h2>
                <p>File ini akan menjadi master alokasi petugas. Setiap import anomali akan menggunakan data alokasi terakhir berdasarkan <strong>assignment_id</strong> dan <strong>periode</strong>.</p>
                <p>Jika tidak ada alokasi khusus per assignment, sistem akan fallback ke alokasi berdasarkan <strong>kode_wilayah</strong> yang sama.</p>
                <p>Jika <strong>periode</strong> sama dan <strong>assignment_id</strong> sama, baris akan diupdate.</p>
                <p>Gunakan format header yang fleksibel: kolom akan dinormalisasi ke <code>snake_case</code>.</p>
            </div>
        </div>

        <div class="table-wrapper">
            <h2>Daftar Alokasi</h2>
            <table>
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
                            <td>
                                @if ($allocation->ppl_nama && $allocation->ppl_id)
                                    {{ $allocation->ppl_nama }} ({{ $allocation->ppl_id }})
                                @elseif ($allocation->ppl_nama)
                                    {{ $allocation->ppl_nama }}
                                @elseif ($allocation->ppl_id)
                                    {{ $allocation->ppl_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($allocation->pml_nama && $allocation->pml_id)
                                    {{ $allocation->pml_nama }} ({{ $allocation->pml_id }})
                                @elseif ($allocation->pml_nama)
                                    {{ $allocation->pml_nama }}
                                @elseif ($allocation->pml_id)
                                    {{ $allocation->pml_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($allocation->taskforce_nama && $allocation->taskforce_id)
                                    {{ $allocation->taskforce_nama }} ({{ $allocation->taskforce_id }})
                                @elseif ($allocation->taskforce_nama)
                                    {{ $allocation->taskforce_nama }}
                                @elseif ($allocation->taskforce_id)
                                    {{ $allocation->taskforce_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ optional($allocation->periode)->format('Y-m-d') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Belum ada data alokasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $allocations->links() }}
        </div>
    </div>
</body>
</html>
