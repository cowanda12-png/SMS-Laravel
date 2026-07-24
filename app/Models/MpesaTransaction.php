<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_no',
        'student_id',
        'phone_number',
        'amount',
        'status',
        'mpesa_receipt',
        'transaction_date',
        'checkout_request_id',
        'merchant_request_id',
        'result_code',
        'result_desc',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'checkout_request_id',
        'merchant_request_id',
    ];

    /**
     * Get the student that owns the M-Pesa transaction.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class);
    }

    /**
     * Get the payment associated with the M-Pesa transaction.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'receipt_no', 'receipt_no');
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by today's transactions.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }

    /**
     * Scope a query to filter by this month's transactions.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                     ->whereYear('transaction_date', now()->year);
    }

    /**
     * Check if the transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if the transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'Failed';
    }

    /**
     * Get the status badge color for UI display.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Completed' => 'success',
            'Pending'   => 'warning',
            'Failed'    => 'danger',
            default     => 'secondary',
        };
    }

    /**
     * Get the status icon for UI display.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'Completed' => 'bi-check-circle-fill',
            'Pending'   => 'bi-clock-fill',
            'Failed'    => 'bi-x-circle-fill',
            default     => 'bi-question-circle-fill',
        };
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'KES ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted phone number.
     */
    public function getFormattedPhoneAttribute(): string
    {
        // Format: 07XX XXX XXX
        if (strlen($this->phone_number) === 10) {
            return substr($this->phone_number, 0, 4) . ' ' . 
                   substr($this->phone_number, 4, 3) . ' ' . 
                   substr($this->phone_number, 7, 3);
        }
        return $this->phone_number;
    }

    /**
     * Get the transaction date in a readable format.
     */
    public function getReadableDateAttribute(): string
    {
        return $this->transaction_date ? $this->transaction_date->format('d-m-Y H:i:s') : 'N/A';
    }

    /**
     * Get the transaction date in a short format.
     */
    public function getShortDateAttribute(): string
    {
        return $this->transaction_date ? $this->transaction_date->format('d-m-Y') : 'N/A';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate receipt number if not provided
        static::creating(function ($transaction) {
            if (empty($transaction->receipt_no)) {
                $transaction->receipt_no = 'MPESA-' . date('Y') . '-' . 
                                          str_pad(static::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = now();
            }
        });
    }

    /**
     * Get the total amount of completed transactions.
     */
    public static function getTotalCompletedAmount(): float
    {
        return static::completed()->sum('amount');
    }

    /**
     * Get the total amount of pending transactions.
     */
    public static function getTotalPendingAmount(): float
    {
        return static::pending()->sum('amount');
    }

    /**
     * Get the total amount of failed transactions.
     */
    public static function getTotalFailedAmount(): float
    {
        return static::failed()->sum('amount');
    }

    /**
     * Get transaction statistics.
     */
    public static function getStatistics(): array
    {
        $total = static::count();
        $completed = static::completed()->count();
        $pending = static::pending()->count();
        $failed = static::failed()->count();

        return [
            'total_transactions' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'failed' => $failed,
            'completed_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 2) : 0,
            'failed_percentage' => $total > 0 ? round(($failed / $total) * 100, 2) : 0,
            'total_amount' => static::sum('amount'),
            'completed_amount' => static::completed()->sum('amount'),
            'pending_amount' => static::pending()->sum('amount'),
            'failed_amount' => static::failed()->sum('amount'),
        ];
    }
}