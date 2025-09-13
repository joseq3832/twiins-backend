<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\ImmediateFamily;

class ImmediateFamilyCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
    }

    public function test_list_immediate_family()
    {
        // Create some immediate family members
        ImmediateFamily::factory()->count(3)->create([
            'employee_id' => $this->employee->id
        ]);

        $response = $this->getJson('/api/v1/immediate-family');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_create_immediate_family()
    {
        $immediateFamilyData = [
            'employee_id' => $this->employee->id,
            'family_name' => 'John Doe',
            'relationship' => 'spouse',
            'date_of_birth' => '1990-05-15'
        ];

        $response = $this->postJson('/api/v1/immediate-family', $immediateFamilyData);

        $response->assertStatus(201)
                 ->assertJson([
                     'employee_id' => $this->employee->id,
                     'family_name' => 'John Doe',
                     'relationship' => 'spouse'
                 ])
                 ->assertJsonFragment([
                     'date_of_birth' => '1990-05-15T00:00:00.000000Z'
                 ]);

        $this->assertDatabaseHas('immediate_family', $immediateFamilyData);
    }

    public function test_show_immediate_family()
    {
        $immediateFamily = ImmediateFamily::factory()->create([
            'employee_id' => $this->employee->id
        ]);

        $response = $this->getJson('/api/v1/immediate-family/' . $immediateFamily->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $immediateFamily->id,
                     'employee_id' => $this->employee->id,
                     'family_name' => $immediateFamily->family_name,
                     'relationship' => $immediateFamily->relationship
                 ]);
    }

    public function test_update_immediate_family()
    {
        $immediateFamily = ImmediateFamily::factory()->create([
            'employee_id' => $this->employee->id
        ]);

        $updateData = [
            'family_name' => 'Jane Smith',
            'relationship' => 'child',
            'date_of_birth' => '2010-03-20'
        ];

        $response = $this->putJson('/api/v1/immediate-family/' . $immediateFamily->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $immediateFamily->id,
                     'family_name' => 'Jane Smith',
                     'relationship' => 'child'
                 ])
                 ->assertJsonFragment([
                     'date_of_birth' => '2010-03-20T00:00:00.000000Z'
                 ]);

        $this->assertDatabaseHas('immediate_family', [
            'id' => $immediateFamily->id,
            'family_name' => 'Jane Smith',
            'relationship' => 'child'
        ]);
    }

    public function test_delete_immediate_family()
    {
        $immediateFamily = ImmediateFamily::factory()->create([
            'employee_id' => $this->employee->id
        ]);

        $response = $this->deleteJson('/api/v1/immediate-family/' . $immediateFamily->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('immediate_family', [
            'id' => $immediateFamily->id
        ]);
    }

    public function test_get_immediate_family_by_employee()
    {
        // Create immediate family for this employee
        ImmediateFamily::factory()->count(2)->create([
            'employee_id' => $this->employee->id
        ]);

        // Create immediate family for another employee
        $anotherEmployee = Employee::factory()->create();
        ImmediateFamily::factory()->create([
            'employee_id' => $anotherEmployee->id
        ]);

        $response = $this->getJson('/api/v1/employees/' . $this->employee->id . '/immediate-family');

        $response->assertStatus(200)
                 ->assertJsonCount(2);

        // Verify all returned family members belong to the correct employee
        $familyMembers = $response->json();
        foreach ($familyMembers as $member) {
            $this->assertEquals($this->employee->id, $member['employee_id']);
        }
    }

    public function test_create_immediate_family_validation_errors()
    {
        // Test missing required fields
        $response = $this->postJson('/api/v1/immediate-family', []);
        $response->assertStatus(422);

        // Test invalid employee_id
        $response = $this->postJson('/api/v1/immediate-family', [
            'employee_id' => 999999,
            'family_name' => 'John Doe',
            'relationship' => 'spouse',
            'date_of_birth' => '1990-05-15'
        ]);
        $response->assertStatus(422);

        // Test invalid date format
        $response = $this->postJson('/api/v1/immediate-family', [
            'employee_id' => $this->employee->id,
            'family_name' => 'John Doe',
            'relationship' => 'spouse',
            'date_of_birth' => 'invalid-date'
        ]);
        $response->assertStatus(422);
    }

    public function test_show_nonexistent_immediate_family()
    {
        $response = $this->getJson('/api/v1/immediate-family/999999');
        $response->assertStatus(404);
    }

    public function test_update_nonexistent_immediate_family()
    {
        $response = $this->putJson('/api/v1/immediate-family/999999', [
            'family_name' => 'Test Name'
        ]);
        $response->assertStatus(404);
    }

    public function test_delete_nonexistent_immediate_family()
    {
        $response = $this->deleteJson('/api/v1/immediate-family/999999');
        $response->assertStatus(404);
    }
}