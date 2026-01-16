<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;
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

        // Get the first available institution or create one via factory if none exist (though InstitutionSeeder should have run)
        $institution = Institution::first();

        if (! $institution) {
            $this->command->warn('No institution found. Please run InstitutionSeeder first.');

            return;
        }

        $user = User::create([
            'name' => env('TEACHER_NAME', 'Teacher User'),
            'email' => $email,
            'password' => env('TEACHER_PASSWORD', 'password'), // Will be hashed by model cast
            'role' => User::ROLE_TEACHER,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),

            // Institution Link
            'institution_id' => $institution->id,
            'user_institution_id' => 'TCH-'.date('Y').'-001', // Example ID format

            // Academic Details
            'subjects' => ['Mathematics', 'Physics', 'Computer Science'],
            'levels' => ['1AS', '2AS', '3AS'],

            // Professional Details
            'years_of_experience' => 5,
            'status' => 'active',
        ]);

        $this->command->info('✅ Teacher created successfully!');
        $this->command->info("   Email: {$user->email}");
        $this->command->info("   Role: {$user->role}");
        $this->command->warn('   ⚠️  Remember to change the default password immediately!');
    }
}
