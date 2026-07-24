@extends('layouts.app')

@section('title', 'Record New Payment')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-plus-circle text-primary me-2"></i> Record New Payment
                    </h4>
                    <p class="text-muted small mb-0">Record a new fee payment for a student</p>
                </div>
                <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
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
                    <ul class="mb-0 mt-1">
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
                <div class="card-body p-4">
                    <form action="{{ route('fees.store') }}" method="POST" id="paymentForm">
                        @csrf

                        <!-- Student Selection -->
                        <div class="mb-4">
                            <label for="student_id" class="form-label fw-semibold">
                                Search Student <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" id="student_id"
                                    class="form-select form-select-lg @error('student_id') is-invalid @enderror"
                                    required>
                                <option value="">— Type student name or admission no... —</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('student_id') == $student->id ? 'selected' : '' }}
                                        data-phone="{{ $student->phone ?? '' }}"
                                        data-name="{{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}"
                                        data-admission="{{ $student->admission_number ?? '' }}">
                                        {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                                        ({{ $student->admission_number ?? 'N/A' }})
                                        @if(isset($student->course->course_name))
                                            - {{ $student->course->course_name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Payment Method <span class="text-danger">*</span></label>
                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-2">
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
                                <button type="button" class="btn btn-sm btn-success ms-3" data-bs-toggle="modal" data-bs-target="#mpesaModal">
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
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
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
                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(5000)">5K</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(10000)">10K</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="quickAmount(15000)">15K</button>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="term" class="form-label fw-semibold">Term</label>
                                <select name="term" id="term" class="form-select">
                                    <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                    <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                    <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                                </select>
                            </div>

                            <div class="col-md-4">
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
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
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

                            <div class="col-md-6">
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
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
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

                            <div class="col-md-6">
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
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
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
    <div class="modal-dialog modal-dialog-centered">
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

                <div class="row g-3">
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
        padding: 14px 8px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        color: #495057;
        background: #f8f9fa;
        transition: all 0.15s ease;
        cursor: pointer;
        min-height: 70px;
    }
    .payment-method-btn i {
        font-size: 1.15rem;
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
</style>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
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
        } else {
            console.warn('CSRF token meta tag not found.');
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

        // ============================================
        // Get current student name from data-name attribute
        // ============================================
        function getCurrentStudentName() {
            if (!studentSelect || !studentSelect.value) {
                return '— No student selected —';
            }
            
            const option = studentSelect.options[studentSelect.selectedIndex];
            if (!option) {
                return '— No student selected —';
            }
            
            const dataName = option.getAttribute('data-name');
            if (dataName && dataName.trim()) {
                return dataName.trim();
            }
            
            let text = option.text.trim();
            text = text.replace(/\s*\([^)]*\)\s*/g, ' ').trim();
            text = text.replace(/\s+/g, ' ').trim();
            
            return text || '— No student selected —';
        }

        // ============================================
        // Get student phone
        // ============================================
        function getStudentPhone() {
            if (!studentSelect || !studentSelect.value) return '';
            const option = studentSelect.options[studentSelect.selectedIndex];
            return option ? option.getAttribute('data-phone') || '' : '';
        }

        // ============================================
        // Sync all modal fields
        // ============================================
        function syncModalFields() {
            console.log('🔄 Syncing modal fields...');
            
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

        // ============================================
        // Event: Student selection changes
        // ============================================
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

        // ============================================
        // Event: Amount changes
        // ============================================
        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.addEventListener('input', function() {
                const modalAmount = document.getElementById('modal_mpesa_amount');
                if (modalAmount) {
                    modalAmount.value = this.value || '0.00';
                }
            });
        }

        // ============================================
        // Event: Payment method changes
        // ============================================
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

        // ============================================
        // Restore state on validation-error reload
        // ============================================
        const mpesaRadio = document.querySelector('.payment-method-radio[data-mpesa="true"]');
        if (mpesaRadio && mpesaRadio.checked) {
            if (reopenPrompt) reopenPrompt.classList.remove('d-none');
            setTimeout(syncModalFields, 200);
        }

        // ============================================
        // Event: Modal hidden (stop polling)
        // ============================================
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                if (pollingInterval && !mpesaConfirmed) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            });
        }

        // ============================================
        // Event: Modal shown (sync fields)
        // ============================================
        if (modalElement) {
            modalElement.addEventListener('show.bs.modal', function() {
                syncModalFields();
                // Reset transaction status
                const statusDiv = document.getElementById('modal_transactionStatus');
                if (statusDiv) statusDiv.classList.add('d-none');
                const sendButton = document.getElementById('modal_sendStkPush');
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                }
            });
        }

        // ============================================
        // Send STK Push button click
        // ============================================
        if (sendStkBtn) {
            sendStkBtn.addEventListener('click', function() {
                initiateMpesaPayment();
            });
        }

        // ============================================
        // Form submission guard with isSubmitting flag
        // ============================================
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

        // ============================================
        // Initial sync
        // ============================================
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
    // Strict phone number validation
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

    // Real-time phone validation
    const phoneInput = document.getElementById('modal_mpesa_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', validateMpesaNumber);
        phoneInput.addEventListener('blur', validateMpesaNumber);
    }

    // ============================================
    // Validate amount
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

    // ============================================
    // Validate student selection
    // ============================================
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

    // ============================================
    // Get CSRF token
    // ============================================
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    // ============================================
    // ⭐ FIXED: Initiate M-Pesa STK Push with phone fallback
    // ============================================
    async function initiateMpesaPayment() {
        // Validate student selection
        if (!validateStudentSelected()) {
            return;
        }

        const studentId = document.getElementById('student_id')?.value;
        const amount = document.getElementById('amount')?.value;
        const phone = document.getElementById('modal_mpesa_phone')?.value;
        const reference = document.getElementById('mpesa_reference')?.value;

        // Validate amount
        if (!validateAmount(amount)) {
            return;
        }

        // Validate phone
        if (!validateMpesaNumber()) {
            Swal.fire({
                icon: 'warning',
                title: 'Phone Number Required',
                text: 'Please enter a valid Safaricom phone number.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }

        // Update hidden phone field
        const mpesaPhone = document.getElementById('mpesa_phone');
        if (mpesaPhone) mpesaPhone.value = phone;

        // Disable button and show loading state
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
            // ⭐ Use the correct route name
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
                
                // Reset button state
                if (sendButton) {
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send STK Push';
                }
                
                // ⭐ Start polling with phone for fallback
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
    // ⭐ FIXED: Poll for transaction status with phone fallback
    // ============================================
    function startPolling(checkoutRequestId, phone) {
        let attempts = 0;
        const maxAttempts = 30; // 30 attempts * 2 seconds = 60 seconds

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
                // ⭐ Include phone in the request for fallback
                const response = await axios.get('{{ route("mpesa.status") }}', {
                    params: {
                        checkout_request_id: checkoutRequestId,
                        phone: phone // ⭐ Pass phone for fallback
                    }
                });

                console.log('📊 Status check attempt ' + attempts + ':', response.data);

                // Check for successful payment
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

                    // Safe receipt number assignment
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

                    // Redirect to receipt after successful payment
                    setTimeout(() => {
                        if (mpesaModalInstance) mpesaModalInstance.hide();
                        
                        const reopenPrompt = document.getElementById('reopenMpesaPrompt');
                        const confirmedBanner = document.getElementById('mpesaConfirmedBanner');
                        
                        if (reopenPrompt) reopenPrompt.classList.add('d-none');
                        if (confirmedBanner) confirmedBanner.classList.remove('d-none');
                        
                        // Submit form
                        const form = document.getElementById('paymentForm');
                        if (!isSubmitting) {
                            isSubmitting = true;
                            form.submit();
                        }
                        
                        // Show success message and redirect
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
                            console.log('🔀 Redirecting to:', redirectUrl);
                            window.location.href = redirectUrl;
                        });
                    }, 1000);

                    return;
                }

                // Check for failure
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

                // Timeout after max attempts - Final check
                if (attempts >= maxAttempts) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                    
                    // ⭐ Final check: Try to find the payment by phone number
                    try {
                        Swal.fire({
                            icon: 'info',
                            title: 'Checking Again...',
                            text: 'One final check for your payment...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        const finalCheck = await axios.get('{{ route("mpesa.status") }}', {
                            params: {
                                checkout_request_id: checkoutRequestId,
                                phone: phone,
                                force_check: true
                            }
                        });
                        
                        Swal.close();
                        
                        if (finalCheck.data.success && finalCheck.data.resultCode === '0') {
                            // Found it! Process as successful
                            console.log('✅ Found payment on final check!');
                            mpesaConfirmed = true;
                            
                            // Update UI
                            if (finalCheck.data.mpesa_receipt_number) {
                                const transCode = document.getElementById('mpesa_transaction_code');
                                const receiptNo = document.getElementById('receipt_no');
                                const confirmedReceipt = document.getElementById('mpesaConfirmedReceipt');
                                
                                if (transCode) transCode.value = finalCheck.data.mpesa_receipt_number;
                                if (receiptNo) receiptNo.value = finalCheck.data.mpesa_receipt_number;
                                if (confirmedReceipt) confirmedReceipt.textContent = finalCheck.data.mpesa_receipt_number;
                            }
                            
                            if (mpesaModalInstance) mpesaModalInstance.hide();
                            
                            let redirectUrl = '{{ route("fees.index") }}';
                            if (finalCheck.data.payment_id) {
                                let url = '{{ route("fees.receipt", ":id") }}';
                                redirectUrl = url.replace(':id', finalCheck.data.payment_id);
                            } else if (finalCheck.data.redirect_url) {
                                redirectUrl = finalCheck.data.redirect_url;
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Found!',
                                text: 'Your payment was successful. Redirecting to receipt...',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = redirectUrl;
                            });
                            return;
                        }
                    } catch (e) {
                        Swal.close();
                        console.error('Final check failed:', e);
                    }
                    
                    // Show "Status Unknown" with options
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
                            // Retry checking
                            startPolling(checkoutRequestId, phone);
                        } else {
                            // Go to fees index
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
    // ⭐ Manual Status Check Function
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
                // Payment successful - redirect to receipt
                let redirectUrl = '{{ route("fees.index") }}';
                if (response.data.payment_id) {
                    let url = '{{ route("fees.receipt", ":id") }}';
                    redirectUrl = url.replace(':id', response.data.payment_id);
                } else if (response.data.redirect_url) {
                    redirectUrl = response.data.redirect_url;
                }
                
                // Update form fields
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