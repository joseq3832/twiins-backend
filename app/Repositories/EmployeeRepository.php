<?php

namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
        
        // Configure searchable columns
        $this->setSearchableColumns([
            'name',
            'email',
            'position'
        ]);
        
        // Configure filterable columns
        $this->setFilterableColumns([
            'id',
            'name',
            'email',
            'position',
            'hire_date',
            'created_at',
            'updated_at'
        ]);
        
        // Configure sortable columns
        $this->setSortableColumns([
            'id',
            'name',
            'email',
            'position',
            'hire_date',
            'created_at',
            'updated_at'
        ]);
        
        // Configure selectable columns
        $this->setSelectableColumns([
            'id',
            'name',
            'email',
            'position',
            'hire_date',
            'created_at',
            'updated_at'
        ]);
        
        // Configure includable relations
        $this->setIncludableRelations([
            'immediateFamily'
        ]);
    }
}
