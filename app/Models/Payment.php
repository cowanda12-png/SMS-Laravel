<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'student_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'term',
        'academic_year',
        'fee_type',
        'description',
        'receipt_no',
        'account_reference',
        'mpesa_transaction_code',
        'mpesa_checkout_request_id',
        'mpesa_result_code',
        'mpesa_phone',
        'completed_at',
        'paid_at',
        'due_date',
        'mpesa_result_desc',
        'mpesa_response',
        'phone', // For backward compatibility
    ];

    protected $casts = [
        'payment_date' => 'date',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'mpesa_response' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the student that owns the payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the student's full name attribute.
     */
    public function getStudentNameAttribute(): string
    {
        // Check if relationship is loaded
        if ($this->relationLoaded('student') && $this->student) {
            $firstName = $this->student->first_name ?? '';
            $lastName = $this->student->last_name ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            return $fullName ?: 'N/A';
        }
        
        // Try to get student without loading relation
        $student = Students::find($this->student_id);
        if ($student) {
            $firstName = $student->first_name ?? '';
            $lastName = $student->last_name ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            return $fullName ?: 'N/A';
        }
        
        return 'Student Not Found (ID: ' . $this->student_id . ')';
    }

    /**
     * Get the student's first name.
     */
    public function getStudentFirstNameAttribute(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->first_name ?? 'N/A';
        }
        
        $student = Students::find($this->student_id);
        return $student->first_name ?? 'N/A';
    }

    /**
     * Get the student's last name.
     */
    public function getStudentLastNameAttribute(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->last_name ?? 'N/A';
        }
        
        $student = Students::find($this->student_id);
        return $student->last_name ?? 'N/A';
    }

    /**
     * Get the student's admission number.
     */
    public function getStudentAdmissionAttribute(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->admission_number ?? 'N/A';
        }
        
        $student = Students::find($this->student_id);
        if ($student) {
            return $student->admission_number ?? 'N/A';
        }
        
        return 'N/A';
    }

    /**
     * Get the student's phone number.
     */
    public function getStudentPhoneAttribute(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->phone ?? '';
        }
        
        $student = Students::find($this->student_id);
        if ($student) {
            return $student->phone ?? '';
        }
        
        return '';
    }

    /**
     * Get the student's email.
     */
    public function getStudentEmailAttribute(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->email ?? 'N/A';
        }
        
        $student = Students::find($this->student_id);
        return $student->email ?? 'N/A';
    }

    /**
     * Get the student's course name.
     */
    public function getStudentCourseAttribute(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            if ($this->student->relationLoaded('course') && $this->student->course) {
                return $this->student->course->course_name ?? 'Not Assigned';
            }
        }
        
        // Load student with course
        $student = Students::with('course')->find($this->student_id);
        if ($student && $student->course) {
            return $student->course->course_name;
        }
        
        return 'Not Assigned';
    }

    /**
     * Get complete student details as array.
     */
    public function getStudentDetailsAttribute(): array
    {
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
    }

    /**
     * Get the payment method with icon.
     */
    public function getPaymentMethodWithIconAttribute(): string
    {
        $icons = [
            'M-Pesa' => '<i class="fas fa-mobile-alt me-1"></i> M-Pesa',
            'Mpesa' => '<i class="fas fa-mobile-alt me-1"></i> M-Pesa',
            'Cash' => '<i class="fas fa-money-bill-wave me-1"></i> Cash',
            'Bank Transfer' => '<i class="fas fa-university me-1"></i> Bank Transfer',
            'Cheque' => '<i class="fas fa-file-invoice me-1"></i> Cheque',
            'Credit Card' => '<i class="fas fa-credit-card me-1"></i> Credit Card',
        ];

        return $icons[$this->payment_method] ?? $this->payment_method ?? 'N/A';
    }

    /**
     * Get payment method badge color.
     */
    public function getPaymentMethodColorAttribute(): string
    {
        $colors = [
            'M-Pesa' => 'success',
            'Mpesa' => 'success',
            'Cash' => 'primary',
            'Bank Transfer' => 'info',
            'Cheque' => 'warning',
            'Credit Card' => 'secondary',
        ];

        return $colors[$this->payment_method] ?? 'secondary';
    }

    /**
     * Check if payment is M-Pesa.
     */
    public function getIsMpesaAttribute(): bool
    {
        return $this->payment_method === 'M-Pesa' || $this->payment_method === 'Mpesa';
    }

    /**
     * Check if payment is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'paid' || $this->status === 'completed';
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'KES ' . number_format($this->amount, 2);
    }

    /**
     * Get payment status with badge class.
     */
    public function getStatusBadgeAttribute(): array
    {
        $configs = [
            'paid' => ['color' => 'success', 'icon' => 'fa-check-circle', 'label' => 'Paid'],
            'pending' => ['color' => 'warning', 'icon' => 'fa-clock', 'label' => 'Pending'],
            'overdue' => ['color' => 'danger', 'icon' => 'fa-exclamation-circle', 'label' => 'Overdue'],
            'cancelled' => ['color' => 'secondary', 'icon' => 'fa-times-circle', 'label' => 'Cancelled'],
        ];

        $status = $this->status ?? 'pending';
        return $configs[$status] ?? ['color' => 'secondary', 'icon' => 'fa-circle', 'label' => ucfirst($status)];
    }

    /**
     * Get M-Pesa transaction status.
     */
    public function getMpesaStatusAttribute(): string
    {
        if (!$this->isMpesa) {
            return 'N/A';
        }

        if ($this->mpesa_result_code === '0') {
            return 'Successful';
        } elseif ($this->mpesa_result_code === '1032') {
            return 'Cancelled';
        } elseif ($this->mpesa_result_code === '1037') {
            return 'Timed Out';
        } elseif ($this->mpesa_result_code === '2001') {
            return 'Wrong PIN';
        } elseif ($this->mpesa_result_code) {
            return 'Failed';
        }

        return 'Pending';
    }

    /**
     * Get M-Pesa status color.
     */
    public function getMpesaStatusColorAttribute(): string
    {
        $colors = [
            'Successful' => 'success',
            'Cancelled' => 'warning',
            'Timed Out' => 'info',
            'Wrong PIN' => 'danger',
            'Failed' => 'danger',
            'Pending' => 'warning',
        ];

        return $colors[$this->mpesa_status] ?? 'secondary';
    }

    /**
     * Get M-Pesa status badge HTML.
     */
    public function getMpesaStatusBadgeAttribute(): string
    {
        $status = $this->mpesa_status;
        $color = $this->mpesa_status_color;
        
        if (!$status || $status === 'N/A') {
            return '<span class="badge bg-secondary">N/A</span>';
        }
        
        return '<span class="badge bg-' . $color . '">' . $status . '</span>';
    }

    /**
     * Check if payment is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'pending' && $this->due_date && $this->due_date->isPast()) {
            return true;
        }
        return false;
    }

    /**
     * Get days until due date.
     */
    public function getDaysUntilDueAttribute(): ?int
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
     * Get formatted payment date.
     */
    public function getFormattedPaymentDateAttribute(): string
    {
        return $this->payment_date ? $this->payment_date->format('d M Y') : 'N/A';
    }

    /**
     * Get formatted due date.
     */
    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date ? $this->due_date->format('d M Y') : 'N/A';
    }

    /**
     * Get formatted paid date.
     */
    public function getFormattedPaidDateAttribute(): string
    {
        return $this->paid_at ? $this->paid_at->format('d M Y H:i') : 'Not Paid Yet';
    }

    /**
     * Get receipt number with badge.
     */
    public function getReceiptBadgeAttribute(): string
    {
        return $this->receipt_no ?? 'N/A';
    }

    /**
     * Get payment summary as array.
     */
    public function getSummaryAttribute(): array
    {
        return [
            'id' => $this->id,
            'student' => $this->student_name,
            'student_id' => $this->student_id,
            'amount' => $this->formatted_amount,
            'payment_method' => $this->payment_method,
            'payment_method_with_icon' => $this->payment_method_with_icon,
            'status' => $this->status,
            'status_badge' => $this->status_badge,
            'date' => $this->formatted_payment_date,
            'receipt' => $this->receipt_badge,
            'due_date' => $this->formatted_due_date,
            'paid_at' => $this->formatted_paid_date,
            'is_mpesa' => $this->is_mpesa,
            'is_completed' => $this->is_completed,
            'is_overdue' => $this->is_overdue,
            'mpesa_status' => $this->mpesa_status,
        ];
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
        return $query->whereDate('payment_date', now()->toDateString());
    }

    /**
     * Scope: This month's payments.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                     ->whereYear('payment_date', now()->year);
    }

    /**
     * Scope: Paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Overdue payments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope: M-Pesa payments.
     */
    public function scopeMpesa($query)
    {
        return $query->where('payment_method', 'M-Pesa')
                     ->orWhere('payment_method', 'Mpesa');
    }

    /**
     * Scope: By status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: By payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope: Search by receipt or student.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('receipt_no', 'LIKE', "%{$search}%")
                     ->orWhereHas('student', function($q) use ($search) {
                         $q->where('first_name', 'LIKE', "%{$search}%")
                           ->orWhere('last_name', 'LIKE', "%{$search}%")
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
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue';
    }

    /**
     * Check if payment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Mark payment as paid.
     */
    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as pending.
     */
    public function markAsPending(): bool
    {
        return $this->update([
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    /**
     * Mark payment as overdue.
     */
    public function markAsOverdue(): bool
    {
        return $this->update([
            'status' => 'overdue',
        ]);
    }

    /**
     * Generate receipt number.
     */
    public function generateReceiptNumber(): string
    {
        return 'RCP-' . now()->format('Ymd') . '-' . 
               str_pad($this->id ?? Payment::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}