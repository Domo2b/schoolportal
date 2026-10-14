<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_update_profile_and_upload_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2nT8AAAAASUVORK5CYII=', true);
        $response = $this->post('/api/profile', ['name' => 'Updated Name', 'email' => 'updated@example.com', 'avatar' => UploadedFile::fake()->createWithContent('avatar.png', $png)], ['Accept' => 'application/json']);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Name');
        Storage::disk('public')->assertExists($user->refresh()->avatar);
    }

    public function test_profile_rejects_non_image_avatar_with_422(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->post('/api/profile', ['name' => $user->name, 'email' => $user->email, 'avatar' => UploadedFile::fake()->create('notes.txt')], ['Accept' => 'application/json'])
            ->assertUnprocessable()->assertJsonValidationErrors('avatar');
    }
}
