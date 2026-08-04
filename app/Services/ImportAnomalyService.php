<?php

namespace App\Services;

use App\Models\AlokasiPetugas;
use App\Models\AnomalyActivity;
use App\Models\AnomalyCase;
use App\Models\AnomalyRun;
use App\Models\AnomalySnapshot;
use App\Models\AnomalyType;
use Illuminate\Support\Facades\DB;

class ImportAnomalyService
{
    /**
     * Import satu file hasil query anomali (sudah dalam bentuk array baris).
     *
     * @param  AnomalyType  $type
     * @param  array  $rows  hasil parsing Excel, tiap baris minimal punya assignment_id
     * @param  \Carbon\Carbon  $tanggalQuery  diambil dari nama file, mis. "Non Respon (23072026)"
     * @param  string|null  $namaFile
     * @param  int|null  $userId
     */
    public function import(AnomalyType $type, array $rows, $tanggalQuery, ?string $namaFile, ?int $userId): AnomalyRun
    {
        return DB::transaction(function () use ($type, $rows, $tanggalQuery, $namaFile, $userId) {

            $run = AnomalyRun::create([
                'anomaly_type_id' => $type->id,
                'tanggal_query' => $tanggalQuery,
                'nama_file' => $namaFile,
                'jumlah_data' => count($rows),
                'created_by' => $userId,
            ]);

            $caseBaru = 0;
            $caseLama = 0;
            $error = 0;

            foreach ($rows as $row) {
                try {
                    $this->processRow($type, $run, $row, $caseBaru, $caseLama);
                } catch (\Throwable $e) {
                    $error++;
                    report($e);
                }
            }

            $previousRun = AnomalyRun::where('anomaly_type_id', $type->id)
                ->where('id', '<', $run->id)
                ->latest('id')
                ->first();

            if ($previousRun) {
                $this->catatCaseYangHilang($type, $run, $previousRun);
            }

            $run->update([
                'jumlah_case_baru' => $caseBaru,
                'jumlah_case_lama' => $caseLama,
                'jumlah_error' => $error,
            ]);

            return $run;
        });
    }

    protected function processRow(AnomalyType $type, AnomalyRun $run, array $row, int &$caseBaru, int &$caseLama): void
    {
        $assignmentId = $row['assignment_id'];

        $anomalyKey = $this->generateKey($assignmentId, $type->kode, $row['nks'] ?? null, $row['id_responden'] ?? null);

        // join alokasi petugas (bukan live join saat tampil, tapi di-freeze saat import)
        $alokasi = AlokasiPetugas::where('assignment_id', $assignmentId)
            ->orderByDesc('periode')
            ->first();

        $existing = AnomalyCase::where('anomaly_key', $anomalyKey)->first();

        if (! $existing) {
            // CASE BARU
            $case = AnomalyCase::create([
                'anomaly_key' => $anomalyKey,
                'anomaly_type_id' => $type->id,
                'assignment_id' => $assignmentId,
                'nks' => $row['nks'] ?? null,
                'id_responden' => $row['id_responden'] ?? null,
                'kode_wilayah' => $alokasi->kode_wilayah ?? ($row['kode_wilayah'] ?? null),
                'status_penanganan' => 'belum_ditangani',
                'first_run_id' => $run->id,
                'latest_run_id' => $run->id,
                'first_seen_at' => $run->tanggal_query,
                'last_seen_at' => $run->tanggal_query,
                'times_seen' => 1,
            ]);

            $activityType = 'IMPORT';
            $caseBaru++;
        } else {
            // CASE LAMA — cek apakah ini "reopen" (sempat hide sebelum run ini)
            $wasHidden = ! $existing->isActiveOnLatestRun();

            $existing->update([
                'latest_run_id' => $run->id,
                'last_seen_at' => $run->tanggal_query,
                'times_seen' => $existing->times_seen + 1,
            ]);

            $case = $existing;
            $activityType = $wasHidden ? 'IMPORT_REOPEN' : 'IMPORT';
            $caseLama++;
        }

        $snapshot = AnomalySnapshot::create([
            'run_id' => $run->id,
            'case_id' => $case->id,
            'assignment_id' => $assignmentId,
            'data_query' => $row,
            'ppl_id' => $alokasi->ppl_id ?? null,
            'ppl_nama' => $alokasi->ppl_nama ?? null,
            'pml_id' => $alokasi->pml_id ?? null,
            'pml_nama' => $alokasi->pml_nama ?? null,
            'taskforce_id' => $alokasi->taskforce_id ?? null,
            'taskforce_nama' => $alokasi->taskforce_nama ?? null,
        ]);

        AnomalyActivity::create([
            'case_id' => $case->id,
            'activity_type' => $activityType,
            'reference_id' => $run->id,
            'activity_date' => $run->tanggal_query,
            'payload' => [
                'pesan' => match ($activityType) {
                    'IMPORT' => "Anomali ditemukan pada query {$run->tanggal_query->format('d-m-Y')}",
                    'IMPORT_REOPEN' => "Anomali muncul kembali setelah sempat hilang (query {$run->tanggal_query->format('d-m-Y')})",
                    default => 'Import',
                },
                'run_id' => $run->id,
                'snapshot_id' => $snapshot->id,
            ],
        ]);
    }

    /**
     * Setelah semua baris run diproses, catat case yang TIDAK muncul di run ini
     * agar tercatat di timeline sebagai "tidak muncul lagi" (opsional, untuk audit trail).
     * Dipanggil terpisah karena butuh bandingkan run sebelumnya vs run ini.
     */
    public function catatCaseYangHilang(AnomalyType $type, AnomalyRun $runBaru, AnomalyRun $runSebelumnya): void
    {
        $caseHilang = AnomalyCase::where('anomaly_type_id', $type->id)
            ->where('latest_run_id', $runSebelumnya->id) // masih nyangkut di run lama
            ->get();

        foreach ($caseHilang as $case) {
            AnomalyActivity::create([
                'case_id' => $case->id,
                'activity_type' => 'IMPORT_HIDDEN',
                'reference_id' => $runBaru->id,
                'activity_date' => $runBaru->tanggal_query,
                'payload' => [
                    'pesan' => "Anomali tidak ditemukan pada query {$runBaru->tanggal_query->format('d-m-Y')} (hide dari daftar aktif)",
                    'run_id' => $runBaru->id,
                ],
            ]);
        }
    }

    protected function generateKey(string $assignmentId, string $kodeAnomali, ?string $nks, ?string $idResponden): string
    {
        // Satu assignment bisa punya banyak jenis anomali, tapi hanya satu kasus per jenis.
        // Jika satu assignment bisa punya banyak responden dgn anomali sama, tambahkan nks/id_responden.
        $parts = array_filter([$assignmentId, $kodeAnomali, $nks, $idResponden]);

        return sha1(implode('|', $parts));
    }
}
