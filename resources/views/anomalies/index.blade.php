<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anomali</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.5; }
        .card { max-width: 1100px; margin: 0 auto; padding: 24px; border: 1px solid #ddd; border-radius: 8px; }
        .alert { padding: 12px 16px; margin-bottom: 16px; border-radius: 6px; }
        .alert-success { background: #eaf7ed; color: #1f6f3f; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        a { color: #2563eb; }
        .actions { margin-bottom: 16px; }
        .actions a { display: inline-block; padding: 8px 12px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Daftar Kasus Anomali</h1>
        <div class="actions">
            <a href="{{ route('anomalies.import') }}">Import Excel / CSV</a>
            <a href="{{ route('dashboard') }}" style="margin-left: 8px; background: #4b5563;">Kembali ke Dashboard</a>
        </div>

        <form method="GET" style="margin-bottom: 16px;">
            <label for="anomaly_type_id" style="display: inline-block; margin-right: 8px; font-weight: 600;">Filter tipe anomali:</label>
            <select name="anomaly_type_id" id="anomaly_type_id" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach ($anomalyTypes as $type)
                    <option value="{{ $type->id }}" {{ request('anomaly_type_id') == $type->id ? 'selected' : '' }}>{{ $type->nama }}</option>
                @endforeach
            </select>
        </form>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div style="margin-bottom:12px;">
            @if (($show ?? null) === 'hidden')
                <a href="{{ route('anomalies.index', array_filter(request()->except('show'))) }}" style="font-size:0.95rem;">Tampilkan hanya kasus aktif (run terbaru)</a>
            @else
                <a href="{{ route('anomalies.index', array_merge(request()->all(), ['show' => 'hidden'])) }}" style="font-size:0.95rem;">Tampilkan kasus tersembunyi (tidak muncul di run terbaru)</a>
            @endif
        </div>

        @if ($cases->isEmpty())
            <p>Belum ada data anomali. Silakan import file pertama Anda.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>IDSLS</th>
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
                                    break;
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                {{ $case->assignment_id ?? '-' }}
                                <div style="margin-top: 4px;">
                                    <a href="{{ route('anomalies.show', $case) }}" style="font-size: 0.9rem; color: #2563eb;">Lihat detail case</a>
                                </div>
                            </td>
                            <td>{{ optional($case->latestRun)->id ?? '-' }}</td>
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
</body>
</html>
