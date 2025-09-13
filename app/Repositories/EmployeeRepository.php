<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }
}
