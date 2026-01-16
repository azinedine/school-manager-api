<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SuperAdminUserController extends Controller
{
    /**
     * Display a listing of ALL users with enhanced details for Super Admins.
     * Eager loads 'institution' to avoid N+1.
     */
    public function index(Request $request)
    {
        // Enforce Super Admin Role strictly
        if ($request->user()->role !== User::ROLE_SUPER_ADMIN) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        // Standard Policy Check (though role check above is stronger/specific)
        Gate::authorize('viewAny', User::class);

        $query = User::query()
            ->with('institution'); // Eager load institution

        // Filtering
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        // Sort by latest
        $query->latest();

        // Use standard UserResource (it handles conditional fields)
        return UserResource::collection($query->paginate($request->get('per_page', 20)));
    }
}
