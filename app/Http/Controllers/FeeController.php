<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FeeController extends Controller
{
    /**
     * Display a listing of fee payments
     */
    public function index(Request $request)
    {
        $query = Fee::with('student.course');
        
        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filter by fee type
        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        
        // Search by receipt number or student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipt_no', 'LIKE', "%{$search}%")
                  ->orWhereHas('student', function($studentQuery) use ($search) {
                      $studentQuery->where('first_name', 'LIKE', "%{$search}%")
                                   ->orWhere('last_name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%")
                                   ->orWhere('admission_number', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Get paginated results
        $fees = $query->orderBy('payment_date', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();
        
        // Calculate statistics
        $totalFees = Fee::sum('amount') ?? 0;
        $todayFees = Fee::whereDate('payment_date', Carbon::today())->sum('amount') ?? 0;
        $pendingFees = Fee::where('status', 'pending')->sum('amount') ?? 0;
        $overdueFees = Fee::where('status', 'overdue')->sum('amount') ?? 0;
        
        // Get counts
        $pendingCount = Fee::where('status', 'pending')->count() ?? 0;
        $overdueCount = Fee::where('status', 'overdue')->count() ?? 0;
        
        // Get distinct values for filters
        $paymentMethods = collect();
        $feeTypes = collect();
        $statuses = ['paid', 'pending', 'overdue'];
        $terms = Fee::distinct()->whereNotNull('term')->pluck('term')->filter()->values();
        $academicYears = Fee::distinct()->whereNotNull('academic_year')->pluck('academic_year')->filter()->values();
        
        // Get students for filter dropdown
        $students = Students::orderBy('first_name')->orderBy('last_name')->get();
        
        // Summary statistics for dashboard integration
        $summary = [
            'total_collected' => $totalFees,
            'today_collected' => $todayFees,
            'pending_amount' => $pendingFees,
            'overdue_amount' => $overdueFees,
            'total_transactions' => Fee::count(),
            'collection_rate' => $totalFees > 0 ? round(($totalFees - $pendingFees) / $totalFees * 100, 1) : 0,
        ];
        
        // Monthly chart data - Database agnostic
        $monthlyData = collect();
        if (Schema::hasColumn('fees', 'payment_date')) {
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'pgsql') {
                $monthlyData = Fee::selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as month, SUM(amount) as total")
                                  ->whereNotNull('payment_date')
                                  ->whereYear('payment_date', Carbon::now()->year)
                                  ->groupBy('month')
                                  ->orderBy('month')
                                  ->get();
            } else {
                // MySQL, SQLite, etc.
                $monthlyData = Fee::selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total")
                                  ->whereNotNull('payment_date')
                                  ->whereYear('payment_date', Carbon::now()->year)
                                  ->groupBy('month')
                                  ->orderBy('month')
                                  ->get();
            }
        }
        
        return view('fees.index', compact(
            'fees', 
            'totalFees', 
            'todayFees', 
            'pendingFees',
            'overdueFees',
            'pendingCount',
            'overdueCount',
            'paymentMethods', 
            'feeTypes',
            'statuses',
            'students',
            'summary',
            'monthlyData'
        ));
    }

    /**
     * Show the form for creating a new fee payment
     */
    public function create()
    {
        $students = Students::with('course')
                            ->orderBy('first_name')
                            ->orderBy('last_name')
                            ->get();
        
        $paymentMethods = ['Cash', 'Bank Transfer', 'Cheque', 'M-Pesa', 'Credit Card', 'Other'];
        $feeTypes = ['Tuition', 'Registration', 'Examination', 'Library', 'Sports', 'Laboratory', 'Other'];
        $statuses = ['pending', 'paid', 'overdue'];
        
        return view('fees.create', compact('students', 'paymentMethods', 'feeTypes', 'statuses'));
    }

    /**
     * Store a newly created fee payment
     */
    public function store(Request $request)
    {
        // 🔍 Debug: log exactly what was received before validation,
        // to catch any invisible whitespace/encoding issues in payment_method.
        Log::info('📝 Fee store() request received:', [
            'payment_method_raw' => $request->input('payment_method'),
            'payment_method_length' => strlen((string) $request->input('payment_method')),
            'payment_method_bytes' => bin2hex((string) $request->input('payment_method')),
            'status' => $request->input('status'),
        ]);

        // Defensive trim in case of stray whitespace/newlines from the hidden field
        if ($request->has('payment_method')) {
            $request->merge([
                'payment_method' => trim((string) $request->input('payment_method')),
            ]);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Cheque,M-Pesa,Credit Card,Other',
            'receipt_no' => 'nullable|string|max:255|unique:fees,receipt_no',
            'payment_date' => 'required|date|before_or_equal:today',
            'fee_type' => 'nullable|string|in:Tuition,Registration,Examination,Library,Sports,Laboratory,Other',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:pending,paid,overdue',
            'due_date' => 'nullable|date|after_or_equal:payment_date',
            'mpesa_phone' => 'nullable|string|max:20',
            'mpesa_transaction_code' => 'nullable|string|max:50',
            'mpesa_checkout_request_id' => 'nullable|string|max:100',
            'term' => 'nullable|string|max:50',
            'academic_year' => 'nullable|string|max:50',
        ]);

        // Generate receipt number if not provided
        if (empty($validated['receipt_no'])) {
            $validated['receipt_no'] = 'RCP-' . now()->format('Ymd') . '-' . 
                                       str_pad(Fee::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        // If M-Pesa payment and transaction code provided, consider it paid
        if ($validated['payment_method'] === 'M-Pesa' && !empty($validated['mpesa_transaction_code'])) {
            $validated['status'] = 'paid';
        }

        // If status is paid, set paid_at date
        if ($validated['status'] === 'paid') {
            $validated['paid_at'] = now();
        }

        // Set default due date if not provided
        if (empty($validated['due_date'])) {
            $validated['due_date'] = Carbon::parse($validated['payment_date'])->addDays(30);
        }

        // Store M-Pesa transaction details if present
        if ($request->has('mpesa_transaction_code')) {
            $validated['mpesa_transaction_code'] = $request->mpesa_transaction_code;
        }
        
        if ($request->has('mpesa_checkout_request_id')) {
            $validated['mpesa_checkout_request_id'] = $request->mpesa_checkout_request_id;
        }

        $fee = Fee::create($validated);

        // If M-Pesa payment, update student's phone if not set
        if ($validated['payment_method'] === 'M-Pesa' && $request->filled('mpesa_phone')) {
            $student = Students::find($validated['student_id']);
            if ($student && empty($student->phone)) {
                $student->update(['phone' => $request->mpesa_phone]);
            }
        }

        // If payment is successful, redirect to receipt
        if ($validated['status'] === 'paid' && $validated['payment_method'] === 'M-Pesa') {
            return redirect()->route('fees.receipt', $fee->id)
                ->with('success', 'Payment successful! Receipt #: ' . ($fee->mpesa_transaction_code ?? $fee->receipt_no));
        }

        return redirect()->route('fees.index')
                        ->with('success', 'Payment recorded successfully! Receipt #: ' . $fee->receipt_no);
    }

    /**
     * Display the specified fee payment
     */
    public function show(Fee $fee)
    {
        $fee->load('student.course');
        
        // Get related fees for the same student
        $relatedFees = Fee::where('student_id', $fee->student_id)
                          ->where('id', '!=', $fee->id)
                          ->orderBy('payment_date', 'desc')
                          ->limit(5)
                          ->get();
        
        // Calculate student's total payments
        $studentTotal = Fee::where('student_id', $fee->student_id)->sum('amount');
        
        return view('fees.show', compact('fee', 'relatedFees', 'studentTotal'));
    }

    /**
     * Show payment receipt
     */
    public function showReceipt($id)
    {
        $fee = Fee::with('student.course')->findOrFail($id);
        return view('fees.receipt', compact('fee'));
    }

    /**
     * Show the form for editing the specified fee payment
     */
    public function edit(Fee $fee)
    {
        $students = Students::with('course')
                            ->orderBy('first_name')
                            ->orderBy('last_name')
                            ->get();
        
        $paymentMethods = ['Cash', 'Bank Transfer', 'Cheque', 'M-Pesa', 'Credit Card', 'Other'];
        $feeTypes = ['Tuition', 'Registration', 'Examination', 'Library', 'Sports', 'Laboratory', 'Other'];
        $statuses = ['pending', 'paid', 'overdue'];
        
        return view('fees.edit', compact('fee', 'students', 'paymentMethods', 'feeTypes', 'statuses'));
    }

    /**
     * Update the specified fee payment
     */
    public function update(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Cheque,M-Pesa,Credit Card,Other',
            'receipt_no' => 'nullable|string|max:255|unique:fees,receipt_no,' . $fee->id,
            'payment_date' => 'required|date|before_or_equal:today',
            'fee_type' => 'nullable|string|in:Tuition,Registration,Examination,Library,Sports,Laboratory,Other',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:pending,paid,overdue',
            'due_date' => 'nullable|date|after_or_equal:payment_date',
            'mpesa_transaction_code' => 'nullable|string|max:50',
        ]);

        // Update paid_at if status changed to paid
        if ($validated['status'] === 'paid' && $fee->status !== 'paid') {
            $validated['paid_at'] = now();
        } elseif ($validated['status'] !== 'paid') {
            $validated['paid_at'] = null;
        }

        // Set default due date if not provided
        if (empty($validated['due_date'])) {
            $validated['due_date'] = Carbon::parse($validated['payment_date'])->addDays(30);
        }

        $fee->update($validated);

        return redirect()->route('fees.index')
                        ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified fee payment
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();

        return redirect()->route('fees.index')
                        ->with('success', 'Payment deleted successfully!');
    }

    /**
     * Generate payment report
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $fees = Fee::with('student.course')
                   ->whereDate('payment_date', '>=', $startDate)
                   ->whereDate('payment_date', '<=', $endDate)
                   ->orderBy('payment_date', 'desc')
                   ->get();

        $summary = [
            'total_amount' => $fees->sum('amount'),
            'total_payments' => $fees->count(),
            'by_method' => $fees->groupBy('payment_method')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount')
                ];
            }),
            'by_type' => $fees->groupBy('fee_type')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount')
                ];
            }),
            'by_status' => $fees->groupBy('status')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount')
                ];
            }),
            'daily' => $fees->groupBy(function($fee) {
                return Carbon::parse($fee->payment_date)->format('Y-m-d');
            })->map(function($group) {
                return $group->sum('amount');
            })
        ];

        // For export functionality
        if ($request->has('export')) {
            return $this->exportReport($fees, $startDate, $endDate);
        }

        return view('fees.report', compact('fees', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Export report to CSV
     */
    private function exportReport($fees, $startDate, $endDate)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=fee_report_{$startDate}_to_{$endDate}.csv",
        ];

        $callback = function() use ($fees) {
            $handle = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($handle, ['Receipt No', 'Student', 'Amount', 'Payment Method', 'Fee Type', 'Status', 'Payment Date', 'Due Date']);
            
            // Add data
            foreach ($fees as $fee) {
                fputcsv($handle, [
                    $fee->receipt_no,
                    $fee->student->first_name . ' ' . $fee->student->last_name,
                    $fee->amount,
                    $fee->payment_method,
                    $fee->fee_type,
                    $fee->status,
                    $fee->payment_date,
                    $fee->due_date,
                ]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get student's fee history (for API or AJAX)
     */
    public function studentFees($studentId)
    {
        $student = Students::findOrFail($studentId);
        $fees = Fee::where('student_id', $studentId)
                   ->orderBy('payment_date', 'desc')
                   ->get();
        
        $totalPaid = $fees->where('status', 'paid')->sum('amount');
        $totalPending = $fees->where('status', 'pending')->sum('amount');
        
        return response()->json([
            'student' => $student,
            'fees' => $fees,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
        ]);
    }

    /**
     * API endpoint for fee statistics (for dashboard widgets)
     */
    public function stats()
    {
        // Monthly trend - Database agnostic
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            $monthlyTrend = Fee::selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as month, SUM(amount) as total")
                               ->whereNotNull('payment_date')
                               ->whereYear('payment_date', Carbon::now()->year)
                               ->groupBy('month')
                               ->orderBy('month')
                               ->get();
        } else {
            $monthlyTrend = Fee::selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total")
                               ->whereNotNull('payment_date')
                               ->whereYear('payment_date', Carbon::now()->year)
                               ->groupBy('month')
                               ->orderBy('month')
                               ->get();
        }
        
        $stats = [
            'total_collected' => Fee::sum('amount') ?? 0,
            'today_collected' => Fee::whereDate('payment_date', Carbon::today())->sum('amount') ?? 0,
            'this_month' => Fee::whereMonth('payment_date', Carbon::now()->month)
                               ->whereYear('payment_date', Carbon::now()->year)
                               ->sum('amount') ?? 0,
            'total_transactions' => Fee::count(),
            'pending_amount' => Fee::where('status', 'pending')->sum('amount') ?? 0,
            'overdue_amount' => Fee::where('status', 'overdue')->sum('amount') ?? 0,
            'by_payment_method' => Fee::selectRaw('payment_method, count(*) as count, sum(amount) as total')
                                        ->whereNotNull('payment_method')
                                        ->groupBy('payment_method')
                                        ->get(),
            'by_fee_type' => Fee::selectRaw('fee_type, count(*) as count, sum(amount) as total')
                                 ->whereNotNull('fee_type')
                                 ->groupBy('fee_type')
                                 ->get(),
            'top_students' => Students::withSum('fees', 'amount')
                                      ->having('fees_sum_amount', '>', 0)
                                      ->orderBy('fees_sum_amount', 'desc')
                                      ->limit(10)
                                      ->get(['id', 'first_name', 'last_name', 'fees_sum_amount']),
            'monthly_trend' => $monthlyTrend,
        ];

        return response()->json($stats);
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment marked as paid']);
        }

        return redirect()->route('fees.index')
                        ->with('success', 'Payment marked as paid successfully!');
    }

    /**
     * Mark payment as overdue
     */
    public function markAsOverdue($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->update([
            'status' => 'overdue',
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment marked as overdue']);
        }

        return redirect()->route('fees.index')
                        ->with('success', 'Payment marked as overdue!');
    }

    /**
     * Bulk delete fees
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No fees selected']);
            }
            return redirect()->route('fees.index')
                            ->with('error', 'No fees selected for deletion.');
        }

        Fee::whereIn('id', $ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Selected fees deleted successfully']);
        }

        return redirect()->route('fees.index')
                        ->with('success', 'Selected fees deleted successfully!');
    }

    // ==================== M-PESA INTEGRATION METHODS ====================

    /**
     * Initiate M-Pesa STK Push
     */
    public function initiateMpesaPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:1|max:999999.99',
                'phone' => 'required|string|max:20',
                'reference' => 'nullable|string',
            ]);

            $student = Students::findOrFail($validated['student_id']);
            $phone = $this->formatPhoneNumber($validated['phone']);
            $amount = $validated['amount'];
            $reference = $request->reference ?? 'PAY-' . $student->id . '-' . time();

            Log::info('📱 Initiating M-Pesa STK Push:', [
                'student_id' => $student->id,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'phone' => $phone,
                'amount' => $amount,
                'reference' => $reference
            ]);

            // Check if M-Pesa is configured
            if (!$this->isMpesaConfigured()) {
                Log::warning('⚠️ M-Pesa not configured, using simulation mode');
                return $this->simulateMpesaPayment($student, $phone, $amount, $reference);
            }

            // Send actual M-Pesa STK Push
            $response = $this->sendMpesaStkPush($phone, $amount, $reference);

            if ($response['success']) {
                // Create pending fee record
                $fee = Fee::create([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'payment_method' => 'M-Pesa',
                    'payment_date' => now(),
                    'status' => 'pending',
                    'mpesa_phone' => $phone,
                    'mpesa_checkout_request_id' => $response['checkout_request_id'],
                    'receipt_no' => 'PND-' . time() . '-' . rand(1000, 9999),
                    'fee_type' => 'Tuition',
                    'description' => 'M-Pesa Payment - ' . ($student->first_name ?? 'Student'),
                ]);

                Log::info('✅ Fee created with checkout ID:', [
                    'fee_id' => $fee->id,
                    'checkout_request_id' => $response['checkout_request_id'],
                    'phone' => $phone
                ]);

                return response()->json([
                    'success' => true,
                    'checkout_request_id' => $response['checkout_request_id'],
                    'payment_id' => $fee->id,
                    'phone' => $phone,
                    'message' => 'STK Push sent successfully. Please check your phone.',
                    'redirect_url' => route('fees.receipt', $fee->id)
                ]);
            }

            Log::error('❌ STK Push failed:', [
                'response' => $response
            ]);

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Failed to initiate payment'
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa Initiation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Check M-Pesa transaction status
     */
    public function checkMpesaStatus(Request $request)
    {
        try {
            $checkoutRequestId = $request->input('checkout_request_id');
            $phone = $request->input('phone');
            $forceCheck = $request->input('force_check', false);

            if (!$checkoutRequestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout request ID is required'
                ], 400);
            }

            Log::info('🔍 Checking M-Pesa Status:', [
                'checkout_request_id' => $checkoutRequestId,
                'phone' => $phone,
                'force_check' => $forceCheck
            ]);

            // FIRST: Check if payment already exists in database with status 'paid'
            $fee = Fee::where('mpesa_checkout_request_id', $checkoutRequestId)->first();
            
            // FALLBACK 1: If not found by checkout_id, try by phone number (paid status)
            if (!$fee && $phone) {
                Log::info('📞 Trying to find fee by phone: ' . $phone);
                $fee = Fee::where('mpesa_phone', $phone)
                          ->where('status', 'paid')
                          ->latest()
                          ->first();
                if ($fee) {
                    Log::info('✅ Found paid fee by phone: ' . $phone . ', ID: ' . $fee->id);
                }
            }
            
            // FALLBACK 2: Try to find by phone with pending status
            if (!$fee && $phone && !$forceCheck) {
                $fee = Fee::where('mpesa_phone', $phone)
                          ->where('status', 'pending')
                          ->latest()
                          ->first();
                if ($fee) {
                    Log::info('📞 Found pending fee by phone, checking if callback was missed: ' . $fee->id);
                }
            }
            
            if ($fee && $fee->status === 'paid') {
                Log::info('✅ Payment already marked as paid in database', [
                    'checkout_request_id' => $checkoutRequestId,
                    'receipt' => $fee->mpesa_transaction_code,
                    'fee_id' => $fee->id
                ]);
                
                return response()->json([
                    'success' => true,
                    'resultCode' => '0',
                    'resultDesc' => 'Payment already completed',
                    'amount' => $fee->amount,
                    'mpesa_receipt_number' => $fee->mpesa_transaction_code ?? $fee->receipt_no,
                    'status' => 'completed',
                    'from_database' => true,
                    'payment_id' => $fee->id,
                    'redirect_url' => route('fees.receipt', $fee->id)
                ]);
            }

            // If not in database, check with M-Pesa API
            if (!$this->isMpesaConfigured()) {
                Log::info('🔄 M-Pesa not configured, using simulation');
                $result = $this->simulateMpesaStatus($checkoutRequestId);
                return response()->json($result);
            }

            // Query actual M-Pesa API
            $status = $this->queryMpesaStatus($checkoutRequestId);

            Log::info('📦 M-Pesa API Status Response:', $status);

            // Update fee record if found
            if (isset($status['resultCode']) && $fee) {
                if ($status['resultCode'] === '0') {
                    $fee->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'mpesa_transaction_code' => $status['mpesa_receipt_number'] ?? null,
                        'mpesa_result_code' => $status['resultCode'],
                        'receipt_no' => $status['mpesa_receipt_number'] ?? $fee->receipt_no,
                        'mpesa_result_desc' => $status['resultDesc'] ?? 'Payment successful',
                    ]);
                    Log::info('✅ Updated fee from API status: ' . $fee->id);
                } elseif (in_array($status['resultCode'], ['1032', '1037', '2001'])) {
                    $fee->update([
                        'status' => 'failed',
                        'mpesa_result_code' => $status['resultCode'],
                        'mpesa_result_desc' => $status['resultDesc'] ?? 'Payment failed',
                    ]);
                    Log::warning('⚠️ Payment failed with code: ' . $status['resultCode']);
                }
            }

            // If payment was successful
            if (isset($status['resultCode']) && $status['resultCode'] === '0') {
                if ($fee && $fee->status !== 'paid') {
                    $fee->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'mpesa_transaction_code' => $status['mpesa_receipt_number'] ?? null,
                        'mpesa_result_code' => $status['resultCode'],
                        'receipt_no' => $status['mpesa_receipt_number'] ?? $fee->receipt_no,
                        'mpesa_result_desc' => $status['resultDesc'] ?? 'Payment successful',
                    ]);
                    Log::info('✅ Updated fee after status check: ' . $fee->id);
                }
                
                return response()->json([
                    'success' => true,
                    'resultCode' => '0',
                    'resultDesc' => $status['resultDesc'] ?? 'Payment successful',
                    'amount' => $fee->amount ?? $status['amount'] ?? 0,
                    'mpesa_receipt_number' => $status['mpesa_receipt_number'] ?? null,
                    'status' => 'completed',
                    'payment_id' => $fee->id ?? null,
                    'redirect_url' => $fee ? route('fees.receipt', $fee->id) : null
                ]);
            }

            // If fee exists but is pending, return pending
            if ($fee && $fee->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Payment still processing',
                    'fee_id' => $fee->id
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => 'pending',
                'message' => 'Payment still processing'
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa Status Check Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status.'
            ], 500);
        }
    }

    /**
     * Get M-Pesa access token, cached to avoid hammering Safaricom's
     * OAuth endpoint on every request (which triggers Incapsula bot
     * blocking when called repeatedly, e.g. during status polling).
     */
    private function getMpesaAccessToken()
    {
        $environment = env('MPESA_ENV', 'sandbox');
        $cacheKey = 'mpesa_access_token_' . $environment;

        return Cache::remember($cacheKey, 3500, function () use ($environment) {
            $consumerKey = env('MPESA_CONSUMER_KEY');
            $consumerSecret = env('MPESA_CONSUMER_SECRET');

            $baseUrl = $environment === 'production'
                ? 'https://api.safaricom.co.ke'
                : 'https://sandbox.safaricom.co.ke';

            $tokenResponse = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                ])
                ->timeout(30)
                ->get($baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if (!$tokenResponse->successful()) {
                Log::error('M-Pesa Token Error: ' . $tokenResponse->body());
                throw new \Exception('Failed to get M-Pesa access token');
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                Log::error('No access token in response:', $tokenData);
                throw new \Exception('No access token returned by M-Pesa');
            }

            Log::info('🔑 Fetched fresh M-Pesa access token (will cache ~58 min)');

            return $accessToken;
        });
    }

    /**
     * Resend M-Pesa STK Push for an existing (unpaid) fee record.
     */
    public function resendMpesaPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'fee_id' => 'required|exists:fees,id',
            ]);

            $fee = Fee::with('student')->findOrFail($validated['fee_id']);

            if ($fee->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'This payment has already been completed.',
                ]);
            }

            if (empty($fee->mpesa_phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No M-Pesa phone number is on file for this payment.',
                ]);
            }

            $reference = 'PAY-' . $fee->student_id . '-' . time();

            Log::info('🔁 Resending M-Pesa STK Push for fee:', [
                'fee_id' => $fee->id,
                'phone' => $fee->mpesa_phone,
                'amount' => $fee->amount,
            ]);

            if (!$this->isMpesaConfigured()) {
                return $this->simulateMpesaPayment($fee->student, $fee->mpesa_phone, $fee->amount, $reference);
            }

            $response = $this->sendMpesaStkPush($fee->mpesa_phone, $fee->amount, $reference);

            if ($response['success']) {
                $fee->update([
                    'status' => 'pending',
                    'mpesa_checkout_request_id' => $response['checkout_request_id'],
                ]);

                Log::info('✅ STK Push resent, fee updated:', [
                    'fee_id' => $fee->id,
                    'checkout_request_id' => $response['checkout_request_id'],
                ]);

                return response()->json([
                    'success' => true,
                    'checkout_request_id' => $response['checkout_request_id'],
                    'message' => 'STK Push resent successfully. Please check your phone.',
                ]);
            }

            Log::error('❌ Resend STK Push failed:', ['response' => $response]);

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Failed to resend payment request.',
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa Resend Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Send M-Pesa STK Push (Actual Implementation)
     */
    private function sendMpesaStkPush($phone, $amount, $reference)
    {
        try {
            $consumerKey = env('MPESA_CONSUMER_KEY');
            $consumerSecret = env('MPESA_CONSUMER_SECRET');
            $shortCode = env('MPESA_SHORTCODE', '174379');
            $passkey = env('MPESA_PASSKEY');
            $callbackUrl = env('MPESA_CALLBACK_URL');
            $environment = env('MPESA_ENV', 'sandbox');
            
            if (empty($consumerKey) || empty($consumerSecret) || empty($passkey)) {
                Log::error('M-Pesa credentials missing!');
                return [
                    'success' => false,
                    'message' => 'M-Pesa credentials not configured. Please check your .env file.'
                ];
            }
            
            if (!$callbackUrl) {
                $callbackUrl = url('/api/mpesa/callback');
            }
            
            $baseUrl = $environment === 'production' 
                ? 'https://api.safaricom.co.ke' 
                : 'https://sandbox.safaricom.co.ke';

            Log::info('📤 Sending STK Push Request:', [
                'phone' => $phone,
                'amount' => $amount,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'base_url' => $baseUrl,
                'shortcode' => $shortCode,
                'environment' => $environment
            ]);

            // Get access token (cached to avoid repeated OAuth calls)
            try {
                $accessToken = $this->getMpesaAccessToken();
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with M-Pesa. Please check your credentials.'
                ];
            }

            $timestamp = date('YmdHis');
            $password = base64_encode($shortCode . $passkey . $timestamp);

            $stkRequest = [
                'BusinessShortCode' => $shortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) ceil($amount),
                'PartyA' => $phone,
                'PartyB' => $shortCode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => $reference,
                'TransactionDesc' => 'School Fee Payment',
            ];

            Log::info('📤 Sending STK Push payload:', $stkRequest);

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                ])
                ->timeout(30)
                ->post($baseUrl . '/mpesa/stkpush/v1/processrequest', $stkRequest);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('📥 STK Push Response:', $data);
                
                if (isset($data['ResponseCode']) && $data['ResponseCode'] === '0') {
                    return [
                        'success' => true,
                        'checkout_request_id' => $data['CheckoutRequestID'],
                        'response_code' => $data['ResponseCode'],
                        'message' => $data['CustomerMessage'] ?? 'STK Push sent successfully'
                    ];
                } else {
                    $errorMessage = $data['ResponseDescription'] ?? $data['errorMessage'] ?? 'Payment request failed';
                    Log::error('STK Push failed:', ['response' => $data]);
                    return [
                        'success' => false,
                        'message' => $errorMessage
                    ];
                }
            }

            Log::error('M-Pesa STK Push HTTP Error: ' . $response->status() . ' - ' . $response->body());
            
            $statusCode = $response->status();
            $messages = [
                401 => 'Authentication failed. Please check your credentials.',
                403 => 'Access forbidden. Please check your permissions.',
                404 => 'M-Pesa API endpoint not found.',
                500 => 'M-Pesa server error. Please try again later.'
            ];
            
            return [
                'success' => false,
                'message' => $messages[$statusCode] ?? 'Failed to send payment request. HTTP ' . $statusCode
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query M-Pesa transaction status
     */
    private function queryMpesaStatus($checkoutRequestId)
    {
        try {
            $consumerKey = env('MPESA_CONSUMER_KEY');
            $consumerSecret = env('MPESA_CONSUMER_SECRET');
            $shortCode = env('MPESA_SHORTCODE', '174379');
            $passkey = env('MPESA_PASSKEY');
            $environment = env('MPESA_ENV', 'sandbox');
            
            $baseUrl = $environment === 'production' 
                ? 'https://api.safaricom.co.ke' 
                : 'https://sandbox.safaricom.co.ke';

            $accessToken = $this->getMpesaAccessToken();

            $timestamp = date('YmdHis');
            $password = base64_encode($shortCode . $passkey . $timestamp);

            $queryRequest = [
                'BusinessShortCode' => $shortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId,
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                ])
                ->timeout(30)
                ->post($baseUrl . '/mpesa/stkpushquery/v1/query', $queryRequest);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('📥 Status Query Response:', $data);
                
                if (isset($data['ResultCode'])) {
                    $resultCode = $data['ResultCode'];
                    
                    if ($resultCode === '0') {
                        $amount = 0;
                        $mpesaReceiptNumber = null;
                        
                        if (isset($data['Result']['ResultParameters']['ResultParameter'])) {
                            foreach ($data['Result']['ResultParameters']['ResultParameter'] as $param) {
                                if ($param['Key'] === 'Amount') {
                                    $amount = $param['Value'];
                                }
                                if ($param['Key'] === 'MpesaReceiptNumber') {
                                    $mpesaReceiptNumber = $param['Value'];
                                }
                            }
                        }
                        
                        return [
                            'success' => true,
                            'resultCode' => $resultCode,
                            'resultDesc' => $data['ResultDesc'] ?? 'Payment successful',
                            'amount' => $amount,
                            'mpesa_receipt_number' => $mpesaReceiptNumber,
                            'transaction_date' => $data['Result']['TransactionDate'] ?? null,
                            'phone_number' => $data['Result']['PhoneNumber'] ?? null,
                        ];
                    } else {
                        return [
                            'success' => false,
                            'resultCode' => $resultCode,
                            'resultDesc' => $data['ResultDesc'] ?? 'Payment not completed',
                        ];
                    }
                }
                
                return [
                    'success' => false,
                    'message' => 'Invalid response from M-Pesa'
                ];
            }

            Log::error('M-Pesa Status Query Response Error: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Failed to check payment status'
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa Status Query Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for M-Pesa
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 254
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // If starts with +, remove it
        if (substr($phone, 0, 1) === '+') {
            $phone = substr($phone, 1);
        }
        
        // Ensure it starts with 254
        if (substr($phone, 0, 3) !== '254') {
            $phone = '254' . $phone;
        }
        
        Log::info('📞 Formatted phone number:', ['formatted' => $phone]);
        
        return $phone;
    }

    /**
     * Check if M-Pesa is configured
     */
    private function isMpesaConfigured()
    {
        $configured = !empty(env('MPESA_CONSUMER_KEY')) && 
                      !empty(env('MPESA_CONSUMER_SECRET')) && 
                      !empty(env('MPESA_PASSKEY'));
        
        Log::info('🔧 M-Pesa Configuration Status:', [
            'configured' => $configured,
            'consumer_key' => env('MPESA_CONSUMER_KEY') ? '***' . substr(env('MPESA_CONSUMER_KEY'), -4) : 'missing',
            'consumer_secret' => env('MPESA_CONSUMER_SECRET') ? '***' . substr(env('MPESA_CONSUMER_SECRET'), -4) : 'missing',
            'passkey' => env('MPESA_PASSKEY') ? '***' . substr(env('MPESA_PASSKEY'), -4) : 'missing',
            'shortcode' => env('MPESA_SHORTCODE', '174379'),
            'environment' => env('MPESA_ENV', 'sandbox')
        ]);
        
        return $configured;
    }

    /**
     * Simulate M-Pesa payment (for testing)
     */
    private function simulateMpesaPayment($student, $phone, $amount, $reference)
    {
        Log::info('🔄 Simulating M-Pesa payment:', [
            'student' => $student->id,
            'phone' => $phone,
            'amount' => $amount,
            'reference' => $reference
        ]);

        $checkoutRequestId = 'SIM_' . time() . '_' . rand(1000, 9999);
        
        $fee = Fee::create([
            'student_id' => $student->id,
            'amount' => $amount,
            'payment_method' => 'M-Pesa',
            'payment_date' => now(),
            'status' => 'pending',
            'mpesa_phone' => $phone,
            'mpesa_checkout_request_id' => $checkoutRequestId,
            'receipt_no' => 'SIM-' . time() . '-' . rand(1000, 9999),
            'fee_type' => 'Tuition',
            'description' => 'Simulated M-Pesa Payment - Testing Mode',
        ]);

        Log::info('✅ Simulated fee created:', ['fee_id' => $fee->id, 'checkout_id' => $checkoutRequestId]);

        return response()->json([
            'success' => true,
            'checkout_request_id' => $checkoutRequestId,
            'payment_id' => $fee->id,
            'phone' => $phone,
            'simulated' => true,
            'message' => 'Simulated M-Pesa payment initiated (Testing mode)',
            'redirect_url' => route('fees.receipt', $fee->id)
        ]);
    }

    /**
     * Simulate M-Pesa status check
     */
    private function simulateMpesaStatus($checkoutRequestId)
    {
        Log::info('🔄 Simulating M-Pesa status check:', ['checkout_id' => $checkoutRequestId]);

        $fee = Fee::where('mpesa_checkout_request_id', $checkoutRequestId)->first();
        
        if ($fee) {
            $fee->update([
                'status' => 'paid',
                'paid_at' => now(),
                'mpesa_transaction_code' => 'SIM' . time() . rand(1000, 9999),
                'mpesa_result_code' => '0',
                'receipt_no' => 'SIM' . time() . rand(1000, 9999),
            ]);
            Log::info('✅ Simulated fee marked as paid:', ['fee_id' => $fee->id]);
        }
        
        return [
            'success' => true,
            'resultCode' => '0',
            'resultDesc' => 'The service request was processed successfully',
            'amount' => $fee->amount ?? 1000,
            'mpesa_receipt_number' => 'SIM' . time() . rand(1000, 9999),
            'transaction_date' => Carbon::now()->format('YmdHis'),
            'phone_number' => $fee->mpesa_phone ?? '254700000000',
            'simulated' => true,
            'payment_id' => $fee->id ?? null,
            'redirect_url' => $fee ? route('fees.receipt', $fee->id) : null
        ];
    }

    /**
     * M-Pesa Callback handler
     */
    public function mpesaCallback(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('📞 M-Pesa Callback Received:', $data);

            if (!isset($data['Body']['stkCallback'])) {
                Log::error('Invalid callback format');
                return response()->json(['error' => 'Invalid format'], 400);
            }

            $callback = $data['Body']['stkCallback'];
            $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
            $resultCode = $callback['ResultCode'] ?? null;
            $resultDesc = $callback['ResultDesc'] ?? '';

            Log::info("📝 Processing: checkout_id={$checkoutRequestId}, result={$resultCode}");

            if (!$checkoutRequestId) {
                Log::error('Missing CheckoutRequestID');
                return response()->json(['error' => 'Missing CheckoutRequestID'], 400);
            }

            // Find the fee - TRY MULTIPLE WAYS
            $fee = null;

            // Method 1: By checkout_request_id
            $fee = Fee::where('mpesa_checkout_request_id', $checkoutRequestId)->first();

            // Method 2: By phone number
            if (!$fee) {
                $phone = null;
                if (isset($callback['CallbackMetadata']['Item'])) {
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        if ($item['Name'] === 'PhoneNumber') {
                            $phone = $item['Value'];
                            break;
                        }
                    }
                }
                
                if ($phone) {
                    $fee = Fee::where('mpesa_phone', $phone)
                              ->where('status', 'pending')
                              ->latest()
                              ->first();
                    if ($fee) {
                        Log::info("✅ Found fee by phone: {$phone}");
                        $fee->mpesa_checkout_request_id = $checkoutRequestId;
                        $fee->save();
                    }
                }
            }

            // Method 3: By account reference
            if (!$fee && isset($callback['AccountReference'])) {
                $fee = Fee::where('receipt_no', $callback['AccountReference'])
                          ->orWhere('mpesa_checkout_request_id', $callback['AccountReference'])
                          ->first();
                if ($fee) {
                    Log::info("✅ Found fee by account reference: {$callback['AccountReference']}");
                    $fee->mpesa_checkout_request_id = $checkoutRequestId;
                    $fee->save();
                }
            }

            // Method 4: Try to create from callback data
            if (!$fee) {
                Log::error('❌ Fee not found for checkout_id: ' . $checkoutRequestId);
                
                $amount = 0;
                $mpesaReceiptNumber = null;
                $phoneNumber = null;
                
                if (isset($callback['CallbackMetadata']['Item'])) {
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        if ($item['Name'] === 'Amount') {
                            $amount = $item['Value'];
                        }
                        if ($item['Name'] === 'MpesaReceiptNumber') {
                            $mpesaReceiptNumber = $item['Value'];
                        }
                        if ($item['Name'] === 'PhoneNumber') {
                            $phoneNumber = $item['Value'];
                        }
                    }
                }
                
                // Try to find student by account reference
                $accountRef = $callback['AccountReference'] ?? null;
                $studentId = null;
                
                if ($accountRef) {
                    preg_match('/PAY-(\d+)/', $accountRef, $matches);
                    if (isset($matches[1])) {
                        $studentId = $matches[1];
                    }
                }
                
                if ($studentId && $resultCode == 0) {
                    $fee = Fee::create([
                        'student_id' => $studentId,
                        'amount' => $amount,
                        'payment_method' => 'M-Pesa',
                        'payment_date' => now(),
                        'status' => 'paid',
                        'paid_at' => now(),
                        'mpesa_phone' => $phoneNumber,
                        'mpesa_checkout_request_id' => $checkoutRequestId,
                        'mpesa_transaction_code' => $mpesaReceiptNumber,
                        'mpesa_result_code' => $resultCode,
                        'receipt_no' => $mpesaReceiptNumber ?? 'RCP-' . time(),
                        'fee_type' => 'Tuition',
                        'description' => 'M-Pesa Payment - Callback',
                    ]);
                    
                    Log::info('✅ Created fee from callback: ' . $fee->id);
                }
                
                if (!$fee) {
                    Log::error('❌ Could not find or create fee for checkout_id: ' . $checkoutRequestId);
                    return response()->json(['error' => 'Fee not found'], 404);
                }
            }

            Log::info('✅ Fee found! ID: ' . $fee->id);

            if ($resultCode == 0) {
                // Extract receipt number and amount
                $receipt = null;
                $amount = 0;
                if (isset($callback['CallbackMetadata']['Item'])) {
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        if ($item['Name'] === 'MpesaReceiptNumber') {
                            $receipt = $item['Value'];
                        }
                        if ($item['Name'] === 'Amount') {
                            $amount = $item['Value'];
                        }
                    }
                }

                // Update fee
                $fee->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'mpesa_transaction_code' => $receipt,
                    'mpesa_result_code' => $resultCode,
                    'mpesa_response' => json_encode($data),
                    'mpesa_result_desc' => $resultDesc,
                    'receipt_no' => $receipt ?? $fee->receipt_no,
                ]);

                Log::info('✅ Payment successful! Fee ID: ' . $fee->id . ', Receipt: ' . $receipt);
                
                return response()->json([
                    'success' => true,
                    'fee_id' => $fee->id,
                    'receipt' => $receipt,
                    'redirect_url' => route('fees.receipt', $fee->id)
                ]);

            } else {
                // Payment failed
                $fee->update([
                    'status' => 'failed',
                    'mpesa_result_code' => $resultCode,
                    'mpesa_response' => json_encode($data),
                    'mpesa_result_desc' => $resultDesc,
                ]);

                Log::warning('❌ Payment failed: ' . $resultCode);
                return response()->json(['success' => false, 'error' => 'Payment failed'], 400);
            }

        } catch (\Exception $e) {
            Log::error('❌ Callback Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint for M-Pesa payment (for mobile apps)
     */
    public function mpesaPayment(Request $request)
    {
        return $this->initiateMpesaPayment($request);
    }

    /**
     * Get M-Pesa transaction details
     */
    public function getMpesaTransaction($id)
    {
        try {
            $fee = Fee::findOrFail($id);
            
            if ($fee->payment_method !== 'M-Pesa') {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not an M-Pesa transaction'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'checkout_request_id' => $fee->mpesa_checkout_request_id,
                    'transaction_code' => $fee->mpesa_transaction_code,
                    'phone' => $fee->mpesa_phone,
                    'status' => $fee->status,
                    'amount' => $fee->amount,
                    'payment_date' => $fee->payment_date,
                    'receipt_no' => $fee->receipt_no,
                    'response' => $fee->mpesa_response,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Index for fees (JSON response)
     */
    public function apiIndex(Request $request)
    {
        $query = Fee::with('student.course');
        
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $fees = $query->orderBy('payment_date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $fees,
            'total' => $fees->count(),
            'total_amount' => $fees->sum('amount')
        ]);
    }

    /**
     * API Show for a single fee
     */
    public function apiShow($id)
    {
        $fee = Fee::with('student.course')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $fee
        ]);
    }

    /**
     * API Stats for fees
     */
    public function apiStats()
    {
        return $this->stats();
    }

    /**
     * Export fees to CSV/Excel
     */
    public function export(Request $request)
    {
        $fees = Fee::with('student.course')
                   ->orderBy('payment_date', 'desc')
                   ->get();
        
        return $this->exportReport($fees, 'all', 'all');
    }
}