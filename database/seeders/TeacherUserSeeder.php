<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use App\Models\Wilaya;
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

        // Get an institution with its location data
        $institution = Institution::with(['wilaya', 'municipality'])->first();

        if (! $institution) {
            $this->command->warn('No institution found. Please run InstitutionSeeder first.');

            return;
        }

        $user = User::create([
            'name' => env('TEACHER_NAME', 'Ahmed Benali'),
            'name_ar' => 'أحمد بن علي',
            'email' => $email,
            'password' => env('TEACHER_PASSWORD', 'password'),
            'role' => User::ROLE_TEACHER,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),

            // Location
            'wilaya' => $institution->wilaya_id,
            'municipality' => $institution->municipality_id,

            // Institution Link
            'institution_id' => $institution->id,
            'user_institution_id' => 'TCH-'.date('Y').'-001',

            // Personal Details
            'gender' => 'male',
            'date_of_birth' => '1985-03-15',
            'phone' => '0551234567',
            'address' => $institution->address ?? 'School Address',

            // Teacher Professional Details
            'teacher_id' => 'T-'.strtoupper(substr($institution->wilaya?->name ?? 'ALG', 0, 3)).'-001',
            'years_of_experience' => 8,
            'employment_status' => 'active',
            'weekly_teaching_load' => 18,

            // Academic Details
            'subjects' => ['Mathematics', 'Physics'],
            'levels' => ['1AM', '2AM', '3AM', '4AM'],
            'assigned_classes' => ['1AM-A', '2AM-B', '3AM-A'],
            'groups' => ['Science Group', 'Math Olympiad'],

            'status' => 'active',
        ]);

        $this->command->info('✅ Teacher created successfully!');
        $this->command->info("   Name: {$user->name}");
        $this->command->info("   Email: {$user->email}");
        $this->command->info("   Institution: {$institution->name}");
        $this->command->warn('   ⚠️  Password: password (change immediately!)');
    }
}
