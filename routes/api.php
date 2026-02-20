<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;
use App\Models\ParticipantAssessment;
use Illuminate\Support\Facades\Route;

Route::post("/auth/login", [AuthController::class, "login"])->name("api.user.login");
Route::post("/auth/participant/login", [ParticipantController::class, 'login'])->name('api.participant.login');

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/user/{id}", [UserController::class, "get"])->name("api.user.get");
    Route::post("/user/lock", [UserController::class, "lock"])->name("api.user.lock");

    Route::prefix("user")->group(function () {
        Route::get("/{id}", [UserController::class, "get"])->name(
            "api.user.get",
        );
    });

    Route::prefix("participant")->group(function () {
        Route::get("/{id}", [ParticipantController::class, "get"])->name(
            "api.participant.get",
        );
    });

    Route::prefix("participant-assessment")->group(function () {
        Route::get("/{id}", [ParticipantAssessment::class, "get"])->name(
            "api.participant.assessment.get",
        );
        Route::post("/lock", [ParticipantAssessment::class, "lock"])->name(
            "api.participant.assessment.lock",
        );
        Route::post("/unlock", [
            ParticipantAssessment::class,
            "unlock",
        ])->name("api.participant.assessment.unlock");
    });

    Route::prefix("assessment")->group(function () {
        Route::get("/", [AssessmentController::class, "get"])->name(
            "api.assessment.get",
        );
        Route::post("/", [AssessmentController::class, "update"])->name(
            "api.assessment.update",
        );
        Route::post("/join", [AssessmentController::class, "join"])->name(
            "api.assessment.join",
        );
        Route::post("/start", [AssessmentController::class, "start"])->name(
            "api.assessment.start",
        );
        Route::post("/submit", [
            AssessmentController::class,
            "submit",
        ])->name("api.assessment.submit");
        Route::get("/result/{assessmentId}/{participantId}", [
            AssessmentController::class,
            "result",
        ])->name("api.assessment.result");
    });

    Route::prefix("test")->group(function () {
        Route::get("/", [TestController::class, "get"])->name(
            "api.test.get",
        );
    });
});
