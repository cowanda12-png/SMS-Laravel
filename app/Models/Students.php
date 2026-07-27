<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Students extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'admission_number',
        'registration_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'date_of_birth',
        'gender',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'class_id',
        'grade_id',
        'course_id',
        'status',
        'enrollment_date',
        'profile_image',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];

    // ==================== RELATIONSHIPS ====================

    // Relationship: Student belongs to one Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    // Relationship: Student belongs to one Class
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    // Relationship: Student belongs to one Grade
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'id');
    }

    // Relationship: Student has many Fee payments
    public function fees()
    {
        return $this->hasMany(Fee::class, 'student_id', 'id');
    }

    // Relationship: Student has many Payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }

    // Relationship: Student has many Fee Structures
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'class_id', 'class_id')
            ->orWhereNull('class_id')
            ->where('status', 'active');
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

    // ==================== ACCESSORS ====================

    /**
     * Get the student's full name.
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
     * Get total fees from fee structures.
     */
    public function getTotalFeesAttribute()
    {
        // Try to get from fee structures first
        $feeStructuresTotal = $this->feeStructures->sum('amount') ?? 0;
        
        // If no fee structures, try to get from fees
        if ($feeStructuresTotal == 0) {
            $feeStructuresTotal = $this->fees()->sum('amount') ?? 0;
        }
        
        return $feeStructuresTotal;
    }

    /**
     * Get outstanding balance.
     */
    public function getOutstandingBalanceAttribute()
    {
        return $this->total_fees - $this->total_paid;
    }

    /**
     * Get balance (alias for outstanding_balance).
     */
    public function getBalanceAttribute()
    {
        return $this->outstanding_balance;
    }

    /**
     * Get fee payment status.
     */
    public function getPaymentStatusAttribute()
    {
        $total = $this->total_fees;
        $paid = $this->total_paid;
        $balance = $this->balance;
        
        if ($total == 0) {
            return 'pending';
        }
        
        if ($balance <= 0) {
            return 'paid';
        }
        
        // Check for overdue payments
        $hasOverdue = $this->overdueFees()->count() > 0;
        if ($hasOverdue) {
            return 'overdue';
        }
        
        if ($paid > 0 && $balance > 0) {
            return 'partial';
        }
        
        return 'pending';
    }

    /**
     * Get fee status label.
     */
    public function getFeeStatusLabelAttribute()
    {
        $status = $this->payment_status;
        return match($status) {
            'paid' => 'Fully Paid',
            'partial' => 'Partially Paid',
            'overdue' => 'Overdue',
            'pending' => 'Pending',
            default => 'No Fees'
        };
    }

    /**
     * Get fee status color.
     */
    public function getFeeStatusColorAttribute()
    {
        $status = $this->payment_status;
        return match($status) {
            'paid' => 'success',
            'partial' => 'warning',
            'overdue' => 'danger',
            'pending' => 'info',
            default => 'secondary'
        };
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
     * Get status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
            'graduated' => 'info',
            'suspended' => 'danger',
            'expelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get student initials.
     */
    public function getInitialsAttribute()
    {
        $first = !empty($this->first_name) ? strtoupper(substr($this->first_name, 0, 1)) : '';
        $last = !empty($this->last_name) ? strtoupper(substr($this->last_name, 0, 1)) : '';
        return $first . $last;
    }

    /**
     * Get display name with admission number.
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . ($this->admission_number ?? 'N/A') . ')';
    }

    /**
     * Get formatted date of birth.
     */
    public function getFormattedDateOfBirthAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->format('M d, Y') : 'N/A';
    }

    /**
     * Get formatted enrollment date.
     */
    public function getFormattedEnrollmentDateAttribute()
    {
        return $this->enrollment_date ? $this->enrollment_date->format('M d, Y') : 'N/A';
    }

    /**
     * Get age of student.
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->age;
    }

    /**
     * Get gender icon.
     */
    public function getGenderIconAttribute()
    {
        return match($this->gender) {
            'male' => 'mars',
            'female' => 'venus',
            default => 'genderless'
        };
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
                     ->orWhere('phone', 'LIKE', "%{$search}%")
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
     * Scope: Filter by class.
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope: Filter by grade.
     */
    public function scopeByGrade($query, $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by gender.
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope: Students with balance.
     */
    public function scopeWithBalance($query)
    {
        return $query->has('fees');
    }

    /**
     * Scope: Students with overdue payments.
     */
    public function scopeWithOverdue($query)
    {
        return $query->whereHas('fees', function($q) {
            $q->where('status', 'overdue');
        });
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
        $total = $this->total_fees;
        $paid = $this->total_paid;
        return $total > 0 && $total == $paid;
    }

    /**
     * Get student's outstanding balance.
     */
    public function getOutstandingBalance()
    {
        return $this->outstanding_balance;
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
            'alternate_phone' => $this->alternate_phone,
            'address' => $this->address,
            'date_of_birth' => $this->formatted_date_of_birth,
            'gender' => $this->gender,
            'guardian_name' => $this->guardian_name,
            'guardian_phone' => $this->guardian_phone,
            'guardian_email' => $this->guardian_email,
            'course' => $this->course_name,
            'course_id' => $this->course_id,
            'class' => $this->class?->name ?? 'N/A',
            'class_id' => $this->class_id,
            'grade' => $this->grade?->name ?? 'N/A',
            'grade_id' => $this->grade_id,
            'status' => $this->status,
            'status_color' => $this->status_color,
            'enrollment_date' => $this->formatted_enrollment_date,
            'total_fees' => $this->total_fees,
            'total_paid' => $this->total_paid,
            'outstanding_balance' => $this->outstanding_balance,
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->fee_status_label,
            'payment_status_color' => $this->fee_status_color,
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
                $year = date('Y');
                $random = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                $student->admission_number = 'ADM-' . $year . '-' . $random;
            }
            
            // Set default status if not provided
            if (empty($student->status)) {
                $student->status = 'active';
            }
            
            // Set enrollment date if not provided
            if (empty($student->enrollment_date)) {
                $student->enrollment_date = now();
            }
        });

        static::deleting(function ($student) {
            // Delete profile image if exists
            if ($student->profile_image && file_exists(storage_path('app/public/' . $student->profile_image))) {
                \Storage::disk('public')->delete($student->profile_image);
            }
        });
    }
}