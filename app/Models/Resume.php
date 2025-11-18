<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'personal_info',
        'objective',
        'education',
        'work_experience',
        'skills',
        'certifications',
        'template_path',
        'profile_image',
        'submitted_to_documents',
        'submitted_at',
    ];

    protected $casts = [
        'personal_info' => 'array',
        'education' => 'array',
        'work_experience' => 'array',
        'skills' => 'array',
        'certifications' => 'array',
        'references' => 'array',
        'submitted_at' => 'datetime',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
