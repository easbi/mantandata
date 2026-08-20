<?php

namespace App\Http\Controllers;

use App\Exports\ExportAnomalyCase;
use App\Imports\AnomalyExcelImport;
use App\Models\AlokasiPetugas;
use App\Models\AnomalyCase;
use App\Models\AnomalyType;
use App\Models\AnomalyRun;
use App\Services\ImportAnomalyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AnomalyImportController extends Controller
{
    public function index(Request $request)
    {
        $query = AnomalyCase::query()->with(['anomalyType', 'latestRun', 'snapshots']);

        if ($request->filled('anomaly_type_id')) {
            $query->where('anomaly_type_id', $request->anomaly_type_id);
        }

        if ($request->filled('status_penanganan')) {
            $query->where('status_penanganan', $request->status_penanganan);
        }

        if ($request->filled('ppl_nama') || $request->filled('pml_nama') || $request->filled('taskforce_nama')) {
            $query->whereExists(function ($subQuery) use ($request) {
                $subQuery->select(DB::raw('1'))
                    ->from('alokasi_petugas')
                    ->whereRaw('alokasi_petugas.kode_wilayah = LEFT(anomaly_cases.nks, 16)')
                    ->when($request->filled('ppl_nama'), function ($subQuery) use ($request) {
                        $subQuery->where('alokasi_petugas.ppl_nama', 'like', '%' . $request->ppl_nama . '%');
                    })
                    ->when($request->filled('pml_nama'), function ($subQuery) use ($request) {
                        $subQuery->where('alokasi_petugas.pml_nama', 'like', '%' . $request->pml_nama . '%');
                    })
                    ->when($request->filled('taskforce_nama'), function ($subQuery) use ($request) {
                        $subQuery->where('alokasi_petugas.taskforce_nama', 'like', '%' . $request->taskforce_nama . '%');
                    });
            });
        }

        // Default: show cases that are present on the latest run for their anomaly type.
        // If ?show=hidden is provided, show cases that were removed from the latest run.
        if ($request->get('show') === 'hidden') {
            $query->hidden($request->anomaly_type_id)->orderByDesc('last_seen_at');
        } else {
            $query->active($request->anomaly_type_id)->orderByDesc('last_seen_at');
        }

        $cases = $query->get();
        $anomalyTypes = AnomalyType::orderBy('nama')->get();

        $pplOptions = AlokasiPetugas::query()
            ->whereNotNull('ppl_nama')
            ->where('ppl_nama', '<>', '')
            ->distinct()
            ->orderBy('ppl_nama')
            ->pluck('ppl_nama');

        $pmlOptions = AlokasiPetugas::query()
            ->whereNotNull('pml_nama')
            ->where('pml_nama', '<>', '')
            ->distinct()
            ->orderBy('pml_nama')
            ->pluck('pml_nama');

        $taskforceOptions = AlokasiPetugas::query()
            ->whereNotNull('taskforce_nama')
            ->where('taskforce_nama', '<>', '')
            ->distinct()
            ->orderBy('taskforce_nama')
            ->pluck('taskforce_nama');

        $show = $request->get('show');

        return view('anomalies.index', compact('cases', 'anomalyTypes', 'show', 'pplOptions', 'pmlOptions', 'taskforceOptions'));
    }

    public function show(AnomalyCase $case)
    {
        $case->load(['anomalyType', 'latestRun', 'snapshots', 'followups.user', 'activities']);

        //  Alokasi Petugas
        $allocation = null;
        $resolvedKodeWilayah = $case->kode_wilayah;

        // Cari alokasi berdasarkan 16 digit pertama NKS
        if (!empty($case->nks) && $case->nks !== '-') {
            $kodeAlokasi = substr(trim($case->nks), 0, 16);

            $allocation = AlokasiPetugas::where(
                'kode_wilayah',
                $kodeAlokasi
            )
                ->orderByDesc('periode')
                ->first();
        }

        // If still not found, attempt to match by assignment_id present in the latest snapshot
        if (!$allocation) {
            $latestSnapshot = $case->snapshots->last();
            $snapshotData = $latestSnapshot?->data_query ?? [];
            $possibleAssignment = null;

            foreach ($snapshotData as $k => $v) {
                $normalizedKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim((string) $k)));
                if (in_array($normalizedKey, ['assignment_id', 'assignment', 'id_assignment'], true) && $v) {
                    $possibleAssignment = trim((string) $v);
                    break;
                }
            }

            if ($possibleAssignment) {
                $allocation = AlokasiPetugas::where('assignment_id', $possibleAssignment)
                    ->orderByDesc('periode')
                    ->first();
            }
        }


        // Riwayat Anomali Case
        $allRuns = AnomalyRun::where('anomaly_type_id', $case->anomaly_type_id)
            ->orderByDesc('tanggal_query')
            ->orderByDesc('id')
            ->get();

        $snapshotsByRun = $case->snapshots->groupBy('run_id');

        return view('anomalies.show', compact('case', 'allocation', 'resolvedKodeWilayah', 'allRuns', 'snapshotsByRun'));
    }

    private function resolveKodeWilayahFromSnapshot($snapshot): ?string
    {
        if (!$snapshot) {
            return null;
        }

        $data = $snapshot->data_query ?? [];
        if (!is_array($data)) {
            return null;
        }

        $candidates = [
            'kode_wilayah',
            'kode_sls_subsls',
            'kode_sls',
            'kode_subsls',
            'id_subsls',
            'idsubsls',
            'id_sub_sls',
            'id_sls',
            'subsls_id',
            'sub_sls_id',
            'kode_id_subsls',
        ];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim((string) $key)));
            if (in_array($normalizedKey, $candidates, true)) {
                $value = trim((string) $value);
                if ($value !== '' && $value !== '-') {
                    return $value;
                }
            }
        }

        return null;
    }

    public function updateStatus(Request $request, AnomalyCase $case)
    {
        $request->validate([
            'status_penanganan' => ['required', 'in:belum_ditangani,proses,menunggu_konfirmasi,selesai'],
            'catatan' => ['nullable', 'string'],
        ]);

        $case->update(['status_penanganan' => $request->status_penanganan]);

        $case->followups()->create([
            'snapshot_id' => $case->snapshots()->latest('created_at')->value('id'),
            'user_id' => auth()->id() ?? 1,
            'status' => $request->status_penanganan,
            'catatan' => $request->catatan,
            'lampiran' => null,
        ]);

        $case->activities()->create([
            'activity_type' => 'FOLLOWUP',
            'reference_id' => $case->followups()->latest('created_at')->first()?->id,
            'activity_date' => Carbon::today(),
            'payload' => [
                'pesan' => 'Status penanganan diperbarui menjadi ' . str_replace('_', ' ', $request->status_penanganan),
                'status' => $request->status_penanganan,
            ],
        ]);

        return back()->with('success', 'Status penanganan berhasil diperbarui.');
    }

    public function storeFollowup(Request $request, AnomalyCase $case)
    {
        $request->validate([
            'status' => ['required', 'in:belum_ditangani,proses,menunggu_konfirmasi,selesai'],
            'catatan' => ['nullable', 'string'],
        ]);

        $followup = $case->followups()->create([
            'snapshot_id' => $case->snapshots()->latest('created_at')->value('id'),
            'user_id' => auth()->id() ?? 1,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'lampiran' => null,
        ]);

        $case->update(['status_penanganan' => $request->status]);

        $case->activities()->create([
            'activity_type' => 'FOLLOWUP',
            'reference_id' => $followup->id,
            'activity_date' => Carbon::today(),
            'payload' => [
                'pesan' => 'Follow up ditambahkan: ' . ($request->catatan ?: 'Tanpa catatan'),
                'status' => $request->status,
            ],
        ]);

        return back()->with('success', 'Follow up berhasil disimpan.');
    }

    public function create()
    {
        $anomalyTypes = AnomalyType::orderBy('nama')->get();

        return view('anomalies.import', compact('anomalyTypes'));
    }

    public function store(Request $request, ImportAnomalyService $service)
    {
        $request->validate([
            'anomaly_type_id' => ['nullable', 'exists:anomaly_types,id'],
            'anomaly_type_kode' => ['nullable', 'string', 'max:50'],
            'anomaly_type_nama' => ['nullable', 'string', 'max:100'],
            'tanggal_query' => ['nullable', 'date'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $type = $this->resolveType($request);
        $file = $request->file('file');
        $path = $file->store('imports');
        $absolutePath = storage_path('app/' . $path);

        $rows = $this->readRows($absolutePath);

        if (empty($rows)) {
            return back()->with('error', 'File tidak berisi data yang bisa diimpor.');
        }

        $tanggalQuery = $request->filled('tanggal_query')
            ? Carbon::parse($request->tanggal_query)
            : Carbon::today();

        $run = $service->import(
            $type,
            $rows,
            $tanggalQuery,
            $file->getClientOriginalName(),
            auth()->id()
        );

        return redirect()->route('anomalies.index')->with('success', 'Import berhasil. ' . $run->jumlah_case_baru . ' case baru, ' . $run->jumlah_case_lama . ' case lama.');
    }

    protected function resolveType(Request $request): AnomalyType
    {
        if ($request->filled('anomaly_type_id')) {
            return AnomalyType::findOrFail($request->anomaly_type_id);
        }

        $kode = trim((string) $request->input('anomaly_type_kode', 'NON_RESP')) ?: 'NON_RESP';
        $nama = trim((string) $request->input('anomaly_type_nama', 'Non Respon')) ?: 'Non Respon';

        $kode = strtoupper(preg_replace('/[^A-Z0-9_]/', '', str_replace(['-', ' '], '_', $kode)));
        $kode = substr($kode, 0, 20);

        if ($kode === '') {
            $kode = 'NON_RESP';
        }

        return AnomalyType::firstOrCreate(
            ['kode' => $kode],
            [
                'nama' => $nama,
                'deskripsi' => 'Diimport dari Excel',
                'kolom_wajib' => ['assignment_id'],
                'kolom_tampil' => ['assignment_id', 'nks'],
                'form_followup' => [],
                'aktif' => true,
            ]
        );
    }

    protected function readRows(string $path): array
    {
        $sheets = Excel::toArray(new AnomalyExcelImport, $path);
        $rows = $sheets[0] ?? [];

        $columnAliases = [
            'link' => 'link_fasih',
            'link_fasih' => 'link_fasih',
            'link_fasih_sm' => 'link_fasih',
            'link_fasih_sm_' => 'link_fasih',
        ];

        return collect($rows)
            ->filter(
                static fn($row) =>
                is_array($row) &&
                count(array_filter(
                    $row,
                    static fn($value) =>
                    $value !== null &&
                    $value !== '' &&
                    $value !== '-'
                )) > 0
            )
            ->map(function ($row) use ($columnAliases) {

                $normalized = [];

                foreach ($row as $key => $value) {

                    // Normalisasi nama kolom
                    $normalizedKey = strtolower(
                        preg_replace(
                            '/[^a-z0-9]+/',
                            '_',
                            trim((string) $key)
                        )
                    );

                    // Mapping alias nama kolom
                    $normalizedKey = $columnAliases[$normalizedKey]
                        ?? $normalizedKey;

                    // Normalisasi nilai
                    $normalizedValue = trim((string) $value);

                    if ($normalizedValue === '' || $normalizedValue === '-') {
                        $normalizedValue = null;
                    }

                    $normalized[$normalizedKey] = $normalizedValue;
                }

                return $normalized;
            })
            ->values()
            ->all();
    }

    /**
     * Export data anomali aktif dengan filter sesuai yang diterapkan
     * Hanya mengambil case yang masih muncul di run terbaru
     */
    public function export(Request $request)
    {
        // Gunakan logic filter yang sama seperti index()
        $query = AnomalyCase::query()->with([
            'anomalyType',
            'latestRun',
            'snapshots',
            'followups'
        ]);

        if ($request->filled('anomaly_type_id')) {
            $query->where('anomaly_type_id', $request->anomaly_type_id);
        }

        if ($request->filled('status_penanganan')) {
            $query->where('status_penanganan', $request->status_penanganan);
        }

        if ($request->filled('ppl_nama') || $request->filled('pml_nama') || $request->filled('taskforce_nama')) {
            $query->whereExists(function ($subQuery) use ($request) {
                $subQuery->select(DB::raw('1'))
                    ->from('alokasi_petugas')
                    ->whereRaw('alokasi_petugas.kode_wilayah = LEFT(anomaly_cases.nks, 16)')
                    ->when($request->filled('ppl_nama'), function ($subQuery) use ($request) {
                        $subQuery->where('alokasi_petugas.ppl_nama', 'like', '%' . $request->ppl_nama . '%');
                    })
                    ->when($request->filled('pml_nama'), function ($subQuery) use ($request) {
                        $subQuery->where('alokasi_petugas.pml_nama', 'like', '%' . $request->pml_nama . '%');
                    })
                    ->when($request->filled('taskforce_nama'), function ($subQuery) use ($request) {
                        $subQuery->where('alokasi_petugas.taskforce_nama', 'like', '%' . $request->taskforce_nama . '%');
                    });
            });
        }

        // Default: hanya case yang muncul di run terbaru (show=hidden tidak diterapkan untuk export)
        $query->active($request->anomaly_type_id)->orderByDesc('last_seen_at');

        $cases = $query->get();

        // Generate filename dengan informasi filter dan timestamp
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $typeInfo = $request->filled('anomaly_type_id')
            ? AnomalyType::find($request->anomaly_type_id)?->kode ?? 'export'
            : 'all';
        $filename = "anomali_export_{$typeInfo}_{$timestamp}.xlsx";

        return Excel::download(new ExportAnomalyCase($cases), $filename);
    }
}
