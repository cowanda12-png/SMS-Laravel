<?php

namespace App\Models;

use App\Models\MpesaTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'amount',
        'payment_date',
        'due_date',
        'status',
        'term',
        'academic_year',
        'payment_method',
        'fee_type',
        'description',
        'receipt_no',
        'paid_at',
        'mpesa_phone',
        'mpesa_transaction_code',
        'mpesa_checkout_request_id',
        'mpesa_result_code',
        'mpesa_response',
        'account_reference',
        'mpesa_result_desc',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'payment_date',
        'due_date',
        'created_at',
        'updated_at',
        'paid_at',
        'completed_at',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the student that owns the fee.
     */
    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id', 'id');
    }

    /**
     * Get the M-Pesa transaction associated with the fee.
     */
    public function mpesaTransaction()
    {
        return $this->hasOne(MpesaTransaction::class, 'fee_id', 'id');
    }

    /**
     * Get all M-Pesa transactions for this fee.
     */
    public function mpesaTransactions()
    {
        return $this->hasMany(MpesaTransaction::class, 'fee_id', 'id');
    }

    /**
     * Get the latest M-Pesa transaction.
     */
    public function latestMpesaTransaction()
    {
        return $this->hasOne(MpesaTransaction::class, 'fee_id', 'id')->latest();
    }

    // ==================== ACCESSORS ====================

    /**
     * Get student's name - FIXED to use 'name' column
     */
    public function getStudentNameAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->name ?? 'Unknown Student';
        }
        
        try {
            $student = Students::find($this->student_id);
            return $student->name ?? 'Unknown Student';
        } catch (\Exception $e) {
            return 'Student #' . $this->student_id;
        }
    }

    /**
     * Get student's first name - FIXED to use 'name' column
     */
    public function getStudentFirstNameAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            $name = $this->student->name ?? 'Unknown';
            $parts = explode(' ', $name);
            return $parts[0] ?? 'Unknown';
        }
        
        try {
            $student = Students::find($this->student_id);
            if ($student) {
                $parts = explode(' ', $student->name ?? 'Unknown');
                return $parts[0] ?? 'Unknown';
            }
            return 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get student's last name - FIXED to use 'name' column
     */
    public function getStudentLastNameAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            $name = $this->student->name ?? 'Unknown';
            $parts = explode(' ', $name);
            return count($parts) > 1 ? end($parts) : $parts[0] ?? 'Unknown';
        }
        
        try {
            $student = Students::find($this->student_id);
            if ($student) {
                $parts = explode(' ', $student->name ?? 'Unknown');
                return count($parts) > 1 ? end($parts) : $parts[0] ?? 'Unknown';
            }
            return 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get student's course name - FIXED
     */
    public function getStudentCourseAttribute()
    {
        // Try via relation
        if ($this->relationLoaded('student') && $this->student) {
            if ($this->student->relationLoaded('course')) {
                return $this->student->course->course_name ?? $this->student->course->name ?? 'Not Assigned';
            }
        }
        
        // Try via direct query
        try {
            $student = Students::with('course')->find($this->student_id);
            if ($student && $student->course) {
                return $student->course->course_name ?? $student->course->name ?? 'Not Assigned';
            }
        } catch (\Exception $e) {
            // Fall through
        }
        
        return 'Not Assigned';
    }

    /**
     * Get student's admission number - FIXED
     */
    public function getStudentAdmissionAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->admission_number ?? 'N/A';
        }
        
        try {
            $student = Students::find($this->student_id);
            return $student->admission_number ?? 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get student's phone number - FIXED
     */
    public function getStudentPhoneAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->phone ?? '';
        }
        
        try {
            $student = Students::find($this->student_id);
            return $student->phone ?? '';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get student's email - FIXED
     */
    public function getStudentEmailAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->email ?? 'N/A';
        }
        
        try {
            $student = Students::find($this->student_id);
            return $student->email ?? 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get complete student details as array - FIXED
     */
    public function getStudentDetailsAttribute()
    {
        try {
            return [
                'id' => $this->student_id,
                'name' => $this->student_name,
                'first_name' => $this->student_first_name,
                'last_name' => $this->student_last_name,
                'admission_number' => $this->student_admission,
                'course' => $this->student_course,
                'phone' => $this->student_phone,
                'email' => $this->student_email,
            ];
        } catch (\Exception $e) {
            return [
                'id' => $this->student_id,
                'name' => 'Unknown Student',
                'first_name' => 'Unknown',
                'last_name' => 'Unknown',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
            ];
        }
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return 'KES ' . number_format($this->amount, 2);
    }

    /**
     * Get status badge data.
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
        ];

        $icons = [
            'pending' => 'fa-clock',
            'paid' => 'fa-check-circle',
            'overdue' => 'fa-exclamation-circle',
        ];

        return [
            'color' => $colors[$this->status] ?? 'secondary',
            'icon' => $icons[$this->status] ?? 'fa-circle',
            'label' => ucfirst($this->status),
        ];
    }

    /**
     * Check if fee is overdue.
     */
    public function getIsOverdueAttribute()
    {
        if ($this->status === 'pending' && $this->due_date && $this->due_date->isPast()) {
            return true;
        }
        return false;
    }

    /**
     * Get days until due date.
     */
    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) {
            return null;
        }
        
        if ($this->due_date->isPast()) {
            return -$this->due_date->diffInDays(now());
        }
        
        return $this->due_date->diffInDays(now());
    }

    /**
     * Get M-Pesa transaction status.
     */
    public function getMpesaTransactionStatusAttribute()
    {
        if (!$this->hasMpesaTransaction()) {
            return null;
        }
        return $this->mpesaTransaction->status ?? 'N/A';
    }

    /**
     * Get M-Pesa receipt number.
     */
    public function getMpesaReceiptAttribute()
    {
        if (!$this->hasMpesaTransaction()) {
            return null;
        }
        return $this->mpesaTransaction->mpesa_receipt_number ?? null;
    }

    /**
     * Get payment summary - FIXED
     */
    public function getSummaryAttribute()
    {
        try {
            return [
                'id' => $this->id,
                'student' => $this->student_name,
                'student_id' => $this->student_id,
                'amount' => $this->formatted_amount,
                'status' => $this->status,
                'status_badge' => $this->status_badge,
                'date' => $this->formatted_payment_date,
                'due_date' => $this->formatted_due_date,
                'is_overdue' => $this->is_overdue,
                'term' => $this->term,
                'academic_year' => $this->academic_year,
            ];
        } catch (\Exception $e) {
            return [
                'id' => $this->id,
                'student' => 'Unknown Student',
                'student_id' => $this->student_id,
                'amount' => 'KES ' . number_format($this->amount, 2),
                'status' => $this->status ?? 'pending',
                'status_badge' => ['color' => 'secondary', 'icon' => 'fa-circle', 'label' => ucfirst($this->status ?? 'pending')],
                'date' => $this->payment_date ? $this->payment_date->format('d M Y') : 'N/A',
                'due_date' => $this->due_date ? $this->due_date->format('d M Y') : 'N/A',
                'is_overdue' => false,
                'term' => $this->term ?? 'N/A',
                'academic_year' => $this->academic_year ?? 'N/A',
            ];
        }
    }

    /**
     * Get formatted payment date.
     */
    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date ? $this->payment_date->format('d M Y') : 'N/A';
    }

    /**
     * Get formatted due date.
     */
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('d M Y') : 'N/A';
    }

    /**
     * Get formatted paid date
     */
    public function getFormattedPaidDateAttribute()
    {
        return $this->paid_at ? $this->paid_at->format('d M Y H:i') : 'N/A';
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Filter by date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Today's payments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', Carbon::today());
    }

    /**
     * Scope: This month's payments.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', Carbon::now()->month)
                     ->whereYear('payment_date', Carbon::now()->year);
    }

    /**
     * Scope: Paid fees.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Pending fees.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Overdue fees.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope: By status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Search by student name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('student', function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('admission_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope: With student details eager loaded.
     */
    public function scopeWithStudent($query)
    {
        return $query->with('student');
    }

    /**
     * Scope: With student and course eager loaded.
     */
    public function scopeWithStudentAndCourse($query)
    {
        return $query->with(['student', 'student.course']);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if fee is paid.
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if fee is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if fee is overdue.
     */
    public function isOverdue()
    {
        return $this->status === 'overdue';
    }

    /**
     * Check if fee has an M-Pesa transaction.
     */
    public function hasMpesaTransaction()
    {
        return $this->mpesaTransaction()->exists();
    }

    /**
     * Check if M-Pesa payment
     */
    public function isMpesaPayment(): bool
    {
        return $this->payment_method === 'M-Pesa' || $this->payment_method === 'Mpesa';
    }

    /**
     * Mark fee as paid.
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark fee as overdue.
     */
    public function markAsOverdue()
    {
        $this->update([
            'status' => 'overdue',
        ]);
    }

    /**
     * Mark fee as pending.
     */
    public function markAsPending()
    {
        $this->update([
            'status' => 'pending',
        ]);
    }

    /**
     * Get M-Pesa transaction status (human readable)
     */
    public function getMpesaStatusAttribute()
    {
        if ($this->status === 'paid') {
            return 'Completed';
        }

        $map = [
            '0'    => 'Completed',
            '1032' => 'Cancelled',
            '1037' => 'Timeout',
            '2001' => 'Wrong PIN',
        ];

        if ($this->mpesa_result_code && isset($map[$this->mpesa_result_code])) {
            return $map[$this->mpesa_result_code];
        }

        if ($this->mpesa_result_code) {
            return 'Failed';
        }

        return 'Pending';
    }

    /**
     * Get M-Pesa status badge color
     */
    public function getMpesaStatusColorAttribute()
    {
        $colors = [
            'Completed' => 'success',
            'Cancelled' => 'warning',
            'Timeout'   => 'info',
            'Wrong PIN' => 'danger',
            'Failed'    => 'danger',
            'Pending'   => 'secondary',
        ];

        return $colors[$this->mpesa_status] ?? 'secondary';
    }

    /**
     * Get student details for the receipt - FIXED
     */
    public function getStudentDetailsForReceiptAttribute()
    {
        try {
            if ($this->relationLoaded('student') && $this->student) {
                $student = $this->student;
            } else {
                $student = Students::with('course')->find($this->student_id);
            }
            
            if ($student) {
                return [
                    'name' => $student->name ?? 'Unknown Student',
                    'admission_number' => $student->admission_number ?? 'N/A',
                    'course' => $student->course->course_name ?? $student->course->name ?? 'Not Assigned',
                    'phone' => $student->phone ?? '',
                    'email' => $student->email ?? 'N/A',
                    'id' => $student->id,
                ];
            }
            
            return [
                'name' => 'Student Not Found',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
                'id' => $this->student_id,
            ];
        } catch (\Exception $e) {
            return [
                'name' => 'Student Not Found',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
                'id' => $this->student_id,
            ];
        }
    }

    // ==================== EVENTS ====================

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($fee) {
            // Set default status if not provided
            if (empty($fee->status)) {
                $fee->status = 'pending';
            }

            // Set due date if not provided
            if (empty($fee->due_date) && !empty($fee->payment_date)) {
                $fee->due_date = Carbon::parse($fee->payment_date)->addDays(30);
            }

            // Set receipt number if not provided
            if (empty($fee->receipt_no)) {
                $fee->receipt_no = 'RCP-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            }
        });

        static::updating(function ($fee) {
            // Auto-set paid_at the moment status transitions to 'paid'
            if ($fee->isDirty('status') && $fee->status === 'paid' && empty($fee->paid_at)) {
                $fee->paid_at = now();
            }
        });

        static::deleting(function ($fee) {
            // Delete associated M-Pesa transactions when fee is deleted
            if ($fee->hasMpesaTransaction()) {
                $fee->mpesaTransactions()->delete();
            }
        });
    }
}