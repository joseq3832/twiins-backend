<?php

namespace App\Repositories;

use App\Models\ImmediateFamily;

class ImmediateFamilyRepository extends BaseRepository
{
    public function __construct(ImmediateFamily $model)
    {
        parent::__construct($model);
        
        // Configure searchable columns
        $this->setSearchableColumns([
            'relative_name',
            'relationship'
        ]);
        
        // Configure filterable columns
        $this->setFilterableColumns([
            'id',
            'employee_id',
            'relative_name',
            'relationship',
            'birth_date',
            'created_at',
            'updated_at'
        ]);
        
        // Configure sortable columns
        $this->setSortableColumns([
            'id',
            'employee_id',
            'relative_name',
            'relationship',
            'birth_date',
            'created_at',
            'updated_at'
        ]);
        
        // Configure selectable columns
        $this->setSelectableColumns([
            'id',
            'employee_id',
            'relative_name',
            'relationship',
            'birth_date',
            'created_at',
            'updated_at'
        ]);
        
        // Configure includable relations
        $this->setIncludableRelations([
            'employee'
        ]);
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
