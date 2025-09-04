<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'client'
        ]);

        $this->client = Client::create([
            'user_id' => $this->user->id,
            'company_name' => 'Test Company',
            'slug' => 'test-company',
            'website_url' => 'https://test.com'
        ]);
    }

    /** @test */
    public function it_can_get_client_profile()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/client/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'company_name', 'slug', 'website_url', 'logo',
                    'logo_path', 'created_at', 'updated_at', 'user' => ['name', 'email']
                ]
            ]);
    }

    /** @test */
    public function it_can_update_client_profile()
    {
        $this->actingAs($this->user);

        $data = [
            'company_name' => 'Updated Company',
            'website_url' => 'https://updated.com'
        ];

        $response = $this->putJson('/api/client/profile', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profile updated successfully',
                'data' => [
                    'company_name' => 'Updated Company',
                    'website_url' => 'https://updated.com'
                ]
            ]);
    }

    /** @test */
    public function it_can_upload_logo()
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('logo.jpg', 100, 100);

        $response = $this->postJson('/api/client/upload-logo', [
            'logo' => $file
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['logo_url', 'logo_path']
            ]);

        Storage::disk('public')->assertExists($response->json('data.logo_path'));
    }

    /** @test */
    public function it_validates_logo_upload()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/client/upload-logo', [
            'logo' => $file
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['logo']);
    }

    /** @test */
    public function it_can_get_client_stats()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/client/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_pages', 'published_pages', 'total_views', 'average_views'
                ]
            ]);
    }
}