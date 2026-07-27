@extends('layouts.app')

@section('title', 'Record New Payment')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">

            <!-- Page Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-plus-circle text-primary me-2"></i> Record New Payment
                    </h4>
                    <p class="text-muted small mb-0">Record a new fee payment for a student</p>
                </div>
                <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-sm mt-2 mt-sm-0">
                    <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back</span>
                </a>
            </div>

            <!-- Display Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> Please fix the following errors:
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- CSRF Token Meta (CRITICAL) -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <!-- Main Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-sm-4">
                    <form action="{{ route('fees.store') }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Student Selection with Search -->
                        <div class="mb-4">
                            <label for="student_search" class="form-label fw-semibold">
                                Search Student <span class="text-danger">*</span>
                            </label>
                            
                            <!-- Search Input -->
                            <div class="position-relative">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           id="student_search" 
                                           class="form-control form-control-lg @error('student_id') is-invalid @enderror"
                                           placeholder="Type student name, admission number, or ID..."
                                           autocomplete="off">
                                    <button type="button" class="btn btn-outline-secondary" id="clearSearchBtn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- Search Results Dropdown -->
                                <div id="searchResults" class="dropdown-menu w-100 p-0 mt-1 shadow-lg" style="max-height: 400px; overflow-y: auto; display: none; position: absolute; z-index: 1000;">
                                    <div class="p-3 text-center text-muted d-none" id="searchLoading">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Searching...
                                    </div>
                                    <div class="p-3 text-center text-muted d-none" id="searchNoResults">
                                        <i class="fas fa-search me-2"></i> No students found
                                    </div>
                                    <div id="searchResultsList"></div>
                                </div>
                            </div>
                            
                            <!-- Selected Student Display with Balance -->
                            <div id="selectedStudentDisplay" class="mt-3 p-3 bg-light rounded-3 border @if(!old('student_id')) d-none @endif">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                             style="width: 48px; height: 48px; font-size: 20px; font-weight: bold;">
                                            <span id="selectedStudentInitial">?</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold" id="selectedStudentName">Student Name</h6>
                                            <small class="text-muted">
                                                <span id="selectedStudentAdmission">Admission: N/A</span>
                                                <span class="mx-1">•</span>
                                                <span id="selectedStudentCourse">Course: N/A</span>
                                            </small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="deselectStudent">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- Balance Information -->
                                <div class="mt-3 pt-3 border-top" id="feeSummarySection">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #e9ecef;">
                                                <span class="text-muted small">Expected:</span>
                                                <span class="fw-bold" id="totalExpectedFees">KES 0.00</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #d4edda;">
                                                <span class="text-muted small">Paid:</span>
                                                <span class="fw-bold text-success" id="totalPaidFees">KES 0.00</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #f8d7da;">
                                                <span class="text-muted small">Balance:</span>
                                                <span class="fw-bold text-danger" id="outstandingBalance">KES 0.00</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #cce5ff;">
                                                <span class="text-muted small">Status:</span>
                                                <span class="fw-bold" id="paymentStatusBadge">
                                                    <span class="badge bg-secondary">No Data</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 6px;">
                                            <div id="paymentProgressBar" class="progress-bar bg-info" role="progressbar" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}">
                            @error('student_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Payment Method <span class="text-danger">*</span></label>
                            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-2">
                                @php
                                    $methods = [
                                        'M-Pesa' => 'fa-mobile-alt',
                                        'Cash' => 'fa-money-bill-wave',
                                        'Bank Transfer' => 'fa-university',
                                        'Cheque' => 'fa-file-invoice',
                                        'Credit Card' => 'fa-credit-card'
                                    ];
                                @endphp
                                
                                @foreach($methods as $method => $icon)
                                    <div class="col">
                                        <input type="radio"
                                               class="btn-check payment-method-radio"
                                               name="payment_method_radio"
                                               id="method_{{ Str::slug($method) }}"
                                               value="{{ $method }}"
                                               autocomplete="off"
                                               {{ old('payment_method') == $method ? 'checked' : '' }}
                                               {{ $method == 'M-Pesa' ? 'data-mpesa="true"' : '' }}>
                                        <label class="btn btn-outline-secondary payment-method-btn w-100 h-100" for="method_{{ Str::slug($method) }}">
                                            <i class="fas {{ $icon }} d-block mb-1"></i>
                                            <span class="small fw-semibold">{{ $method }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" value="{{ old('payment_method') }}">
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <!-- Reopen STK push modal if M-Pesa is chosen but not yet confirmed -->
                            <div id="reopenMpesaPrompt" class="alert alert-success d-none align-items-center justify-content-between mt-3 mb-0">
                                <div>
                                    <i class="fas fa-mobile-alt me-1"></i>
                                    M-Pesa selected — an STK push is needed to confirm this payment.
                                </div>
                                <button type="button" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#mpesaModal">
                                    <i class="fas fa-paper-plane me-1"></i> Send STK Push
                                </button>
                            </div>

                            <!-- Confirmation banner once M-Pesa payment succeeds -->
                            <div id="mpesaConfirmedBanner" class="alert alert-success d-none mt-3 mb-0">
                                <i class="fas fa-check-circle me-1"></i>
                                M-Pesa payment confirmed. Receipt: <strong id="mpesaConfirmedReceipt"></strong>
                            </div>
                        </div>

                        <!-- Amount / Term / Academic Year -->
                        <div class="row g-2 g-sm-3 mb-4">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label for="amount" class="form-label fw-semibold">
                                    Amount (KES) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">KES</span>
                                    <input type="number" step="0.01" min="1"
                                           name="amount" id="amount"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           value="{{ old('amount') }}"
                                           placeholder="15000" required>
                                </div>
                                <div class="d-flex gap-1 mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(5000)">5K</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(10000)">10K</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(15000)">15K</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(20000)">20K</button>
                                    <button type="button" class="btn btn-sm btn-success flex-fill" id="payFullBalanceBtn" style="display:none;">
                                        <i class="fas fa-check-circle me-1"></i> Full Balance
                                    </button>
                                </div>
                                <div id="balanceHint" class="small text-muted mt-1 d-none">
                                    <i class="fas fa-info-circle me-1"></i> Outstanding balance: <span id="hintBalanceAmount">KES 0.00</span>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 col-sm-6 col-md-4">
                                <label for="term" class="form-label fw-semibold">Term</label>
                                <select name="term" id="term" class="form-select">
                                    <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                    <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                    <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                                </select>
                            </div>

                            <div class="col-6 col-sm-12 col-md-4">
                                <label for="academic_year" class="form-label fw-semibold">Academic Year</label>
                                <select name="academic_year" id="academic_year" class="form-select">
                                    <option value="{{ date('Y') }}/{{ date('Y')+1 }}" selected>
                                        {{ date('Y') }}/{{ date('Y')+1 }}
                                    </option>
                                    <option value="{{ date('Y')-1 }}/{{ date('Y') }}">
                                        {{ date('Y')-1 }}/{{ date('Y') }}
                                    </option>
                                    <option value="{{ date('Y')+1 }}/{{ date('Y')+2 }}">
                                        {{ date('Y')+1 }}/{{ date('Y')+2 }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Receipt & Date -->
                        <div class="row g-2 g-sm-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label for="receipt_no" class="form-label fw-semibold">Receipt Number</label>
                                <input type="text" name="receipt_no" id="receipt_no"
                                       class="form-control @error('receipt_no') is-invalid @enderror"
                                       value="{{ old('receipt_no') }}"
                                       placeholder="Auto-generated if left blank">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Leave blank to auto-generate
                                </small>
                                @error('receipt_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="payment_date" class="form-label fw-semibold">
                                    Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="payment_date" id="payment_date"
                                       class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', date('Y-m-d')) }}"
                                       required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fee Type & Status -->
                        <div class="row g-2 g-sm-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label for="fee_type" class="form-label fw-semibold">Fee Type</label>
                                <select name="fee_type" id="fee_type"
                                        class="form-select @error('fee_type') is-invalid @enderror">
                                    <option value="">— Select Type —</option>
                                    @foreach($feeTypes as $type)
                                        <option value="{{ $type }}"
                                            {{ old('fee_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fee_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="status" class="form-label fw-semibold">Payment Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                    <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>
                                        Overdue
                                    </option>
                                    <option value="partial" {{ old('status') == 'partial' ? 'selected' : '' }}>
                                        Partial
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description / Notes</label>
                            <textarea name="description" id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Additional notes about this payment...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden fields for M-Pesa -->
                        <input type="hidden" name="mpesa_transaction_code" id="mpesa_transaction_code" value="{{ old('mpesa_transaction_code') }}">
                        <input type="hidden" name="mpesa_checkout_request_id" id="mpesa_checkout_request_id" value="{{ old('mpesa_checkout_request_id') }}">
                        <input type="hidden" name="mpesa_result_code" id="mpesa_result_code" value="{{ old('mpesa_result_code') }}">
                        <input type="hidden" name="mpesa_phone" id="mpesa_phone" value="{{ old('mpesa_phone') }}">
                        <input type="hidden" name="mpesa_reference" id="mpesa_reference" value="PAY-{{ date('Ymd') }}-{{ rand(1000, 9999) }}">

                        <!-- Form Actions -->
                        <div class="d-flex flex-wrap gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-lg px-4">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="fas fa-save me-2"></i> Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- M-Pesa STK Push Modal -->
<div class="modal fade" id="mpesaModal" tabindex="-1" aria-labelledby="mpesaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="mpesaModalLabel">
                    <span class="mpesa-icon-badge me-2">
                        <i class="fas fa-mobile-alt text-white"></i>
                    </span>
                    M-Pesa STK Push
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    A payment prompt will be sent to the phone number below. Ask the payee to enter their M-Pesa PIN to complete the transaction.
                </p>

                <!-- STUDENT NAME DISPLAY -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Student</label>
                    <div class="form-control-plaintext fw-semibold py-0" id="modal_student_name">— No student selected —</div>
                </div>

                <div class="row g-2 g-sm-3">
                    <div class="col-8">
                        <label for="modal_mpesa_phone" class="form-label fw-semibold">
                            Phone Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-phone text-success"></i>
                            </span>
                            <input type="tel" id="modal_mpesa_phone"
                                   class="form-control"
                                   placeholder="07XXXXXXXX or 2547XXXXXXXX">
                        </div>
                        <div id="modal_phoneValidationResult" class="small mt-1"></div>
                    </div>
                    <div class="col-4">
                        <label for="modal_mpesa_amount" class="form-label fw-semibold">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">KES</span>
                            <input type="text" id="modal_mpesa_amount" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <!-- Transaction Status -->
                <div id="modal_transactionStatus" class="d-none mt-4 p-3 rounded border" style="background: #f8f9fa;">
                    <div class="d-flex align-items-center">
                        <div id="modal_statusIcon" class="status-icon" style="background: #ffc107;">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h6 class="mb-0" id="modal_statusTitle">Processing Payment</h6>
                            <small class="text-muted" id="modal_statusMessage">Waiting for confirmation...</small>
                        </div>
                        <div id="modal_statusSpinner" class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 4px;">
                        <div id="modal_statusProgress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- ⭐ Manual Check Status Button -->
                <div class="mt-3 text-center">
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="manualStatusCheck()" id="manualCheckBtn">
                        <i class="fas fa-sync me-1"></i> Check Status Manually
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-success" id="modal_sendStkPush">
                    <i class="fas fa-paper-plane me-1"></i> Send STK Push
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-method-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 6px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        color: #495057;
        background: #f8f9fa;
        transition: all 0.15s ease;
        cursor: pointer;
        min-height: 60px;
    }
    
    .payment-method-btn i {
        font-size: 1rem;
    }
    
    .payment-method-btn:hover {
        border-color: #6c8cff;
        background: #f0f4ff;
        color: #212529;
    }
    
    .btn-check:checked + .payment-method-btn {
        border-color: #6c8cff;
        background: #eef2ff;
        color: #3b4ee0;
        box-shadow: 0 0 0 1px #6c8cff inset;
    }

    .mpesa-icon-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #28a745;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.3s ease;
    }

    #modal_transactionStatus {
        animation: fadeIn 0.4s ease;
        background: #f8f9fa;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .alert {
        border-radius: 10px;
    }
    
    /* Search Results Styling */
    #searchResults .dropdown-item {
        padding: 10px 16px;
        border-bottom: 1px solid #f1f3f5;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    
    #searchResults .dropdown-item:last-child {
        border-bottom: none;
    }
    
    #searchResults .dropdown-item:hover,
    #searchResults .dropdown-item:focus {
        background: #f0f4ff;
    }
    
    #searchResults .dropdown-item .student-name {
        font-weight: 600;
        color: #212529;
    }
    
    #searchResults .dropdown-item .student-detail {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    #searchResults .dropdown-item .badge {
        font-size: 0.7rem;
    }
    
    #searchResults .dropdown-item.active {
        background: #6c8cff;
        color: white;
    }
    
    #searchResults .dropdown-item.active .student-name,
    #searchResults .dropdown-item.active .student-detail {
        color: white;
    }
    
    /* Selected Student Display */
    #selectedStudentDisplay {
        border-color: #6c8cff !important;
        background: #f8faff !important;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .payment-method-btn {
            min-height: 50px;
            padding: 8px 4px;
        }
        
        .payment-method-btn i {
            font-size: 0.85rem;
        }
        
        .payment-method-btn .small {
            font-size: 0.65rem !important;
        }
        
        #searchResults {
            max-height: 300px !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .card-body {
            padding: 0.75rem !important;
        }
        
        .payment-method-btn {
            min-height: 44px;
            padding: 6px 4px;
        }
        
        .payment-method-btn i {
            font-size: 0.75rem;
        }
        
        .payment-method-btn .small {
            font-size: 0.55rem !important;
        }
        
        .btn-lg {
            font-size: 0.8rem;
            padding: 6px 14px;
        }
        
        #selectedStudentDisplay .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            font-size: 16px !important;
        }
    }
</style>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // ============================================
    // STUDENT SEARCH FUNCTIONALITY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('student_search');
        const searchResults = document.getElementById('searchResults');
        const searchResultsList = document.getElementById('searchResultsList');
        const searchLoading = document.getElementById('searchLoading');
        const searchNoResults = document.getElementById('searchNoResults');
        const studentIdInput = document.getElementById('student_id');
        const selectedDisplay = document.getElementById('selectedStudentDisplay');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const deselectBtn = document.getElementById('deselectStudent');
        const payFullBalanceBtn = document.getElementById('payFullBalanceBtn');
        
        let searchTimeout = null;
        let selectedStudent = null;
        let feeSummaryData = null;
        
        // Check if there's a pre-selected student (from validation error)
        const preSelectedId = studentIdInput.value;
        if (preSelectedId) {
            fetchStudentById(preSelectedId);
        }
        
        // Search input handler
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            if (!query) {
                searchResults.style.display = 'none';
                return;
            }
            
            // Show loading
            searchLoading.classList.remove('d-none');
            searchNoResults.classList.add('d-none');
            searchResultsList.innerHTML = '';
            searchResults.style.display = 'block';
            
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 400);
        });
        
        // Perform search via AJAX
        async function performSearch(query) {
            try {
                const response = await axios.get('{{ route("students.search") }}', {
                    params: { query: query }
                });
                
                searchLoading.classList.add('d-none');
                
                if (!response.data || response.data.length === 0) {
                    searchNoResults.classList.remove('d-none');
                    return;
                }
                
                searchNoResults.classList.add('d-none');
                searchResultsList.innerHTML = '';
                
                response.data.forEach(student => {
                    const item = document.createElement('a');
                    item.className = 'dropdown-item d-flex align-items-center';
                    item.href = '#';
                    item.innerHTML = `
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="student-name">${highlightMatch(student.name, query)}</span>
                                <span class="badge bg-primary">#${student.id}</span>
                            </div>
                            <div class="student-detail">
                                <i class="fas fa-id-card me-1"></i> ${student.admission_number || 'N/A'}
                                ${student.course ? `<span class="mx-1">•</span> <i class="fas fa-graduation-cap me-1"></i> ${student.course}` : ''}
                                ${student.phone ? `<span class="mx-1">•</span> <i class="fas fa-phone me-1"></i> ${student.phone}` : ''}
                            </div>
                        </div>
                    `;
                    
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        selectStudent(student);
                        fetchStudentFeeSummary(student.id);
                        searchResults.style.display = 'none';
                        searchInput.value = student.name;
                    });
                    
                    searchResultsList.appendChild(item);
                });
            } catch (error) {
                console.error('Search Error:', error);
                searchLoading.classList.add('d-none');
                searchNoResults.classList.remove('d-none');
                let errorMsg = 'Error searching students';
                if (error.response && error.response.data && error.response.data.message) {
                    errorMsg = error.response.data.message;
                }
                searchNoResults.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> ' + errorMsg;
            }
        }
        
        // Fetch student by ID
        async function fetchStudentById(studentId) {
            try {
                const response = await axios.get('{{ route("students.get") }}', {
                    params: { id: studentId }
                });
                
                if (response.data && response.data.id) {
                    selectStudent(response.data);
                    fetchStudentFeeSummary(studentId);
                }
            } catch (error) {
                console.error('Error fetching student:', error);
            }
        }
        
        // ============================================
        // FETCH STUDENT FEE SUMMARY - FIXED
        // ============================================
        async function fetchStudentFeeSummary(studentId) {
            const term = document.getElementById('term').value;
            const academicYear = document.getElementById('academic_year').value;
            
            try {
                // Use the query parameter version with URL helper
                const url = '{{ url("fees/calculate-expected") }}' + 
                    '?student_id=' + encodeURIComponent(studentId) + 
                    '&term=' + encodeURIComponent(term) + 
                    '&academic_year=' + encodeURIComponent(academicYear);
                
                const response = await axios.get(url);
                
                if (response.data.success) {
                    feeSummaryData = response.data.data;
                    updateFeeSummary(feeSummaryData);
                }
            } catch (error) {
                console.error('Error fetching fee summary:', error);
            }
        }
        
        // Update fee summary display
        function updateFeeSummary(data) {
            const totalExpected = data.total_expected || 0;
            const totalPaid = data.total_paid || 0;
            const balance = data.balance || 0;
            const allPaid = data.all_paid || false;
            const percentage = data.payment_percentage || 0;

            document.getElementById('totalExpectedFees').textContent = 'KES ' + Number(totalExpected).toFixed(2);
            document.getElementById('totalPaidFees').textContent = 'KES ' + Number(totalPaid).toFixed(2);
            document.getElementById('outstandingBalance').textContent = 'KES ' + Number(balance).toFixed(2);
            
            const statusBadge = document.getElementById('paymentStatusBadge');
            if (allPaid) {
                statusBadge.innerHTML = '<span class="badge bg-success">✓ Fully Paid</span>';
            } else if (totalPaid > 0) {
                statusBadge.innerHTML = '<span class="badge bg-warning text-dark">⚠ Partially Paid</span>';
            } else {
                statusBadge.innerHTML = '<span class="badge bg-danger">✗ Not Paid</span>';
            }
            
            document.getElementById('paymentProgressBar').style.width = Math.min(percentage, 100) + '%';
            document.getElementById('paymentProgressBar').className = 'progress-bar ' + (percentage >= 100 ? 'bg-success' : percentage >= 50 ? 'bg-info' : 'bg-warning');
            
            // Show balance hint and auto-fill amount
            const balanceHint = document.getElementById('balanceHint');
            const hintBalance = document.getElementById('hintBalanceAmount');
            if (balance > 0) {
                balanceHint.classList.remove('d-none');
                hintBalance.textContent = 'KES ' + Number(balance).toFixed(2);
                
                // Show full balance button
                document.getElementById('payFullBalanceBtn').style.display = 'block';
                
                // Auto-fill amount with balance if empty
                const amountInput = document.getElementById('amount');
                if (!amountInput.value || amountInput.value == '0') {
                    amountInput.value = balance;
                }
            } else {
                balanceHint.classList.add('d-none');
                document.getElementById('payFullBalanceBtn').style.display = 'none';
            }
            
            // Update fee type dropdown
            if (data.fee_structures && data.fee_structures.length > 0) {
                const feeTypeSelect = document.getElementById('fee_type');
                const currentVal = feeTypeSelect.value;
                feeTypeSelect.innerHTML = '<option value="">— Select Type —</option>';
                
                data.fee_structures.forEach(fee => {
                    const opt = document.createElement('option');
                    opt.value = fee.fee_type;
                    opt.textContent = fee.fee_type + ' (KES ' + Number(fee.amount).toFixed(2) + ')';
                    if (fee.fee_type === currentVal || (!currentVal && data.fee_structures.length === 1)) {
                        opt.selected = true;
                    }
                    feeTypeSelect.appendChild(opt);
                });
            }
        }
        
        // Highlight matched text
        function highlightMatch(text, query) {
            if (!text) return text;
            const index = text.toLowerCase().indexOf(query.toLowerCase());
            if (index === -1) return text;
            
            return text.substring(0, index) + 
                   '<span class="bg-warning bg-opacity-25 fw-bold">' + 
                   text.substring(index, index + query.length) + 
                   '</span>' + 
                   text.substring(index + query.length);
        }
        
        // Select a student
        function selectStudent(student) {
            selectedStudent = student;
            studentIdInput.value = student.id;
            
            document.getElementById('selectedStudentName').textContent = student.name || 'Unknown Student';
            document.getElementById('selectedStudentAdmission').textContent = 'Admission: ' + (student.admission_number || 'N/A');
            document.getElementById('selectedStudentCourse').textContent = 'Course: ' + (student.course || 'N/A');
            const initial = student.name ? student.name.charAt(0).toUpperCase() : '?';
            document.getElementById('selectedStudentInitial').textContent = initial;
            
            selectedDisplay.classList.remove('d-none');
            searchInput.value = student.name || '';
            
            if (student.phone) {
                const mpesaPhone = document.getElementById('mpesa_phone');
                const modalPhone = document.getElementById('modal_mpesa_phone');
                if (mpesaPhone) mpesaPhone.value = student.phone;
                if (modalPhone) modalPhone.value = student.phone;
            }
            
            const modalStudentName = document.getElementById('modal_student_name');
            if (modalStudentName) {
                modalStudentName.textContent = student.name || '— No student selected —';
            }
            
            searchResults.style.display = 'none';
        }
        
        // Clear search
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchResults.style.display = 'none';
            if (!selectedStudent) {
                studentIdInput.value = '';
            }
        });
        
        // Deselect student
        deselectBtn.addEventListener('click', function() {
            selectedStudent = null;
            feeSummaryData = null;
            studentIdInput.value = '';
            selectedDisplay.classList.add('d-none');
            searchInput.value = '';
            searchInput.focus();
            
            document.getElementById('totalExpectedFees').textContent = 'KES 0.00';
            document.getElementById('totalPaidFees').textContent = 'KES 0.00';
            document.getElementById('outstandingBalance').textContent = 'KES 0.00';
            document.getElementById('paymentStatusBadge').innerHTML = '<span class="badge bg-secondary">No Data</span>';
            document.getElementById('paymentProgressBar').style.width = '0%';
            document.getElementById('balanceHint').classList.add('d-none');
            document.getElementById('payFullBalanceBtn').style.display = 'none';
            
            const modalStudentName = document.getElementById('modal_student_name');
            if (modalStudentName) {
                modalStudentName.textContent = '— No student selected —';
            }
        });
        
        // Pay full balance button
        payFullBalanceBtn.addEventListener('click', function() {
            const balanceText = document.getElementById('outstandingBalance').textContent;
            const balance = parseFloat(balanceText.replace('KES ', '')) || 0;
            if (balance > 0) {
                document.getElementById('amount').value = balance;
                Swal.fire({
                    icon: 'success',
                    title: 'Amount Set',
                    text: 'Amount set to full balance: KES ' + balance.toFixed(2),
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
        
        // Term/Year change - re-fetch fee summary
        document.getElementById('term').addEventListener('change', function() {
            if (studentIdInput.value) {
                fetchStudentFeeSummary(studentIdInput.value);
            }
        });
        
        document.getElementById('academic_year').addEventListener('change', function() {
            if (studentIdInput.value) {
                fetchStudentFeeSummary(studentIdInput.value);
            }
        });
        
        // Close search on outside click
        document.addEventListener('click', function(e) {
            const searchContainer = document.querySelector('.position-relative');
            if (searchContainer && !searchContainer.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
        
        // Keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            const items = searchResultsList.querySelectorAll('.dropdown-item');
            if (items.length === 0) return;
            
            let currentIndex = -1;
            items.forEach((item, index) => {
                if (item.classList.contains('active')) {
                    currentIndex = index;
                    item.classList.remove('active');
                }
            });
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextIndex = Math.min(currentIndex + 1, items.length - 1);
                items[nextIndex].classList.add('active');
                items[nextIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevIndex = Math.max(currentIndex - 1, 0);
                items[prevIndex].classList.add('active');
                items[prevIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const activeItem = searchResultsList.querySelector('.dropdown-item.active');
                if (activeItem) {
                    activeItem.click();
                }
            } else if (e.key === 'Escape') {
                searchResults.style.display = 'none';
            }
        });
    });

    // ============================================
    // GLOBAL VARIABLES
    // ============================================
    let pollingInterval = null;
    let mpesaModalInstance = null;
    let mpesaConfirmed = false;
    let isSubmitting = false;

    // ============================================
    // CLEAN UP POLLING ON PAGE UNLOAD
    // ============================================
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    });

    // ============================================
    // DOCUMENT READY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Configure Axios with CSRF token
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
        }

        // Initialize Bootstrap modal
        const modalElement = document.getElementById('mpesaModal');
        if (modalElement) {
            mpesaModalInstance = new bootstrap.Modal(modalElement);
        }

        const paymentMethodRadios = document.querySelectorAll('.payment-method-radio');
        const paymentMethodHidden = document.getElementById('payment_method');
        const studentSelect = document.getElementById('student_id');
        const reopenPrompt = document.getElementById('reopenMpesaPrompt');
        const confirmedBanner = document.getElementById('mpesaConfirmedBanner');
        const sendStkBtn = document.getElementById('modal_sendStkPush');

        function getCurrentStudentName() {
            const studentId = studentSelect.value;
            if (!studentId) return '— No student selected —';
            const nameElement = document.getElementById('selectedStudentName');
            if (nameElement && nameElement.textContent !== 'Student Name') {
                return nameElement.textContent;
            }
            return '— No student selected —';
        }

        function getStudentPhone() {
            return document.getElementById('modal_mpesa_phone')?.value || '';
        }

        function syncModalFields() {
            const studentNameElement = document.getElementById('modal_student_name');
            if (studentNameElement) {
                studentNameElement.textContent = getCurrentStudentName();
            }
            
            const modalAmount = document.getElementById('modal_mpesa_amount');
            const amountInput = document.getElementById('amount');
            if (modalAmount && amountInput) {
                modalAmount.value = amountInput.value || '0.00';
            }
            
            const phoneInput = document.getElementById('modal_mpesa_phone');
            const studentPhone = getStudentPhone();
            if (phoneInput && studentPhone) {
                phoneInput.value = studentPhone;
            }
            
            const mpesaPhone = document.getElementById('mpesa_phone');
            if (mpesaPhone && studentPhone) {
                mpesaPhone.value = studentPhone;
            }
        }

        if (studentSelect) {
            studentSelect.addEventListener('change', function() {
                const phone = getStudentPhone();
                const mpesaPhone = document.getElementById('mpesa_phone');
                if (mpesaPhone) mpesaPhone.value = phone;
                
                const modalPhone = document.getElementById('modal_mpesa_phone');
                if (modalPhone) modalPhone.value = phone;
                
                const studentNameElement = document.getElementById('modal_student_name');
                if (studentNameElement) {
                    studentNameElement.textContent = getCurrentStudentName();
                }
            });
        }

        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.addEventListener('input', function() {
                const modalAmount = document.getElementById('modal_mpesa_amount');
                if (modalAmount) {
                    modalAmount.value = this.value || '0.00';
                }
            });
        }

        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                paymentMethodHidden.value = this.value;
                const isMpesa = this.getAttribute('data-mpesa') === 'true';

                if (isMpesa) {
                    mpesaConfirmed = false;
                    if (confirmedBanner) confirmedBanner.classList.add('d-none');
                    if (reopenPrompt) reopenPrompt.classList.remove('d-none');
                    
                    syncModalFields();
                    
                    if (mpesaModalInstance) {
                        mpesaModalInstance.show();
                    }
                } else {
                    if (reopenPrompt) reopenPrompt.classList.add('d-none');
                    if (confirmedBanner) confirmedBanner.classList.add('d-none');
                }
            });
        });

        const mpesaRadio = document.querySelector('.payment-method-radio[data-mpesa="true"]');
        if (mpesaRadio && mpesaRadio.checked) {
            if (reopenPrompt) reopenPrompt.classList.remove('d-none');
            setTimeout(syncModalFields, 200);
        }

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                if (pollingInterval && !mpesaConfirmed) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            });
        }

        if (modalElement) {
            modalElement.addEventListener('show.bs.modal', function() {
                syncModalFields();
                const statusDiv = document.getElementById('modal_transactionStatus');
                if (statusDiv) statusDiv.classList.add('d-none');
                const sendButton = document.getElementById('modal_sendStkPush');
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                }
            });
        }

        if (sendStkBtn) {
            sendStkBtn.addEventListener('click', function() {
                initiateMpesaPayment();
            });
        }

        const form = document.getElementById('paymentForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }

                const paymentMethod = document.querySelector('input[name="payment_method_radio"]:checked');
                const status = document.getElementById('status')?.value;

                if (paymentMethod && paymentMethod.value === 'M-Pesa' && status === 'pending') {
                    e.preventDefault();
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pending M-Pesa Payment',
                        text: 'The M-Pesa payment has not been confirmed yet. Do you want to continue anyway?',
                        showCancelButton: true,
                        confirmButtonColor: '#6c8cff',
                        cancelButtonColor: '#dc3545',
                        confirmButtonText: 'Continue Anyway',
                        cancelButtonText: 'Send STK Push'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isSubmitting = true;
                            form.submit();
                        } else {
                            if (mpesaModalInstance) mpesaModalInstance.show();
                        }
                    });
                }
            });
        }

        setTimeout(syncModalFields, 300);
    });

    // ============================================
    // Quick amount buttons
    // ============================================
    function quickAmount(amount) {
        const amountInput = document.getElementById('amount');
        const modalAmount = document.getElementById('modal_mpesa_amount');
        if (amountInput) {
            amountInput.value = amount;
            amountInput.focus();
            amountInput.dispatchEvent(new Event('input'));
        }
        if (modalAmount) modalAmount.value = amount;
    }

    // ============================================
    // Phone number validation
    // ============================================
    function validateMpesaNumber() {
        const phoneInput = document.getElementById('modal_mpesa_phone');
        const result = document.getElementById('modal_phoneValidationResult');
        
        if (!phoneInput) return false;
        
        let phone = phoneInput.value.trim();

        if (!phone) {
            if (result) {
                result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Phone number is required</span>';
            }
            return false;
        }

        phone = phone.replace(/\s+/g, '');

        if (phone.startsWith('0')) {
            phone = '254' + phone.substring(1);
        }
        if (phone.startsWith('+254')) {
            phone = phone.substring(1);
        }

        const valid = /^2547\d{8}$/.test(phone);

        if (valid) {
            phoneInput.value = phone;
            if (result) {
                result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Valid Safaricom number</span>';
            }
            return true;
        }

        if (result) {
            result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Invalid Safaricom number (must be 07XXXXXXXX or 2547XXXXXXXX)</span>';
        }
        return false;
    }

    const phoneInput = document.getElementById('modal_mpesa_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', validateMpesaNumber);
        phoneInput.addEventListener('blur', validateMpesaNumber);
    }

    // ============================================
    // Validate functions
    // ============================================
    function validateAmount(amount) {
        const amountValue = Number(amount);
        if (isNaN(amountValue) || amountValue < 1) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Amount must be at least KES 1.',
                confirmButtonColor: '#6c8cff'
            });
            return false;
        }
        return true;
    }

    function validateStudentSelected() {
        const studentSelect = document.getElementById('student_id');
        if (!studentSelect || !studentSelect.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Student Required',
                text: 'Please select a student first.',
                confirmButtonColor: '#6c8cff'
            });
            return false;
        }
        return true;
    }

    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    // ============================================
    // Initiate M-Pesa STK Push
    // ============================================
    async function initiateMpesaPayment() {
        if (!validateStudentSelected()) {
            return;
        }

        const studentId = document.getElementById('student_id')?.value;
        const amount = document.getElementById('amount')?.value;
        const phone = document.getElementById('modal_mpesa_phone')?.value;
        const reference = document.getElementById('mpesa_reference')?.value;

        if (!validateAmount(amount)) {
            return;
        }

        if (!validateMpesaNumber()) {
            Swal.fire({
                icon: 'warning',
                title: 'Phone Number Required',
                text: 'Please enter a valid Safaricom phone number.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }

        const mpesaPhone = document.getElementById('mpesa_phone');
        if (mpesaPhone) mpesaPhone.value = phone;

        const sendButton = document.getElementById('modal_sendStkPush');
        if (sendButton) {
            sendButton.disabled = true;
            sendButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
        }

        const statusDiv = document.getElementById('modal_transactionStatus');
        if (statusDiv) statusDiv.classList.remove('d-none');
        
        const statusIcon = document.getElementById('modal_statusIcon');
        const statusTitle = document.getElementById('modal_statusTitle');
        const statusMessage = document.getElementById('modal_statusMessage');
        const statusProgress = document.getElementById('modal_statusProgress');

        if (statusIcon) {
            statusIcon.style.background = '#ffc107';
            statusIcon.innerHTML = '<i class="fas fa-clock text-white"></i>';
        }
        if (statusTitle) statusTitle.textContent = 'Processing Payment';
        if (statusMessage) statusMessage.textContent = 'Sending STK Push to the phone...';
        if (statusProgress) statusProgress.style.width = '20%';

        try {
            const response = await axios.post('{{ route("mpesa.stkpush") }}', {
                student_id: studentId,
                amount: amount,
                phone: phone,
                reference: reference,
                _token: getCsrfToken()
            });

            if (response.data.success) {
                if (statusIcon) {
                    statusIcon.style.background = '#28a745';
                    statusIcon.innerHTML = '<i class="fas fa-check-circle text-white"></i>';
                }
                if (statusTitle) statusTitle.textContent = 'STK Push Sent!';
                if (statusMessage) {
                    statusMessage.textContent = 'Ask the payee to check their phone and enter their M-Pesa PIN.';
                }
                if (statusProgress) statusProgress.style.width = '40%';

                const checkoutId = document.getElementById('mpesa_checkout_request_id');
                if (checkoutId) {
                    checkoutId.value = response.data.checkout_request_id;
                }
                
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                }
                
                startPolling(response.data.checkout_request_id, response.data.phone || phone);
            } else {
                mpesaFailed(response.data.message || 'Failed to send payment request.');
            }
        } catch (error) {
            console.error('M-Pesa Error:', error);
            const message = error.response?.data?.message || 'Failed to process payment request.';
            mpesaFailed(message);
        }
    }

    // ============================================
    // Poll for transaction status
    // ============================================
    function startPolling(checkoutRequestId, phone) {
        let attempts = 0;
        const maxAttempts = 30;

        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }

        pollingInterval = setInterval(async () => {
            attempts++;
            
            const statusProgress = document.getElementById('modal_statusProgress');
            if (statusProgress) {
                const progress = Math.min(40 + (attempts * 2), 95);
                statusProgress.style.width = progress + '%';
            }

            try {
                const response = await axios.get('{{ route("mpesa.status") }}', {
                    params: {
                        checkout_request_id: checkoutRequestId,
                        phone: phone
                    }
                });

                if (response.data.success && response.data.resultCode === '0') {
                    clearInterval(pollingInterval);
                    pollingInterval = null;

                    mpesaConfirmed = true;

                    const statusIcon = document.getElementById('modal_statusIcon');
                    const statusTitle = document.getElementById('modal_statusTitle');
                    const statusMessage = document.getElementById('modal_statusMessage');
                    const statusProgress = document.getElementById('modal_statusProgress');
                    const sendButton = document.getElementById('modal_sendStkPush');

                    if (statusIcon) {
                        statusIcon.style.background = '#28a745';
                        statusIcon.innerHTML = '<i class="fas fa-check-circle text-white"></i>';
                    }
                    if (statusTitle) statusTitle.textContent = 'Payment Successful!';
                    if (statusMessage) {
                        statusMessage.textContent = `Payment of KES ${response.data.amount || '0'} received. Receipt: ${response.data.mpesa_receipt_number || 'N/A'}`;
                    }
                    if (statusProgress) statusProgress.style.width = '100%';

                    if (response.data.mpesa_receipt_number) {
                        const transCode = document.getElementById('mpesa_transaction_code');
                        const receiptNo = document.getElementById('receipt_no');
                        const confirmedReceipt = document.getElementById('mpesaConfirmedReceipt');
                        
                        if (transCode) transCode.value = response.data.mpesa_receipt_number;
                        if (receiptNo) receiptNo.value = response.data.mpesa_receipt_number;
                        if (confirmedReceipt) confirmedReceipt.textContent = response.data.mpesa_receipt_number;
                    }
                    
                    const resultCode = document.getElementById('mpesa_result_code');
                    const statusSelect = document.getElementById('status');
                    
                    if (resultCode) resultCode.value = '0';
                    if (statusSelect) statusSelect.value = 'paid';

                    if (sendButton) {
                        sendButton.disabled = false;
                        sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                    }

                    setTimeout(() => {
                        if (mpesaModalInstance) mpesaModalInstance.hide();
                        
                        const reopenPrompt = document.getElementById('reopenMpesaPrompt');
                        const confirmedBanner = document.getElementById('mpesaConfirmedBanner');
                        
                        if (reopenPrompt) reopenPrompt.classList.add('d-none');
                        if (confirmedBanner) confirmedBanner.classList.remove('d-none');
                        
                        const form = document.getElementById('paymentForm');
                        if (!isSubmitting) {
                            isSubmitting = true;
                            form.submit();
                        }
                        
                        let redirectUrl = '{{ route("fees.index") }}';
                        
                        if (response.data.payment_id) {
                            let url = '{{ route("fees.receipt", ":id") }}';
                            redirectUrl = url.replace(':id', response.data.payment_id);
                        } else if (response.data.redirect_url) {
                            redirectUrl = response.data.redirect_url;
                        } else if (response.data.redirect) {
                            redirectUrl = response.data.redirect;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful!',
                            html: `
                                <p><strong>Amount:</strong> KES ${response.data.amount || '0'}</p>
                                <p><strong>Receipt Number:</strong> ${response.data.mpesa_receipt_number || 'N/A'}</p>
                            `,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'View Receipt'
                        }).then(() => {
                            window.location.href = redirectUrl;
                        });
                    }, 1000);

                    return;
                }

                if (response.data.success === false && response.data.resultCode) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    
                    const resultCodes = {
                        '1032': 'The M-Pesa payment request was cancelled by the user.',
                        '1037': 'The M-Pesa payment request timed out.',
                        '2001': 'Wrong PIN entered. Please try again.',
                    };
                    
                    const errorMessage = resultCodes[response.data.resultCode] || response.data.resultDesc || 'Payment failed. Please try again.';
                    mpesaFailed(errorMessage);
                    return;
                }

                if (attempts >= maxAttempts) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    
                    const sendButton = document.getElementById('modal_sendStkPush');
                    if (sendButton) {
                        sendButton.disabled = false;
                        sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                    }
                    
                    const statusIcon = document.getElementById('modal_statusIcon');
                    const statusTitle = document.getElementById('modal_statusTitle');
                    const statusMessage = document.getElementById('modal_statusMessage');
                    const statusProgress = document.getElementById('modal_statusProgress');

                    if (statusIcon) {
                        statusIcon.style.background = '#ffc107';
                        statusIcon.innerHTML = '<i class="fas fa-clock text-white"></i>';
                    }
                    if (statusTitle) statusTitle.textContent = 'Status Unknown';
                    if (statusMessage) {
                        statusMessage.textContent = 'Still waiting for confirmation. Check M-Pesa messages.';
                    }
                    if (statusProgress) statusProgress.style.width = '100%';

                    Swal.fire({
                        icon: 'info',
                        title: 'Payment Status Unknown',
                        text: 'We are still waiting for confirmation. Check your M-Pesa messages for the transaction.',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c8cff',
                        confirmButtonText: 'Check Again',
                        cancelButtonText: 'View Payments'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            startPolling(checkoutRequestId, phone);
                        } else {
                            window.location.href = '{{ route("fees.index") }}';
                        }
                    });
                }
            } catch (error) {
                console.error('Polling Error:', error);
                if (attempts >= maxAttempts) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    const sendButton = document.getElementById('modal_sendStkPush');
                    if (sendButton) {
                        sendButton.disabled = false;
                        sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                    }
                }
            }
        }, 2000);
    }

    // ============================================
    // Handle M-Pesa failure
    // ============================================
    function mpesaFailed(message) {
        const statusIcon = document.getElementById('modal_statusIcon');
        const statusTitle = document.getElementById('modal_statusTitle');
        const statusMessage = document.getElementById('modal_statusMessage');
        const statusProgress = document.getElementById('modal_statusProgress');
        const sendButton = document.getElementById('modal_sendStkPush');

        if (statusIcon) {
            statusIcon.style.background = '#dc3545';
            statusIcon.innerHTML = '<i class="fas fa-times-circle text-white"></i>';
        }
        if (statusTitle) statusTitle.textContent = 'Payment Failed';
        if (statusMessage) statusMessage.textContent = message;
        if (statusProgress) statusProgress.style.width = '100%';
        if (sendButton) {
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
        }

        Swal.fire({
            icon: 'error',
            title: 'STK Push Failed',
            text: message,
            confirmButtonColor: '#dc3545'
        });
    }

    // ============================================
    // Manual Status Check
    // ============================================
    async function manualStatusCheck() {
        const checkoutId = document.getElementById('mpesa_checkout_request_id')?.value;
        const phone = document.getElementById('modal_mpesa_phone')?.value;
        
        if (!checkoutId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Transaction',
                text: 'Please send an STK Push first.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }
        
        Swal.fire({
            icon: 'info',
            title: 'Checking Status...',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        try {
            const response = await axios.get('{{ route("mpesa.status") }}', {
                params: {
                    checkout_request_id: checkoutId,
                    phone: phone,
                    force_check: true
                }
            });
            
            Swal.close();
            
            if (response.data.success && response.data.resultCode === '0') {
                let redirectUrl = '{{ route("fees.index") }}';
                if (response.data.payment_id) {
                    let url = '{{ route("fees.receipt", ":id") }}';
                    redirectUrl = url.replace(':id', response.data.payment_id);
                } else if (response.data.redirect_url) {
                    redirectUrl = response.data.redirect_url;
                }
                
                if (response.data.mpesa_receipt_number) {
                    const transCode = document.getElementById('mpesa_transaction_code');
                    const receiptNo = document.getElementById('receipt_no');
                    if (transCode) transCode.value = response.data.mpesa_receipt_number;
                    if (receiptNo) receiptNo.value = response.data.mpesa_receipt_number;
                }
                document.getElementById('mpesa_result_code').value = '0';
                document.getElementById('status').value = 'paid';
                
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Found!',
                    text: 'Your payment was successful. Redirecting to receipt...',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = redirectUrl;
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Status',
                    text: response.data.message || 'Payment still pending or not found.',
                    confirmButtonColor: '#6c8cff'
                });
            }
        } catch (error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to check status. Please try again.',
                confirmButtonColor: '#dc3545'
            });
        }
    }
</script>
@endpush