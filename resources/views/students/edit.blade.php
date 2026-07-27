@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
        <div class="mb-2 mb-sm-0">
            <h1 class="h4 h-md-3 mb-0">Edit Student</h1>
            <p class="text-muted small mb-0">Update student information</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to Students</span>
        </a>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3 p-md-4">
            <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Student ID Info -->
                <div class="alert alert-info py-2 mb-4">
                    <i class="fas fa-info-circle me-1"></i>
                    Editing Student #<strong>{{ $student->id }}</strong>
                    @if($student->admission_number)
                        - Admission: <strong>{{ $student->admission_number }}</strong>
                    @endif
                </div>

                <!-- Personal Information Section -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-user me-2 text-primary"></i> Personal Information
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-semibold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $student->first_name) }}" 
                                   placeholder="Enter first name" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-semibold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $student->last_name) }}" 
                                   placeholder="Enter last name" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label fw-semibold">
                                Date of Birth
                            </label>
                            <input type="date" 
                                   class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   id="date_of_birth" 
                                   name="date_of_birth" 
                                   value="{{ old('date_of_birth', $student->date_of_birth ? date('Y-m-d', strtotime($student->date_of_birth)) : '') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label fw-semibold">
                                Gender
                            </label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-phone me-2 text-primary"></i> Contact Information
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $student->email) }}" 
                                   placeholder="Enter email address" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">
                                Phone Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $student->phone) }}" 
                                   placeholder="Enter phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="alternate_phone" class="form-label fw-semibold">
                                Alternate Phone
                            </label>
                            <input type="text" 
                                   class="form-control @error('alternate_phone') is-invalid @enderror" 
                                   id="alternate_phone" 
                                   name="alternate_phone" 
                                   value="{{ old('alternate_phone', $student->alternate_phone) }}" 
                                   placeholder="Enter alternate phone number">
                            @error('alternate_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="address" class="form-label fw-semibold">
                                Address
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2" 
                                      placeholder="Enter address">{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Guardian Information Section -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-user-shield me-2 text-primary"></i> Guardian Information
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="guardian_name" class="form-label fw-semibold">
                                Guardian Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('guardian_name') is-invalid @enderror" 
                                   id="guardian_name" 
                                   name="guardian_name" 
                                   value="{{ old('guardian_name', $student->guardian_name) }}" 
                                   placeholder="Enter guardian name">
                            @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="guardian_phone" class="form-label fw-semibold">
                                Guardian Phone
                            </label>
                            <input type="text" 
                                   class="form-control @error('guardian_phone') is-invalid @enderror" 
                                   id="guardian_phone" 
                                   name="guardian_phone" 
                                   value="{{ old('guardian_phone', $student->guardian_phone) }}" 
                                   placeholder="Enter guardian phone">
                            @error('guardian_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="guardian_email" class="form-label fw-semibold">
                                Guardian Email
                            </label>
                            <input type="email" 
                                   class="form-control @error('guardian_email') is-invalid @enderror" 
                                   id="guardian_email" 
                                   name="guardian_email" 
                                   value="{{ old('guardian_email', $student->guardian_email) }}" 
                                   placeholder="Enter guardian email">
                            @error('guardian_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i> Academic Information
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="admission_number" class="form-label fw-semibold">
                                Admission Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('admission_number') is-invalid @enderror" 
                                   id="admission_number" 
                                   name="admission_number" 
                                   value="{{ old('admission_number', $student->admission_number) }}" 
                                   placeholder="e.g., ADM-2024-001">
                            <small class="text-muted">Leave blank to auto-generate</small>
                            @error('admission_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="registration_number" class="form-label fw-semibold">
                                Registration Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('registration_number') is-invalid @enderror" 
                                   id="registration_number" 
                                   name="registration_number" 
                                   value="{{ old('registration_number', $student->registration_number) }}" 
                                   placeholder="e.g., REG-2024-001">
                            @error('registration_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="course_id" class="form-label fw-semibold">
                                Course
                            </label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id">
                                <option value="">Select Course</option>
                                @foreach($courses ?? [] as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name ?? $course->name ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="class_id" class="form-label fw-semibold">
                                Class
                            </label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id">
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="grade_id" class="form-label fw-semibold">
                                Grade
                            </label>
                            <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" name="grade_id">
                                <option value="">Select Grade</option>
                                @foreach($grades ?? [] as $grade)
                                    <option value="{{ $grade->id }}" {{ old('grade_id', $student->grade_id) == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="enrollment_date" class="form-label fw-semibold">
                                Enrollment Date
                            </label>
                            <input type="date" 
                                   class="form-control @error('enrollment_date') is-invalid @enderror" 
                                   id="enrollment_date" 
                                   name="enrollment_date" 
                                   value="{{ old('enrollment_date', $student->enrollment_date ? date('Y-m-d', strtotime($student->enrollment_date)) : date('Y-m-d')) }}">
                            @error('enrollment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">
                                Status
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ old('status', $student->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="expelled" {{ old('status', $student->status) == 'expelled' ? 'selected' : '' }}>Expelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Profile Image Section -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-image me-2 text-primary"></i> Profile Image
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            @if($student->profile_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $student->profile_image) }}" 
                                         alt="{{ $student->first_name }}" 
                                         class="rounded-circle" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                    <br>
                                    <small class="text-muted">Current profile image</small>
                                </div>
                            @endif
                            <label for="profile_image" class="form-label fw-semibold">
                                Profile Picture
                            </label>
                            <input type="file" 
                                   class="form-control @error('profile_image') is-invalid @enderror" 
                                   id="profile_image" 
                                   name="profile_image" 
                                   accept="image/*">
                            <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                            @error('profile_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Fee Summary (Read-only) -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-money-bill-wave me-2 text-success"></i> Fee Summary
                    </h5>
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Total Fees</small>
                                    <span class="fw-bold">KES {{ number_format($student->total_fees ?? 0, 2) }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Total Paid</small>
                                    <span class="fw-bold text-success">KES {{ number_format($student->total_paid ?? 0, 2) }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Pending</small>
                                    <span class="fw-bold text-warning">KES {{ number_format($student->total_pending ?? 0, 2) }}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Balance</small>
                                    <span class="fw-bold text-danger">KES {{ number_format(($student->total_fees ?? 0) - ($student->total_paid ?? 0), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Student
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        font-weight: 500;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 3px rgba(108, 140, 255, 0.1);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
    }
    
    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: #6c8cff;
        border: none;
    }
    
    .btn-primary:hover {
        background: #5a7ae6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 140, 255, 0.3);
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-muted {
        font-size: 0.8rem;
    }
    
    .border-bottom {
        border-bottom: 2px solid #f0f0f0 !important;
    }
    
    .border-top {
        border-top: 2px solid #f0f0f0 !important;
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
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .form-control, .form-select {
            padding: 8px 10px;
            font-size: 0.85rem;
        }
        
        .btn {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        h5 {
            font-size: 1rem;
        }
    }
    
    @media (min-width: 768px) {
        .container-fluid {
            padding: 0 2rem;
        }
    }
</style>

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
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');
        
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