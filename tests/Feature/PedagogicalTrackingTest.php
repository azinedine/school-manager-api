<?php

namespace Tests\Feature;

use App\Models\GradeClass;
use App\Models\GradeStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PedagogicalTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_tracking_for_own_student()
    {
        $teacher = User::factory()->create();
        $class = GradeClass::create([
            'user_id' => $teacher->id,
            'name' => 'Test Class',
            'academic_year' => '2025-2026',
        ]);
        $student = GradeStudent::create([
            'grade_class_id' => $class->id,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'sort_order' => 1,
        ]);

        Sanctum::actingAs($teacher);

        $response = $this->putJson("/api/v1/grade-students/{$student->id}/tracking", [
            'term' => 1,
            'oral_interrogation' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'oral_interrogation' => true,
                    'notebook_checked' => false,
                ],
            ]);

        $this->assertDatabaseHas('student_pedagogical_tracking', [
            'grade_student_id' => $student->id,
            'term' => 1,
            'oral_interrogation' => 1,
        ]);
    }
}
