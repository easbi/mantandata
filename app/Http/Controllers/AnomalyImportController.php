<?php

namespace App\Http\Controllers;

use App\Imports\AnomalyExcelImport;
use App\Models\AnomalyCase;
use App\Models\AnomalyType;
use App\Services\ImportAnomalyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AnomalyImportController extends Controller
{
    public function index(Request $request)
    {
        $query = AnomalyCase::query()->with(['anomalyType', 'latestRun', 'snapshots']);

        if ($request->filled('anomaly_type_id')) {
            $query->where('anomaly_type_id', $request->anomaly_type_id);
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

        $show = $request->get('show');

        return view('anomalies.index', compact('cases', 'anomalyTypes', 'show'));
    }

    public function show(AnomalyCase $case)
    {
        $case->load(['anomalyType', 'latestRun', 'snapshots', 'followups.user', 'activities']);

        return view('anomalies.show', compact('case'));
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
                'pesan' => 'Status penanganan diperbarui menjadi '.str_replace('_', ' ', $request->status_penanganan),
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
                'pesan' => 'Follow up ditambahkan: '.($request->catatan ?: 'Tanpa catatan'),
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
        $absolutePath = storage_path('app/'.$path);

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

        return redirect()->route('anomalies.index')->with('success', 'Import berhasil. '.$run->jumlah_case_baru.' case baru, '.$run->jumlah_case_lama.' case lama.');
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

        return collect($rows)
            ->filter(static fn ($row) => is_array($row) && count(array_filter($row, static fn ($value) => $value !== null && $value !== '')) > 0)
            ->map(function ($row) {
                $normalized = [];

                foreach ($row as $key => $value) {
                    $normalizedKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', trim((string) $key)));
                    $normalized[$normalizedKey] = $value;
                }

                return $normalized;
            })
            ->values()
            ->all();
    }
}
