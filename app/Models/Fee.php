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
        'fee_structure_id',
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
        'class_id',
        'grade_id',
        'amount_paid',
        'balance',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
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
     * Get the fee structure associated with this fee.
     */
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    /**
     * Get the class associated with this fee.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the grade associated with this fee.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
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
     * Get student's full name
     */
    public function getStudentNameAttribute()
    {
        if ($this->relationLoaded('student') && $this->student) {
            return ($this->student->first_name ?? '') . ' ' . ($this->student->last_name ?? '');
        }
        
        try {
            $student = Students::find($this->student_id);
            if ($student) {
                return ($student->first_name ?? '') . ' ' . ($student->last_name ?? '');
            }
            return 'Unknown Student';
        } catch (\Exception $e) {
            return 'Student #' . $this->student_id;
        }
    }

    /**
     * Get student's first name
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
     * Get student's last name
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
     * Get student's course name
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
     * Get student's admission number
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
     * Get student's phone number
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
     * Get student's email
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
     * Get complete student details as array
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
     * Get formatted amount paid with currency.
     */
    public function getFormattedAmountPaidAttribute()
    {
        return 'KES ' . number_format($this->amount_paid ?? 0, 2);
    }

    /**
     * Get formatted balance with currency.
     */
    public function getFormattedBalanceAttribute()
    {
        return 'KES ' . number_format($this->balance ?? 0, 2);
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
            'partial' => 'info',
        ];

        $icons = [
            'pending' => 'fa-clock',
            'paid' => 'fa-check-circle',
            'overdue' => 'fa-exclamation-circle',
            'partial' => 'fa-half-alt',
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
        if (in_array($this->status, ['pending', 'partial']) && $this->due_date && $this->due_date->isPast()) {
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
     * Get payment summary
     */
    public function getSummaryAttribute()
    {
        try {
            return [
                'id' => $this->id,
                'student' => $this->student_name,
                'student_id' => $this->student_id,
                'amount' => $this->formatted_amount,
                'amount_paid' => $this->formatted_amount_paid,
                'balance' => $this->formatted_balance,
                'status' => $this->status,
                'status_badge' => $this->status_badge,
                'date' => $this->formatted_payment_date,
                'due_date' => $this->formatted_due_date,
                'is_overdue' => $this->is_overdue,
                'term' => $this->term,
                'academic_year' => $this->academic_year,
                'fee_type' => $this->fee_type,
                'payment_method' => $this->payment_method,
                'receipt_no' => $this->receipt_no,
            ];
        } catch (\Exception $e) {
            return [
                'id' => $this->id,
                'student' => 'Unknown Student',
                'student_id' => $this->student_id,
                'amount' => 'KES ' . number_format($this->amount, 2),
                'amount_paid' => 'KES ' . number_format($this->amount_paid ?? 0, 2),
                'balance' => 'KES ' . number_format($this->balance ?? 0, 2),
                'status' => $this->status ?? 'pending',
                'status_badge' => ['color' => 'secondary', 'icon' => 'fa-circle', 'label' => ucfirst($this->status ?? 'pending')],
                'date' => $this->payment_date ? $this->payment_date->format('d M Y') : 'N/A',
                'due_date' => $this->due_date ? $this->due_date->format('d M Y') : 'N/A',
                'is_overdue' => false,
                'term' => $this->term ?? 'N/A',
                'academic_year' => $this->academic_year ?? 'N/A',
                'fee_type' => $this->fee_type ?? 'N/A',
                'payment_method' => $this->payment_method ?? 'N/A',
                'receipt_no' => $this->receipt_no ?? 'N/A',
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

    /**
     * Get fee structure details if linked.
     */
    public function getFeeStructureDetailsAttribute()
    {
        if (!$this->fee_structure_id) {
            return null;
        }

        if ($this->relationLoaded('feeStructure') && $this->feeStructure) {
            return $this->feeStructure;
        }

        try {
            return FeeStructure::find($this->fee_structure_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if fee is fully paid.
     */
    public function getIsFullyPaidAttribute()
    {
        return ($this->balance ?? 0) <= 0;
    }

    /**
     * Get payment completion percentage.
     */
    public function getPaymentPercentageAttribute()
    {
        if ($this->amount <= 0) {
            return 0;
        }
        
        $paid = $this->amount_paid ?? 0;
        return min(100, round(($paid / $this->amount) * 100, 2));
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
     * Scope: Partial payments.
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope: By status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: By term.
     */
    public function scopeByTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope: By academic year.
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope: By fee type.
     */
    public function scopeByFeeType($query, $feeType)
    {
        return $query->where('fee_type', $feeType);
    }

    /**
     * Scope: By student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: By fee structure.
     */
    public function scopeByFeeStructure($query, $feeStructureId)
    {
        return $query->where('fee_structure_id', $feeStructureId);
    }

    /**
     * Scope: With fee structure linked.
     */
    public function scopeWithFeeStructure($query)
    {
        return $query->with('feeStructure');
    }

    /**
     * Scope: Search by student name - FIXED to use first_name and last_name
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('student', function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('admission_number', 'LIKE', "%{$search}%");
        })->orWhere('receipt_no', 'LIKE', "%{$search}%");
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

    /**
     * Scope: Active fee structures only.
     */
    public function scopeWithActiveFeeStructure($query)
    {
        return $query->whereHas('feeStructure', function($q) {
            $q->where('status', 'active');
        });
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
     * Check if fee is partial.
     */
    public function isPartial()
    {
        return $this->status === 'partial';
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
     * Check if linked to fee structure.
     */
    public function hasFeeStructure(): bool
    {
        return !is_null($this->fee_structure_id);
    }

    /**
     * Mark fee as paid.
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'balance' => 0,
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
     * Mark fee as partial.
     */
    public function markAsPartial()
    {
        $this->update([
            'status' => 'partial',
        ]);
    }

    /**
     * Update balance and status based on payments.
     */
    public function updateBalanceAndStatus()
    {
        $balance = $this->amount - ($this->amount_paid ?? 0);
        $this->balance = max(0, $balance);

        if ($balance <= 0) {
            $this->status = 'paid';
            $this->paid_at = now();
        } elseif ($this->amount_paid > 0 && $balance > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }

        // Check overdue
        if (in_array($this->status, ['pending', 'partial']) && $this->due_date && $this->due_date->isPast()) {
            $this->status = 'overdue';
        }

        $this->save();
        return $this;
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
     * Get student details for the receipt - FIXED to use first_name and last_name
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
                    'name' => ($student->first_name ?? '') . ' ' . ($student->last_name ?? 'Unknown'),
                    'first_name' => $student->first_name ?? 'Unknown',
                    'last_name' => $student->last_name ?? 'Unknown',
                    'admission_number' => $student->admission_number ?? 'N/A',
                    'course' => $student->course->course_name ?? $student->course->name ?? 'Not Assigned',
                    'phone' => $student->phone ?? '',
                    'email' => $student->email ?? 'N/A',
                    'id' => $student->id,
                ];
            }
            
            return [
                'name' => 'Student Not Found',
                'first_name' => 'Unknown',
                'last_name' => 'Unknown',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
                'id' => $this->student_id,
            ];
        } catch (\Exception $e) {
            return [
                'name' => 'Student Not Found',
                'first_name' => 'Unknown',
                'last_name' => 'Unknown',
                'admission_number' => 'N/A',
                'course' => 'Not Assigned',
                'phone' => '',
                'email' => 'N/A',
                'id' => $this->student_id,
            ];
        }
    }

    /**
     * Calculate expected fees for a student based on class, grade, term, and year.
     */
    public static function calculateExpectedFees($studentId, $term, $academicYear)
    {
        $student = Students::with(['class', 'grade'])->find($studentId);
        if (!$student) {
            return 0;
        }

        return FeeStructure::active()
            ->where('class_id', $student->class_id)
            ->where('grade_id', $student->grade_id)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->sum('amount');
    }

    /**
     * Get all expected fee structures for a student.
     */
    public static function getExpectedFeeStructures($studentId, $term, $academicYear)
    {
        $student = Students::with(['class', 'grade'])->find($studentId);
        if (!$student) {
            return collect();
        }

        return FeeStructure::active()
            ->where('class_id', $student->class_id)
            ->where('grade_id', $student->grade_id)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->get();
    }

    /**
     * Get all fees for a student grouped by fee structure.
     */
    public static function getStudentFeesByStructure($studentId, $term, $academicYear)
    {
        $feeStructures = self::getExpectedFeeStructures($studentId, $term, $academicYear);
        
        $result = [];
        foreach ($feeStructures as $structure) {
            $paid = self::where('student_id', $studentId)
                ->where('fee_structure_id', $structure->id)
                ->where('status', 'paid')
                ->sum('amount_paid');
            
            $pending = self::where('student_id', $studentId)
                ->where('fee_structure_id', $structure->id)
                ->whereIn('status', ['pending', 'partial'])
                ->sum('amount');
            
            $result[] = [
                'structure' => $structure,
                'expected' => $structure->amount,
                'paid' => $paid,
                'pending' => $pending,
                'balance' => $structure->amount - $paid,
                'is_fully_paid' => ($structure->amount - $paid) <= 0,
            ];
        }
        
        return $result;
    }

    /**
     * Check if all expected fees for a student are paid.
     */
    public static function areAllFeesPaid($studentId, $term, $academicYear)
    {
        $expectedTotal = self::calculateExpectedFees($studentId, $term, $academicYear);
        $totalPaid = self::where('student_id', $studentId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('status', 'paid')
            ->sum('amount_paid');

        return $totalPaid >= $expectedTotal;
    }

    /**
     * Get payment summary for a student.
     */
    public static function getStudentPaymentSummary($studentId, $term, $academicYear)
    {
        $expected = self::calculateExpectedFees($studentId, $term, $academicYear);
        $paid = self::where('student_id', $studentId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->where('status', 'paid')
            ->sum('amount_paid');
        
        $pending = self::where('student_id', $studentId)
            ->where('term', $term)
            ->where('academic_year', $academicYear)
            ->whereIn('status', ['pending', 'partial'])
            ->sum('amount');
        
        $balance = $expected - $paid;

        return [
            'expected' => $expected,
            'paid' => $paid,
            'pending' => $pending,
            'balance' => $balance,
            'all_paid' => $balance <= 0,
            'payment_percentage' => $expected > 0 ? round(($paid / $expected) * 100, 2) : 0,
        ];
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

            // Calculate balance
            if (isset($fee->amount) && isset($fee->amount_paid)) {
                $fee->balance = $fee->amount - $fee->amount_paid;
            } elseif (isset($fee->amount)) {
                $fee->balance = $fee->amount;
            }

            // Auto-determine status based on balance
            if (isset($fee->balance)) {
                if ($fee->balance <= 0) {
                    $fee->status = 'paid';
                } elseif ($fee->amount_paid > 0 && $fee->balance > 0) {
                    $fee->status = 'partial';
                }
            }

            // Set class and grade from student if not provided
            if (empty($fee->class_id) || empty($fee->grade_id)) {
                try {
                    $student = Students::find($fee->student_id);
                    if ($student) {
                        if (empty($fee->class_id)) {
                            $fee->class_id = $student->class_id;
                        }
                        if (empty($fee->grade_id)) {
                            $fee->grade_id = $student->grade_id;
                        }
                    }
                } catch (\Exception $e) {
                    // Continue without class/grade
                }
            }
        });

        static::updating(function ($fee) {
            // Auto-set paid_at when status transitions to 'paid'
            if ($fee->isDirty('status') && $fee->status === 'paid' && empty($fee->paid_at)) {
                $fee->paid_at = now();
            }

            // Recalculate balance if amount or amount_paid changes
            if ($fee->isDirty('amount') || $fee->isDirty('amount_paid')) {
                $fee->balance = ($fee->amount ?? 0) - ($fee->amount_paid ?? 0);
                
                // Update status based on balance
                if ($fee->balance <= 0) {
                    $fee->status = 'paid';
                } elseif ($fee->amount_paid > 0 && $fee->balance > 0) {
                    $fee->status = 'partial';
                } else {
                    $fee->status = 'pending';
                }
            }

            // Check overdue status
            if (in_array($fee->status, ['pending', 'partial']) && $fee->due_date && $fee->due_date->isPast()) {
                $fee->status = 'overdue';
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