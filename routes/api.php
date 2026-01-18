<?php

use App\Http\Controllers\Api\AttendanceRecordController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/auth/login', [AuthController::class, 'login'])
->name('api.user.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/absen', [AttendanceRecordController::class, 'get'])->name('api.user.absen.get');
});