<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $role = DB::table('roles')->where('id', $request->user()->role_id)->first();
    return ['data' => [
        'name' => $request->user()->name,
        'nick_name' => $request->user()->nick_name,
        'role_id' => $request->user()->role_id,
        'role_name' => $role->name,
    ]];
})->middleware('auth:sanctum');


Route::prefix('users')->group(function () {
    Route::post('login', [UserController::class, 'login']);
});

Route::prefix('report')->group(function () {
    Route::get('chart1', [DashboardController::class, 'getChart1Data']);
    Route::get('detail', [DashboardController::class, 'getDetail']);
    Route::get('report1', [ReportController::class, 'report1']);
    Route::get('report1-to-spreadsheet', [ReportController::class, 'toSpreadsheet']);
});
Route::prefix('device-master')->group(function () {
    Route::get('', [DeviceController::class, 'getDevice']);
    Route::post('', [DeviceController::class, 'saveDevice']);
    Route::put('', [DeviceController::class, 'updateDevice']);
    Route::delete('', [DeviceController::class, 'deleteDevice']);
});
Route::prefix('sms-master')->group(function () {
    Route::get('', [SMSController::class, 'search']);
    Route::post('', [SMSController::class, 'save']);
    Route::put('', [SMSController::class, 'update']);
    Route::delete('', [SMSController::class, 'delete']);
});

Route::prefix('role-access')->group(function () {
    Route::get('', [RoleController::class, 'search']);
    Route::get('list', [RoleController::class, 'getRole']);
    Route::post('', [RoleController::class, 'save'])->middleware('auth:sanctum');
    Route::put('', [RoleController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('', [RoleController::class, 'delete'])->middleware('auth:sanctum');
});
Route::prefix('user-access')->group(function () {
    Route::get('', [UserController::class, 'search']);
    Route::post('', [UserController::class, 'save'])->middleware('auth:sanctum');
    Route::put('', [UserController::class, 'update']);
    Route::put('activation', [UserController::class, 'updateActivation']);
    Route::delete('', [UserController::class, 'delete'])->middleware('auth:sanctum');
});
