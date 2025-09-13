<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImmediateFamily extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'immediate_family';

    protected $fillable = [
        'employee_id',
        'family_name',
        'relationship',
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
