<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        // Filter by user permissions if needed (e.g. Manager only sees own school?) - for future.
        // Current policy just allows viewAny.

        return UserResource::collection($query->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // policy check is inside Request::authorize

        $user = $this->userService->create($request->validated());

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        \App\Http\Requests\User\UpdateUserProfileRequest $request,
        User $user,
        \App\UseCases\User\UpdateUserProfileUseCase $updateUserProfileUseCase
    ) {
        // Policy check is handled in Request::authorize()
        // Validation is handled in Request::rules() (Strict Whitelist)

        $user = $updateUserProfileUseCase->execute($user, $request->validated());

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $this->userService->delete($user);

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
