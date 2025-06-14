<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function getDevice(Request $request)
    {
        $additionalWhere = [];
        switch ($request->searchBy) {
            case 'txIP':
                $additionalWhere[] = ['IP_device_tx', 'like', '%' . $request->searchValue . '%'];
            case 'rxIP':
                $additionalWhere[] = ['IP_device_rx', 'like', '%' . $request->searchValue . '%'];
            case 'model':
                $additionalWhere[] = ['model', 'like', '%' . $request->searchValue . '%'];
            case 'line':
                $additionalWhere[] = ['line_name', 'like', '%' . $request->searchValue . '%'];
        }
        $data = DB::table('tbl_devices')
            ->where($additionalWhere)->get();
        return ['data' => $data];
    }

    public function saveDevice(Request $request) {}
}
