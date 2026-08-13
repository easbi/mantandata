<?php

namespace App\Exports;

use App\Models\AlokasiPetugas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ExportAnomalyCase implements FromCollection, WithHeadings, WithMapping
{
    protected $cases;

    public function __construct(Collection $cases)
    {
        $this->cases = $cases;
    }

    public function collection()
    {
        return $this->cases;
    }

    public function headings(): array
    {
        return [
            'Anomaly Key',
            'Assignment ID',
            'NKS',
            'Status Penanganan',
            'First Seen At',
            'Last Seen At',
            'Times Seen',
            'Nama Usaha/Keluarga/Bangunan',
            'Tipe Anomali',
            'Nama PPL',
            'Nama PML',
            'Nama Taskforce',
            'Status Penanganan',
            'Follow Up Terakhir',
            'Tanggal Follow Up Terakhir',
            'Catatan Follow Up Terakhir',
            'Link Detail Anomali',
            'Link FASIH-SM'
        ];
    }

    public function map($case): array
    {
        // Ambil snapshot terbaru hanya untuk data hasil query
        $latestSnapshot = $case->snapshots->last();
        $snapshotData = $latestSnapshot?->data_query ?? [];

        $linkFasih = null;
        if (is_array($snapshotData)) {
            $linkFasih = $snapshotData['link'] ?? null;

            if (!empty($linkFasih)) {
                $linkFasih = rtrim($linkFasih, '/');
                if (!str_ends_with($linkFasih, '/edit')) {
                    $linkFasih .= '/edit';
                }
            }
        }

        // Ambil nama usaha/keluarga/bangunan dari snapshot
        $namaUsaha = null;

        if (is_array($snapshotData)) {
            foreach ($snapshotData as $key => $value) {
                $normalizedKey = strtolower(
                    preg_replace(
                        '/[^a-z0-9]+/',
                        '_',
                        trim((string) $key)
                    )
                );

                if (
                    !empty($value) &&
                    in_array($normalizedKey, [
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
                    ], true)
                ) {
                    $namaUsaha = $value;
                    break;
                }
            }
        }

        /*
         * ==========================================================
         * AMBIL PETUGAS DARI TABEL ALOKASI PETUGAS
         * ==========================================================
         */

        $allocation = null;

        if (!empty($case->kode_wilayah) && $case->kode_wilayah !== '-') {

            // 1. Exact match
            $allocation = AlokasiPetugas::where(
                'kode_wilayah',
                $case->kode_wilayah
            )
                ->orderByDesc('periode')
                ->first();

            // 2. Normalized match
            if (!$allocation) {
                $normalized = strtoupper(
                    preg_replace(
                        '/\s+/',
                        '',
                        $case->kode_wilayah
                    )
                );

                $allocation = AlokasiPetugas::whereRaw(
                    'REPLACE(UPPER(kode_wilayah), " ", "") = ?',
                    [$normalized]
                )
                    ->orderByDesc('periode')
                    ->first();
            }

            // 3. Like match
            if (!$allocation) {
                $allocation = AlokasiPetugas::where(
                    'kode_wilayah',
                    'like',
                    '%' . $case->kode_wilayah . '%'
                )
                    ->orderByDesc('periode')
                    ->first();
            }
        }

        /*
         * Jika alokasi berdasarkan kode wilayah tidak ditemukan,
         * coba berdasarkan Assignment ID.
         */
        if (!$allocation && !empty($case->assignment_id)) {
            $allocation = AlokasiPetugas::where(
                'assignment_id',
                $case->assignment_id
            )
                ->orderByDesc('periode')
                ->first();
        }

        $pplNama = $allocation?->ppl_nama ?? '-';
        $pmlNama = $allocation?->pml_nama ?? '-';
        $taskforceNama = $allocation?->taskforce_nama ?? '-';


        // Ambil follow up terakhir
        $latestFollowup = $case->followups
            ->sortByDesc('created_at')
            ->first();

        $statusPenanganan = $case->status_penanganan ?? '-';

        $followupTerakhir = $latestFollowup
            ? str_replace('_', ' ', $latestFollowup->status ?? '-')
            : '-';

        $tanggalFollowup = $latestFollowup?->created_at
            ? $latestFollowup->created_at->format('Y-m-d H:i')
            : '-';

        $catatanFollowup = $latestFollowup?->catatan ?? '-';

        return [
            $case->anomaly_key ?? '-',
            $case->assignment_id ?? '-',
            $case->kode_wilayah ?? '-',

            str_replace(
                '_',
                ' ',
                $case->status_penanganan ?? '-'
            ),

            $case->first_seen_at
            ? $case->first_seen_at->format('Y-m-d')
            : '-',

            $case->last_seen_at
            ? $case->last_seen_at->format('Y-m-d')
            : '-',

            $case->times_seen ?? 0,

            $namaUsaha ?? '-',

            optional($case->anomalyType)->nama ?? '-',

            $pplNama,
            $pmlNama,
            $taskforceNama,
            $statusPenanganan,
            $followupTerakhir,
            $tanggalFollowup,
            $catatanFollowup,
            '=HYPERLINK("' . route('anomalies.show', $case) . '","Buka Detail Anomali")',
            $linkFasih
            ? '=HYPERLINK("' . $linkFasih . '","Buka FASIH SM")'
            : '-',
        ];
    }
}
