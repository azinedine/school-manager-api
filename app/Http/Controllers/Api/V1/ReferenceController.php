<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    /**
     * Get all references
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Reference::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new reference
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:references,name',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $reference = Reference::create($validated);

        return response()->json([
            'message' => 'Reference created successfully',
            'data' => $reference,
        ], 201);
    }

    /**
     * Show a specific reference
     */
    public function show(Reference $reference): JsonResponse
    {
        return response()->json([
            'data' => $reference,
        ]);
    }

    /**
     * Update a reference
     */
    public function update(Request $request, Reference $reference): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:references,name,'.$reference->id,
            'name_ar' => 'nullable|string|max:255',
        ]);

        $reference->update($validated);

        return response()->json([
            'message' => 'Reference updated successfully',
            'data' => $reference,
        ]);
    }

    /**
     * Delete a reference
     */
    public function destroy(Reference $reference): JsonResponse
    {
        $reference->delete();

        return response()->json([
            'message' => 'Reference deleted successfully',
        ]);
    }
}
