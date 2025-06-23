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

        return ['data' => $data];
    }
}
