<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_api_login_successful(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil',
                'admin' => [
                    'id' => $admin->id,
                    'name' => 'Admin Test',
                    'email' => 'admin@test.com',
                ],
            ])
            ->assertJsonStructure(['success', 'message', 'token', 'admin']);
    }

    public function test_admin_api_login_invalid_credentials(): void
    {
        Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email atau password salah.',
            ]);
    }

    public function test_unauthenticated_api_request_returns_json_401(): void
    {
        $response = $this->getJson('/admin');

        $response->assertStatus(401);
    }

    public function test_admin_api_logout(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($admin, 'admin')->postJson('/admin/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil logout',
            ]);
    }
}
