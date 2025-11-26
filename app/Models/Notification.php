<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($notification) {
            $data = $notification->data;
            if (! $notification->title && is_array($data) && isset($data['title'])) {
                $notification->title = $data['title'];
            }
            if (! $notification->message && is_array($data) && isset($data['message'])) {
                $notification->message = $data['message'];
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
