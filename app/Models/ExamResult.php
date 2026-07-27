<?php
// app/Models/ExamResult.php

namespace App\Models;

use App\Models\GradeScale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $table = 'exam_results';

    protected $fillable = [
        'exam_id',
        'student_id',
        'course_id',
        'class_id',
        'score',
        'percentage',
        'grade',
        'remarks',
        'feedback',
        'status',
        'submitted_at',
        'graded_at'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime'
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

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
    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'submitted' => 'info',
            'graded' => 'success',
            'absent' => 'danger'
        ];

        return [
            'color' => $colors[$this->status] ?? 'secondary',
            'label' => ucfirst($this->status)
        ];
    }

    public function getFormattedScoreAttribute()
    {
        return $this->score ? number_format($this->score, 2) : 'N/A';
    }

    public function getFormattedPercentageAttribute()
    {
        return $this->percentage ? number_format($this->percentage, 2) . '%' : 'N/A';
    }

    // Helper Methods
    public function calculateGrade()
    {
        if ($this->percentage === null) return null;
        
        $gradeScale = GradeScale::where('min_score', '<=', $this->percentage)
            ->where('max_score', '>=', $this->percentage)
            ->first();
        
        return $gradeScale ? $gradeScale->grade : 'F';
    }

    public function calculateRemarks()
    {
        if ($this->percentage === null) return null;
        
        $gradeScale = GradeScale::where('min_score', '<=', $this->percentage)
            ->where('max_score', '>=', $this->percentage)
            ->first();
        
        return $gradeScale ? $gradeScale->remark : 'Fail';
    }

    public function isPassed()
    {
        if ($this->percentage === null) return false;
        return $this->percentage >= ($this->exam->passing_score ?? 50);
    }
}