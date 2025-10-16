<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_required',
        'file_types',
        'max_file_size_mb',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'file_types' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function submissions()
    {
        return $this->hasMany(StudentDocumentSubmission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrePlacement($query)
    {
        return $query->where('type', 'pre_placement');
    }

    public function scopePostPlacement($query)
    {
        return $query->where('type', 'post_placement');
    }

    public function scopeOngoing($query)
    {
        return $query->where('type', 'ongoing');
    }

    // Helper methods
    public function getFileTypesStringAttribute()
    {
        return $this->file_types ? implode(', ', $this->file_types) : 'Any';
    }

    public function getMaxFileSizeStringAttribute()
    {
        return $this->max_file_size_mb . ' MB';
    }
}
