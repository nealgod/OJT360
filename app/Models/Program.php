<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'slug',
        'required_hours',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function coordinators()
    {
        return $this->hasManyThrough(
            User::class,
            CoordinatorProfile::class,
            'program_id',
            'id',
            'id',
            'user_id'
        );
    }
}


