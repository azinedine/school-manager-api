<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GradeClass;
use App\Models\GradeStudent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeStudentController extends Controller
{
    /**
     * Get all students for a class with their term grades.
     */
    public function index(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $term = $request->query('term', 1);

        $students = $gradeClass->students()
            ->with([
                'grades' => function ($q) use ($term) {
                    $q->where('term', $term);
                },
                'pedagogicalTracking' => function ($q) use ($term) {
                    $q->where('term', $term);
                }
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($student) use ($term) {
                // Get or create term grades to ensure they always exist
                $grades = $student->grades->first() ?? $student->getOrCreateTermGrades($term);
                
                // Get or create term tracking
                $tracking = $student->pedagogicalTracking->first() ?? $student->getOrCreateTermTracking($term);
                
                return [
                    'id' => $student->id,
                    'student_number' => $student->student_number,
                    'last_name' => $student->last_name,
                    'first_name' => $student->first_name,
                    'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                    'special_case' => $student->special_case,
                    'sort_order' => $student->sort_order,
                    'behavior' => $grades->behavior,
                    'applications' => $grades->applications,
                    'notebook' => $grades->notebook,
                    'assignment' => $grades->assignment,
                    'exam' => $grades->exam,
                    // Pedagogical tracking fields
                    'oral_interrogation' => $tracking->oral_interrogation,
                    'notebook_checked' => $tracking->notebook_checked,
                    'last_interrogation_at' => $tracking->last_interrogation_at?->toISOString(),
                    'last_notebook_check_at' => $tracking->last_notebook_check_at?->toISOString(),
                ];
            });

        return response()->json([
            'data' => $students,
        ]);
    }

    /**
     * Add a single student to a class.
     */
    public function store(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'student_number' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'special_case' => 'nullable|string|max:50',
        ]);

        $maxOrder = $gradeClass->students()->max('sort_order') ?? 0;

        $student = $gradeClass->students()->create([
            ...$validated,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'data' => $student,
            'message' => 'Student added successfully',
        ], 201);
    }

    /**
     * Batch add students to a class (for Excel import).
     */
    public function batchStore(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.student_number' => 'nullable|string|max:50',
            'students.*.last_name' => 'required|string|max:100',
            'students.*.first_name' => 'required|string|max:100',
            'students.*.date_of_birth' => 'nullable|date',
            'students.*.special_case' => 'nullable|string|max:50',
        ]);

        $maxOrder = $gradeClass->students()->max('sort_order') ?? 0;
        $created = [];

        DB::transaction(function () use ($gradeClass, $validated, &$maxOrder, &$created) {
            foreach ($validated['students'] as $studentData) {
                $maxOrder++;
                $created[] = $gradeClass->students()->create([
                    ...$studentData,
                    'sort_order' => $maxOrder,
                ]);
            }
        });

        return response()->json([
            'data' => $created,
            'message' => count($created) . ' students added successfully',
        ], 201);
    }

    /**
     * Update a student.
     */
    public function update(Request $request, GradeStudent $gradeStudent): JsonResponse
    {
        $gradeClass = $gradeStudent->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'student_number' => 'nullable|string|max:50',
            'last_name' => 'sometimes|string|max:100',
            'first_name' => 'sometimes|string|max:100',
            'date_of_birth' => 'nullable|date',
            'special_case' => 'nullable|string|max:50',
        ]);

        $gradeStudent->update($validated);

        return response()->json([
            'data' => $gradeStudent,
            'message' => 'Student updated successfully',
        ]);
    }

    /**
     * Delete a student.
     */
    public function destroy(GradeStudent $gradeStudent): JsonResponse
    {
        $gradeClass = $gradeStudent->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $gradeStudent->delete();

        return response()->json([
            'message' => 'Student removed successfully',
        ]);
    }

    /**
     * Move a student to a different class.
     */
    public function move(Request $request, GradeStudent $gradeStudent): JsonResponse
    {
        $gradeClass = $gradeStudent->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'grade_class_id' => 'required|uuid|exists:grade_classes,id',
        ]);

        // Verify target class belongs to same teacher
        $targetClass = GradeClass::find($validated['grade_class_id']);
        if ($targetClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Target class not found'], 404);
        }

        $maxOrder = $targetClass->students()->max('sort_order') ?? 0;
        $gradeStudent->update([
            'grade_class_id' => $validated['grade_class_id'],
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'data' => $gradeStudent->fresh(),
            'message' => 'Student moved successfully',
        ]);
    }

    /**
     * Reorder students in a class.
     */
    public function reorder(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'uuid|exists:grade_students,id',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $index => $studentId) {
                GradeStudent::where('id', $studentId)->update(['sort_order' => $index + 1]);
            }
        });

        return response()->json([
            'message' => 'Students reordered successfully',
        ]);
    }
}
