<?php
// app/Models/FeeStructure.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_type',
        'class_id',
        'grade_id',
        'term',
        'academic_year',
        'amount',
        'description',
        'is_compulsory',
        'due_date',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_compulsory' => 'boolean',
        'due_date' => 'date'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByGrade($query, $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    public function scopeByTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'KES ' . number_format($this->amount, 2);
    }

    // Helper Methods
    public static function getFeesForStudent($studentId, $term, $academicYear)
    {
        $student = Students::with(['class', 'grade'])->find($studentId);
        if (!$student) {
            return collect();
        }

        return self::active()
            ->where('class_id', $student->class_id)
            ->where('grade_id', $student->grade_id)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->get();
    }

    public static function getTotalFeesForStudent($studentId, $term, $academicYear)
    {
        return self::getFeesForStudent($studentId, $term, $academicYear)
            ->sum('amount');
    }
}