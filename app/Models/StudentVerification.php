<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentVerification extends Model
{
	use HasFactory;

	protected $fillable = [
		'student_id',
		'token',
		'expires_at',
	];

	protected $casts = [
		'expires_at' => 'datetime',
	];
}


