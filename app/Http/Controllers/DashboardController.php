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
        */

        $anomalyTypeCounts = (clone $baseQuery)
            ->join(
                'anomaly_types',
                'anomaly_cases.anomaly_type_id',
                '=',
                'anomaly_types.id'
            )
            ->select(
                'anomaly_types.nama',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('anomaly_types.nama')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Ambil Alokasi Terbaru Per Kode Wilayah
        |--------------------------------------------------------------------------
        */

        $taskforceStats = DB::table('anomaly_cases as ac')
            ->leftJoin('alokasi_petugas as ap', function ($join) {
                $join->on(
                    'ap.kode_wilayah',
                    '=',
                    DB::raw("CONCAT(LEFT(ac.nks, 15), '0')")
                );
            })
            ->when(
                $runId,
                fn($query) => $query->where('ac.latest_run_id', $runId),
                fn($query) => $query->whereRaw(
                    'ac.latest_run_id = (
                SELECT MAX(id)
                FROM anomaly_runs
                WHERE anomaly_type_id = ac.anomaly_type_id
            )'
                )
            )
            ->when(
                $anomalyTypeId,
                fn($query) => $query->where('ac.anomaly_type_id', $anomalyTypeId)
            )
            ->when(
                $kodeWilayah,
                fn($query) => $query->where('ac.kode_wilayah', $kodeWilayah)
            )
            ->selectRaw("
        COALESCE(
            NULLIF(TRIM(ap.taskforce_nama), ''),
            'Tanpa Task Force'
        ) AS taskforce_nama
    ")
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("
        SUM(
            CASE
                WHEN ac.status_penanganan = 'belum_ditangani'
                THEN 1 ELSE 0
            END
        ) AS belum
    ")
            ->selectRaw("
        SUM(
            CASE
                WHEN ac.status_penanganan = 'proses'
                THEN 1 ELSE 0
            END
        ) AS proses
    ")
            ->selectRaw("
        SUM(
            CASE
                WHEN ac.status_penanganan = 'menunggu_konfirmasi'
                THEN 1 ELSE 0
            END
        ) AS menunggu
    ")
            ->selectRaw("
        SUM(
            CASE
                WHEN ac.status_penanganan = 'selesai'
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
