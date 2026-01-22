<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\GradeClass;
use App\Models\StudentWeeklyReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentWeeklyReviewController extends Controller
{
    /**
     * Get weekly reviews summary for a class.
     * Returns aggregated status per student for the current/last week view.
     */
    public function summary(Request $request, GradeClass $gradeClass): JsonResponse
    {
        // Authorization: Teacher can only access their own classes
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currentWeek = StudentWeeklyReview::getCurrentWeek();
        $lastWeek = StudentWeeklyReview::getLastWeek();

        // Fetch all reviews for current and last week in a single query
        $reviews = StudentWeeklyReview::forClass($gradeClass->id)
            ->where(function ($query) use ($currentWeek, $lastWeek) {
                $query->where(function ($q) use ($currentWeek) {
                    $q->where('year', $currentWeek['year'])
                        ->where('week_number', $currentWeek['week']);
                })->orWhere(function ($q) use ($lastWeek) {
                    $q->where('year', $lastWeek['year'])
                        ->where('week_number', $lastWeek['week']);
                });
            })
            ->get()
            ->groupBy('grade_student_id');

        // Get all students in the class
        $students = $gradeClass->students()->get();

        // Build summary for each student
        $studentSummaries = [];
        foreach ($students as $student) {
            $studentReviews = $reviews->get($student->id, collect());

            $thisWeekReview = $studentReviews->first(function ($review) use ($currentWeek) {
                return $review->year === $currentWeek['year']
                    && $review->week_number === $currentWeek['week'];
            });

            $lastWeekReview = $studentReviews->first(function ($review) use ($lastWeek) {
                return $review->year === $lastWeek['year']
                    && $review->week_number === $lastWeek['week'];
            });

            // Determine if there's a pending alert
            // Alert is pending if: last week had an issue, not resolved, and not reviewed this week
            $hasPendingAlert = false;
            if ($lastWeekReview && $lastWeekReview->hasPendingAlert() && ! $thisWeekReview) {
                $hasPendingAlert = true;
            }

            $studentSummaries[$student->id] = [
                'reviewed_this_week' => $thisWeekReview !== null,
                'reviewed_last_week' => $lastWeekReview !== null,
                'this_week_review' => $thisWeekReview ? [
                    'id' => $thisWeekReview->id,
                    'observation_type' => $thisWeekReview->observation_type,
                    'notebook_checked' => $thisWeekReview->notebook_checked,
                    'lesson_written' => $thisWeekReview->lesson_written,
                    'homework_done' => $thisWeekReview->homework_done,
                    'score' => $thisWeekReview->score,
                    'observation_notes' => $thisWeekReview->observation_notes,
                ] : null,
                'last_review' => $lastWeekReview ? [
                    'id' => $lastWeekReview->id,
                    'week' => $lastWeekReview->week_number,
                    'year' => $lastWeekReview->year,
                    'observation_type' => $lastWeekReview->observation_type,
                    'alert_resolved' => $lastWeekReview->alert_resolved,
                ] : null,
                'has_pending_alert' => $hasPendingAlert,
            ];
        }

        return response()->json([
            'data' => [
                'current_week' => [
                    'year' => $currentWeek['year'],
                    'week' => $currentWeek['week'],
                    'week_start' => $currentWeek['week_start'],
                ],
                'last_week' => [
                    'year' => $lastWeek['year'],
                    'week' => $lastWeek['week'],
                    'week_start' => $lastWeek['week_start'],
                ],
                'students' => $studentSummaries,
            ],
        ]);
    }

    /**
     * List all reviews for a class (filterable by week).
     */
    public function index(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = StudentWeeklyReview::forClass($gradeClass->id)
            ->with('student:id,first_name,last_name')
            ->orderByDesc('year')
            ->orderByDesc('week_number');

        // Filter by specific week if provided
        if ($request->has('year') && $request->has('week')) {
            $query->forWeek((int) $request->year, (int) $request->week);
        }

        // Filter by student if provided
        if ($request->has('student_id')) {
            $query->forStudent($request->student_id);
        }

        // Filter pending alerts only
        if ($request->boolean('pending_only')) {
            $query->pendingAlerts();
        }

        $reviews = $query->paginate($request->input('per_page', 50));

        return response()->json($reviews);
    }

    /**
     * Batch create or update reviews for multiple students.
     */
    public function batchStore(Request $request, GradeClass $gradeClass): JsonResponse
    {
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'week_number' => 'required|integer|min:1|max:53',
            'reviews' => 'required|array|min:1',
            'reviews.*.student_id' => [
                'required',
                'uuid',
                Rule::exists('grade_students', 'id')->where('grade_class_id', $gradeClass->id),
            ],
            'reviews.*.notebook_checked' => 'sometimes|boolean',
            'reviews.*.lesson_written' => 'sometimes|boolean',
            'reviews.*.homework_done' => 'sometimes|boolean',
            'reviews.*.score' => 'sometimes|nullable|numeric|min:0|max:20',
            'reviews.*.observation_type' => [
                'sometimes',
                Rule::in(StudentWeeklyReview::OBSERVATION_TYPES),
            ],
            'reviews.*.observation_notes' => 'sometimes|nullable|string|max:1000',
        ]);

        $weekStartDate = StudentWeeklyReview::calculateWeekStartDate(
            $validated['year'],
            $validated['week_number']
        );

        $createdOrUpdated = [];

        DB::transaction(function () use ($validated, $gradeClass, $weekStartDate, &$createdOrUpdated) {
            foreach ($validated['reviews'] as $reviewData) {
                $review = StudentWeeklyReview::updateOrCreate(
                    [
                        'grade_student_id' => $reviewData['student_id'],
                        'year' => $validated['year'],
                        'week_number' => $validated['week_number'],
                    ],
                    [
                        'grade_class_id' => $gradeClass->id,
                        'teacher_id' => Auth::id(),
                        'week_start_date' => $weekStartDate,
                        'notebook_checked' => $reviewData['notebook_checked'] ?? false,
                        'lesson_written' => $reviewData['lesson_written'] ?? true,
                        'homework_done' => $reviewData['homework_done'] ?? true,
                        'score' => $reviewData['score'] ?? null,
                        'observation_type' => $reviewData['observation_type'] ?? StudentWeeklyReview::OBSERVATION_OK,
                        'observation_notes' => $reviewData['observation_notes'] ?? null,
                        // Reset alert status when re-reviewed
                        'alert_resolved' => false,
                        'resolved_at' => null,
                    ]
                );

                $createdOrUpdated[] = $review;
            }
        });

        return response()->json([
            'data' => $createdOrUpdated,
            'message' => count($createdOrUpdated).' reviews saved successfully',
        ], 201);
    }

    /**
     * Update a single review.
     */
    public function update(Request $request, StudentWeeklyReview $studentWeeklyReview): JsonResponse
    {
        // Authorization: Teacher can only update reviews for their own classes
        $gradeClass = $studentWeeklyReview->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'notebook_checked' => 'sometimes|boolean',
            'lesson_written' => 'sometimes|boolean',
            'homework_done' => 'sometimes|boolean',
            'score' => 'sometimes|nullable|numeric|min:0|max:20',
            'observation_type' => [
                'sometimes',
                Rule::in(StudentWeeklyReview::OBSERVATION_TYPES),
            ],
            'observation_notes' => 'sometimes|nullable|string|max:1000',
        ]);

        $studentWeeklyReview->update($validated);

        return response()->json([
            'data' => $studentWeeklyReview->fresh(),
            'message' => 'Review updated successfully',
        ]);
    }

    /**
     * Mark an alert as resolved.
     */
    public function resolve(StudentWeeklyReview $studentWeeklyReview): JsonResponse
    {
        // Authorization
        $gradeClass = $studentWeeklyReview->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $studentWeeklyReview->markResolved();

        return response()->json([
            'data' => $studentWeeklyReview->fresh(),
            'message' => 'Alert resolved successfully',
        ]);
    }

    /**
     * Delete a review.
     */
    public function destroy(StudentWeeklyReview $studentWeeklyReview): JsonResponse
    {
        // Authorization
        $gradeClass = $studentWeeklyReview->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $studentWeeklyReview->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }
}
