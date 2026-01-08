<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have at least one institution and one teacher
        $institution = Institution::first() ?? Institution::factory()->create();
        $teacher = User::where('role', 'teacher')->first() ?? User::factory()->create(['role' => 'teacher']);

        Lesson::factory()
            ->count(10)
            ->create([
                'institution_id' => $institution->id,
                'teacher_id' => $teacher->id,
            ]);
        
        // Also create some random ones
        Lesson::factory()
            ->count(5)
            ->create();
    }
}
