<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        // Accept institution_id directly from frontend
        $institutionId = $data['institution_id'] ?? null;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'student',
            'wilaya' => $data['wilaya'] ?? null,
            'municipality' => $data['municipality'] ?? null,
            'institution_id' => $institutionId,
            'status' => 'active',

            // Profile fields
            'name_ar' => $data['name_ar'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,

            // Teacher fields
            'teacher_id' => $data['teacher_id'] ?? null,
            'years_of_experience' => $data['years_of_experience'] ?? null,
            'employment_status' => $data['employment_status'] ?? 'active',
            'weekly_teaching_load' => $data['weekly_teaching_load'] ?? null,
            'subjects' => $data['subjects'] ?? null,
            'levels' => $data['levels'] ?? null,
            'assigned_classes' => $data['assigned_classes'] ?? null,
            'groups' => $data['groups'] ?? null,

            // Student fields
            'class' => $data['class'] ?? null,
            'linked_student_id' => $data['linked_student_id'] ?? null,

            // Admin fields
            'department' => $data['department'] ?? null,
            'position' => $data['position'] ?? null,
            'date_of_hiring' => $data['date_of_hiring'] ?? null,
            'work_phone' => $data['work_phone'] ?? null,
            'office_location' => $data['office_location'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new \App\Http\Resources\UserResource($user->load(['institution', 'wilaya', 'municipality'])),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Login a user.
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        if (! auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new \App\Http\Resources\UserResource($user->load(['institution', 'wilaya', 'municipality'])),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout the user.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Delete the user account.
     */
    public function deleteAccount(User $user): void
    {
        $user->tokens()->delete();
        $user->delete();
    }
}
