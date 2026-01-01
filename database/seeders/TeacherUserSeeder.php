<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherUserSeeder extends Seeder
{
    /**
     * Seed a Teacher user.
     *
     * Run with: php artisan db:seed --class=TeacherUserSeeder
     */
    public function run(): void
    {
        $email = env('TEACHER_EMAIL', 'teacher@school.com');

        // Check if teacher already exists
        if (User::where('email', $email)->exists()) {
            $this->command->info("Teacher with email {$email} already exists. Skipping.");
            return;
        }

        $user = User::create([
            'name' => env('TEACHER_NAME', 'Teacher User'),
            'email' => $email,
            'password' => env('TEACHER_PASSWORD', 'password'), // Will be hashed by model cast
            'role' => User::ROLE_TEACHER,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            // Teacher specific fields can be added here if needed default values
            'status' => 'active',
        ]);

        $this->command->info("✅ Teacher created successfully!");
        $this->command->info("   Email: {$user->email}");
        $this->command->info("   Role: {$user->role}");
        $this->command->warn("   ⚠️  Remember to change the default password immediately!");
    }
}
