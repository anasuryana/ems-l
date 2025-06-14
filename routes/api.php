<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('users')->group(function () {
    Route::post('login', [UserController::class, 'login']);
});

Route::prefix('report')->group(function () {
    Route::get('chart1', [DashboardController::class, 'getChart1Data']);
});
Route::prefix('device-master')->group(function () {
    Route::get('', [DeviceController::class, 'getDevice']);
    Route::post('', [DeviceController::class, 'saveDevice']);
    Route::put('', [DeviceController::class, 'updateDevice']);
});
