<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/user', [AuthController::class, 'destroy']);

    // V1 API Routes
    Route::prefix('v1')->group(function () {
        Route::apiResource('institutions', \App\Http\Controllers\Api\V1\InstitutionController::class);
        Route::apiResource('teachers', \App\Http\Controllers\Api\V1\TeacherController::class)->only(['index', 'destroy']);
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
    });
});
