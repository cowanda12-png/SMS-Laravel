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
        'payment_method',
        'fee_type',
        'description',
        'receipt_no',
        'payment_date',
        'due_date',
        'status',
        'paid_at',
        'mpesa_phone',
        'mpesa_transaction_code',
        'mpesa_checkout_request_id',
        'mpesa_result_code',
        'mpesa_response',
        'term',
        'academic_year',
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
        'paid_at' => 'datetime',
        'mpesa_response' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'payment_date',
        'due_date',
        'paid_at',
        'created_at',
        'updated_at',
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
     * Get student's full name - FIXED with proper fallback
     */
    public function getStudentNameAttribute()
    {
        // Check if relationship is loaded
        if ($this->relationLoaded('student') && $this->student) {
            $firstName = $this->student->first_name ?? '';
            $lastName = $this->student->last_name ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            return $fullName ?: 'Unknown Student';
        }
        
        // Try to load student directly if not loaded
        if ($this->student_id) {
            try {
                $student = Students::find($this->student_id);
                if ($student) {
                    $firstName = $student->first_name ?? '';
                    $lastName = $student->last_name ?? '';
                    $fullName = trim($firstName . ' ' . $lastName);
                    return $fullName ?: 'Unknown Student';
                }
            } catch (\Exception $e) {
                // Fall through to default
            }
        }
        
        return 'Student #' . $this->student_id;
    }

    /**
     * Get student's first name - FIXED with proper fallback
     */
    public function getStudentFirstNameAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->first_name ?? 'Unknown';
        }
        
        try {
            $student = Students::find($this->student_id);
            return $student->first_name ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get student's last name - FIXED with proper fallback
     */
    public function getStudentLastNameAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->last_name ?? 'Unknown';
        }
        
        try {
            $student = Students::find($this->student_id);
            return $student->last_name ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get student's course name - FIXED with proper fallback
     */
    public function getStudentCourseAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            if ($this->student->relationLoaded('course')) {
                return $this->student->course->course_name ?? 'Not Assigned';
            }
        }
        
        try {
            $student = Students::with('course')->find($this->student_id);
            if ($student && $student->course) {
                return $student->course->course_name;
            }
        } catch (\Exception $e) {
            // Fall through
        }
        
        return 'Not Assigned';
    }

    /**
     * Get student's admission number - FIXED with proper fallback
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
     * Get student's phone number - FIXED with proper fallback
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
     * Get student's email - FIXED with proper fallback
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
            'cancelled' => 'secondary',
        ];

        $icons = [
            'pending' => 'fa-clock',
            'paid' => 'fa-check-circle',
            'overdue' => 'fa-exclamation-circle',
            'cancelled' => 'fa-times-circle',
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
     * Get payment method with icon HTML.
     */
    public function getPaymentMethodWithIconAttribute()
    {
        $icons = [
            'Cash' => '<i class="fas fa-money-bill-wave me-1"></i> Cash',
            'Bank Transfer' => '<i class="fas fa-university me-1"></i> Bank Transfer',
            'Cheque' => '<i class="fas fa-file-invoice me-1"></i> Cheque',
            'M-Pesa' => '<i class="fas fa-mobile-alt me-1"></i> M-Pesa',
            'Mpesa' => '<i class="fas fa-mobile-alt me-1"></i> M-Pesa',
            'Credit Card' => '<i class="fas fa-credit-card me-1"></i> Credit Card',
            'Other' => '<i class="fas fa-hand-holding-usd me-1"></i> Other',
        ];

        return $icons[$this->payment_method] ?? $this->payment_method ?? 'N/A';
    }

    /**
     * Get payment method icon class.
     */
    public function getPaymentMethodIconAttribute()
    {
        $icons = [
            'Cash' => 'fa-money-bill-wave',
            'Bank Transfer' => 'fa-university',
            'Cheque' => 'fa-file-invoice',
            'M-Pesa' => 'fa-mobile-alt',
            'Mpesa' => 'fa-mobile-alt',
            'Credit Card' => 'fa-credit-card',
            'Other' => 'fa-hand-holding-usd',
        ];

        return $icons[$this->payment_method] ?? 'fa-circle';
    }

    /**
     * Get payment method badge color.
     */
    public function getPaymentMethodColorAttribute()
    {
        $colors = [
            'Cash' => 'success',
            'Bank Transfer' => 'primary',
            'Cheque' => 'info',
            'M-Pesa' => 'success',
            'Mpesa' => 'success',
            'Credit Card' => 'warning',
            'Other' => 'secondary',
        ];

        return $colors[$this->payment_method] ?? 'secondary';
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
     * Get payment summary - FIXED with error handling
     */
    public function getSummaryAttribute()
    {
        try {
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
                'is_overdue' => $this->is_overdue,
                'is_mpesa' => $this->isMpesaPayment(),
                'mpesa_status' => $this->mpesa_status,
            ];
        } catch (\Exception $e) {
            return [
                'id' => $this->id,
                'student' => 'Unknown Student',
                'student_id' => $this->student_id,
                'amount' => 'KES ' . number_format($this->amount, 2),
                'payment_method' => $this->payment_method ?? 'N/A',
                'payment_method_with_icon' => $this->payment_method ?? 'N/A',
                'status' => $this->status ?? 'pending',
                'status_badge' => ['color' => 'secondary', 'icon' => 'fa-circle', 'label' => ucfirst($this->status ?? 'pending')],
                'date' => $this->payment_date ? $this->payment_date->format('d M Y') : 'N/A',
                'receipt' => $this->receipt_no ?? 'N/A',
                'due_date' => $this->due_date ? $this->due_date->format('d M Y') : 'N/A',
                'is_overdue' => false,
                'is_mpesa' => false,
                'mpesa_status' => null,
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
     * Get formatted paid date.
     */
    public function getFormattedPaidDateAttribute()
    {
        return $this->paid_at ? $this->paid_at->format('d M Y H:i') : 'Not Paid Yet';
    }

    /**
     * Get receipt with badge.
     */
    public function getReceiptBadgeAttribute()
    {
        return $this->receipt_no ?? 'N/A';
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
     * Scope: Filter by payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
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
     * Scope: M-Pesa payments.
     */
    public function scopeMpesa($query)
    {
        return $query->where('payment_method', 'M-Pesa')
                     ->orWhere('payment_method', 'Mpesa');
    }

    /**
     * Scope: Search by receipt number or student.
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
     * Check if fee is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if fee has an M-Pesa transaction.
     */
    public function hasMpesaTransaction()
    {
        return $this->mpesaTransaction()->exists();
    }

    /**
     * Check if M-Pesa payment.
     */
    public function isMpesaPayment()
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
            'paid_at' => null,
        ]);
    }

    /**
     * Generate receipt number.
     */
    public function generateReceiptNumber()
    {
        return 'RCP-' . now()->format('Ymd') . '-' . 
               str_pad($this->id ?? Fee::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get M-Pesa transaction status.
     */
    public function getMpesaStatusAttribute()
    {
        if (!$this->isMpesaPayment()) {
            return null;
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
     * Get M-Pesa status badge color.
     */
    public function getMpesaStatusColorAttribute()
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
     * Get M-Pesa status with badge HTML.
     */
    public function getMpesaStatusBadgeAttribute()
    {
        $status = $this->mpesa_status;
        $color = $this->mpesa_status_color;
        
        if (!$status) {
            return '<span class="badge bg-secondary">N/A</span>';
        }
        
        return '<span class="badge bg-' . $color . '">' . $status . '</span>';
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
                    'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'Unknown Student',
                    'admission_number' => $student->admission_number ?? 'N/A',
                    'course' => $student->course->course_name ?? 'Not Assigned',
                    'phone' => $student->phone ?? '',
                    'email' => $student->email ?? 'N/A',
                ];
            }
            
            return [
                'name' => 'Student Not Found',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
            ];
        } catch (\Exception $e) {
            return [
                'name' => 'Student Not Found',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
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
            // Auto-generate receipt number if not provided
            if (empty($fee->receipt_no)) {
                $fee->receipt_no = $fee->generateReceiptNumber();
            }

            // Set default status if not provided
            if (empty($fee->status)) {
                $fee->status = 'pending';
            }

            // Set due date if not provided
            if (empty($fee->due_date) && !empty($fee->payment_date)) {
                $fee->due_date = Carbon::parse($fee->payment_date)->addDays(30);
            }

            // If status is paid, set paid_at
            if ($fee->status === 'paid' && empty($fee->paid_at)) {
                $fee->paid_at = now();
            }
        });

        static::updating(function ($fee) {
            // If status changes to paid, set paid_at
            if ($fee->status === 'paid' && $fee->getOriginal('status') !== 'paid') {
                $fee->paid_at = now();
            }

            // If status changes from paid, clear paid_at
            if ($fee->status !== 'paid' && $fee->getOriginal('status') === 'paid') {
                $fee->paid_at = null;
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