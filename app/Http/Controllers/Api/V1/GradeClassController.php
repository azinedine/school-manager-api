<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GradeClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeClassController extends Controller
{
    /**
     * Get all classes for the authenticated teacher.
     */
    public function index(Request $request): JsonResponse
    {
        $year = $request->query('year');
        
        $query = GradeClass::forTeacher(Auth::id())
            ->with(['students' => function ($q) {
                $q->orderBy('sort_order');
            }]);
        
        if ($year) {
            $query->forYear($year);
        }
        
        $classes = $query->orderBy('name')->get();
        
        return response()->json([
            'data' => $classes,
        ]);
    }

    /**
     * Create a new class.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'subject' => 'nullable|string|max:100',
            'grade_level' => 'nullable|string|max:100',
            'academic_year' => 'required|string|max:20',
        ]);

        $gradeClass = GradeClass::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        return response()->json([
            'data' => $gradeClass,
            'message' => 'Class created successfully',
        ], 201);
    }

    /**
     * Get a specific class with students.
     */
    public function show(GradeClass $gradeClass): JsonResponse
    {
        // Authorization check
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $gradeClass->load(['students.grades']);

        return response()->json([
            'data' => $gradeClass,
        ]);
    }

    /**
     * Update a class.
     */
    public function update(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'subject' => 'nullable|string|max:100',
            'grade_level' => 'nullable|string|max:100',
        ]);

        $gradeClass->update($validated);

        return response()->json([
            'data' => $gradeClass,
            'message' => 'Class updated successfully',
        ]);
    }

    /**
     * Delete a class and all its students.
     */
    public function destroy(GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $gradeClass->delete();

        return response()->json([
            'message' => 'Class deleted successfully',
        ]);
    }
}
