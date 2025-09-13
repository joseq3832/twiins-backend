<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user registration
     */
    public function test_user_can_register(): void
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'access_token',
                'refresh_token',
                'token_type',
                'expires_in',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);
    }

    /**
     * Test user login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'access_token',
                'refresh_token',
                'token_type',
                'expires_in',
            ]);
    }

    /**
     * Test user login with invalid credentials
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Credenciales inválidas',
            ]);
    }

    /**
     * Test authenticated user can access protected routes
     */
    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
            ]);
    }

    /**
     * Test unauthenticated user cannot access protected routes
     */
    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test user can refresh token
     */
    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Crear un refresh token manualmente para el test
        $refreshToken = $user->refreshTokens()->create([
            'token' => bin2hex(random_bytes(32)),
            'expires_at' => now()->addDays(30),
            'user_agent' => 'Test Agent',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'refresh_token',
                'token_type',
                'expires_in',
            ]);
    }

    /**
     * Test user can logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout exitoso',
            ]);
    }

    /**
     * Test registration validation
     */
    public function test_registration_validation(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test login validation
     */
    public function test_login_validation(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
