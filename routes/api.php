<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/auth/login', [AuthController::class, 'login'])
->name('api.user.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{id}', [UserController::class, 'get'])->name('api.user.get');
    Route::post('/user/lock', [UserController::class, 'lock'])->name('api.user.lock');
    Route::post('/user/unlock', [UserController::class, 'unlock'])->name('api.user.unlock');

    Route::get('/assessment', [AssessmentController::class, 'get'])->name('api.assessment.get');
    Route::post('/assessment', [AssessmentController::class, 'update'])->name('api.assessment.update');
    Route::post('/assessment/join', [AssessmentController::class, 'join'])->name('api.assessment.join');
});