<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Students extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'class_id',
        'course_id',
        'status',
        'registration_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    // Relationship: Student belongs to one Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    // Relationship: Student belongs to one Class (ADD THIS)
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    // Relationship: Student belongs to one Grade (ADD THIS)
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'id');
    }

    // Relationship: Student has many Fee payments
    public function fees()
    {
        return $this->hasMany(Fee::class, 'student_id', 'id');
    }

    // Get paid fees
    public function paidFees()
    {
        return $this->hasMany(Fee::class, 'student_id', 'id')->where('status', 'paid');
    }

    // Get pending fees
    public function pendingFees()
    {
        return $this->hasMany(Fee::class, 'student_id', 'id')->where('status', 'pending');
    }

    // Get overdue fees
    public function overdueFees()
    {
        return $this->hasMany(Fee::class, 'student_id', 'id')->where('status', 'overdue');
    }

    // Get the latest paid fee
    public function latestPaidFee()
    {
        return $this->hasOne(Fee::class, 'student_id', 'id')
            ->where('status', 'paid')
            ->latest('created_at');
    }

    // Relationship: Student has many Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the student's full name.
     * This is the primary name accessor used by Fee model.
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get full name (alias for name).
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get total paid amount.
     */
    public function getTotalPaidAttribute()
    {
        return $this->paidFees()->sum('amount') ?? 0;
    }

    /**
     * Get total pending amount.
     */
    public function getTotalPendingAttribute()
    {
        return $this->pendingFees()->sum('amount') ?? 0;
    }

    /**
     * Get total overdue amount.
     */
    public function getTotalOverdueAttribute()
    {
        return $this->overdueFees()->sum('amount') ?? 0;
    }

    /**
     * Get total fees.
     */
    public function getTotalFeesAttribute()
    {
        return $this->fees()->sum('amount') ?? 0;
    }

    /**
     * Get fee payment status summary.
     */
    public function getFeeStatusAttribute()
    {
        $total = $this->getTotalFeesAttribute();
        $paid = $this->getTotalPaidAttribute();
        
        if ($total == 0) {
            return 'No Fees';
        }
        
        $percentage = ($paid / $total) * 100;
        
        if ($percentage == 100) {
            return 'Fully Paid';
        } elseif ($percentage >= 50) {
            return 'Partially Paid';
        } else {
            return 'Low Payment';
        }
    }

    /**
     * Get course name directly from the relationship.
     */
    public function getCourseNameAttribute()
    {
        if ($this->relationLoaded('course') && $this->course) {
            return $this->course->course_name ?? $this->course->name ?? 'Not Assigned';
        }
        
        if ($this->course_id) {
            $course = Course::find($this->course_id);
            if ($course) {
                return $course->course_name ?? $course->name ?? 'Not Assigned';
            }
        }
        
        return 'Not Assigned';
    }

    /**
     * Accessor for status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'graduated' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Accessor for student initials.
     */
    public function getInitialsAttribute()
    {
        $first = !empty($this->first_name) ? strtoupper(substr($this->first_name, 0, 1)) : '';
        $last = !empty($this->last_name) ? strtoupper(substr($this->last_name, 0, 1)) : '';
        return $first . $last;
    }

    /**
     * Accessor for display name with admission number.
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . ($this->admission_number ?? 'N/A') . ')';
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Search students.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('first_name', 'LIKE', "%{$search}%")
                     ->orWhere('last_name', 'LIKE', "%{$search}%")
                     ->orWhere('email', 'LIKE', "%{$search}%")
                     ->orWhere('admission_number', 'LIKE', "%{$search}%")
                     ->orWhere('registration_number', 'LIKE', "%{$search}%")
                     ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
    }

    /**
     * Scope: Filter by course.
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // ==================== MUTATORS ====================

    /**
     * Set first name attribute.
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Set last name attribute.
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Set email attribute.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if student is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if student has any fees.
     */
    public function hasFees()
    {
        return $this->fees()->exists();
    }

    /**
     * Check if student has paid all fees.
     */
    public function hasPaidAllFees()
    {
        $total = $this->getTotalFeesAttribute();
        $paid = $this->getTotalPaidAttribute();
        return $total > 0 && $total == $paid;
    }

    /**
     * Get student's outstanding balance.
     */
    public function getOutstandingBalanceAttribute()
    {
        return $this->getTotalFeesAttribute() - $this->getTotalPaidAttribute();
    }

    /**
     * Get student details as array for API.
     */
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'admission_number' => $this->admission_number,
            'registration_number' => $this->registration_number,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'course' => $this->course_name,
            'course_id' => $this->course_id,
            'status' => $this->status,
            'status_color' => $this->status_color,
            'total_fees' => $this->total_fees,
            'total_paid' => $this->total_paid,
            'outstanding_balance' => $this->outstanding_balance,
            'fee_status' => $this->fee_status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    // ==================== EVENTS ====================

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($student) {
            // Generate admission number if not provided
            if (empty($student->admission_number)) {
                $student->admission_number = 'ADM-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            }
            
            // Set default status if not provided
            if (empty($student->status)) {
                $student->status = 'active';
            }
        });
    }
}