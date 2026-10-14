<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'student']),
            'matric_no' => fake()->unique()->bothify('SCP/####'),
            'department' => fake()->randomElement(['Computer Science', 'Information Technology', 'Software Engineering']),
            'level' => fake()->randomElement([100, 200, 300, 400]),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
