<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherLessonSeeder extends Seeder
{
    /**
     * Seed lessons for the default teacher.
     *
     * Run with: php artisan db:seed --class=TeacherLessonSeeder
     */
    public function run(): void
    {
        $teacherEmail = env('TEACHER_EMAIL', 'teacher@school.com');
        $teacher = User::where('email', $teacherEmail)->first();

        if (! $teacher) {
            $this->command->warn("Teacher {$teacherEmail} not found. Please run TeacherUserSeeder first.");

            return;
        }

        $institutionId = $teacher->institution_id ?? Institution::first()->id;

        if (! $institutionId) {
            $this->command->warn('No institution found to link lessons to.');

            return;
        }

        $this->command->info("Creating lessons for {$teacher->name}...");

        // Create Published Lessons
        Lesson::factory()
            ->count(5)
            ->create([
                'teacher_id' => $teacher->id,
                'institution_id' => $institutionId,
                'status' => Lesson::STATUS_PUBLISHED,
                'subject_name' => 'Mathematics',
                'class_name' => '1AS',
            ]);

        // Create Draft Lessons
        Lesson::factory()
            ->count(3)
            ->create([
                'teacher_id' => $teacher->id,
                'institution_id' => $institutionId,
                'status' => Lesson::STATUS_DRAFT,
                'subject_name' => 'Physics',
                'class_name' => '2AS',
                'lesson_date' => now()->addDays(rand(1, 10)),
            ]);

        // Create Future Lessons
        Lesson::factory()
            ->count(3)
            ->create([
                'teacher_id' => $teacher->id,
                'institution_id' => $institutionId,
                'status' => Lesson::STATUS_PUBLISHED,
                'subject_name' => 'Computer Science',
                'class_name' => '3AS',
                'lesson_date' => now()->addDays(rand(5, 20)),
            ]);

        $this->command->info("✅ Created 11 lessons for {$teacher->email}!");
    }
}
