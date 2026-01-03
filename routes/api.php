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

    // Timetable Routes (at root level to match frontend)
    Route::get('/timetable', [\App\Http\Controllers\Api\V1\TimetableEntryController::class, 'index']);
    Route::post('/timetable', [\App\Http\Controllers\Api\V1\TimetableEntryController::class, 'store']);

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

        // Timetable Routes
        Route::get('timetable', [\App\Http\Controllers\Api\V1\TimetableEntryController::class, 'index']);
        Route::post('timetable', [\App\Http\Controllers\Api\V1\TimetableEntryController::class, 'store']);

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

        // Grades Management Routes (Teacher)
        Route::apiResource('grade-classes', \App\Http\Controllers\Api\V1\GradeClassController::class);
        Route::delete('grade-classes', [\App\Http\Controllers\Api\V1\GradeClassController::class, 'destroyAll'])
            ->name('grade-classes.destroy-all');
        
        // Students within a class
        Route::get('grade-classes/{gradeClass}/students', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'index'])
            ->name('grade-classes.students.index');
        Route::post('grade-classes/{gradeClass}/students', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'store'])
            ->name('grade-classes.students.store');
        Route::post('grade-classes/{gradeClass}/students/batch', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'batchStore'])
            ->name('grade-classes.students.batch');
        Route::post('grade-classes/{gradeClass}/students/reorder', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'reorder'])
            ->name('grade-classes.students.reorder');
        
        // Individual student operations
        Route::put('grade-students/{gradeStudent}', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'update'])
            ->name('grade-students.update');
        Route::delete('grade-students/{gradeStudent}', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'destroy'])
            ->name('grade-students.destroy');
        Route::post('grade-students/{gradeStudent}/move', [\App\Http\Controllers\Api\V1\GradeStudentController::class, 'move'])
            ->name('grade-students.move');
        
        // Student grades
        Route::put('grade-students/{gradeStudent}/grades', [\App\Http\Controllers\Api\V1\StudentGradeController::class, 'update'])
            ->name('grade-students.grades.update');
        Route::post('grades/batch', [\App\Http\Controllers\Api\V1\StudentGradeController::class, 'batchUpdate'])
            ->name('grades.batch');
    });
});

// Public V1 API Routes
Route::prefix('v1')->group(function () {
    // Location Data
    Route::get('wilayas', [WilayaController::class, 'index']);
    Route::get('wilayas/{wilaya}/municipalities', [WilayaController::class, 'municipalities']);

    // Core Data
    Route::get('subjects', [\App\Http\Controllers\Api\V1\SubjectController::class, 'index']);
    Route::get('levels', [\App\Http\Controllers\Api\V1\LevelController::class, 'index']);
    Route::get('materials', [\App\Http\Controllers\Api\V1\MaterialController::class, 'index']);
    Route::get('references', [\App\Http\Controllers\Api\V1\ReferenceController::class, 'index']);
    Route::get('learning-objectives', [\App\Http\Controllers\Api\V1\LearningObjectiveController::class, 'index']);
    Route::get('teaching-methods', [\App\Http\Controllers\Api\V1\TeachingMethodController::class, 'index']);

    // Institutions Read (for selection in registration)
    Route::get('institutions', [InstitutionController::class, 'index']);
    Route::get('institutions/{institution}', [InstitutionController::class, 'show']);
    // Dedicated route for filtering by location
    Route::get('wilayas/{wilaya}/municipalities/{municipality}/institutions', [InstitutionController::class, 'getByLocation']);
});
