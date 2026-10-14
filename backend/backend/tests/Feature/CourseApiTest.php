<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_course_and_duplicate_code_returns_422(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $payload = ['code' => 'CSC101', 'title' => 'Introduction to Computing', 'credit_unit' => 3, 'status' => 'active'];

        $this->postJson('/api/courses', $payload)->assertCreated()->assertJsonPath('data.code', 'CSC101');
        $this->postJson('/api/courses', $payload)->assertUnprocessable()->assertJsonValidationErrors('code');

        $this->assertDatabaseCount('courses', 1);
    }

    public function test_deleting_course_with_enrolments_archives_it(): void
    {
        $enrolment = Enrolment::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->deleteJson("/api/courses/{$enrolment->course_id}")->assertOk();

        $this->assertDatabaseHas('courses', ['id' => $enrolment->course_id, 'status' => 'archived']);
        $this->assertDatabaseHas('course_student', ['id' => $enrolment->id]);
    }

    public function test_student_can_view_but_cannot_manage_courses(): void
    {
        $course = Course::factory()->create();
        Sanctum::actingAs(User::factory()->student()->create());

        $this->getJson('/api/courses')->assertOk();
        $this->patchJson("/api/courses/{$course->id}", ['title' => 'Changed'])->assertForbidden();
    }
}
