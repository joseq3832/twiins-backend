<?php

namespace App\Repositories;

use App\Models\ImmediateFamily;

class ImmediateFamilyRepository extends BaseRepository
{
    public function __construct(ImmediateFamily $model)
    {
        parent::__construct($model);
    }

    public function getByEmployeeId($employeeId)
    {
        return $this->model->where('employee_id', $employeeId)->get();
    }

    public function createForEmployee($employeeId, array $data)
    {
        $data['employee_id'] = $employeeId;

        return $this->create($data);
    }
}
