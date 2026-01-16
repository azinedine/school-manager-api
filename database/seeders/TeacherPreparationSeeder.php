<?php

namespace Database\Seeders;

use App\Models\LessonPreparation;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherPreparationSeeder extends Seeder
{
    /**
     * Seed lesson preparations for the default teacher.
     *
     * Run with: php artisan db:seed --class=TeacherPreparationSeeder
     */
    public function run(): void
    {
        $teacherEmail = env('TEACHER_EMAIL', 'teacher@school.com');
        $teacher = User::where('email', $teacherEmail)->first();

        if (! $teacher) {
            $this->command->warn("Teacher {$teacherEmail} not found. Please run TeacherUserSeeder first.");

            return;
        }

        $this->command->info("Creating lesson preparations for {$teacher->name}...");

        // Create Ready Preparations (Visible in Library)
        LessonPreparation::factory()
            ->count(6)
            ->create([
                'teacher_id' => $teacher->id,
                'status' => 'ready',
                'subject' => 'Mathematics',
                'level' => '1AS',
            ]);

        LessonPreparation::factory()
            ->count(4)
            ->create([
                'teacher_id' => $teacher->id,
                'status' => 'ready',
                'subject' => 'Physics',
                'level' => '2AS',
            ]);

        // Create Draft Preparations (Visible in Preparation Tab)
        LessonPreparation::factory()
            ->count(3)
            ->create([
                'teacher_id' => $teacher->id,
                'status' => 'draft',
                'subject' => 'Computer Science',
                'level' => '3AS',
            ]);

        // Create Delivered Preparations
        LessonPreparation::factory()
            ->count(2)
            ->create([
                'teacher_id' => $teacher->id,
                'status' => 'delivered',
                'subject' => 'Mathematics',
                'level' => '1AS',
            ]);

        $this->command->info("✅ Created 15 lesson preparations for {$teacher->email}!");
    }
}
