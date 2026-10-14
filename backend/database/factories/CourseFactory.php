<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CSC###')),
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'credit_unit' => fake()->numberBetween(1, 6),
            'status' => 'active',
        ];
    }
}
