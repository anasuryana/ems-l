<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }
        $dataReq = $request->json()->all();
        $data = [
            'nick_name' => $dataReq['username'],
            'password' => $dataReq['password'],
            'active' => '1',
        ];

        if (Auth::attempt($data)) {
            $user = User::where('nick_name', $dataReq['username'])->first();
            $user->token = $user->createToken($dataReq['password'] . 'bebas')->plainTextToken;
            return $user;
        } else {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'username or password wrong'
                    ],
                    'data' => $data
                ]
            ], 401));
        }
    }

    public function search(Request $request)
    {
        $additionalWhere = [];
        switch ($request->searchBy) {
            case 'nick_name':
                $additionalWhere[] = ['nick_name', 'like', '%' . $request->searchValue . '%'];
                break;
        }
        $data = DB::table('users')
            ->leftJoin('roles', 'role_id', '=', 'roles.id')
            ->where($additionalWhere)->whereNull('deleted_at')
            ->select(
                "users.id",
                "users.name",
                "role_id",
                "users.created_at",
                "users.updated_at",
                "nick_name",
                "active",
                DB::raw("roles.name as role_name")
            )->orderBy('users.name');
        return ['data' => $data->paginate(500)];
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nick_name' => 'required',
            'password' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('roles')->insert([
            'name' => $request->name,
            'nick_name' => $request->nick_name,
            'password' => Hash::make($request->password),
            'active' => '1',
            'role_id' => $request->role_id,
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
            'role_id' => $request->role_id
        ]);

        return ['message' => 'Updated successfully'];
    }

    public function updateActivation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'active' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }

        DB::table('users')->where('id', $request->id)->update([
            'active' => $request->active,
            'updated_at' => date('Y-m-d H:i:s'),

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
        ]);

        return ['message' => 'Deleted successfully'];
    }
}
