<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EnrolmentApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_enrol_student_and_duplicate_returns_422(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create());
        $payload = ['student_id' => $student->id, 'course_id' => $course->id, 'session' => '2026/2027', 'semester' => 'first', 'status' => 'enrolled'];

        $this->postJson('/api/enrolments', $payload)->assertCreated()->assertJsonPath('data.course.code', $course->code);
        $this->postJson('/api/enrolments', $payload)->assertUnprocessable()->assertJsonValidationErrors('course_id');

        $this->assertDatabaseCount('course_student', 1);
    }

    public function test_student_only_lists_their_enrolments_and_cannot_delete_them(): void
    {
        $ownEnrolment = Enrolment::factory()->create();
        Enrolment::factory()->create();
        Sanctum::actingAs($ownEnrolment->student->user);

        $this->getJson('/api/enrolments')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ownEnrolment->id);
        $this->deleteJson("/api/enrolments/{$ownEnrolment->id}")->assertForbidden();
    }

    public function test_admin_deletes_enrolment_without_deleting_student_or_course(): void
    {
        $enrolment = Enrolment::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->deleteJson("/api/enrolments/{$enrolment->id}")->assertNoContent();

        $this->assertDatabaseMissing('course_student', ['id' => $enrolment->id]);
        $this->assertDatabaseHas('students', ['id' => $enrolment->student_id]);
        $this->assertDatabaseHas('courses', ['id' => $enrolment->course_id]);
    }
}
