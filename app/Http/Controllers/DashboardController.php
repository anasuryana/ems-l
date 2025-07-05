<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function getChart1Data()
    {
        $data = [];
        $dataLine = DB::table('tbl_devices')->orderBy('id')->first();
        $dataDB = DB::table('tbl_pcb_logs_real')
            ->where('date', date('Y-m-d'))
            ->where('line_name', $dataLine->line_name ?? '')
            ->groupBy(DB::raw('HOUR(time)'))
            ->select(
                DB::raw("HOUR(time) as time_"),
                DB::raw("MAX(qty_red) as ng"),
                DB::raw("MAX(qty_yellow) as retry"),
                DB::raw("MAX(line_name) as mline_name")
            )
            ->get();

        $isDataExist = true;
        if ($dataDB->count() == 0) {
            $isDataExist = false;
            $lastData = DB::table('tbl_pcb_logs_real')
                ->where('line_name', $dataLine->line_name ?? '')
                ->select(
                    'date',
                )
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('id')
                ->first();
                
        } else {
            $lastData = DB::table('tbl_pcb_logs_real')
                ->where('date', date('Y-m-d'))
                ->where('line_name', $dataLine->line_name ?? '')
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('id')
                ->first();
        }

        for ($i = 0; $i <= 23; $i++) {
            $startHour = substr('0' . $i, -2);
            $ng = $retry = 0;
            foreach ($dataDB as $r) {
                if (substr($startHour, 0, 2) == substr($r->time_, 0, 2)) {
                    $ng = $r->ng ?? 0;
                    $retry = $r->retry ?? 0;
                    break;
                }
            }
            $data[] = [
                'time' =>  $startHour, //. '~' . substr('0' . ($i + 1), -2),
                'ng' => $ng,
                'retry' => $retry
            ];
        }

        $status = null;

        if ($lastData) {
            if ($lastData->qty_off == 1) {
                $status = 'Off';
            } elseif ($lastData->qty_red > 0 && $lastData->qty_red >= $lastData->qty_yellow) {
                $status = 'Red';
            } elseif ($lastData->qty_yellow > 0) {
                $status = 'Yellow';
            } else {
                $status = 'Green';
            }
        }
        
        return [
            'data' => $data,
            'line_name' => $dataLine->line_name ?? '',
            'qty_red' => $lastData->qty_red ?? 0,
            'qty_yellow' => $lastData->qty_yellow ?? 0,
            'qty_off' => $lastData->qty_off ?? 0,
            'is_data_exist' => $isDataExist ? '1' : '0',
            'status' => $status
        ];
    }

    // function getDetail(Request $request)
    // {
    //     $dataLine = DB::table('tbl_devices')->orderBy('id')->first();
    //     $data = DB::table('tbl_pcb_logs')->where('date', date('Y-m-d'))
    //         ->where('line_name', $dataLine->line_name ?? '')
    //         ->where('status', $request->status)
    //         ->orderBy('date')
    //         ->orderBy('time')
    //         ->paginate(10);
    //     return ['data' => $data];
    // }

    function getDetail(Request $request)
    {
        $dataLine = DB::table('tbl_devices')->orderBy('id')->first();

        $query = DB::table('tbl_pcb_logs_real')
            ->where('date', date('Y-m-d'))
            ->where('line_name', $dataLine->line_name ?? '');

        if ($request->status == "Yellow") {
            $query->where('qty_yellow', '>', 0);
        } elseif ($request->status == "Red") {
            $query->where('qty_red', '>', 0);
        }

        $data = $query->orderByDesc('date')->orderByDesc('time')->paginate(10);
        return ['data' => $data];
    }

    // function getDetail(Request $request)
    // {
    //     $dataLine = DB::table('tbl_devices')->orderBy('id')->first();

    //     $query = DB::table('tbl_pcb_logs2')
    //         ->where('date', date('Y-m-d'))
    //         ->where('line_name', $dataLine->line_name ?? '');

    //     if ($request->status == "Yellow") {
    //         $query->where('qty_yellow', '>', 0);
    //     } elseif ($request->status == "Red") {
    //         $query->where('qty_red', '>', 0);
    //     } elseif ($request->status == "Off") {
    //         $query->where('qty_off', '>', 0);
    //     } elseif ($request->status == "Green") {
    //         $query->where('qty_red', 0)->where('qty_yellow', 0)->where('qty_off', 0);
    //     }

    //     $data = $query->orderByDesc('date')->orderByDesc('time')->paginate(10);

    //     return ['data' => $data];
    // }


}
