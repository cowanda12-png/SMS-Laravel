<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'gender',
        'date_of_birth',
        'address',
        'guardian_name',
        'guardian_phone',
        'emergency_contact',
        'medical_notes',
        'profile_picture'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with Student (fixed - use singular Student)
    public function student()
    {
        return $this->belongsTo(Students::class); // Changed from Students::class to Student::class
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        return $this->address ?? 'No address provided';
    }

    // Accessor for age
    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age;
        }
        return null;
    }

    // Accessor for formatted date of birth
    public function getFormattedDobAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->format('F d, Y');
        }
        return null;
    }

    // Mutator for date of birth
    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $value;
    }

    // Scope for specific gender
    public function scopeGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    // Scope for male students
    public function scopeMale($query)
    {
        return $query->where('gender', 'male');
    }

    // Scope for female students
    public function scopeFemale($query)
    {
        return $query->where('gender', 'female');
    }
}