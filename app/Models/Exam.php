<?php
// app/Models/Exam.php

namespace App\Models;

use App\Models\ExamResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'course_id',
        'class_id',
        'type',
        'exam_date',
        'submission_date',
        'max_score',
        'passing_score',
        'weight',
        'status',
        'is_active',
        'instructions',
        'term',           // Added
        'academic_year'   // Added
    ];

    protected $casts = [
        'exam_date' => 'date',
        'submission_date' => 'date',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
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
    public function getFormattedDateAttribute()
    {
        return $this->exam_date ? $this->exam_date->format('d M Y') : 'N/A';
    }

    public function getTypeLabelAttribute()
    {
        return ucfirst($this->type);
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'published' => 'info',
            'completed' => 'primary',
            'graded' => 'success'
        ];

        return [
            'color' => $colors[$this->status] ?? 'secondary',
            'label' => ucfirst($this->status)
        ];
    }

    public function getPassingStatusAttribute()
    {
        return $this->passing_score ? 'Pass: ' . $this->passing_score . '%' : 'N/A';
    }

    // Helper Methods
    public function getAverageScore()
    {
        return $this->results()->where('status', 'graded')->avg('score') ?? 0;
    }

    public function getPassRate()
    {
        $total = $this->results()->where('status', 'graded')->count();
        if ($total == 0) return 0;
        
        $passed = $this->results()->where('status', 'graded')
            ->where('percentage', '>=', $this->passing_score)->count();
        
        return round(($passed / $total) * 100, 2);
    }

    public function getSubmissionRate()
    {
        $total = $this->results()->count();
        if ($total == 0) return 0;
        
        $submitted = $this->results()->where('status', 'submitted')->count();
        
        return round(($submitted / $total) * 100, 2);
    }
}