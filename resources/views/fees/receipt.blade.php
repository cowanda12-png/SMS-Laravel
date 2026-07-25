<!-- resources/views/fees/receipt.blade.php -->
@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center py-2 py-sm-3">
                    <h4 class="mb-0 fs-5 fs-sm-4">
                        <i class="fas fa-check-circle me-2"></i> Payment Successful!
                    </h4>
                </div>
                <div class="card-body p-3 p-sm-4">
                    <!-- Success Animation -->
                    <div class="text-center mb-3 mb-sm-4">
                        <div class="receipt-icon mx-auto">
                            <i class="fas fa-receipt fa-3x fa-sm-4x text-success"></i>
                        </div>
                        <h5 class="mt-2 mt-sm-3 fw-bold fs-6 fs-sm-5">Payment Receipt</h5>
                        <p class="text-muted small mb-0">Thank you for your payment</p>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Details -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th width="30%" class="bg-light">Student Name</th>
                                    <td width="70%">
                                        <strong>
                                            @php
                                                // Get student name directly from database if accessor fails
                                                $studentName = $fee->student_name ?? 'N/A';
                                                if ($studentName === 'N/A' || $studentName === 'Unknown Student' || $studentName === 'Student Not Found') {
                                                    try {
                                                        $student = \App\Models\Students::find($fee->student_id);
                                                        $studentName = $student->name ?? 'N/A';
                                                    } catch (\Exception $e) {
                                                        $studentName = 'N/A';
                                                    }
                                                }
                                            @endphp
                                            {{ $studentName }}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Admission Number</th>
                                    <td>
                                        @php
                                            $admission = $fee->student_admission ?? 'N/A';
                                            if ($admission === 'N/A') {
                                                try {
                                                    $student = \App\Models\Students::find($fee->student_id);
                                                    $admission = $student->admission_number ?? 'N/A';
                                                } catch (\Exception $e) {
                                                    $admission = 'N/A';
                                                }
                                            }
                                        @endphp
                                        {{ $admission }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Course</th>
                                    <td>
                                        @php
                                            $course = $fee->student_course ?? 'N/A';
                                            if ($course === 'N/A' || $course === 'Not Assigned') {
                                                try {
                                                    $student = \App\Models\Students::with('course')->find($fee->student_id);
                                                    if ($student && $student->course) {
                                                        $course = $student->course->course_name ?? $student->course->name ?? 'Not Assigned';
                                                    }
                                                } catch (\Exception $e) {
                                                    $course = 'Not Assigned';
                                                }
                                            }
                                        @endphp
                                        {{ $course }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Amount Paid</th>
                                    <td>
                                        <strong class="text-success fs-6 fs-sm-5">
                                            KES {{ number_format(session('payment_success.amount', $fee->amount ?? 0), 2) }}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Receipt Number</th>
                                    <td>
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-hashtag me-1"></i>
                                            {{ session('payment_success.receipt_number', $fee->mpesa_transaction_code ?? $fee->receipt_no ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Payment Method</th>
                                    <td>
                                        @if(isset($fee) && method_exists($fee, 'isMpesaPayment') && $fee->isMpesaPayment())
                                            <span class="badge bg-success">
                                                <i class="fas fa-mobile-alt me-1"></i> M-Pesa
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-money-bill-wave me-1"></i> {{ $fee->payment_method ?? 'Cash' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Transaction Date</th>
                                    <td>{{ now()->format('d M Y H:i:s') }}</td>
                                </tr>
                                @if(isset($fee->mpesa_phone) && $fee->mpesa_phone)
                                <tr>
                                    <th class="bg-light">Phone Number</th>
                                    <td>
                                        <i class="fas fa-phone me-1 text-success"></i>
                                        {{ session('payment_success.phone', $fee->mpesa_phone ?? 'N/A') }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th class="bg-light">Transaction Status</th>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Completed
                                        </span>
                                    </td>
                                </tr>
                                @if(isset($fee->description) && $fee->description)
                                <tr>
                                    <th class="bg-light">Description</th>
                                    <td>{{ $fee->description }}</td>
                                </tr>
                                @endif
                                @if(isset($fee->fee_type) && $fee->fee_type)
                                <tr>
                                    <th class="bg-light">Fee Type</th>
                                    <td>{{ $fee->fee_type }}</td>
                                </tr>
                                @endif
                                @if(isset($fee->term) && $fee->term)
                                <tr>
                                    <th class="bg-light">Term</th>
                                    <td>{{ $fee->term }}</td>
                                </tr>
                                @endif
                                @if(isset($fee->academic_year) && $fee->academic_year)
                                <tr>
                                    <th class="bg-light">Academic Year</th>
                                    <td>{{ $fee->academic_year }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- M-Pesa Transaction Details -->
                    @if(isset($fee) && method_exists($fee, 'isMpesaPayment') && $fee->isMpesaPayment() && $fee->mpesa_transaction_code)
                    <div class="card border-success mt-3 mt-sm-4">
                        <div class="card-header bg-success text-white py-2">
                            <i class="fas fa-mobile-alt me-1"></i> M-Pesa Transaction Details
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <strong>Transaction Code:</strong>
                                    <p class="mb-0"><span class="badge bg-success">{{ $fee->mpesa_transaction_code }}</span></p>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <strong>Checkout Request ID:</strong>
                                    <p class="mb-0"><small class="text-muted">{{ $fee->mpesa_checkout_request_id ?? 'N/A' }}</small></p>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-12 col-sm-6">
                                    <strong>Result Code:</strong>
                                    <p class="mb-0">
                                        @if($fee->mpesa_result_code === '0')
                                            <span class="badge bg-success">Success (0)</span>
                                        @elseif($fee->mpesa_result_code === '1032')
                                            <span class="badge bg-warning">Cancelled (1032)</span>
                                        @elseif($fee->mpesa_result_code === '1037')
                                            <span class="badge bg-info">Timeout (1037)</span>
                                        @elseif($fee->mpesa_result_code === '2001')
                                            <span class="badge bg-danger">Wrong PIN (2001)</span>
                                        @elseif($fee->mpesa_result_code)
                                            <span class="badge bg-danger">{{ $fee->mpesa_result_code }}</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <strong>M-Pesa Status:</strong>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $fee->mpesa_status_color ?? 'secondary' }}">
                                            {{ $fee->mpesa_status ?? 'Pending' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                        <button onclick="window.print()" class="btn btn-secondary btn-sm">
                            <i class="fas fa-print me-1"></i> <span class="d-none d-sm-inline">Print Receipt</span>
                        </button>
                        <a href="{{ route('fees.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to Payments</span>
                        </a>
                        <a href="{{ route('fees.show', $fee->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i> <span class="d-none d-sm-inline">View Details</span>
                        </a>
                        <a href="{{ route('fees.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">New Payment</span>
                        </a>
                    </div>

                    <!-- Footer Note -->
                    <div class="text-center mt-3 mt-sm-4">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            This receipt is automatically generated. Please keep it for your records.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .receipt-icon {
        width: 70px;
        height: 70px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .table {
        font-size: 0.9rem;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        padding: 8px 12px;
    }
    
    .table td {
        vertical-align: middle;
        padding: 8px 12px;
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
    }
    
    .card {
        border-radius: 12px !important;
        overflow: hidden;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .btn {
        border-radius: 8px;
        padding: 6px 16px;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .btn-sm {
        padding: 5px 12px;
        font-size: 0.8rem;
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .table {
            font-size: 0.8rem;
        }
        
        .table th, .table td {
            padding: 6px 8px;
        }
        
        .receipt-icon {
            width: 60px;
            height: 60px;
        }
        
        .receipt-icon i {
            font-size: 2rem !important;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 4px 10px;
        }
        
        .btn {
            font-size: 0.75rem;
            padding: 4px 10px;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
    }
    
    @media (max-width: 575.98px) {
        .card-body {
            padding: 0.75rem !important;
        }
        
        .table {
            font-size: 0.7rem;
        }
        
        .table th, .table td {
            padding: 4px 6px;
        }
        
        .receipt-icon {
            width: 50px;
            height: 50px;
        }
        
        .receipt-icon i {
            font-size: 1.5rem !important;
        }
        
        .badge {
            font-size: 0.6rem;
            padding: 3px 8px;
        }
        
        .btn {
            font-size: 0.7rem;
            padding: 3px 8px;
            min-width: 60px;
        }
        
        .btn-sm {
            font-size: 0.65rem;
            padding: 3px 6px;
            min-width: 50px;
        }
        
        .card-header h4 {
            font-size: 1rem !important;
        }
    }
    
    @media print {
        .btn, .card-header .btn, .no-print {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #28a745 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: white !important;
        }
        
        .badge {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,0.02) !important;
        }
        
        .bg-light {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .bg-success {
            background-color: #28a745 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .bg-info {
            background-color: #17a2b8 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .receipt-icon {
            background: rgba(40, 167, 69, 0.1) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .card-body {
            padding: 20px !important;
        }
    }
</style>
@endsection