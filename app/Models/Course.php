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

    // ==================== RELATIONSHIPS ====================

    /**
     * Relationship with Students.
     */
    public function students()
    {
        return $this->hasMany(Students::class, 'course_id', 'id');
    }

    /**
     * Relationship with Fees (through students).
     */
    public function fees()
    {
        return $this->hasManyThrough(Fee::class, Students::class, 'course_id', 'student_id', 'id', 'id');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the course name.
     * This is the primary name accessor used by other models.
     */
    public function getNameAttribute()
    {
        return $this->course_name ?? $this->name ?? 'Unnamed Course';
    }

    /**
     * Get display name (alias for name).
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get course name with code.
     */
    public function getFullNameAttribute()
    {
        $name = $this->course_name ?? 'Unnamed Course';
        if ($this->code) {
            return $name . ' (' . $this->code . ')';
        }
        return $name;
    }

    /**
     * Get student count.
     */
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Get total revenue from this course.
     */
    public function getTotalRevenueAttribute()
    {
        $total = 0;
        foreach ($this->students as $student) {
            $total += $student->fees()->sum('amount') ?? 0;
        }
        return $total;
    }

    /**
     * Get total paid revenue from this course.
     */
    public function getTotalPaidRevenueAttribute()
    {
        $total = 0;
        foreach ($this->students as $student) {
            $total += $student->fees()->where('status', 'paid')->sum('amount') ?? 0;
        }
        return $total;
    }

    /**
     * Get total pending revenue from this course.
     */
    public function getTotalPendingRevenueAttribute()
    {
        $total = 0;
        foreach ($this->students as $student) {
            $total += $student->fees()->where('status', 'pending')->sum('amount') ?? 0;
        }
        return $total;
    }

    /**
     * Get collection rate for this course.
     */
    public function getCollectionRateAttribute()
    {
        $total = $this->total_revenue;
        if ($total == 0) {
            return 0;
        }
        return round(($this->total_paid_revenue / $total) * 100, 1);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get course summary as array.
     */
    public function getSummaryAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'students_count' => $this->student_count,
            'total_revenue' => $this->total_revenue,
            'total_paid' => $this->total_paid_revenue,
            'total_pending' => $this->total_pending_revenue,
            'collection_rate' => $this->collection_rate,
            'status' => $this->status,
            'status_color' => $this->status_color,
        ];
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Search courses.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('course_name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%")
                     ->orWhere('description', 'LIKE', "%{$search}%");
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if course is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if course has students.
     */
    public function hasStudents()
    {
        return $this->students()->exists();
    }

    /**
     * Get students with fee status.
     */
    public function getStudentsWithFeeStatus()
    {
        return $this->students->map(function($student) {
            return [
                'student' => $student,
                'total_fees' => $student->total_fees,
                'total_paid' => $student->total_paid,
                'outstanding' => $student->outstanding_balance,
                'fee_status' => $student->fee_status,
            ];
        });
    }

    // ==================== EVENTS ====================

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($course) {
            // Set default status if not provided
            if (empty($course->status)) {
                $course->status = 'active';
            }
            
            // Auto-generate code if not provided
            if (empty($course->code)) {
                $prefix = 'CRS';
                $year = date('Y');
                $lastId = self::max('id') + 1;
                $course->code = $prefix . $year . str_pad($lastId, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}