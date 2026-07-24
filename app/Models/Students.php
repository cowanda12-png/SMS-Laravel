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

    // Relationship: Student belongs to one Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
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
            ->latest('created_at'); // Using created_at instead of payment_date
    }

    // Get total paid amount
    public function getTotalPaidAttribute()
    {
        return $this->paidFees()->sum('amount') ?? 0;
    }

    // Get total pending amount
    public function getTotalPendingAttribute()
    {
        return $this->pendingFees()->sum('amount') ?? 0;
    }

    // Get total overdue amount
    public function getTotalOverdueAttribute()
    {
        return $this->overdueFees()->sum('amount') ?? 0;
    }

    // Get total fees
    public function getTotalFeesAttribute()
    {
        return $this->fees()->sum('amount') ?? 0;
    }

    // Get fee payment status summary
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

    // Get full name
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Get course name directly from the relationship
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

    // Accessor for status color
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('first_name', 'LIKE', "%{$search}%")
                     ->orWhere('last_name', 'LIKE', "%{$search}%")
                     ->orWhere('email', 'LIKE', "%{$search}%")
                     ->orWhere('admission_number', 'LIKE', "%{$search}%")
                     ->orWhere('registration_number', 'LIKE', "%{$search}%");
    }

    // Mutators
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower(trim($value)));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucwords(strtolower(trim($value)));
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    // Relationship: Student has many Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}