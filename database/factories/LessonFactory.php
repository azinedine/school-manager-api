<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'teacher_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs(3, true),
            'lesson_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'academic_year' => '2023-2024',
            'class_name' => $this->faker->randomElement(['1AM', '2AM', '3AM', '4AM']),
            'subject_name' => $this->faker->randomElement(['Mathematics', 'Physics', 'Science', 'History', 'Geography']),
            'status' => $this->faker->randomElement([Lesson::STATUS_DRAFT, Lesson::STATUS_PUBLISHED]),
        ];
    }
}
