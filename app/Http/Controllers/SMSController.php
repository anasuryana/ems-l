<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SMSController extends Controller
{
    public function search(Request $request)
    {
        $additionalWhere = [];
        switch ($request->searchBy) {
            case 'name':
                $additionalWhere[] = ['name', 'like', '%' . $request->searchValue . '%'];
                break;
            case 'telp_no':
                $additionalWhere[] = ['telp_no', 'like', '%' . $request->searchValue . '%'];
                break;
            case 'status':
                $additionalWhere[] = ['status', 'like', '%' . $request->searchValue . '%'];
                break;
        }
        $data = DB::table('tbl_user_sms')
            ->where($additionalWhere)->paginate(500);
        return ['data' => $data];
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'telp_no' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('tbl_user_sms')->insert([
            'name' => $request->name,
            'telp_no' => str_replace([" ", "-"], "", $request->telp_no),
            'status' => $request->status,
        ]);

        return ['message' => 'Saved successfully'];
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'telp_no' => 'required',
            'status' => 'required',
            'id_user' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('tbl_user_sms')->where('id_user', $request->id_user)->update([
            'name' => $request->name,
            'telp_no' => str_replace([" ", "-"], "", $request->telp_no),
            'status' => $request->status,
        ]);

        return ['message' => 'Updated successfully'];
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('tbl_user_sms')->where('id_user', $request->id_user)->delete();

        return ['message' => 'Deleted successfully'];
    }
}
