<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Case Anomali</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .card { max-width: 1000px; margin: 0 auto; padding: 24px; border: 1px solid #ddd; border-radius: 8px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 18px; }
        .box { background: #f8fafc; padding: 12px; border-radius: 6px; }
        .muted { color: #666; font-size: 0.9rem; }
        .timeline-item { border-left: 3px solid #cbd5e1; padding-left: 12px; margin: 12px 0; }
        .row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        select, textarea, button { padding: 8px 10px; border-radius: 6px; border: 1px solid #cbd5e1; }
        button { background: #2563eb; color: white; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Detail Case Anomali</h1>
        <p><a href="{{ route('anomalies.index') }}">&larr; Kembali ke daftar</a></p>

        @php $latestSnapshot = $case->snapshots->last(); @endphp

        @if (session('success'))
            <div class="box" style="background: #ecfdf3; margin-bottom: 12px;">{{ session('success') }}</div>
        @endif

        <div class="grid">
            <div class="box"><strong>Assignment ID</strong><br>{{ $case->assignment_id }}</div>
            <div class="box"><strong>Tipe</strong><br>{{ optional($case->anomalyType)->nama ?? '-' }}</div>
            <div class="box"><strong>Status Penanganan</strong><br>{{ str_replace('_', ' ', $case->status_penanganan) }}</div>
            <div class="box"><strong>PPL</strong><br>
                @if ($allocation?->ppl_nama && $allocation?->ppl_id)
                    {{ $allocation->ppl_nama }} ({{ $allocation->ppl_id }})
                @elseif ($allocation?->ppl_nama)
                    {{ $allocation->ppl_nama }}
                @elseif ($allocation?->ppl_id)
                    {{ $allocation->ppl_id }}
                @else
                    -
                @endif
            </div>
            <div class="box"><strong>PML</strong><br>
                @if ($allocation?->pml_nama && $allocation?->pml_id)
                    {{ $allocation->pml_nama }} ({{ $allocation->pml_id }})
                @elseif ($allocation?->pml_nama)
                    {{ $allocation->pml_nama }}
                @elseif ($allocation?->pml_id)
                    {{ $allocation->pml_id }}
                @else
                    -
                @endif
            </div>
            <div class="box"><strong>Taskforce</strong><br>
                @if ($allocation?->taskforce_nama && $allocation?->taskforce_id)
                    {{ $allocation->taskforce_nama }} ({{ $allocation->taskforce_id }})
                @elseif ($allocation?->taskforce_nama)
                    {{ $allocation->taskforce_nama }}
                @elseif ($allocation?->taskforce_id)
                    {{ $allocation->taskforce_id }}
                @else
                    -
                @endif
            </div>
            <div class="box"><strong>Times Seen</strong><br>{{ $case->times_seen }}</div>
            <div class="box"><strong>First Seen</strong><br>{{ $case->first_seen_at ? $case->first_seen_at->format('Y-m-d') : '-' }}</div>
            <div class="box"><strong>Last Seen</strong><br>{{ $case->last_seen_at ? $case->last_seen_at->format('Y-m-d') : '-' }}</div>
        </div>

        <div class="row">
            <form action="{{ route('anomalies.updateStatus', $case) }}" method="POST" style="min-width: 280px;">
                @csrf
                <label for="status_penanganan">Ubah Status Penanganan</label>
                <select name="status_penanganan" id="status_penanganan">
                    @foreach (['belum_ditangani','proses','menunggu_konfirmasi','selesai'] as $status)
                        <option value="{{ $status }}" {{ $case->status_penanganan === $status ? 'selected' : '' }}>{{ str_replace('_', ' ', $status) }}</option>
                    @endforeach
                </select>
                <label for="catatan_status" style="margin-top: 8px;">Catatan</label>
                <textarea name="catatan" id="catatan_status" rows="3" style="width: 100%;"></textarea>
                <button type="submit" style="margin-top: 8px;">Simpan Status</button>
            </form>

            <form action="{{ route('anomalies.storeFollowup', $case) }}" method="POST" style="min-width: 280px;">
                @csrf
                <label for="followup_status">Tambah Follow Up</label>
                <select name="status" id="followup_status">
                    @foreach (['belum_ditangani','proses','menunggu_konfirmasi','selesai'] as $status)
                        <option value="{{ $status }}">{{ str_replace('_', ' ', $status) }}</option>
                    @endforeach
                </select>
                <label for="catatan_followup" style="margin-top: 8px;">Catatan Follow Up</label>
                <textarea name="catatan" id="catatan_followup" rows="3" style="width: 100%;"></textarea>
                <button type="submit" style="margin-top: 8px;">Simpan Follow Up</button>
            </form>
        </div>

        <h3>Snapshot Terakhir</h3>
        @if ($case->snapshots->isEmpty())
            <p class="muted">Belum ada snapshot.</p>
        @else
            @php $latestSnapshot = $case->snapshots->last(); @endphp
            <div class="box">
                <div class="muted">Run ID: {{ $latestSnapshot->run_id }}</div>
                <pre>{{ json_encode($latestSnapshot->data_query, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        @endif

        <h3>Timeline</h3>
        @if ($case->activities->isEmpty() && $case->followups->isEmpty())
            <p class="muted">Belum ada aktivitas.</p>
        @else
            @php $timeline = collect($case->activities->toArray())->merge($case->followups->map(fn ($item) => [
                'type' => 'FOLLOWUP',
                'created_at' => $item->created_at,
                'message' => $item->catatan ?: 'Follow up dilakukan',
                'status' => $item->status,
                'user' => optional($item->user)->name ?? 'User',
            ])); @endphp
            @foreach ($timeline->sortByDesc('created_at') as $item)
                <div class="timeline-item">
                    <div class="muted">{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y H:i') }}</div>
                    @if (($item['type'] ?? null) === 'FOLLOWUP')
                        <strong>Follow Up</strong> — {{ $item['message'] }}
                        <div class="muted">Status: {{ str_replace('_', ' ', $item['status']) }} · Oleh: {{ $item['user'] }}</div>
                    @else
                        <strong>{{ $item['activity_type'] ?? '-' }}</strong> — {{ $item['payload']['pesan'] ?? '-' }}
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</body>
</html>
