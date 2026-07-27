{{-- resources/views/fees/create.blade.php (Updated with auto-calculate) --}}

@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-plus-circle text-primary me-2"></i> Record Fee Payment
            </h4>
            <p class="text-muted small mb-0">System will automatically show expected fees based on class and grade</p>
        </div>
        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('fees.store') }}" method="POST" id="feeForm">
                @csrf

                <!-- Student Selection and Auto-calculate -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Student *</label>
                        <select name="student_id" id="studentSelect" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Select student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" 
                                    data-class="{{ $student->class_id }}" 
                                    data-grade="{{ $student->grade_id }}"
                                    {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->admission_number ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold">Term *</label>
                        <select name="term" id="termSelect" class="form-select @error('term') is-invalid @enderror" required>
                            <option value="">Select term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term }}" {{ old('term') == $term ? 'selected' : '' }}>
                                    {{ $term }}
                                </option>
                            @endforeach
                        </select>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-semibold">Academic Year *</label>
                        <select name="academic_year" id="yearSelect" class="form-select @error('academic_year') is-invalid @enderror" required>
                            <option value="">Select year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="button" id="calculateBtn" class="btn btn-info btn-sm">
                            <i class="fas fa-calculator me-1"></i> Calculate Expected Fees
                        </button>
                    </div>
                </div>

                <!-- Expected Fees Display -->
                <div id="expectedFeesDisplay" class="mb-4" style="display: none;">
                    <div class="alert alert-info">
                        <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-1"></i> Expected Fees Breakdown</h6>
                        <div id="feeBreakdown"></div>
                        <hr>
                        <div class="d-flex flex-wrap gap-3">
                            <span class="fw-bold">Total Expected: <span id="totalExpected">0</span></span>
                            <span class="fw-bold text-success">Total Paid: <span id="totalPaid">0</span></span>
                            <span class="fw-bold text-danger">Balance: <span id="balance">0</span></span>
                            <span id="paymentStatus" class="badge bg-success"></span>
                        </div>
                    </div>
                </div>

                <!-- Fee Details -->
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Fee Type *</label>
                        <select name="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
                            <option value="">Select fee type</option>
                            @foreach($feeTypes as $type)
                                <option value="{{ $type }}" {{ old('fee_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('fee_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Amount (KES) *</label>
                        <input type="number" name="amount" id="feeAmount" 
                               class="form-control @error('amount') is-invalid @enderror" 
                               placeholder="0.00" step="0.01" min="0" 
                               value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Expected amount will auto-fill when calculating fees</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Amount Paid *</label>
                        <input type="number" name="amount_paid" id="amountPaid" 
                               class="form-control @error('amount_paid') is-invalid @enderror" 
                               placeholder="0.00" step="0.01" min="0" 
                               value="{{ old('amount_paid') }}" required>
                        @error('amount_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Payment Method *</label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Payment Date *</label>
                        <input type="date" name="payment_date" 
                               class="form-control @error('payment_date') is-invalid @enderror" 
                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Class</label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Grade</label>
                        <select name="grade_id" class="form-select @error('grade_id') is-invalid @enderror" required>
                            <option value="">Select grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Receipt Number</label>
                        <input type="text" name="receipt_no" class="form-control @error('receipt_no') is-invalid @enderror" 
                               placeholder="Auto-generated if left blank" value="{{ old('receipt_no') }}">
                        @error('receipt_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" placeholder="Additional notes">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Record Payment
                            </button>
                            <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('studentSelect');
    const termSelect = document.getElementById('termSelect');
    const yearSelect = document.getElementById('yearSelect');
    const calculateBtn = document.getElementById('calculateBtn');
    const expectedFeesDisplay = document.getElementById('expectedFeesDisplay');
    const feeBreakdown = document.getElementById('feeBreakdown');
    const totalExpectedSpan = document.getElementById('totalExpected');
    const totalPaidSpan = document.getElementById('totalPaid');
    const balanceSpan = document.getElementById('balance');
    const paymentStatus = document.getElementById('paymentStatus');
    const feeAmount = document.getElementById('feeAmount');

    calculateBtn.addEventListener('click', function() {
        const studentId = studentSelect.value;
        const term = termSelect.value;
        const academicYear = yearSelect.value;

        if (!studentId || !term || !academicYear) {
            alert('Please select student, term, and academic year');
            return;
        }

        // Show loading state
        calculateBtn.disabled = true;
        calculateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Calculating...';

        // Make AJAX request
        fetch(`{{ route('fees.calculate-expected') }}?student_id=${studentId}&term=${term}&academic_year=${academicYear}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const feeData = data.data;
                    
                    // Display fee breakdown
                    let breakdownHtml = '<ul class="list-unstyled mb-2">';
                    let total = 0;
                    feeData.fee_structures.forEach(fee => {
                        total += parseFloat(fee.amount);
                        breakdownHtml += `
                            <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span>${fee.fee_type}</span>
                                <span class="fw-semibold">KES ${Number(fee.amount).toFixed(2)}</span>
                            </li>
                        `;
                    });
                    breakdownHtml += '</ul>';
                    
                    if (feeData.fee_structures.length === 0) {
                        breakdownHtml = '<p class="text-muted mb-0">No fee structures defined for this class/grade/term/year.</p>';
                    }

                    feeBreakdown.innerHTML = breakdownHtml;
                    
                    // Update summary
                    totalExpectedSpan.textContent = `KES ${Number(feeData.total_expected).toFixed(2)}`;
                    totalPaidSpan.textContent = `KES ${Number(feeData.total_paid).toFixed(2)}`;
                    balanceSpan.textContent = `KES ${Number(feeData.balance).toFixed(2)}`;
                    
                    // Update payment status
                    if (feeData.all_paid) {
                        paymentStatus.textContent = '✓ Fully Paid';
                        paymentStatus.className = 'badge bg-success';
                    } else if (feeData.total_paid > 0) {
                        paymentStatus.textContent = '⚠ Partial Payment';
                        paymentStatus.className = 'badge bg-warning text-dark';
                    } else {
                        paymentStatus.textContent = '✗ Not Paid';
                        paymentStatus.className = 'badge bg-danger';
                    }
                    
                    // Auto-fill fee amount if only one fee type
                    if (feeData.fee_structures.length === 1) {
                        feeAmount.value = feeData.fee_structures[0].amount;
                    }
                    
                    // Show the display
                    expectedFeesDisplay.style.display = 'block';
                } else {
                    alert(data.message || 'Error calculating fees');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while calculating fees');
            })
            .finally(() => {
                calculateBtn.disabled = false;
                calculateBtn.innerHTML = '<i class="fas fa-calculator me-1"></i> Calculate Expected Fees';
            });
    });

    // Auto-populate class and grade when student is selected
    studentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const classId = selectedOption.dataset.class;
        const gradeId = selectedOption.dataset.grade;
        
        if (classId) {
            document.querySelector('select[name="class_id"]').value = classId;
        }
        if (gradeId) {
            document.querySelector('select[name="grade_id"]').value = gradeId;
        }
    });
});
</script>
@endsection