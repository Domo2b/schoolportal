<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_valid_credentials_return_user_and_token(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com', 'password' => 'secret123']);

        $response = $this->postJson('/api/login', ['identifier' => 'admin@example.com', 'password' => 'secret123']);

        $response->assertOk()->assertJsonPath('user.email', 'admin@example.com')->assertJsonPath('user.role', 'admin')->assertJsonStructure(['token']);
    }

    public function test_invalid_credentials_return_422(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $this->postJson('/api/login', ['identifier' => 'user@example.com', 'password' => 'wrong-password'])
            ->assertUnprocessable()->assertJsonValidationErrors('identifier');
    }

    public function test_student_can_login_with_matric_number(): void
    {
        $student = Student::factory()->create(['matric_no' => 'SCP/1234']);

        $this->postJson('/api/login', ['identifier' => 'SCP/1234', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('user.role', 'student')
            ->assertJsonStructure(['token']);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test');

        $this->withToken($token->plainTextToken)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->accessToken->id]);
    }
}
