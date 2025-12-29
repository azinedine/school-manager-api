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

Route::middleware(['auth:sanctum', 'check.suspended'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/user', [AuthController::class, 'destroy']);

    // V1 API Routes
    Route::prefix('v1')->group(function () {
        // Institutions (Write operations & Restore)
        Route::apiResource('institutions', InstitutionController::class)->except(['index', 'show']);

        Route::post('institutions/{id}/restore', [InstitutionController::class, 'restore'])
            ->name('institutions.restore');

        // Lesson Preparation Routes (Teacher only)
        Route::apiResource('lesson-preparations', \App\Http\Controllers\Api\V1\LessonPreparationController::class);
        Route::get('lesson-preparations/statistics/summary', [\App\Http\Controllers\Api\V1\LessonPreparationController::class, 'statistics'])
            ->name('lesson-preparations.statistics');

        // Lessons CRUD Routes (Teacher only)
        Route::apiResource('lessons', \App\Http\Controllers\Api\V1\LessonController::class);
        Route::get('lessons/statistics/summary', [\App\Http\Controllers\Api\V1\LessonController::class, 'statistics'])
            ->name('lessons.statistics');

        // Legacy routes for backward compatibility
        Route::apiResource('teachers', \App\Http\Controllers\Api\V1\TeacherController::class)->only(['index', 'destroy']);
        Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
        // Admin Scope Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('users', [\App\Http\Controllers\Api\V1\AdminUserController::class, 'index'])->name('users.index');
        });

        // Super Admin Scope Routes
        Route::prefix('super-admin')->name('super-admin.')->group(function () {
             Route::get('users', [\App\Http\Controllers\Api\V1\SuperAdminUserController::class, 'index'])->name('users.index');
        });
    });
});

// Public V1 API Routes
Route::prefix('v1')->group(function () {
    // Location Data
    Route::get('wilayas', [WilayaController::class, 'index']);
    Route::get('wilayas/{wilaya}/municipalities', [WilayaController::class, 'municipalities']);

    // Institutions Read (for selection in registration)
    Route::get('institutions', [InstitutionController::class, 'index']);
    Route::get('institutions/{institution}', [InstitutionController::class, 'show']);
    // Dedicated route for filtering by location
    Route::get('wilayas/{wilaya}/municipalities/{municipality}/institutions', [InstitutionController::class, 'getByLocation']);
});
