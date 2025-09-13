<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticatedHeaders()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_list_employees()
    {
        Employee::factory()->count(3)->create();
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson('/api/v1/employees');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'position', 'created_at', 'updated_at'],
                ],
                'meta' => [
                    'current_page',
                    'total',
                    'per_page',
                    'last_page',
                    'from',
                    'to',
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);
    }

    public function test_create_employee()
    {
        $data = [
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'position' => 'Developer',
            'hire_date' => '2023-01-15',
        ];
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/v1/employees', $data);
        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Juan Perez',
                'email' => 'juan@example.com',
                'position' => 'Developer',
            ]);
        $this->assertDatabaseHas('employees', $data);
    }

    public function test_show_employee()
    {
        $employee = Employee::factory()->create();
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->getJson("/api/v1/employees/{$employee->id}");
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $employee->id,
                'name' => $employee->name,
            ]);
    }

    public function test_update_employee()
    {
        $employee = Employee::factory()->create();
        $data = [
            'name' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
            'position' => 'Manager',
        ];
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->putJson("/api/v1/employees/{$employee->id}", $data);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('employees', $data);
    }

    public function test_delete_employee()
    {
        $employee = Employee::factory()->create();
        $response = $this->withHeaders($this->authenticatedHeaders())
            ->deleteJson("/api/v1/employees/{$employee->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }
}
