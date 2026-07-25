@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Payment Details</h5>
                    <div>
                        <a href="{{ route('fees.edit', $fee) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
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

                    <div class="row">
                        <!-- Student Information -->
                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-user-graduate me-1"></i> Student Information
                                    </h6>
                                    <hr>
                                    <p><strong>Name:</strong> 
                                        <span class="fw-bold">{{ $fee->student_name ?? 'N/A' }}</span>
                                    </p>
                                    <p><strong>Admission No.:</strong> 
                                        {{ $fee->student_admission ?? 'N/A' }}
                                    </p>
                                    <p><strong>Course:</strong> 
                                        {{ $fee->student_course ?? 'Not Assigned' }}
                                    </p>
                                    <p><strong>Email:</strong> 
                                        {{ $fee->student->email ?? 'N/A' }}
                                    </p>
                                    <p><strong>Phone:</strong> 
                                        {{ $fee->student->phone ?? 'N/A' }}
                                    </p>
                                    <p><strong>Student ID:</strong> 
                                        <span class="badge bg-secondary">{{ $fee->student_id }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-credit-card me-1"></i> Payment Information
                                    </h6>
                                    <hr>
                                    <p><strong>Amount:</strong> 
                                        <span class="badge bg-light text-dark fs-5">
                                            KES {{ number_format($fee->amount, 2) }}
                                        </span>
                                    </p>
                                    <p><strong>Payment Method:</strong> 
                                        @if($fee->isMpesaPayment())
                                            <span class="badge bg-info">
                                                <i class="fas fa-mobile-alt me-1"></i> {{ $fee->payment_method }}
                                            </span>
                                        @else
                                            <span class="badge bg-info">{{ $fee->payment_method }}</span>
                                        @endif
                                    </p>
                                    <p><strong>Fee Type:</strong> 
                                        <span class="badge bg-warning text-dark">{{ $fee->fee_type ?? 'General' }}</span>
                                    </p>
                                    <p><strong>Payment Date:</strong> 
                                        {{ $fee->payment_date ? $fee->payment_date->format('d M Y') : 'N/A' }}
                                    </p>
                                    <p><strong>Due Date:</strong> 
                                        @if($fee->due_date)
                                            <span class="badge bg-light text-dark">
                                                {{ $fee->due_date->format('d M Y') }}
                                            </span>
                                            @if($fee->is_overdue)
                                                <span class="badge bg-danger ms-1">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $fee->status_badge['color'] ?? 'secondary' }}">
                                            <i class="fas {{ $fee->status_badge['icon'] ?? 'fa-circle' }} me-1"></i>
                                            {{ ucfirst($fee->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Receipt Number:</strong> 
                                        <span class="badge bg-dark">{{ $fee->receipt_no ?? 'N/A' }}</span>
                                    </p>
                                    <p><strong>Term:</strong> 
                                        <span class="badge bg-secondary">{{ $fee->term ?? 'N/A' }}</span>
                                    </p>
                                    <p><strong>Academic Year:</strong> 
                                        <span class="badge bg-secondary">{{ $fee->academic_year ?? 'N/A' }}</span>
                                    </p>
                                    @if($fee->description)
                                        <p><strong>Description:</strong><br>
                                            <span class="text-light">{{ $fee->description }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- M-Pesa Details (if applicable) -->
                    @if($fee->isMpesaPayment())
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-mobile-alt me-1"></i> M-Pesa Transaction Details
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>M-Pesa Phone:</strong>
                                                <p>{{ $fee->mpesa_phone ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Transaction Code:</strong>
                                                <p><span class="badge bg-success">{{ $fee->mpesa_transaction_code ?? 'Pending' }}</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Checkout Request ID:</strong>
                                                <p><small class="text-muted">{{ $fee->mpesa_checkout_request_id ?? 'N/A' }}</small></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
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
                                            <div class="col-md-4">
                                                <strong>M-Pesa Status:</strong>
                                                <p>
                                                    <span class="badge bg-{{ $fee->mpesa_status_color ?? 'secondary' }}">
                                                        {{ $fee->mpesa_status ?? 'Pending' }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Paid At:</strong>
                                                <p>{{ $fee->paid_at ? $fee->paid_at->format('d M Y H:i') : 'N/A' }}</p>
                                            </div>
                                        </div>
                                        @if($fee->mpesa_response)
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#mpesaResponse">
                                                    <i class="fas fa-code me-1"></i> View M-Pesa Response
                                                </button>
                                                <div class="collapse mt-2" id="mpesaResponse">
                                                    <div class="card card-body bg-dark text-white" style="max-height: 200px; overflow-y: auto;">
                                                        <pre class="mb-0"><code>{{ json_encode(json_decode($fee->mpesa_response), JSON_PRETTY_PRINT) }}</code></pre>
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
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <i class="fas fa-clock me-1"></i> Payment Timeline
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Created At:</strong>
                                            <p>{{ $fee->created_at ? $fee->created_at->format('d M Y H:i:s') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Updated At:</strong>
                                            <p>{{ $fee->updated_at ? $fee->updated_at->format('d M Y H:i:s') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Paid At:</strong>
                                            <p>{{ $fee->paid_at ? $fee->paid_at->format('d M Y H:i:s') : 'Not Paid Yet' }}</p>
                                        </div>
                                    </div>
                                    @if($fee->isOverdue())
                                        <div class="alert alert-danger mt-2">
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
                    <div class="mt-4 text-center">
                        @if($fee->status === 'pending')
                            <form action="{{ route('fees.mark-paid', $fee) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i> Mark as Paid
                                </button>
                            </form>
                        @endif

                        @if($fee->status === 'pending' && $fee->isMpesaPayment() && !$fee->mpesa_transaction_code)
                            <button type="button" class="btn btn-info" onclick="resendMpesaPayment({{ $fee->id }})">
                                <i class="fas fa-redo me-1"></i> Resend STK Push
                            </button>
                        @endif

                        <form action="{{ route('fees.destroy', $fee) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this payment? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i> Delete Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resendMpesaPayment(feeId) {
        if (!confirm('Are you sure you want to resend the STK Push to the student\'s phone?')) {
            return;
        }

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
</script>
@endpush

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-body p {
        margin-bottom: 0.5rem;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .bg-light .card-title {
        color: #0d6efd !important;
    }
</style>
@endpush
@endsection