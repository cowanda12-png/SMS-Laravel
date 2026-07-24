<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Students;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Show payment creation form
     */
    public function create()
    {
        $students = Students::with('course')->get();
        $paymentMethods = ['M-Pesa', 'Cash', 'Bank Transfer', 'Cheque', 'Credit Card'];
        $feeTypes = ['Tuition', 'Registration', 'Exam', 'Library', 'Sports', 'Other'];

        return view('payments.create', compact('students', 'paymentMethods', 'feeTypes'));
    }

    /**
     * Store payment record
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|string|in:M-Pesa,Cash,Bank Transfer,Cheque,Credit Card',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,paid,overdue',
            'term' => 'nullable|string',
            'academic_year' => 'nullable|string',
            'fee_type' => 'nullable|string',
            'description' => 'nullable|string',
            'receipt_no' => 'nullable|string',
            'mpesa_transaction_code' => 'nullable|string',
            'mpesa_checkout_request_id' => 'nullable|string',
            'mpesa_result_code' => 'nullable|string',
            'mpesa_phone' => 'nullable|string',
        ]);

        $receiptNo = $request->receipt_no ?? $this->generateReceiptNumber();

        if ($request->mpesa_checkout_request_id) {
            $existingPayment = Payment::where('mpesa_checkout_request_id', $request->mpesa_checkout_request_id)->first();
            if ($existingPayment) {
                return redirect()->route('fees.index')
                    ->with('warning', 'Payment already recorded. Receipt #' . $existingPayment->receipt_no);
            }
        }

        $payment = Payment::create([
            'student_id' => $request->student_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'status' => $request->status,
            'term' => $request->term,
            'academic_year' => $request->academic_year,
            'fee_type' => $request->fee_type,
            'description' => $request->description,
            'receipt_no' => $receiptNo,
            'account_reference' => $request->account_reference ?? 'PAY-' . $receiptNo,
            'mpesa_transaction_code' => $request->mpesa_transaction_code,
            'mpesa_checkout_request_id' => $request->mpesa_checkout_request_id,
            'mpesa_result_code' => $request->mpesa_result_code,
            'mpesa_phone' => $request->mpesa_phone,
            'completed_at' => $request->status === 'paid' ? now() : null,
        ]);

        Log::info('Payment recorded successfully', ['payment_id' => $payment->id, 'receipt_no' => $receiptNo]);

        // If payment was successful via M-Pesa, redirect to receipt
        if ($request->status === 'paid' && $request->payment_method === 'M-Pesa') {
            return redirect()->route('payments.receipt', $payment->id)
                ->with('success', 'Payment successful! Receipt #' . $receiptNo);
        }

        return redirect()->route('fees.index')
            ->with('success', 'Payment recorded successfully! Receipt #' . $receiptNo);
    }

    /**
     * Show payment form for a specific student
     */
    public function showPaymentForm($studentId)
    {
        $student = Students::with('course')->findOrFail($studentId);
        $paymentMethods = ['M-Pesa', 'Cash', 'Bank Transfer', 'Cheque', 'Credit Card'];
        $feeTypes = ['Tuition', 'Registration', 'Exam', 'Library', 'Sports', 'Other'];

        return view('payments.form', compact('student', 'paymentMethods', 'feeTypes'));
    }

    /**
     * Show payment receipt
     */
    public function showReceipt($id)
    {
        $payment = Payment::with('student')->findOrFail($id);
        
        // Get payment data from session if available
        $paymentData = session('payment_data', []);
        
        return view('payments.receipt', compact('payment', 'paymentData'));
    }

    /**
     * Initiate STK Push (Legacy)
     */
    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'phone' => 'required|string',
                'amount' => 'required|numeric|min:1',
                'account_ref' => 'nullable|string|max:20',
            ]);

            $phone = $this->formatPhoneNumber($request->phone);
            $accountRef = $request->account_ref ?? 'STU-' . $request->student_id . '-' . now()->format('YmdHis');

            $result = $this->mpesaService->stkPush($phone, $request->amount, $accountRef);

            if ($result['success']) {
                $payment = Payment::create([
                    'student_id' => $request->student_id,
                    'amount' => $request->amount,
                    'payment_method' => 'M-Pesa',
                    'payment_date' => now(),
                    'status' => 'pending',
                    'mpesa_phone' => $phone,
                    'account_reference' => $accountRef,
                    'mpesa_checkout_request_id' => $result['checkout_request_id'],
                    'receipt_no' => $this->generateReceiptNumber(),
                ]);

                Log::info('STK Push initiated', [
                    'payment_id' => $payment->id,
                    'checkout_request_id' => $result['checkout_request_id']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'STK Push sent successfully. Please check your phone.',
                    'payment_id' => $payment->id,
                    'checkout_request_id' => $result['checkout_request_id'],
                ]);
            }

            Log::error('STK Push failed', ['result' => $result]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Payment initiation failed',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Initiate Payment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initiate M-Pesa STK Push (for the modal) - FIXED to return student data
     */
    public function initiateMpesaPayment(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:1',
                'phone' => 'required|string',
                'reference' => 'nullable|string',
            ]);

            // Get student with full details
            $student = Students::with('course')->find($request->student_id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Get student name
            $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
            
            $reference = $request->reference ?? 'PAY-' . ($student->admission_number ?? $student->id) . '-' . now()->format('YmdHis');
            $phone = $this->formatPhoneNumber($request->phone);

            Log::info('Initiating M-Pesa Payment for student:', [
                'student_id' => $student->id,
                'student_name' => $studentName,
                'phone' => $phone,
                'amount' => $request->amount,
                'reference' => $reference
            ]);

            // Send STK Push
            $result = $this->mpesaService->stkPush($phone, $request->amount, $reference);

            if ($result['success']) {
                // Save payment record with pending status
                $payment = Payment::create([
                    'student_id' => $request->student_id,
                    'amount' => $request->amount,
                    'payment_method' => 'M-Pesa',
                    'status' => 'pending',
                    'mpesa_phone' => $phone,
                    'mpesa_checkout_request_id' => $result['checkout_request_id'],
                    'account_reference' => $reference,
                    'payment_date' => now(),
                    'receipt_no' => $this->generateReceiptNumber(),
                    'fee_type' => 'Tuition',
                    'description' => 'M-Pesa Payment - ' . $studentName,
                ]);

                Log::info('M-Pesa STK Push initiated via modal', [
                    'payment_id' => $payment->id,
                    'checkout_request_id' => $result['checkout_request_id']
                ]);

                // Return student data along with the response
                return response()->json([
                    'success' => true,
                    'message' => 'STK Push sent successfully!',
                    'checkout_request_id' => $result['checkout_request_id'],
                    'payment_id' => $payment->id,
                    'student' => [
                        'id' => $student->id,
                        'name' => $studentName,
                        'admission_number' => $student->admission_number ?? 'N/A',
                        'phone' => $phone,
                        'course' => $student->course->course_name ?? 'N/A',
                    ],
                    'amount' => $request->amount,
                    'reference' => $reference,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to initiate payment',
            ], 400);

        } catch (\Exception $e) {
            Log::error('M-Pesa Initiation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check M-Pesa transaction status - FIXED to check database first
     */
    public function checkMpesaStatus(Request $request)
    {
        try {
            $checkoutRequestId = $request->input('checkout_request_id');

            if (!$checkoutRequestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout request ID is required'
                ], 400);
            }

            // FIRST: Check if payment already exists in database with status 'paid'
            $payment = Payment::where('mpesa_checkout_request_id', $checkoutRequestId)->first();
            
            if ($payment && $payment->status === 'paid') {
                Log::info('Payment already marked as paid in database', [
                    'checkout_request_id' => $checkoutRequestId,
                    'receipt' => $payment->mpesa_transaction_code
                ]);
                
                return response()->json([
                    'success' => true,
                    'resultCode' => '0',
                    'resultDesc' => 'Payment already completed',
                    'amount' => $payment->amount,
                    'mpesa_receipt_number' => $payment->mpesa_transaction_code,
                    'status' => 'completed',
                    'from_database' => true,
                    'payment_id' => $payment->id
                ]);
            }

            // Query M-Pesa API
            $result = $this->mpesaService->queryStatus($checkoutRequestId);

            if ($result['success']) {
                $resultCode = $result['result_code'] ?? null;

                if ($resultCode === '0') {
                    // Payment successful
                    $mpesaReceiptNumber = $result['data']['MpesaReceiptNumber'] ?? null;
                    
                    if ($payment) {
                        $payment->update([
                            'status' => 'paid',
                            'mpesa_result_code' => $resultCode,
                            'mpesa_transaction_code' => $mpesaReceiptNumber,
                            'completed_at' => now(),
                            'receipt_no' => $mpesaReceiptNumber ?? $payment->receipt_no,
                        ]);
                    }

                    // Store payment data in session for receipt
                    $paymentData = [
                        'amount' => $payment->amount ?? $result['amount'] ?? 0,
                        'mpesa_receipt_number' => $mpesaReceiptNumber,
                        'result_desc' => $result['result_desc'] ?? 'Payment successful',
                        'transaction_date' => now()->format('YmdHis'),
                        'phone_number' => $payment->mpesa_phone ?? null,
                    ];
                    
                    session()->put('payment_data', $paymentData);
                    session()->put('payment_success', true);

                    return response()->json([
                        'success' => true,
                        'resultCode' => '0',
                        'resultDesc' => $result['result_desc'] ?? 'Payment successful',
                        'amount' => $payment->amount ?? null,
                        'mpesa_receipt_number' => $mpesaReceiptNumber,
                        'status' => 'completed',
                        'payment_id' => $payment->id ?? null,
                    ]);
                } elseif ($resultCode === '1032') {
                    if ($payment) {
                        $payment->update([
                            'status' => 'failed',
                            'mpesa_result_code' => $resultCode,
                            'mpesa_transaction_code' => null,
                        ]);
                    }

                    return response()->json([
                        'success' => false,
                        'resultCode' => '1032',
                        'resultDesc' => 'Payment was cancelled',
                        'status' => 'failed',
                    ]);
                } elseif ($resultCode === '1037') {
                    if ($payment) {
                        $payment->update([
                            'status' => 'failed',
                            'mpesa_result_code' => $resultCode,
                            'mpesa_transaction_code' => null,
                        ]);
                    }

                    return response()->json([
                        'success' => false,
                        'resultCode' => '1037',
                        'resultDesc' => 'Payment timed out',
                        'status' => 'failed',
                    ]);
                } elseif ($resultCode === '2001') {
                    if ($payment) {
                        $payment->update([
                            'status' => 'failed',
                            'mpesa_result_code' => $resultCode,
                            'mpesa_transaction_code' => null,
                        ]);
                    }

                    return response()->json([
                        'success' => false,
                        'resultCode' => '2001',
                        'resultDesc' => 'Wrong PIN entered',
                        'status' => 'failed',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'resultCode' => $resultCode,
                        'resultDesc' => $result['result_desc'] ?? 'Payment failed or pending',
                        'status' => 'pending',
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status',
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            Log::error('Status Check Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking status',
                'status' => 'pending',
            ], 500);
        }
    }

    /**
     * Check payment status (original polling method)
     */
    public function checkStatus(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|exists:payments,id',
            ]);

            $payment = Payment::with('student')->find($request->payment_id);

            if ($payment->status === 'paid') {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'message' => 'Payment already completed',
                    'payment' => $payment
                ]);
            }

            if (!$payment->mpesa_checkout_request_id) {
                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'message' => 'No checkout request ID found',
                ]);
            }

            $result = $this->mpesaService->queryStatus($payment->mpesa_checkout_request_id);

            if ($result['success']) {
                $resultCode = $result['result_code'] ?? null;
                
                if ($resultCode === '0') {
                    $mpesaReceiptNumber = $result['data']['MpesaReceiptNumber'] ?? null;
                    
                    $payment->update([
                        'status' => 'paid',
                        'mpesa_result_code' => $resultCode,
                        'mpesa_transaction_code' => $mpesaReceiptNumber,
                        'completed_at' => now(),
                        'receipt_no' => $mpesaReceiptNumber ?? $payment->receipt_no,
                    ]);

                    return response()->json([
                        'success' => true,
                        'status' => 'completed',
                        'message' => 'Payment successful!',
                        'payment' => $payment,
                        'mpesa_receipt_number' => $mpesaReceiptNumber,
                    ]);
                } elseif (in_array($resultCode, ['1032', '1037', '2001'])) {
                    $payment->update([
                        'status' => 'failed',
                        'mpesa_result_code' => $resultCode,
                        'mpesa_transaction_code' => null,
                    ]);

                    return response()->json([
                        'success' => false,
                        'status' => 'failed',
                        'message' => $result['result_desc'] ?? 'Payment failed',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Payment still pending...',
            ]);

        } catch (\Exception $e) {
            Log::error('Check Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'pending',
                'message' => 'An error occurred while checking status',
            ], 500);
        }
    }

    /**
     * M-Pesa Callback handler
     */
    public function mpesaCallback(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('M-Pesa Callback Received', $data);

            if (isset($data['Body']['stkCallback'])) {
                $callback = $data['Body']['stkCallback'];
                $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
                $resultCode = $callback['ResultCode'] ?? null;
                $resultDesc = $callback['ResultDesc'] ?? '';

                Log::info('Callback Details:', [
                    'checkoutRequestId' => $checkoutRequestId,
                    'resultCode' => $resultCode,
                    'resultDesc' => $resultDesc
                ]);

                if (!$checkoutRequestId) {
                    Log::error('M-Pesa Callback: Missing CheckoutRequestID');
                    return response()->json(['error' => 'Missing CheckoutRequestID'], 400);
                }

                // Find the payment record by checkout_request_id
                $payment = Payment::where('mpesa_checkout_request_id', $checkoutRequestId)->first();

                if (!$payment) {
                    Log::warning('M-Pesa Callback: Payment not found for checkout_id: ' . $checkoutRequestId);
                    
                    // Try to find by account reference if available
                    if (isset($callback['AccountReference'])) {
                        $payment = Payment::where('account_reference', $callback['AccountReference'])->first();
                        if ($payment) {
                            Log::info('M-Pesa Callback: Found payment by account reference', [
                                'account_reference' => $callback['AccountReference'],
                                'payment_id' => $payment->id
                            ]);
                            // Update the checkout_request_id on the payment
                            $payment->update([
                                'mpesa_checkout_request_id' => $checkoutRequestId
                            ]);
                        }
                    }
                    
                    if (!$payment) {
                        // Create a new payment record from callback
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
                            // Try to extract student ID from account reference
                            preg_match('/PAY-(\d+)/', $accountRef, $matches);
                            if (isset($matches[1])) {
                                $studentId = $matches[1];
                            }
                        }
                        
                        if ($studentId && $resultCode === 0) {
                            $payment = Payment::create([
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
                                'account_reference' => $accountRef,
                                'description' => 'M-Pesa Payment - Callback',
                            ]);
                            
                            Log::info('M-Pesa Callback: Created new payment record', [
                                'payment_id' => $payment->id,
                                'receipt' => $mpesaReceiptNumber
                            ]);
                        }
                        
                        if (!$payment) {
                            Log::error('M-Pesa Callback: Unable to find or create payment');
                            return response()->json(['error' => 'Payment not found'], 404);
                        }
                    }
                }

                if ($resultCode === 0) {
                    // Payment successful - extract details
                    $amount = 0;
                    $mpesaReceiptNumber = null;
                    $transactionDate = null;
                    $phoneNumber = null;
                    
                    if (isset($callback['CallbackMetadata']['Item'])) {
                        foreach ($callback['CallbackMetadata']['Item'] as $item) {
                            if ($item['Name'] === 'Amount') {
                                $amount = $item['Value'];
                            }
                            if ($item['Name'] === 'MpesaReceiptNumber') {
                                $mpesaReceiptNumber = $item['Value'];
                            }
                            if ($item['Name'] === 'TransactionDate') {
                                $transactionDate = $item['Value'];
                            }
                            if ($item['Name'] === 'PhoneNumber') {
                                $phoneNumber = $item['Value'];
                            }
                        }
                    }
                    
                    // Update payment record
                    $payment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'mpesa_transaction_code' => $mpesaReceiptNumber,
                        'mpesa_result_code' => $resultCode,
                        'mpesa_response' => $data,
                        'receipt_no' => $mpesaReceiptNumber ?? $payment->receipt_no,
                        'mpesa_result_desc' => $resultDesc,
                    ]);
                    
                    Log::info('M-Pesa Payment Successful - Payment Updated', [
                        'payment_id' => $payment->id,
                        'receipt' => $mpesaReceiptNumber,
                        'amount' => $amount,
                        'phone' => $phoneNumber
                    ]);
                    
                    return response()->json(['message' => 'Callback processed successfully']);

                } else {
                    // Payment failed
                    $payment->update([
                        'status' => 'failed',
                        'mpesa_result_code' => $resultCode,
                        'mpesa_response' => $data,
                        'mpesa_result_desc' => $resultDesc,
                    ]);
                    
                    Log::warning('M-Pesa Payment Failed', [
                        'payment_id' => $payment->id,
                        'result_code' => $resultCode,
                        'result_desc' => $resultDesc
                    ]);
                    
                    return response()->json(['message' => 'Payment failed'], 400);
                }
            }

            Log::warning('M-Pesa Callback: Invalid format', ['data' => $data]);
            return response()->json(['error' => 'Invalid callback format'], 400);

        } catch (\Exception $e) {
            Log::error('M-Pesa Callback Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to process callback'], 500);
        }
    }

    /**
     * List all payments
     */
    public function index()
    {
        $payments = Payment::with('student')->latest()->paginate(20);
        return view('payments.index', compact('payments'));
    }

    /**
     * Generate receipt number
     */
    private function generateReceiptNumber()
    {
        return 'RCP-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Format phone number for M-Pesa
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Delete a payment (optional)
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('fees.index')
            ->with('success', 'Payment deleted successfully!');
    }
}