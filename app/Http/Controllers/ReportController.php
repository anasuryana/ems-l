<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    function report1(Request $request)
    {
        $data = DB::table('tbl_pcb_logs')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(10);
        $dataStatusResumeo = DB::table('tbl_pcb_logs')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->groupBy('status')
            ->select('status', DB::raw("SUM(qty) ttl_qty"))
            ->get();

        $dataStatusResume = ['ng' => 0, 'retry' => 0];

        if ($dataStatusResumeo->count() > 0) {
            $_dataNGo = $dataStatusResumeo->where('status', 'Red')->first();
            if (!empty($_dataNGo)) {
                $dataStatusResume['ng'] = $_dataNGo->ttl_qty;
            }

            $_dataRetryo = $dataStatusResumeo->where('status', 'Yellow')->first();
            if (!empty($_dataRetryo)) {
                $dataStatusResume['retry'] = $_dataRetryo->ttl_qty;
            }
        }

        return ['data' => $data, 'dataStatus' => $dataStatusResume];
    }
}
