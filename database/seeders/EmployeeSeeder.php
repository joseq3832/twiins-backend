<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\ImmediateFamily;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 employees
        Employee::factory(100)->create()->each(function ($employee) {
            // Create 1-4 immediate family members for each employee
            ImmediateFamily::factory(rand(1, 4))->create([
                'employee_id' => $employee->id
            ]);
        });
    }
}
