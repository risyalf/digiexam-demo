<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post("/auth/login", [AuthController::class, "login"])->name(
    "api.user.login",
);

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/user/{id}", [UserController::class, "get"])->name(
        "api.user.get",
    );
    Route::post("/user/lock", [UserController::class, "lock"])->name(
        "api.user.lock",
    );
    Route::post("/user/unlock", [UserController::class, "unlock"])->name(
        "api.user.unlock",
    );

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/{id}', [UserController::class, 'get'])->name('api.user.get');
        });

        Route::prefix('participant')->group(function () {
            Route::get('/{id}', [ParticipantController::class, 'get'])->name('api.participant.get');
            Route::post('/lock', [ParticipantController::class, 'lock'])->name('api.participant.lock');
            Route::post('/unlock', [ParticipantController::class, 'unlock'])->name('api.participant.unlock');
        });

        Route::prefix('assessment')->group(function () {
            Route::get('/', [AssessmentController::class, 'get'])->name('api.assessment.get');
            Route::post('/', [AssessmentController::class, 'update'])->name('api.assessment.update');
            Route::post('/join', [AssessmentController::class, 'join'])->name('api.assessment.join');
            Route::post("/assessment/submit", [AssessmentController::class, "submit"])->name('api.assessment.submit');
            Route::get("/assessment/result/{assessmentId}/{participantId}", [
                AssessmentController::class,
                "result",
            ])->name('api.assessment.result');
        });
    });
});
