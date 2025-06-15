<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function search(Request $request)
    {
        $additionalWhere = [];
        switch ($request->searchBy) {
            case 'name':
                $additionalWhere[] = ['name', 'like', '%' . $request->searchValue . '%'];
                break;
        }
        $data = DB::table('roles')
            ->where($additionalWhere)->whereNull('deleted_at')->get();
        return ['data' => $data];
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('roles')->insert([
            'name' => $request->name,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $request->user()->nick_name
        ]);

        return ['message' => 'Saved successfully'];
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('roles')->where('id', $request->id)->update([
            'name' => $request->name,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $request->user()->nick_name
        ]);

        return ['message' => 'Updated successfully'];
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('roles')->where('id', $request->id)->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $request->user()->nick_name
        ]);

        return ['message' => 'Deleted successfully'];
    }
}
