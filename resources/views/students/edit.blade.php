@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-edit text-primary me-2"></i> Edit Student
                    </h5>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to Students</span>
                    </a>
                </div>

                <div class="card-body p-3 p-sm-4">
                    <!-- Display Validation Errors -->
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

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('students.update', $student) }}" method="POST" id="editStudentForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Student ID (Read-only) -->
                            <div class="col-12">
                                <div class="alert alert-info py-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Editing Student #<strong>{{ $student->id }}</strong>
                                    @if($student->admission_number)
                                        - Admission: <strong>{{ $student->admission_number }}</strong>
                                    @endif
                                </div>
                            </div>

                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label fw-semibold">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       id="first_name" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', $student->first_name) }}"
                                       placeholder="Enter first name"
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="last_name" class="form-label fw-semibold">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       id="last_name" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name', $student->last_name) }}"
                                       placeholder="Enter last name"
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $student->email) }}"
                                       placeholder="Enter email address"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Phone Number
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $student->phone) }}"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Admission Number -->
                            <div class="col-md-6">
                                <label for="admission_number" class="form-label fw-semibold">
                                    Admission Number
                                </label>
                                <input type="text" 
                                       name="admission_number" 
                                       id="admission_number" 
                                       class="form-control @error('admission_number') is-invalid @enderror" 
                                       value="{{ old('admission_number', $student->admission_number) }}"
                                       placeholder="Enter admission number">
                                @error('admission_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Leave blank to auto-generate
                                </small>
                            </div>

                            <!-- Registration Number -->
                            <div class="col-md-6">
                                <label for="registration_number" class="form-label fw-semibold">
                                    Registration Number
                                </label>
                                <input type="text" 
                                       name="registration_number" 
                                       id="registration_number" 
                                       class="form-control @error('registration_number') is-invalid @enderror" 
                                       value="{{ old('registration_number', $student->registration_number) }}"
                                       placeholder="Enter registration number">
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">
                                    Address
                                </label>
                                <textarea name="address" 
                                          id="address" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          rows="2"
                                          placeholder="Enter address">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Course -->
                            <div class="col-md-6">
                                <label for="course_id" class="form-label fw-semibold">
                                    Course <span class="text-danger">*</span>
                                </label>
                                <select name="course_id" 
                                        id="course_id" 
                                        class="form-select @error('course_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- Select Course --</option>
                                    @foreach($courses ?? [] as $course)
                                        <option value="{{ $course->id }}" 
                                                {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name ?? $course->name ?? 'Course #' . $course->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" 
                                        id="status" 
                                        class="form-select @error('status') is-invalid @enderror"
                                        required>
                                    <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                    <option value="pending" {{ old('status', $student->status) == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>
                                        Graduated
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fee Information (Read-only) -->
                            <div class="col-12 mt-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-2">
                                            <i class="fas fa-money-bill-wave text-success me-1"></i> Fee Summary
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Total Fees</small>
                                                <span class="fw-bold">KES {{ number_format($student->total_fees ?? 0, 2) }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Total Paid</small>
                                                <span class="fw-bold text-success">KES {{ number_format($student->total_paid ?? 0, 2) }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Pending</small>
                                                <span class="fw-bold text-warning">KES {{ number_format($student->total_pending ?? 0, 2) }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Balance</small>
                                                <span class="fw-bold text-danger">KES {{ number_format(($student->total_fees ?? 0) - ($student->total_paid ?? 0), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-1"></i> Update Student
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border-color: #e9ecef;
        font-size: 0.9rem;
        padding: 8px 14px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
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
        font-weight: 500;
        padding: 8px 20px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .btn-sm {
        padding: 6px 14px;
        font-size: 0.8rem;
    }
    
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }
    
    .alert-success {
        border-left-color: #28a745;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
    
    .alert-info {
        border-left-color: #17a2b8;
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .form-control, .form-select {
            font-size: 0.82rem;
            padding: 6px 12px;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 6px 14px;
        }
        
        .form-label {
            font-size: 0.78rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .card-body {
            padding: 0.75rem !important;
        }
        
        .form-control, .form-select {
            font-size: 0.75rem;
            padding: 5px 10px;
        }
        
        .btn {
            font-size: 0.75rem;
            padding: 5px 12px;
        }
        
        .form-label {
            font-size: 0.7rem;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 4px 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Form submission confirmation
        const form = document.getElementById('editStudentForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                // Disable button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
                
                // Re-enable after 5 seconds if something goes wrong
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Update Student';
                }, 5000);
            });
        }
    });
</script>
@endpush

@endsection