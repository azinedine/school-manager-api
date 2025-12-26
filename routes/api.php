<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\InstitutionController;
use App\Http\Controllers\Api\V1\WilayaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Health check endpoint (public)
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/user', [AuthController::class, 'destroy']);

    // V1 API Routes
    Route::prefix('v1')->group(function () {
        // Wilayas & Municipalities (read-only for location selection)
        Route::get('wilayas', [WilayaController::class, 'index']);
        Route::get('wilayas/{wilaya}/municipalities', [WilayaController::class, 'municipalities']);

        // Institutions (full CRUD)
        Route::apiResource('institutions', InstitutionController::class);
        Route::post('institutions/{id}/restore', [InstitutionController::class, 'restore'])
            ->name('institutions.restore');

        // Legacy routes for backward compatibility
        Route::apiResource('teachers', \App\Http\Controllers\Api\V1\TeacherController::class)->only(['index', 'destroy']);
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
    });
});
