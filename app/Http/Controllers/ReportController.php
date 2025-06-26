<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

    function toSpreadsheet(Request $request)
    {

        $data = DB::table('tbl_pcb_logs')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->orderBy('date')
            ->orderBy('time')
            ->get();


        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setTitle('Log');
        $sheet->setCellValue([1, 1], 'Date');
        $sheet->setCellValue([2, 1], 'Time');
        $sheet->setCellValue([3, 1], 'Line Number');
        $sheet->setCellValue([4, 1], 'Status');
        $sheet->setCellValue([5, 1], 'Qty');

        $i = 2;
        foreach ($data as $r) {
            $sheet->setCellValue([1, $i], $r->date);
            $sheet->setCellValue([2, $i], $r->time);
            $sheet->setCellValue([3, $i], $r->line_name);
            $sheet->setCellValue([4, $i], $r->status);
            $sheet->setCellValue([5, $i], $r->qty);

            $i++;
        }

        foreach (range('A', 'E') as $v) {
            $sheet->getColumnDimension($v)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $stringjudul = "Logs, " . date('H:i:s');
        $filename = $stringjudul;

        $writer = IOFactory::createWriter($spreadSheet, 'Csv');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
        $writer->save('php://output');
    }
}
