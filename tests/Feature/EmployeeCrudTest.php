<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_employees()
    {
        Employee::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/employees');
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'fullname', 'email', 'position', 'created_at', 'updated_at'],
            ]);
    }

    public function test_create_employee()
    {
        $data = [
            'fullname' => 'Juan Perez',
            'email' => 'juan@example.com',
            'position' => 'Developer',
        ];
        $response = $this->postJson('/api/v1/employees', $data);
        $response->assertStatus(201)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('employees', $data);
    }

    public function test_show_employee()
    {
        $employee = Employee::factory()->create();
        $response = $this->getJson("/api/v1/employees/{$employee->id}");
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $employee->id,
                'fullname' => $employee->fullname,
            ]);
    }

    public function test_update_employee()
    {
        $employee = Employee::factory()->create();
        $data = [
            'fullname' => 'Nuevo Nombre',
            'email' => 'nuevo@example.com',
            'position' => 'Manager',
        ];
        $response = $this->putJson("/api/v1/employees/{$employee->id}", $data);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('employees', $data);
    }

    public function test_delete_employee()
    {
        $employee = Employee::factory()->create();
        $response = $this->deleteJson("/api/v1/employees/{$employee->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }
}
