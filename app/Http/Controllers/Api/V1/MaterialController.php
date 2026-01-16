<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Get all materials
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Material::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new material
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:materials,name',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $material = Material::create($validated);

        return response()->json([
            'message' => 'Material created successfully',
            'data' => $material,
        ], 201);
    }

    /**
     * Show a specific material
     */
    public function show(Material $material): JsonResponse
    {
        return response()->json([
            'data' => $material,
        ]);
    }

    /**
     * Update a material
     */
    public function update(Request $request, Material $material): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:materials,name,'.$material->id,
            'name_ar' => 'nullable|string|max:255',
        ]);

        $material->update($validated);

        return response()->json([
            'message' => 'Material updated successfully',
            'data' => $material,
        ]);
    }

    /**
     * Delete a material
     */
    public function destroy(Material $material): JsonResponse
    {
        $material->delete();

        return response()->json([
            'message' => 'Material deleted successfully',
        ]);
    }
}
