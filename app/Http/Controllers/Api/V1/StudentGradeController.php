<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GradeStudent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentGradeController extends Controller
{
    /**
     * Update grades for a student for a specific term.
     */
    public function update(Request $request, GradeStudent $gradeStudent): JsonResponse
    {
        $gradeClass = $gradeStudent->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'term' => 'required|integer|in:1,2,3',
            'behavior' => 'sometimes|numeric|min:0|max:5',
            'applications' => 'sometimes|numeric|min:0|max:5',
            'notebook' => 'sometimes|numeric|min:0|max:5',
            'assignment' => 'sometimes|numeric|min:0|max:20',
            'exam' => 'sometimes|numeric|min:0|max:20',
        ]);

        $term = $validated['term'];
        unset($validated['term']);

        $grade = $gradeStudent->grades()->updateOrCreate(
            ['term' => $term],
            $validated
        );

        return response()->json([
            'data' => $grade,
            'message' => 'Grades updated successfully',
        ]);
    }

    /**
     * Batch update grades for multiple students.
     */
    public function batchUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'term' => 'required|integer|in:1,2,3',
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'required|uuid|exists:grade_students,id',
            'grades.*.behavior' => 'sometimes|numeric|min:0|max:5',
            'grades.*.applications' => 'sometimes|numeric|min:0|max:5',
            'grades.*.notebook' => 'sometimes|numeric|min:0|max:5',
            'grades.*.assignment' => 'sometimes|numeric|min:0|max:20',
            'grades.*.exam' => 'sometimes|numeric|min:0|max:20',
        ]);

        $term = $validated['term'];
        $userId = Auth::id();
        $updated = 0;

        DB::transaction(function () use ($validated, $term, $userId, &$updated) {
            foreach ($validated['grades'] as $gradeData) {
                $student = GradeStudent::with('gradeClass')->find($gradeData['student_id']);

                // Skip if not owner
                if (! $student || $student->gradeClass->user_id !== $userId) {
                    continue;
                }

                $gradeFields = collect($gradeData)->except('student_id')->toArray();

                $student->grades()->updateOrCreate(
                    ['term' => $term],
                    $gradeFields
                );

                $updated++;
            }
        });

        return response()->json([
            'message' => "$updated grades updated successfully",
            'updated' => $updated,
        ]);
    }
}
