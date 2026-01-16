<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Institution::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' School',
            'name_ar' => 'مدرسة '.$this->faker->company(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'type' => $this->faker->randomElement(['primary', 'middle', 'high']),
            'is_active' => $this->faker->boolean(90),
            // These will likely need to be provided when using the factory, OR we can default to creating them
            'wilaya_id' => 1, // Default to Algiers or similar if exists, or use factory
            'municipality_id' => 1,
        ];
    }
}
