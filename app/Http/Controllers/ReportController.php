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
        $data = DB::table('tbl_pcb_logs_real')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(10);

        $dataStatusResumeo = DB::table('tbl_pcb_logs_real')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->selectRaw("
                SUM(qty_red) as red,
                SUM(qty_yellow) as yellow,
                SUM(qty_green) as green,
                SUM(qty_off) as off
            ")
        ->first();

        $dataStatusResume = ['ng' => 0, 'retry' => 0];

        $dataStatusResumeo = DB::table('tbl_pcb_logs_real')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->selectRaw('SUM(qty_red) as total_red, SUM(qty_yellow) as total_yellow')
            ->first();

        // Isi nilai resume jika ada hasil
        if ($dataStatusResumeo) {
            $dataStatusResume['ng'] = $dataStatusResumeo->total_red ?? 0;
            $dataStatusResume['retry'] = $dataStatusResumeo->total_yellow ?? 0;
        }

        return ['data' => $data, 'dataStatus' => $dataStatusResume];

    }

    function toSpreadsheet(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Expose-Headers: Content-Disposition");

        $data = DB::table('tbl_pcb_logs_real')
            ->where('date', '>=', $request->dateFrom ?? '2000-01-01')
            ->where('date', '<=', $request->dateTo ?? '2025-06-01')
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    
        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setTitle('Logs');
    
        $sheet->setCellValue([1, 1], 'Date');
        $sheet->setCellValue([2, 1], 'Time');
        $sheet->setCellValue([3, 1], 'Line Name');
        $sheet->setCellValue([4, 1], 'Model');
        $sheet->setCellValue([5, 1], 'IP Device');
        $sheet->setCellValue([6, 1], 'Qty Red');
        $sheet->setCellValue([7, 1], 'Qty Yellow');
        $sheet->setCellValue([8, 1], 'Qty Green');
        $sheet->setCellValue([9, 1], 'Qty Off');
    
        $i = 2;
        foreach ($data as $r) {
            $sheet->setCellValue([1, $i], $r->date);
            $sheet->setCellValue([2, $i], $r->time);
            $sheet->setCellValue([3, $i], $r->line_name);
            $sheet->setCellValue([4, $i], $r->model);
            $sheet->setCellValue([5, $i], $r->IP_device);
            $sheet->setCellValue([6, $i], $r->qty_red);
            $sheet->setCellValue([7, $i], $r->qty_yellow);
            $sheet->setCellValue([8, $i], $r->qty_green);
            $sheet->setCellValue([9, $i], $r->qty_off);
            $i++;
        }
    
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        $sheet->freezePane('A2');
    
        $filename = "Logs_" . date('Ymd_His');
    
        $writer = IOFactory::createWriter($spreadSheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');        
        $writer->save('php://output');
        exit;
    }
}
