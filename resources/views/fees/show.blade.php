@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-lg">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center py-2 py-sm-3">
                    <h5 class="mb-0 fs-6 fs-sm-5">
                        <i class="fas fa-receipt me-2"></i> Payment Details
                    </h5>
                    <div class="d-flex gap-2 mt-2 mt-sm-0">
                        <a href="{{ route('fees.edit', $fee) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> <span class="d-none d-sm-inline">Edit</span>
                        </a>
                        <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back</span>
                        </a>
                    </div>
                </div>

                <div class="card-body p-3 p-sm-4">
                    <!-- Display Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        <!-- Student Information -->
                        <div class="col-12 col-lg-6">
                            <div class="card bg-light h-100 border-0">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-primary fw-bold">
                                        <i class="fas fa-user-graduate me-1"></i> Student Information
                                    </h6>
                                    <hr>
                                    <div class="row g-1">
                                        <div class="col-4 text-muted small">Name:</div>
                                        <div class="col-8 fw-semibold">{{ $fee->student_name ?? 'N/A' }}</div>
                                        
                                        <div class="col-4 text-muted small">Admission:</div>
                                        <div class="col-8">{{ $fee->student_admission ?? 'N/A' }}</div>
                                        
                                        <div class="col-4 text-muted small">Course:</div>
                                        <div class="col-8">{{ $fee->student_course ?? 'Not Assigned' }}</div>
                                        
                                        <div class="col-4 text-muted small">Email:</div>
                                        <div class="col-8" style="word-break: break-all;">
                                            @if(isset($fee->student) && $fee->student)
                                                {{ $fee->student->email ?? 'N/A' }}
                                            @else
                                                @php
                                                    $student = \App\Models\Students::find($fee->student_id);
                                                @endphp
                                                {{ $student->email ?? 'N/A' }}
                                            @endif
                                        </div>
                                        
                                        <div class="col-4 text-muted small">Phone:</div>
                                        <div class="col-8">
                                            @if(isset($fee->student) && $fee->student)
                                                {{ $fee->student->phone ?? 'N/A' }}
                                            @else
                                                @php
                                                    $student = \App\Models\Students::find($fee->student_id);
                                                @endphp
                                                {{ $student->phone ?? 'N/A' }}
                                            @endif
                                        </div>
                                        
                                        <div class="col-4 text-muted small">Student ID:</div>
                                        <div class="col-8"><span class="badge bg-secondary">{{ $fee->student_id }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="col-12 col-lg-6">
                            <div class="card bg-success text-white h-100 border-0">
                                <div class="card-body p-3">
                                    <h6 class="card-title fw-bold">
                                        <i class="fas fa-credit-card me-1"></i> Payment Information
                                    </h6>
                                    <hr>
                                    <div class="row g-1">
                                        <div class="col-5 col-sm-4 text-white-50 small">Amount:</div>
                                        <div class="col-7 col-sm-8">
                                            <span class="badge bg-light text-dark fs-6">
                                                KES {{ number_format($fee->amount, 2) }}
                                            </span>
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Method:</div>
                                        <div class="col-7 col-sm-8">
                                            @if(isset($fee) && method_exists($fee, 'isMpesaPayment') && $fee->isMpesaPayment())
                                                <span class="badge bg-info">
                                                    <i class="fas fa-mobile-alt me-1"></i> {{ $fee->payment_method }}
                                                </span>
                                            @else
                                                <span class="badge bg-info">{{ $fee->payment_method ?? 'N/A' }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Fee Type:</div>
                                        <div class="col-7 col-sm-8">
                                            <span class="badge bg-warning text-dark">{{ $fee->fee_type ?? 'General' }}</span>
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Payment Date:</div>
                                        <div class="col-7 col-sm-8">{{ $fee->payment_date ? $fee->payment_date->format('d M Y') : 'N/A' }}</div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Due Date:</div>
                                        <div class="col-7 col-sm-8">
                                            @if($fee->due_date)
                                                <span class="badge bg-light text-dark">
                                                    {{ $fee->due_date->format('d M Y') }}
                                                </span>
                                                @if(isset($fee) && method_exists($fee, 'isOverdue') && $fee->isOverdue())
                                                    <span class="badge bg-danger ms-1">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Status:</div>
                                        <div class="col-7 col-sm-8">
                                            <span class="badge bg-{{ $fee->status_badge['color'] ?? 'secondary' }}">
                                                <i class="fas {{ $fee->status_badge['icon'] ?? 'fa-circle' }} me-1"></i>
                                                {{ ucfirst($fee->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Receipt:</div>
                                        <div class="col-7 col-sm-8">
                                            <span class="badge bg-dark">{{ $fee->receipt_no ?? 'N/A' }}</span>
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Term:</div>
                                        <div class="col-7 col-sm-8">
                                            <span class="badge bg-secondary">{{ $fee->term ?? 'N/A' }}</span>
                                        </div>
                                        
                                        <div class="col-5 col-sm-4 text-white-50 small">Academic Year:</div>
                                        <div class="col-7 col-sm-8">
                                            <span class="badge bg-secondary">{{ $fee->academic_year ?? 'N/A' }}</span>
                                        </div>
                                        
                                        @if($fee->description)
                                            <div class="col-5 col-sm-4 text-white-50 small">Description:</div>
                                            <div class="col-7 col-sm-8 text-white">{{ $fee->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- M-Pesa Details (if applicable) -->
                    @if(isset($fee) && method_exists($fee, 'isMpesaPayment') && $fee->isMpesaPayment())
                        <div class="row mt-3 mt-sm-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white py-2">
                                        <i class="fas fa-mobile-alt me-1"></i> M-Pesa Transaction Details
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-2">
                                            <div class="col-12 col-sm-6 col-md-4">
                                                <strong>M-Pesa Phone:</strong>
                                                <p class="mb-0">{{ $fee->mpesa_phone ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4">
                                                <strong>Transaction Code:</strong>
                                                <p class="mb-0"><span class="badge bg-success">{{ $fee->mpesa_transaction_code ?? 'Pending' }}</span></p>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-4">
                                                <strong>Checkout Request ID:</strong>
                                                <p class="mb-0"><small class="text-muted">{{ $fee->mpesa_checkout_request_id ?? 'N/A' }}</small></p>
                                            </div>
                                        </div>
                                        <div class="row g-2 mt-2">
                                            <div class="col-12 col-sm-6 col-md-4">
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
                                            <div class="col-12 col-sm-6 col-md-4">
                                                <strong>M-Pesa Status:</strong>
                                                <p class="mb-0">
                                                    <span class="badge bg-{{ $fee->mpesa_status_color ?? 'secondary' }}">
                                                        {{ $fee->mpesa_status ?? 'Pending' }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-4">
                                                <strong>Paid At:</strong>
                                                <p class="mb-0">{{ $fee->paid_at ? $fee->paid_at->format('d M Y H:i') : 'N/A' }}</p>
                                            </div>
                                        </div>
                                        @if($fee->mpesa_response)
                                            <div class="mt-3">
                                                <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#mpesaResponse">
                                                    <i class="fas fa-code me-1"></i> View M-Pesa Response
                                                </button>
                                                <div class="collapse mt-2" id="mpesaResponse">
                                                    <div class="card card-body bg-dark text-white" style="max-height: 200px; overflow-y: auto;">
                                                        <pre class="mb-0" style="font-size: 0.7rem;"><code>{{ json_encode(json_decode($fee->mpesa_response), JSON_PRETTY_PRINT) }}</code></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Timeline -->
                    <div class="row mt-3 mt-sm-4">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white py-2">
                                    <i class="fas fa-clock me-1"></i> Payment Timeline
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-4">
                                            <strong>Created At:</strong>
                                            <p class="mb-0">{{ $fee->created_at ? $fee->created_at->format('d M Y H:i:s') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <strong>Updated At:</strong>
                                            <p class="mb-0">{{ $fee->updated_at ? $fee->updated_at->format('d M Y H:i:s') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <strong>Paid At:</strong>
                                            <p class="mb-0">{{ $fee->paid_at ? $fee->paid_at->format('d M Y H:i:s') : 'Not Paid Yet' }}</p>
                                        </div>
                                    </div>
                                    @if(isset($fee) && method_exists($fee, 'isOverdue') && $fee->isOverdue())
                                        <div class="alert alert-danger mt-2 mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>Overdue!</strong> This payment is overdue by 
                                            {{ abs($fee->days_until_due) }} days.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-4 pt-3 border-top">
                        @if($fee->status === 'pending')
                            <form action="{{ route('fees.mark-paid', $fee) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check-circle me-1"></i> Mark as Paid
                                </button>
                            </form>
                        @endif

                        @if($fee->status === 'pending' && isset($fee) && method_exists($fee, 'isMpesaPayment') && $fee->isMpesaPayment() && !$fee->mpesa_transaction_code)
                            <button type="button" class="btn btn-info btn-sm" onclick="resendMpesaPayment({{ $fee->id }})">
                                <i class="fas fa-redo me-1"></i> Resend STK Push
                            </button>
                        @endif

                        @if($fee->status === 'paid')
                            <a href="{{ route('fees.receipt', $fee->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-receipt me-1"></i> View Receipt
                            </a>
                        @endif

                        <form action="{{ route('fees.destroy', $fee) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Are you sure you want to delete this payment? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function resendMpesaPayment(feeId) {
        Swal.fire({
            title: 'Resend STK Push?',
            text: 'Are you sure you want to resend the STK Push to the student\'s phone?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Resend',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Resending STK Push...',
                    text: 'Please wait while we resend the payment request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route("mpesa.resend") }}', {
                    fee_id: feeId,
                    _token: '{{ csrf_token() }}'
                })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'STK Push Resent!',
                            text: 'The payment request has been sent to the student\'s phone.',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Resend',
                            text: response.data.message || 'An error occurred. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    .card {
        border-radius: 12px !important;
        overflow: hidden;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .card-body p {
        margin-bottom: 0.25rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }
    
    .badge.fs-6 {
        font-size: 0.9rem !important;
        padding: 6px 14px;
    }
    
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 0.7rem;
    }
    
    .bg-light .card-title {
        color: #0d6efd !important;
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }
        
        .badge.fs-6 {
            font-size: 0.8rem !important;
            padding: 4px 10px;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
        
        .row.g-1 {
            --bs-gutter-x: 0.25rem;
        }
        
        .row.g-1 .col-4,
        .row.g-1 .col-5,
        .row.g-1 .col-7,
        .row.g-1 .col-8 {
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .card-body {
            padding: 0.75rem !important;
        }
        
        .row.g-1 .col-4,
        .row.g-1 .col-5,
        .row.g-1 .col-7,
        .row.g-1 .col-8 {
            font-size: 0.75rem;
        }
        
        .badge {
            font-size: 0.55rem;
            padding: 2px 6px;
        }
        
        .badge.fs-6 {
            font-size: 0.7rem !important;
            padding: 3px 8px;
        }
        
        .btn-sm {
            font-size: 0.65rem;
            padding: 3px 6px;
        }
        
        .card-header h5 {
            font-size: 0.9rem !important;
        }
        
        pre {
            font-size: 0.55rem;
        }
    }
</style>
@endpush
@endsection