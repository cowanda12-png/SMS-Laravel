<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'course_name',
        'code',
        'description',
        'credits',
        'status'
    ];

    // Relationship with Students
    public function students()
    {
        return $this->hasMany(Students::class, 'course_id', 'id');
    }

    // Get display name
    public function getDisplayNameAttribute()
    {
        return $this->course_name ?? $this->name ?? 'Unnamed Course';
    }
}