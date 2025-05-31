<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                    ], 'data' => $data
                ]
            ], 401));
        }
    }
}
