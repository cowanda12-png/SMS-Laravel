<!-- resources/views/fees/receipt.blade.php -->
@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i> Payment Successful!
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Success Animation -->
                    <div class="text-center mb-4">
                        <div class="receipt-icon mx-auto">
                            <i class="fas fa-receipt fa-4x text-success"></i>
                        </div>
                        <h5 class="mt-3 fw-bold">Payment Receipt</h5>
                        <p class="text-muted">Thank you for your payment</p>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Details -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="35%" class="bg-light">Student Name</th>
                                    <td width="65%">
                                        <strong>{{ $fee->student_name ?? session('payment_success.student_name', 'N/A') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Admission Number</th>
                                    <td>{{ $fee->student_admission ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Course</th>
                                    <td>{{ $fee->student_course ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Amount Paid</th>
                                    <td>
                                        <strong class="text-success fs-5">
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
                                        <span class="badge bg-info">
                                            <i class="fas fa-mobile-alt me-1"></i> M-Pesa
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Transaction Date</th>
                                    <td>{{ now()->format('d M Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Phone Number</th>
                                    <td>
                                        <i class="fas fa-phone me-1 text-success"></i>
                                        {{ session('payment_success.phone', $fee->mpesa_phone ?? 'N/A') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Transaction Status</th>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Completed
                                        </span>
                                    </td>
                                </tr>
                                @if($fee->description ?? null)
                                <tr>
                                    <th class="bg-light">Description</th>
                                    <td>{{ $fee->description }}</td>
                                </tr>
                                @endif
                                @if($fee->fee_type ?? null)
                                <tr>
                                    <th class="bg-light">Fee Type</th>
                                    <td>{{ $fee->fee_type }}</td>
                                </tr>
                                @endif
                                @if($fee->term ?? null)
                                <tr>
                                    <th class="bg-light">Term</th>
                                    <td>{{ $fee->term }}</td>
                                </tr>
                                @endif
                                @if($fee->academic_year ?? null)
                                <tr>
                                    <th class="bg-light">Academic Year</th>
                                    <td>{{ $fee->academic_year }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- M-Pesa Transaction Details -->
                    @if($fee->isMpesaPayment() && $fee->mpesa_transaction_code)
                    <div class="card border-info mt-3">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-mobile-alt me-1"></i> M-Pesa Transaction Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Transaction Code:</strong>
                                    <p><span class="badge bg-success">{{ $fee->mpesa_transaction_code }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Checkout Request ID:</strong>
                                    <p><small class="text-muted">{{ $fee->mpesa_checkout_request_id ?? 'N/A' }}</small></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Result Code:</strong>
                                    <p>
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
                                <div class="col-md-6">
                                    <strong>M-Pesa Status:</strong>
                                    <p>
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
                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="fas fa-print me-1"></i> Print Receipt
                        </button>
                        <a href="{{ route('fees.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Payments
                        </a>
                        <a href="{{ route('fees.show', $fee->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                        <a href="{{ route('fees.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> New Payment
                        </a>
                    </div>

                    <!-- Footer Note -->
                    <div class="text-center mt-4">
                        <p class="text-muted small">
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
        width: 80px;
        height: 80px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .table {
        font-size: 0.95rem;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
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
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    @media print {
        .btn, .card-header .btn {
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
    }
    
    @media (max-width: 768px) {
        .table {
            font-size: 0.85rem;
        }
        .table th, .table td {
            padding: 8px 10px;
        }
        .btn {
            padding: 6px 14px;
            font-size: 0.85rem;
        }
        .card-body {
            padding: 15px;
        }
    }
</style>
@endsection