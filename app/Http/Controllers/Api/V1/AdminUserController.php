<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users for the authenticated administrator's institution.
     * Restricts access to only relevant roles (Teacher, Manager, Admin).
     */
    public function index(Request $request)
    {
        $admin = $request->user();

        // Security: Ensure the admin actually belongs to an institution
        if (! $admin->institution_id) {
            abort(403, 'Administrator is not authorized for any institution.');
        }

        // Policy: Check if user can view users (Standard Gate check)
        Gate::authorize('viewAny', User::class); // Or a specific 'viewInstitutionUsers' policy if we were granular

        $query = User::query()
            ->forInstitution($admin->institution_id)
            // Strict Role Filtering: Only show staff/teachers, hide students/parents/super_admins
            ->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_MANAGER,
                User::ROLE_TEACHER
            ]);

        // Optional Filters
        if ($request->has('role') && in_array($request->role, [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_TEACHER])) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Default Sort
        $query->latest();

        return UserResource::collection($query->paginate(20));
    }
}
