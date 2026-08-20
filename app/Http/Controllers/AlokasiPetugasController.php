<?php

namespace App\Http\Controllers;

use App\Exports\AlokasiPetugasTemplateExport;
use App\Imports\AnomalyExcelImport;
use App\Models\AlokasiPetugas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AlokasiPetugasController extends Controller
{
    public function index(Request $request)
    {
        $query = AlokasiPetugas::query();

        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', 'like', '%'.$request->assignment_id.'%');
        }

        $allocations = $query->orderBy('assignment_id')->orderByDesc('periode')->paginate(25);

        return view('alokasi_petugas.index', compact('allocations'));
    }

    public function template()
    {
        return Excel::download(
            new AlokasiPetugasTemplateExport,
            'template_alokasi_petugas.xlsx'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            'periode' => ['nullable', 'date'],
        ]);

        $periode = $request->filled('periode')
            ? Carbon::parse($request->periode)->startOfDay()
            : Carbon::today()->startOfDay();

        $file = $request->file('file');
        $path = $file->store('imports');
        $absolutePath = storage_path('app/'.$path);
        $rows = $this->readRows($absolutePath);

        if (empty($rows)) {
            return back()->with('error', 'File tidak berisi data yang bisa diimpor.');
        }

        $processed = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $kodeWilayah = $row['kode_wilayah'] ?? null;

            if (! $kodeWilayah) {
                $skipped++;
                continue;
            }

            $data = [
                'assignment_id' => $row['assignment_id'] ?? null,
                'kode_wilayah' => $kodeWilayah,
                'nama_wilayah' => $row['nama_wilayah'] ?? null,
                'ppl_id' => $row['ppl_id'] ?? null,
                'ppl_nama' => $row['ppl_nama'] ?? $row['ppl'] ?? null,
                'pml_id' => $row['pml_id'] ?? null,
                'pml_nama' => $row['pml_nama'] ?? $row['pml'] ?? null,
                'taskforce_id' => $row['taskforce_id'] ?? null,
                'taskforce_nama' => $row['taskforce_nama'] ?? $row['taskforce'] ?? null,
                'periode' => $periode,
            ];

            $record = AlokasiPetugas::updateOrCreate([
                'kode_wilayah' => $kodeWilayah,
                'periode' => $periode,
            ], $data);

            $processed++;
            if ($record->wasChanged()) {
                $updated++;
            }
        }

        return back()->with('success', "Upload master alokasi selesai. Baris berhasil diproses: {$processed}. Terlewatkan: {$skipped}. Perubahan: {$updated}.");
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
                    $normalizedValue = trim((string) $value);
                    if ($normalizedValue === '-' || $normalizedValue === '') {
                        $normalizedValue = null;
                    }
                    $normalized[$normalizedKey] = $normalizedValue;
                }

                return $normalized;
            })
            ->values()
            ->all();
    }
}
