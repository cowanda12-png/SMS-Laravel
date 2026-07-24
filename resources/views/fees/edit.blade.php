@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Payment</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('fees.update', $fee) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" 
                                        class="form-select @error('student_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" 
                                            {{ old('student_id', $fee->student_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                            @if($student->course_name)
                                                - {{ $student->course_name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (KES) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="1" 
                                       name="amount" id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $fee->amount) }}" 
                                       placeholder="0.00" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="payment_method" 
                                        class="form-select @error('payment_method') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Method</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method }}" 
                                            {{ old('payment_method', $fee->payment_method) == $method ? 'selected' : '' }}>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fee_type" class="form-label">Fee Type</label>
                                <select name="fee_type" id="fee_type" 
                                        class="form-select @error('fee_type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    @foreach($feeTypes as $type)
                                        <option value="{{ $type }}" 
                                            {{ old('fee_type', $fee->fee_type) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fee_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="receipt_no" class="form-label">Receipt Number</label>
                                <input type="text" name="receipt_no" id="receipt_no" 
                                       class="form-control @error('receipt_no') is-invalid @enderror" 
                                       value="{{ old('receipt_no', $fee->receipt_no) }}" 
                                       placeholder="Receipt number">
                                @error('receipt_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" 
                                       class="form-control @error('payment_date') is-invalid @enderror" 
                                       value="{{ old('payment_date', $fee->payment_date->format('Y-m-d')) }}" 
                                       required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="2" 
                                      placeholder="Additional notes about this payment">{{ old('description', $fee->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('fees.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection