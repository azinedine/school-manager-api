<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\UpdatePedagogicalTrackingRequest;
use App\Models\GradeStudent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StudentPedagogicalTrackingController extends Controller
{
    /**
     * Update pedagogical tracking for a student.
     */
    public function update(UpdatePedagogicalTrackingRequest $request, GradeStudent $gradeStudent): JsonResponse
    {
        // Authorization: Teacher can only update tracking for their own classes
        $gradeClass = $gradeStudent->gradeClass;
        if ($gradeClass->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();
        $term = $validated['term'];

        // Get or create tracking record for this term
        $tracking = $gradeStudent->getOrCreateTermTracking($term);

        // Update tracking fields with timestamps
        $updateData = [];

        if (isset($validated['oral_interrogation'])) {
            $updateData['oral_interrogation'] = $validated['oral_interrogation'];
            // Set timestamp only when toggling to true
            if ($validated['oral_interrogation'] && ! $tracking->oral_interrogation) {
                $updateData['last_interrogation_at'] = now();
            }
        }

        if (isset($validated['notebook_checked'])) {
            $updateData['notebook_checked'] = $validated['notebook_checked'];
            // Set timestamp only when toggling to true
            if ($validated['notebook_checked'] && ! $tracking->notebook_checked) {
                $updateData['last_notebook_check_at'] = now();
            }
        }

        if (! empty($updateData)) {
            $tracking->update($updateData);
        }

        return response()->json([
            'data' => [
                'id' => $gradeStudent->id,
                'oral_interrogation' => $tracking->oral_interrogation,
                'notebook_checked' => $tracking->notebook_checked,
                'last_interrogation_at' => $tracking->last_interrogation_at?->toISOString(),
                'last_notebook_check_at' => $tracking->last_notebook_check_at?->toISOString(),
            ],
            'message' => 'Tracking updated successfully',
        ]);
    }
}
