<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function coordinators()
    {
        return $this->hasManyThrough(
            User::class,
            CoordinatorProfile::class,
            'department_id',
            'id',
            'id',
            'user_id'
        );
    }
}
