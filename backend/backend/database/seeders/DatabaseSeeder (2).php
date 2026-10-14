<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Portal Administrator',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        $students = Student::factory()->count(10)->create();
        $courses = Course::factory()->count(6)->create();
        $students->each(function (Student $student) use ($courses): void {
            Enrolment::factory()->create([
                'student_id' => $student->id,
                'course_id' => $courses->random()->id,
            ]);
        });
    }
}
