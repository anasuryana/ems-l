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
        $dataDB = DB::table('tbl_pcb_logs')->where('status', '!=', 'Off')
            ->where('date', date('Y-m-d'))
            ->groupBy(DB::raw('HOUR(time)'))
            ->select(
                DB::raw("HOUR(TIME) time_"),
                DB::raw("SUM(case when status = 'Red' then  qty end) ng"),
                DB::raw("SUM(case when status = 'Yellow ' then  qty end) retry"),
                DB::raw("MAX(line_name) mline_name"),
            )->get();

        if ($dataDB->count() == 0) {
            $latestdataDB = DB::table('tbl_pcb_logs')->where('status', '!=', 'Off')
                ->select(
                    'date',
                    'status'
                )
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->first();

            $dataDB = DB::table('tbl_pcb_logs')->where('status', '!=', 'Off')
                ->where('date', $latestdataDB->date)
                ->groupBy(DB::raw('HOUR(time)'))
                ->select(
                    DB::raw("HOUR(TIME) time_"),
                    DB::raw("SUM(case when status = 'Red' then  qty end) ng"),
                    DB::raw("SUM(case when status = 'Yellow ' then  qty end) retry"),
                    DB::raw("MAX(line_name) mline_name")
                )->get();
        } else {
            $latestdataDB = DB::table('tbl_pcb_logs')
                ->where('date', date('Y-m-d'))
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->first();
        }

        for ($i = 0; $i <= 24; $i++) {
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

        return [
            'data' => $data,
            'line_name' => $dataLine->line_name ?? '',
            'last_status' => $latestdataDB->status
        ];
    }
}
