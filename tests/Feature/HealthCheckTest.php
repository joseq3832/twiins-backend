<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test health check endpoint returns successful response
     */
    public function test_health_check_returns_successful_response(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'timestamp',
                     'response_time_ms',
                     'version',
                     'environment',
                     'checks' => [
                         'database' => [
                             'status',
                             'message'
                         ],
                         'cache' => [
                             'status',
                             'message'
                         ],
                         'storage' => [
                             'status',
                             'message'
                         ],
                         'application' => [
                             'status',
                             'message'
                         ]
                     ]
                 ]);

        $this->assertContains($response->json('status'), ['healthy', 'degraded', 'unhealthy']);
    }

    /**
     * Test health check includes response time
     */
    public function test_health_check_includes_response_time(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $this->assertIsNumeric($response->json('response_time_ms'));
        $this->assertGreaterThan(0, $response->json('response_time_ms'));
    }

    /**
     * Test health check includes environment information
     */
    public function test_health_check_includes_environment_info(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'environment' => config('app.env')
                 ]);
    }

    /**
     * Test health check database connectivity
     */
    public function test_health_check_database_connectivity(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $databaseCheck = $response->json('checks.database');
        
        $this->assertArrayHasKey('status', $databaseCheck);
        $this->assertArrayHasKey('message', $databaseCheck);
        $this->assertContains($databaseCheck['status'], ['healthy', 'degraded', 'unhealthy']);
    }

    /**
     * Test health check cache functionality
     */
    public function test_health_check_cache_functionality(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $cacheCheck = $response->json('checks.cache');
        
        $this->assertArrayHasKey('status', $cacheCheck);
        $this->assertArrayHasKey('message', $cacheCheck);
        $this->assertContains($cacheCheck['status'], ['healthy', 'degraded', 'unhealthy']);
    }

    /**
     * Test health check storage functionality
     */
    public function test_health_check_storage_functionality(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $storageCheck = $response->json('checks.storage');
        
        $this->assertArrayHasKey('status', $storageCheck);
        $this->assertArrayHasKey('message', $storageCheck);
        $this->assertContains($storageCheck['status'], ['healthy', 'degraded', 'unhealthy']);
    }

    /**
     * Test health check application status
     */
    public function test_health_check_application_status(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $appCheck = $response->json('checks.application');
        
        $this->assertArrayHasKey('status', $appCheck);
        $this->assertArrayHasKey('message', $appCheck);
        $this->assertArrayHasKey('php_version', $appCheck);
        $this->assertArrayHasKey('laravel_version', $appCheck);
        $this->assertContains($appCheck['status'], ['healthy', 'degraded', 'unhealthy']);
    }

    /**
     * Test health check throttling
     */
    public function test_health_check_throttling(): void
    {
        // Make multiple requests to test throttling
        for ($i = 0; $i < 65; $i++) {
            $response = $this->getJson('/api/health');
            
            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                // Should be throttled after 60 requests per minute
                $response->assertStatus(429);
                break;
            }
        }
    }

    /**
     * Test health check returns 503 when database is unavailable
     */
    public function test_health_check_returns_503_when_database_unavailable(): void
    {
        // Mock database connection failure
        DB::shouldReceive('connection->getPdo')
          ->andThrow(new \Exception('Database connection failed'));
        
        DB::shouldReceive('select')
          ->andThrow(new \Exception('Database connection failed'));

        $response = $this->getJson('/api/health');

        $response->assertStatus(503)
                 ->assertJsonFragment([
                     'status' => 'unhealthy'
                 ]);
    }
}