<?php
// app/Models/PerformanceTracking.php

namespace App\Models;

use App\Models\GradeScale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceTracking extends Model
{
    use HasFactory;

    protected $table = 'performance_tracking';

    protected $fillable = [
        'student_id',
        'course_id',
        'class_id',
        'term',
        'academic_year',
        'average_score',
        'cumulative_average',
        'overall_grade',
        'rank',
        'total_students',
        'subject_breakdown',
        'teacher_remarks',
        'status'
    ];

    protected $casts = [
        'average_score' => 'decimal:2',
        'cumulative_average' => 'decimal:2',
        'subject_breakdown' => 'array'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Students::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
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
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'active' => 'success',
            'completed' => 'info',
            'archived' => 'secondary'
        ];

        return [
            'color' => $colors[$this->status] ?? 'secondary',
            'label' => ucfirst($this->status)
        ];
    }

    public function getOverallGradeAttribute()
    {
        if ($this->overall_grade) return $this->overall_grade;
        
        $gradeScale = GradeScale::where('min_score', '<=', $this->average_score)
            ->where('max_score', '>=', $this->average_score)
            ->first();
        
        return $gradeScale ? $gradeScale->grade : 'F';
    }

    public function getFormattedAverageAttribute()
    {
        return number_format($this->average_score, 2) . '%';
    }

    // Helper Methods
    public function updatePerformance()
    {
        $results = ExamResult::where('student_id', $this->student_id)
            ->where('course_id', $this->course_id)
            ->where('status', 'graded')
            ->get();

        if ($results->isEmpty()) return;

        // Calculate average
        $this->average_score = $results->avg('percentage') ?? 0;

        // Update overall grade
        $this->overall_grade = $this->overall_grade;

        // Update subject breakdown
        $this->subject_breakdown = $this->getSubjectBreakdown();

        // Save
        $this->save();
    }

    private function getSubjectBreakdown()
    {
        $results = ExamResult::where('student_id', $this->student_id)
            ->where('course_id', $this->course_id)
            ->with('exam')
            ->where('status', 'graded')
            ->get();

        return $results->map(function($result) {
            return [
                'exam_name' => $result->exam->name,
                'exam_type' => $result->exam->type,
                'score' => $result->score,
                'percentage' => $result->percentage,
                'grade' => $result->grade,
            ];
        })->toArray();
    }
}