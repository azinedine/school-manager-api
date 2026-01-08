<?php

namespace Database\Seeders;

use App\Models\LessonPreparation;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonPreparationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have at least one teacher
        $teacher = User::where('role', 'teacher')->first() ?? User::factory()->create(['role' => 'teacher']);

        // Create some ready preparations for the library
        LessonPreparation::factory()
            ->count(10)
            ->create([
                'teacher_id' => $teacher->id,
                'status' => 'ready',
            ]);
        
        // Create some draft preparations
        LessonPreparation::factory()
            ->count(5)
            ->create([
                'teacher_id' => $teacher->id,
                'status' => 'draft',
            ]);
    }
}
