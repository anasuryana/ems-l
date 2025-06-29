<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    public function getDevice(Request $request)
    {
        $additionalWhere = [];
        switch ($request->searchBy) {
            case 'txIP':
                $additionalWhere[] = ['IP_device_tx', 'like', '%' . $request->searchValue . '%'];
                break;
            case 'rxIP':
                $additionalWhere[] = ['IP_device_rx', 'like', '%' . $request->searchValue . '%'];
                break;
            case 'model':
                $additionalWhere[] = ['model', 'like', '%' . $request->searchValue . '%'];
                break;
            case 'line':
                $additionalWhere[] = ['line_name', 'like', '%' . $request->searchValue . '%'];
                break;
        }
        $data = DB::table('tbl_devices')
            ->where($additionalWhere)->paginate(10);
        return ['data' => $data];
    }

    public function saveDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IP_device_tx' => 'required|ip',
            'IP_device_rx' => 'required|ip',
            'model' => 'required',
            'line_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('tbl_devices')->insert([
            'IP_device_tx' => $request->IP_device_tx,
            'IP_device_rx' => $request->IP_device_rx,
            'model' => $request->model,
            'line_name' => $request->line_name,
        ]);

        return ['message' => 'Saved successfully'];
    }

    public function updateDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IP_device_tx' => 'required|ip',
            'IP_device_rx' => 'required|ip',
            'model' => 'required',
            'line_name' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('tbl_devices')->where('id', $request->id)->update([
            'IP_device_tx' => $request->IP_device_tx,
            'IP_device_rx' => $request->IP_device_rx,
            'model' => $request->model,
            'line_name' => $request->line_name,
        ]);

        return ['message' => 'Updated successfully'];
    }

    public function deleteDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('tbl_devices')->where('id', $request->id)->delete();

        return ['message' => 'Deleted successfully'];
    }
}
