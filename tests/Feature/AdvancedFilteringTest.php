<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\ImmediateFamily;
use App\Repositories\EmployeeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdvancedFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected EmployeeRepository $employeeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeRepository = new EmployeeRepository(new Employee());
    }

    public function test_basic_pagination()
    {
        // Create test data
        Employee::factory()->count(25)->create();

        $request = new Request([
            'page' => 1,
            'limit' => 10,
        ]);

        $result = $this->employeeRepository->filter($request);

        $this->assertEquals(10, count($result['data']));
        $this->assertEquals(25, $result['meta']['total']);
        $this->assertEquals(1, $result['meta']['current_page']);
    }

    public function test_search_functionality()
    {
        // Create test employees
        Employee::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        Employee::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        Employee::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

        $request = new Request(['search' => 'John']);
        $result = $this->employeeRepository->filter($request);

        $this->assertEquals(2, $result['meta']['total']); // John Doe and Bob Johnson
    }

    public function test_sorting_functionality()
    {
        // Create employees with different names
        Employee::factory()->create(['name' => 'Charlie']);
        Employee::factory()->create(['name' => 'Alice']);
        Employee::factory()->create(['name' => 'Bob']);

        // Test ascending sort
        $request = new Request(['sort' => 'name']);
        $result = $this->employeeRepository->filter($request);
        $names = array_map(fn ($emp) => $emp->name, $result['data']);
        $this->assertEquals(['Alice', 'Bob', 'Charlie'], $names);

        // Test descending sort
        $request = new Request(['sort' => '-name']);
        $result = $this->employeeRepository->filter($request);
        $names = array_map(fn ($emp) => $emp->name, $result['data']);
        $this->assertEquals(['Charlie', 'Bob', 'Alice'], $names);
    }

    public function test_multiple_column_sorting()
    {
        // Create employees with same position but different names
        Employee::factory()->create(['name' => 'Bob', 'position' => 'Developer']);
        Employee::factory()->create(['name' => 'Alice', 'position' => 'Developer']);
        Employee::factory()->create(['name' => 'Charlie', 'position' => 'Manager']);

        $request = new Request(['sort' => 'position,-name']);
        $result = $this->employeeRepository->filter($request);
        $data = array_map(fn ($emp) => $emp->position.':'.$emp->name, $result['data']);
        $this->assertEquals(['Developer:Bob', 'Developer:Alice', 'Manager:Charlie'], $data);
    }

    public function test_column_selection()
    {
        Employee::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);

        $request = new Request(['select' => 'id,name']);
        $result = $this->employeeRepository->filter($request);
        $employee = $result['data'][0];

        $this->assertArrayHasKey('id', $employee->toArray());
        $this->assertArrayHasKey('name', $employee->toArray());
        $this->assertArrayNotHasKey('email', $employee->toArray());
    }

    public function test_equality_filter()
    {
        Employee::factory()->create(['position' => 'Developer']);
        Employee::factory()->create(['position' => 'Manager']);
        Employee::factory()->create(['position' => 'Developer']);

        $request = new Request([
            'filter' => [
                'position' => ['$eq' => 'Developer'],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(2, $result['meta']['total']);
    }

    public function test_not_equal_filter()
    {
        Employee::factory()->create(['position' => 'Developer']);
        Employee::factory()->create(['position' => 'Manager']);
        Employee::factory()->create(['position' => 'Developer']);

        $request = new Request([
            'filter' => [
                'position' => ['$not' => 'Developer'],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(1, $result['meta']['total']);
    }

    public function test_in_filter()
    {
        Employee::factory()->create(['position' => 'Developer']);
        Employee::factory()->create(['position' => 'Manager']);
        Employee::factory()->create(['position' => 'Designer']);
        Employee::factory()->create(['position' => 'Tester']);

        $request = new Request([
            'filter' => [
                'position' => ['$in' => ['Developer', 'Manager']],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(2, $result['meta']['total']);
    }

    public function test_greater_than_filter()
    {
        Employee::factory()->create(['hire_date' => '2020-01-01']);
        Employee::factory()->create(['hire_date' => '2022-01-01']);
        Employee::factory()->create(['hire_date' => '2023-01-01']);

        $request = new Request([
            'filter' => [
                'hire_date' => ['$gt' => '2021-01-01'],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(2, $result['meta']['total']);
    }

    public function test_between_filter()
    {
        Employee::factory()->create(['hire_date' => '2020-01-01']);
        Employee::factory()->create(['hire_date' => '2022-01-01']);
        Employee::factory()->create(['hire_date' => '2023-01-01']);
        Employee::factory()->create(['hire_date' => '2024-01-01']);

        $request = new Request([
            'filter' => [
                'hire_date' => ['$btw' => ['2021-01-01', '2023-12-31']],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(2, $result['meta']['total']);
    }

    public function test_ilike_filter()
    {
        Employee::factory()->create(['name' => 'John Doe']);
        Employee::factory()->create(['name' => 'Jane Smith']);
        Employee::factory()->create(['name' => 'Johnny Cash']);

        $request = new Request([
            'filter' => [
                'name' => ['$ilike' => 'john'],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(2, $result['meta']['total']); // John Doe and Johnny Cash
    }

    public function test_starts_with_filter()
    {
        Employee::factory()->create(['name' => 'John Doe']);
        Employee::factory()->create(['name' => 'Jane Smith']);
        Employee::factory()->create(['name' => 'Johnny Cash']);

        $request = new Request([
            'filter' => [
                'name' => ['$sw' => 'John'],
            ],
        ]);

        $result = $this->employeeRepository->filter($request);
        $this->assertEquals(2, $result['meta']['total']); // John Doe and Johnny Cash
    }

    public function test_includes_relations()
    {
        $employee = Employee::factory()->create();
        ImmediateFamily::factory()->count(2)->create(['employee_id' => $employee->id]);

        $request = new Request(['include' => 'immediateFamily']);
        $result = $this->employeeRepository->filter($request);
        $employee = $result['data'][0];

        $this->assertTrue($employee->relationLoaded('immediateFamily'));
        $this->assertEquals(2, $employee->immediateFamily->count());
    }

    public function test_complex_combined_filters()
    {
        // Create test data
        Employee::factory()->create([
            'name' => 'John Developer',
            'position' => 'Developer',
            'hire_date' => '2022-01-01',
        ]);
        Employee::factory()->create([
            'name' => 'Jane Manager',
            'position' => 'Manager',
            'hire_date' => '2023-01-01',
        ]);
        Employee::factory()->create([
            'name' => 'Bob Developer',
            'position' => 'Developer',
            'hire_date' => '2021-01-01',
        ]);

        $request = new Request([
            'search' => 'Developer',
            'filter' => [
                'position' => ['$eq' => 'Developer'],
                'hire_date' => ['$gte' => '2022-01-01'],
            ],
            'sort' => '-name',
            'select' => 'id,name,position',
            'page' => 1,
            'limit' => 10,
        ]);

        $result = $this->employeeRepository->filter($request);

        $this->assertEquals(1, $result['meta']['total']); // Only John Developer matches all criteria
        $employee = $result['data'][0];
        $this->assertEquals('John Developer', $employee->name);
        $this->assertArrayNotHasKey('email', $employee->toArray());
    }
}
