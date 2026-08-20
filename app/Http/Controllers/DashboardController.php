<?php

namespace App\Http\Controllers;

use App\Models\AlokasiPetugas;
use App\Models\AnomalyCase;
use App\Models\AnomalyRun;
use App\Models\AnomalyType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $anomalyTypeId = $request->query('anomaly_type_id');
        $runId = $request->query('run_id');
        $kodeWilayah = $request->query('kode_wilayah');

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $baseQuery = AnomalyCase::query()
            ->when(
                $runId,
                fn($query) => $query->where('latest_run_id', $runId),
                fn($query) => $query->active($anomalyTypeId)
            )
            ->when(
                $anomalyTypeId,
                fn($query) => $query->where(
                    'anomaly_type_id',
                    $anomalyTypeId
                )
            )
            ->when(
                $kodeWilayah,
                fn($query) => $query->where(
                    'kode_wilayah',
                    $kodeWilayah
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Statistik Utama
        |--------------------------------------------------------------------------
        */

        $totalActive = (clone $baseQuery)->count();

        $newCasesCount = (clone $baseQuery)
            ->whereColumn('first_run_id', 'latest_run_id')
            ->count();

        $statusCounts = (clone $baseQuery)
            ->select(
                'status_penanganan',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status_penanganan')
            ->pluck('total', 'status_penanganan')
            ->toArray();

        /*
|--------------------------------------------------------------------------
| Anomali Per Jenis
|--------------------------------------------------------------------------
|
| Target     = jumlah Assignment ID unik pada case anomali
| Realisasi  = jumlah Assignment ID unik yang sudah selesai
|
*/

        $anomalyTypeCounts = (clone $baseQuery)
            ->join(
                'anomaly_types',
                'anomaly_cases.anomaly_type_id',
                '=',
                'anomaly_types.id'
            )
            ->select(
                'anomaly_types.id',
                'anomaly_types.nama',

                // Target = seluruh Assignment ID unik
                DB::raw('COUNT(DISTINCT anomaly_cases.assignment_id) as target'),

                // Realisasi = Assignment ID unik yang sudah selesai
                DB::raw("
            COUNT(
                DISTINCT CASE
                    WHEN anomaly_cases.status_penanganan = 'selesai'
                    THEN anomaly_cases.assignment_id
                END
            ) as realisasi
        ")
            )
            ->groupBy(
                'anomaly_types.id',
                'anomaly_types.nama'
            )
            ->orderByDesc('target')
            ->get();


        $anomalyTypeCounts->transform(function ($item) {

            $item->target = (int) $item->target;
            $item->realisasi = (int) $item->realisasi;

            $item->persen_target = $item->target > 0
                ? 100
                : 0;

            $item->persen_realisasi = $item->target > 0
                ? round(($item->realisasi / $item->target) * 100, 1)
                : 0;

            return $item;
        });

        /*
        |--------------------------------------------------------------------------
        | Ambil Alokasi Terbaru Per Kode Wilayah
        |--------------------------------------------------------------------------
        */

        $taskforceStats = DB::table('anomaly_cases')
            ->leftJoin('alokasi_petugas', function ($join) {
                $join->on(
                    'alokasi_petugas.kode_wilayah',
                    '=',
                    DB::raw('LEFT(anomaly_cases.nks, 16)')
                );
            })
            ->when(
                $runId,
                fn($query) => $query->where(
                    'anomaly_cases.latest_run_id',
                    $runId
                ),
                fn($query) => $query->whereRaw(
                    'anomaly_cases.latest_run_id = (
                SELECT MAX(id)
                FROM anomaly_runs
                WHERE anomaly_type_id = anomaly_cases.anomaly_type_id
            )'
                )
            )
            ->when(
                $anomalyTypeId,
                fn($query) => $query->where(
                    'anomaly_cases.anomaly_type_id',
                    $anomalyTypeId
                )
            )
            ->when(
                $kodeWilayah,
                fn($query) => $query->where(
                    'anomaly_cases.kode_wilayah',
                    $kodeWilayah
                )
            )
            ->selectRaw("
        COALESCE(
            NULLIF(alokasi_petugas.taskforce_nama, ''),
            'Tanpa Task Force'
        ) AS taskforce_nama
    ")
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("
        SUM(
            CASE
                WHEN anomaly_cases.status_penanganan = 'belum_ditangani'
                THEN 1 ELSE 0
            END
        ) AS belum
    ")
            ->selectRaw("
        SUM(
            CASE
                WHEN anomaly_cases.status_penanganan = 'proses'
                THEN 1 ELSE 0
            END
        ) AS proses
    ")
            ->selectRaw("
        SUM(
            CASE
                WHEN anomaly_cases.status_penanganan = 'menunggu_konfirmasi'
                THEN 1 ELSE 0
            END
        ) AS menunggu
    ")
            ->selectRaw("
        SUM(
            CASE
                WHEN anomaly_cases.status_penanganan = 'selesai'
                THEN 1 ELSE 0
            END
        ) AS selesai
    ")
            ->groupBy('taskforce_nama')
            ->orderByDesc('total')
            ->get();
        /*
        |--------------------------------------------------------------------------
        | Data Filter
        |--------------------------------------------------------------------------
        */

        $anomalyTypes = AnomalyType::orderBy('nama')->get();

        $runOptions = AnomalyRun::orderByDesc('tanggal_query')
            ->limit(20)
            ->get();

        $wilayahOptions = (clone $baseQuery)
            ->whereNotNull('kode_wilayah')
            ->where('kode_wilayah', '<>', '')
            ->select('kode_wilayah')
            ->distinct()
            ->orderBy('kode_wilayah')
            ->pluck('kode_wilayah');

        $selectedRun = $runId
            ? AnomalyRun::find($runId)
            : null;

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'totalActive',
            'newCasesCount',
            'statusCounts',
            'anomalyTypeCounts',
            'taskforceStats',
            'anomalyTypes',
            'runOptions',
            'wilayahOptions',
            'anomalyTypeId',
            'runId',
            'kodeWilayah',
            'selectedRun'
        ));
    }
}
