<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $request->has('per_page') 
            ? $query->paginate($request->per_page) 
            : $query->get();

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Prevent updating system role name
        $data = $request->validated();
        if ($role->is_system && isset($data['name'])) {
            unset($data['name']);
        }

        $role->update($data);

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting system roles
        if ($role->is_system) {
            return response()->json([
                'message' => 'Cannot delete system roles.'
            ], 403);
        }

        // Check if role is in use
        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete role that is assigned to users.'
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}
