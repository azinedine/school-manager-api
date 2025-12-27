<?php

namespace App\Services;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $institutionId = null;
        if (!empty($data['institution'])) {
            $institution = Institution::firstOrCreate(
                ['name' => $data['institution']],
                [
                    'wilaya_code' => $data['wilaya'] ?? 'UNKNOWN',
                    'municipality_id' => $data['municipality'] ?? 'UNKNOWN',
                    'code' => strtoupper(substr($data['institution'], 0, 3)) . rand(1000, 9999),
                ]
            );
            $institutionId = $institution->id;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'student',
            'wilaya' => $data['wilaya'] ?? null,
            'municipality' => $data['municipality'] ?? null,
            'institution_id' => $institutionId,
            'class' => $data['class'] ?? null,
            'linked_student_id' => $data['linked_student_id'] ?? null,
            'subjects' => $data['subjects'] ?? null,
            'levels' => $data['levels'] ?? null,
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
            'user' => new \App\Http\Resources\UserResource($user->load('institution')),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Login a user.
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;


        return [
            'user' => new \App\Http\Resources\UserResource($user->load('institution')),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout the user.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Delete the user account.
     *
     * @param User $user
     * @return void
     */
    public function deleteAccount(User $user): void
    {
        $user->tokens()->delete();
        $user->delete();
    }
}
