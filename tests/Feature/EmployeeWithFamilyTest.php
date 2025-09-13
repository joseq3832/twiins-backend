<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\ImmediateFamily;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeeWithFamilyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    protected function authenticatedHeaders()
    {
        $token = JWTAuth::fromUser($this->user);

        return [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function test_create_employee_with_immediate_family()
    {
        $employeeData = [
            'name' => 'José Alarcón',
            'email' => 'jose.alarcon@example.com',
            'position' => 'Developer',
            'hire_date' => '2025-09-17',
            'immediate_family' => [
                [
                    'family_name' => 'jose antonio',
                    'relationship' => 'Hijo/a',
                    'date_of_birth' => '2025-09-16',
                ],
                [
                    'family_name' => 'María Alarcón',
                    'relationship' => 'Esposa',
                    'date_of_birth' => '1990-05-15',
                ],
            ],
        ];

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/v1/employees', $employeeData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'José Alarcón',
                'email' => 'jose.alarcon@example.com',
                'position' => 'Developer',
            ])
            ->assertJsonCount(2, 'immediate_family');

        // Verify employee was created
        $this->assertDatabaseHas('employees', [
            'name' => 'José Alarcón',
            'email' => 'jose.alarcon@example.com',
            'position' => 'Developer',
        ]);

        // Verify immediate family members were created
        $employee = Employee::where('email', 'jose.alarcon@example.com')->first();
        $this->assertDatabaseHas('immediate_family', [
            'employee_id' => $employee->id,
            'family_name' => 'jose antonio',
            'relationship' => 'Hijo/a',
        ]);
        $this->assertDatabaseHas('immediate_family', [
            'employee_id' => $employee->id,
            'family_name' => 'María Alarcón',
            'relationship' => 'Esposa',
        ]);
    }

    public function test_create_employee_without_immediate_family()
    {
        $employeeData = [
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'position' => 'Manager',
            'hire_date' => '2025-09-17',
        ];

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/v1/employees', $employeeData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'position' => 'Manager',
            ])
            ->assertJsonCount(0, 'immediate_family');

        // Verify employee was created
        $this->assertDatabaseHas('employees', [
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'position' => 'Manager',
        ]);
    }

    public function test_update_employee_add_immediate_family()
    {
        // Create employee without family
        $employee = Employee::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'immediate_family' => [
                [
                    'family_name' => 'New Family Member',
                    'relationship' => 'Hijo/a',
                    'date_of_birth' => '2020-01-01',
                ],
            ],
        ];

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->putJson('/api/v1/employees/'.$employee->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Name',
            ])
            ->assertJsonCount(1, 'immediate_family');

        // Verify immediate family member was created
        $this->assertDatabaseHas('immediate_family', [
            'employee_id' => $employee->id,
            'family_name' => 'New Family Member',
            'relationship' => 'Hijo/a',
        ]);
    }

    public function test_update_employee_modify_immediate_family()
    {
        // Create employee with family
        $employee = Employee::factory()->create();
        $familyMember = ImmediateFamily::factory()->create([
            'employee_id' => $employee->id,
            'family_name' => 'Original Name',
            'relationship' => 'Hijo/a',
        ]);

        $updateData = [
            'immediate_family' => [
                [
                    'id' => $familyMember->id,
                    'family_name' => 'Updated Name',
                    'relationship' => 'Hijo/a',
                    'date_of_birth' => $familyMember->date_of_birth->format('Y-m-d'),
                ],
            ],
        ];

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->putJson('/api/v1/employees/'.$employee->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'immediate_family');

        // Verify immediate family member was updated
        $this->assertDatabaseHas('immediate_family', [
            'id' => $familyMember->id,
            'employee_id' => $employee->id,
            'family_name' => 'Updated Name',
            'relationship' => 'Hijo/a',
        ]);
    }

    public function test_update_employee_delete_immediate_family()
    {
        // Create employee with family
        $employee = Employee::factory()->create();
        $familyMember = ImmediateFamily::factory()->create([
            'employee_id' => $employee->id,
        ]);

        $updateData = [
            'immediate_family' => [
                [
                    'id' => $familyMember->id,
                    'family_name' => $familyMember->family_name,
                    'relationship' => $familyMember->relationship,
                    'date_of_birth' => $familyMember->date_of_birth->format('Y-m-d'),
                    '_delete' => true,
                ],
            ],
        ];

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->putJson('/api/v1/employees/'.$employee->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonCount(0, 'immediate_family');

        // Verify immediate family member was soft deleted
        $this->assertSoftDeleted('immediate_family', [
            'id' => $familyMember->id,
        ]);
    }

    public function test_validation_errors_for_immediate_family()
    {
        $employeeData = [
            'name' => 'Test Employee',
            'email' => 'test@example.com',
            'position' => 'Developer',
            'hire_date' => '2025-09-17',
            'immediate_family' => [
                [
                    // Missing required fields
                    'family_name' => '',
                    'relationship' => '',
                ],
            ],
        ];

        $response = $this->withHeaders($this->authenticatedHeaders())
            ->postJson('/api/v1/employees', $employeeData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'immediate_family.0.family_name',
                'immediate_family.0.relationship',
                'immediate_family.0.date_of_birth',
            ]);
    }
}