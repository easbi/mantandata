<?php

namespace App\Http\Controllers;

use App\Models\AnomalyCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $anomalyTypeId = $request->get('anomaly_type_id');

        // Kasus aktif (muncul di run terbaru)
        $activeCases = AnomalyCase::active($anomalyTypeId)
            ->with(['latestRun', 'anomalyType'])
            ->get();

        // Kasus dengan status penanganan tertentu
        $casesByStatus = AnomalyCase::active($anomalyTypeId)
            ->select('status_penanganan', DB::raw('count(*) as total'))
            ->groupBy('status_penanganan')
            ->get();

        // Kasus yang pernah hilang dan muncul lagi (recurring)
        $recurringCases = AnomalyCase::query()
            ->where('anomaly_type_id', $anomalyTypeId)
            ->recurring()
            ->get();

        return view('dashboard', compact('activeCases', 'casesByStatus', 'recurringCases'));
    }
}