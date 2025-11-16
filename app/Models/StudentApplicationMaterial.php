<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentApplicationMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'application_letter_path',
        'resume_path',
        'resume_id',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
