<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_receives_portal_counts(): void
    {
        Student::factory()->count(2)->create(['status' => 'active']);
        Course::factory()->create(['status' => 'active']);
        $student = Student::factory()->create(['status' => 'active']);
        Enrolment::factory()->create(['student_id' => $student->id]);
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/dashboard')->assertOk()->assertJson(['students' => 3, 'active_students' => 3, 'courses' => 2, 'enrolments' => 1]);
    }

    public function test_student_receives_only_their_dashboard_counts(): void
    {
        $student = Student::factory()->create();
        Enrolment::factory()->create(['student_id' => $student->id, 'status' => 'enrolled']);
        Enrolment::factory()->create(['student_id' => $student->id, 'status' => 'completed']);
        Enrolment::factory()->create();
        Sanctum::actingAs($student->user);

        $this->getJson('/api/dashboard')->assertOk()->assertJson([
            'courses' => 2,
            'enrolments' => 1,
            'completed_courses' => 1,
        ])->assertJsonMissingPath('students');
    }
}
