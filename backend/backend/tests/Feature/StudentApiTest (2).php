<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_401_when_no_token_is_provided(): void
    {
        $this->getJson('/api/students')->assertUnauthorized();
    }

    public function test_admin_can_create_update_and_delete_student(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $payload = ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'password' => 'password', 'matric_no' => 'SCP/0001', 'department' => 'Computer Science', 'level' => 300, 'status' => 'active'];

        $created = $this->postJson('/api/students', $payload)->assertCreated()->assertJsonPath('data.matric_no', 'SCP/0001');
        $studentId = $created->json('data.id');
        $this->patchJson("/api/students/{$studentId}", ['level' => 400])->assertOk()->assertJsonPath('data.level', 400);
        $this->deleteJson("/api/students/{$studentId}")->assertNoContent();

        $this->assertDatabaseMissing('students', ['id' => $studentId]);
        $this->assertDatabaseMissing('users', ['email' => 'ada@example.com']);
    }

    public function test_duplicate_student_data_returns_422_without_creating_records(): void
    {
        $student = Student::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->postJson('/api/students', ['name' => 'Duplicate', 'email' => $student->user->email, 'password' => 'password', 'matric_no' => $student->matric_no, 'department' => 'Science', 'level' => 200, 'status' => 'active'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'matric_no']);

        $this->assertDatabaseCount('students', 1);
    }

    public function test_password_is_required_when_creating_a_student(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->postJson('/api/students', ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'matric_no' => 'SCP/0001', 'department' => 'Computer Science', 'level' => 300, 'status' => 'active'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    public function test_admin_can_upload_and_remove_a_student_avatar(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->admin()->create());
        $student = Student::factory()->create();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2nT8AAAAASUVORK5CYII=', true);

        $this->post("/api/students/{$student->id}", [
            '_method' => 'PATCH',
            'avatar' => UploadedFile::fake()->createWithContent('avatar.png', $png),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.avatar_url', fn (string $url): bool => str_contains($url, '/storage/avatars/'));

        $avatar = $student->user->refresh()->avatar;
        Storage::disk('public')->assertExists($avatar);

        $this->patchJson("/api/students/{$student->id}", ['remove_avatar' => true])
            ->assertOk()
            ->assertJsonPath('data.avatar_url', null);
        Storage::disk('public')->assertMissing($avatar);
    }

    public function test_student_can_only_list_and_view_their_own_record(): void
    {
        $ownStudent = Student::factory()->create();
        $otherStudent = Student::factory()->create();
        Sanctum::actingAs($ownStudent->user);

        $this->getJson('/api/students')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $ownStudent->id);
        $this->getJson("/api/students/{$otherStudent->id}")->assertForbidden();
        $this->postJson('/api/students', [])->assertForbidden();
    }
}
