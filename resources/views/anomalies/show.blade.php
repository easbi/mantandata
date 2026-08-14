<x-app-layout>
    <x-slot name="header">{{ __('Detail Case Anomali') }}</x-slot>

    <div class="row row-cards">
        @php
            $latestSnapshot = $case->snapshots->last();
            $fasihLink = $latestSnapshot?->data_query['link'] ?? null;

            // Jika link FASIH belum memiliki /edit, tambahkan otomatis
            if ($fasihLink && !str_ends_with(rtrim($fasihLink, '/'), '/edit')) {
                $fasihLink = rtrim($fasihLink, '/') . '/edit';
            }
        @endphp

        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div
                        class="d-flex flex-column flex-lg-row
                        align-items-start
                        align-items-lg-center
                        justify-content-between
                        gap-3">

                        {{-- Judul --}}
                        <div>
                            <h1 class="card-title mb-1">
                                Detail Case Anomali
                            </h1>

                            <p class="text-muted mb-0">
                                Informasi lengkap kasus dan follow-up terbaru.
                            </p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex flex-wrap gap-2">

                            {{-- Kembali --}}
                            <a href="{{ route('anomalies.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>
                                Kembali
                            </a>

                            {{-- Detail Penanganan --}}
                            <a href="#penanganan" class="btn btn-primary">
                                <i class="ti ti-clipboard-check me-1"></i>
                                Detail Penanganan
                            </a>

                            {{-- FASIH SM --}}
                            @if ($fasihLink)
                                <a href="{{ $fasihLink }}" target="_blank" rel="noopener noreferrer"
                                    class="btn btn-success">
                                    <i class="ti ti-edit me-1"></i>
                                    Buka FASIH SM
                                </a>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="ti ti-link-off me-1"></i>
                                    FASIH Tidak Tersedia
                                </button>
                            @endif

                        </div>

                    </div>

                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="col-12">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif

        <div class="col-12">
            <div class="row row-deck row-cards">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Assignment ID</div>
                            <div class="h3 mt-2">{{ $case->assignment_id }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Tipe</div>
                            <div class="h3 mt-2">{{ optional($case->anomalyType)->nama ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Status Penanganan</div>
                            <div class="h3 mt-2">{{ str_replace('_', ' ', $case->status_penanganan) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Times Seen</div>
                            <div class="h3 mt-2">{{ $case->times_seen }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">First Seen</div>
                            <div class="h3 mt-2">
                                {{ $case->first_seen_at ? $case->first_seen_at->format('Y-m-d') : '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Last Seen</div>
                            <div class="h3 mt-2">{{ $case->last_seen_at ? $case->last_seen_at->format('Y-m-d') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">PPL</div>
                            <div class="h3 mt-2">
                                @if ($allocation?->ppl_nama && $allocation?->ppl_id)
                                    {{ $allocation->ppl_nama }} ({{ $allocation->ppl_id }})
                                @elseif($allocation?->ppl_nama)
                                    {{ $allocation->ppl_nama }}
                                @elseif($allocation?->ppl_id)
                                    {{ $allocation->ppl_id }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">PML</div>
                            <div class="h3 mt-2">
                                @if ($allocation?->pml_nama && $allocation?->pml_id)
                                    {{ $allocation->pml_nama }} ({{ $allocation->pml_id }})
                                @elseif($allocation?->pml_nama)
                                    {{ $allocation->pml_nama }}
                                @elseif($allocation?->pml_id)
                                    {{ $allocation->pml_id }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Taskforce</div>
                            <div class="h3 mt-2">
                                @if ($allocation?->taskforce_nama && $allocation?->taskforce_id)
                                    {{ $allocation->taskforce_nama }} ({{ $allocation->taskforce_id }})
                                @elseif($allocation?->taskforce_nama)
                                    {{ $allocation->taskforce_nama }}
                                @elseif($allocation?->taskforce_id)
                                    {{ $allocation->taskforce_id }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="penanganan">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Penanganan Anomali</h3>
                        <div class="text-muted mt-1">
                            Perbarui status dan tambahkan catatan penanganan.
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('anomalies.storeFollowup', $case) }}" method="POST">
                        @csrf

                        <div class="row g-4">

                            {{-- Status --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="status">
                                    Status Penanganan
                                </label>

                                <select id="status" name="status" class="form-select" required>
                                    @foreach (['belum_ditangani', 'proses', 'menunggu_konfirmasi', 'selesai'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $case->status_penanganan === $status ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Catatan --}}
                            @php
                                $catatanTerakhir = $case->followups->last()?->catatan;
                            @endphp
                            <div class="col-12 col-md-8">
                                <label class="form-label" for="catatan">
                                    Catatan
                                </label>

                                <textarea id="catatan" name="catatan" rows="3" class="form-control"
                                    data-last-note="{{ $catatanTerakhir ?? '' }}" placeholder="Tuliskan catatan penanganan terbaru..."></textarea>

                                @if ($catatanTerakhir)
                                    <div class="form-hint">
                                        Catatan terakhir ditampilkan sebagai panduan. Ketik untuk membuat catatan
                                        penanganan baru.
                                    </div>
                                @else
                                    <div class="form-hint">
                                        Belum ada catatan penanganan sebelumnya.
                                    </div>
                                @endif
                            </div>

                            {{-- Button --}}
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Simpan Penanganan
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Snapshot Terakhir</h3>
                </div>
                <div class="card-body">
                    @if ($case->snapshots->isEmpty())
                        <p class="text-muted">Belum ada snapshot.</p>
                    @else
                        @php $latestSnapshot = $case->snapshots->last(); @endphp
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted">Run ID: {{ $latestSnapshot->run_id }}</p>
                                <pre class="overflow-x-auto rounded-2xl p-4 text-sm" style="background:#0f172a;color:#e6edf3">{{ json_encode($latestSnapshot->data_query, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Penanganan</h3>
                </div>

                <div class="card-body">

                    @if ($case->followups->isEmpty())

                        <p class="text-muted mb-0">
                            Belum ada riwayat penanganan.
                        </p>
                    @else
                        <div class="timeline timeline-split">

                            @foreach ($case->followups->sortByDesc('created_at') as $followup)
                                <div class="timeline-item">

                                    <div class="timeline-time">
                                        {{ $followup->created_at->format('d M Y H:i') }}
                                    </div>

                                    <div class="timeline-body">

                                        <div class="fw-semibold">
                                            {{ str_replace('_', ' ', $followup->status) }}
                                        </div>

                                        <div class="mt-1">
                                            {{ $followup->catatan ?: '-' }}
                                        </div>

                                        <div class="text-muted mt-2">
                                            Oleh:
                                            {{ $followup->user?->name ?? 'User' }}
                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('catatan');

            if (!textarea) return;

            const lastNote = textarea.dataset.lastNote;

            if (!lastNote) return;

            let showingLastNote = false;

            textarea.value = lastNote;
            textarea.classList.add('text-muted');
            showingLastNote = true;

            textarea.addEventListener('focus', function() {
                if (showingLastNote) {
                    textarea.value = '';
                    textarea.classList.remove('text-muted');
                    showingLastNote = false;
                }
            });
        });
    </script>

</x-app-layout>
