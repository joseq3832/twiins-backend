<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'position',
        'hire_date',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function immediateFamily()
    {
        return $this->hasMany(ImmediateFamily::class);
    }
}
