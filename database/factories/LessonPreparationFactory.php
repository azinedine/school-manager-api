<?php

namespace Database\Factories;

use App\Models\LessonPreparation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LessonPreparation>
 */
class LessonPreparationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonPreparation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory(),
            'lesson_number' => $this->faker->numberBetween(1, 20),
            'subject' => $this->faker->randomElement(['Mathematics', 'Science', 'History', 'Physics']),
            'level' => $this->faker->randomElement(['1AM', '2AM', '3AM', '4AM']),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'duration_minutes' => $this->faker->randomElement([45, 60, 90]),
            'learning_objectives' => $this->faker->sentences(3),
            'description' => $this->faker->paragraph(),
            'teaching_methods' => $this->faker->words(3),
            'resources_needed' => $this->faker->words(4),
            'assessment_methods' => $this->faker->words(2),
            'assessment_criteria' => $this->faker->sentence(),
            'notes' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['draft', 'ready', 'delivered']),

            // Pedagogical Fields
            'domain' => $this->faker->word(),
            'learning_unit' => $this->faker->words(2, true),
            'knowledge_resource' => $this->faker->sentence(),
            'lesson_elements' => [
                ['id' => uniqid(), 'content' => $this->faker->sentence()],
                ['id' => uniqid(), 'content' => $this->faker->sentence()],
            ],
            'evaluation_type' => $this->faker->randomElement(['assessment', 'homework']),
            'evaluation_content' => $this->faker->paragraph(),

            // New Fields
            'targeted_knowledge' => $this->faker->sentences(2),
            'used_materials' => $this->faker->words(3),
            'references' => $this->faker->words(2),
            'phases' => [
                [
                    'type' => 'departure',
                    'content' => $this->faker->paragraph(),
                    'duration_minutes' => 10,
                ],
                [
                    'type' => 'presentation',
                    'content' => $this->faker->paragraph(),
                    'duration_minutes' => 30,
                ],
                [
                    'type' => 'consolidation',
                    'content' => $this->faker->paragraph(),
                    'duration_minutes' => 5,
                ],
            ],
            'activities' => [
                ['content' => $this->faker->sentence()],
            ],
        ];
    }
}
