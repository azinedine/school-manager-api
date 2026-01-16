<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimetableRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TimetableEntryController extends Controller
{
    /**
     * Get all timetable entries for the authenticated user
     */
    public function index(): JsonResponse
    {
        $entries = auth()->user()->timetableEntries;

        return response()->json($entries);
    }

    /**
     * Store (bulk replace) timetable entries
     */
    public function store(StoreTimetableRequest $request): JsonResponse
    {
        $entries = $request->validated()['entries'];

        $savedEntries = DB::transaction(function () use ($entries) {
            // Delete existing entries for this user
            auth()->user()->timetableEntries()->delete();

            // Create new entries
            // The createMany method expects an array of arrays
            return auth()->user()->timetableEntries()->createMany($entries);
        });

        return response()->json($savedEntries);
    }
}
