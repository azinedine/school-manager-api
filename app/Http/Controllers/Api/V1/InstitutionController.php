<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Http\Resources\InstitutionResource;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Institution::query();

        if ($request->has('wilaya')) {
            $query->where('wilaya_code', $request->wilaya);
        }

        if ($request->has('municipality')) {
            $query->where('municipality_id', $request->municipality);
        }

        return InstitutionResource::collection($query->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ideally use a FormRequest here, but keeping inline for brevity as per plan MVP
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'wilaya_code' => 'required|string',
            'municipality_id' => 'required|string',
            'type' => 'nullable|string|in:primary,middle,high,school',
        ]);

        $institution = Institution::create([
            'name' => $validated['name'],
            'wilaya_code' => $validated['wilaya_code'],
            'municipality_id' => $validated['municipality_id'],
            'type' => $validated['type'] ?? 'school',
            'code' => strtoupper(substr($validated['name'], 0, 3)) . rand(1000, 9999),
        ]);

        return new InstitutionResource($institution);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $institution = Institution::findOrFail($id);
        return new InstitutionResource($institution);
    }
}
