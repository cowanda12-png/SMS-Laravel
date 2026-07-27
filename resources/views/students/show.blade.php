@extends('layouts.app') 
@section('title') 
Student Details 
@endsection 

@section('content') 

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
        <div class="mb-2 mb-sm-0">
            <h1 class="h4 h-md-3 mb-0">Student Details</h1>
            <p class="text-muted small mb-0">View student information and financial status</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to List</span>
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row g-2 g-sm-3 mb-3 mb-md-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100 border-{{ $student->total_fees > 0 ? 'primary' : 'secondary' }}">
                <div class="card-body text-center p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <i class="fas fa-coins text-primary me-1" style="font-size: 1.2rem;"></i>
                        <h6 class="card-title mb-0 fs-6">Total Fees</h6>
                    </div>
                    <h5 class="mb-0 text-primary">KES {{ number_format($student->total_fees ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100 border-{{ $student->total_paid > 0 ? 'success' : 'secondary' }}">
                <div class="card-body text-center p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <i class="fas fa-check-circle text-success me-1" style="font-size: 1.2rem;"></i>
                        <h6 class="card-title mb-0 fs-6">Total Paid</h6>
                    </div>
                    <h5 class="mb-0 text-success">KES {{ number_format($student->total_paid ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100 border-{{ $student->balance > 0 ? 'danger' : 'success' }}">
                <div class="card-body text-center p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <i class="fas fa-wallet me-1" style="font-size: 1.2rem;"></i>
                        <h6 class="card-title mb-0 fs-6">Balance</h6>
                    </div>
                    <h5 class="mb-0 {{ $student->balance > 0 ? 'text-danger' : 'text-success' }}">
                        KES {{ number_format($student->balance ?? 0, 2) }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100 border-{{ $student->payment_status == 'paid' ? 'success' : ($student->payment_status == 'overdue' ? 'danger' : 'warning') }}">
                <div class="card-body text-center p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <i class="fas fa-credit-card me-1" style="font-size: 1.2rem;"></i>
                        <h6 class="card-title mb-0 fs-6">Status</h6>
                    </div>
                    <h5 class="mb-0">
                        <span class="badge bg-{{ $student->payment_status == 'paid' ? 'success' : ($student->payment_status == 'overdue' ? 'danger' : 'warning') }}">
                            {{ ucfirst($student->payment_status ?? 'pending') }}
                        </span>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Main Information - Left Column -->
        <div class="col-12 col-lg-8 order-2 order-lg-1">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent py-2 py-sm-3">
                    <h5 class="card-title mb-0 fs-6 fs-sm-5">
                        <i class="fas fa-user me-2"></i> Personal Information
                    </h5>
                </div>
                <div class="card-body p-2 p-sm-3 p-md-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 35%; min-width: 100px;">ID</th>
                                    <td>{{ $student->id }}</td>
                                </tr>
                                <tr>
                                    <th>Admission Number</th>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $student->admission_number ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Full Name</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2 flex-shrink-0">
                                                {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold text-truncate">
                                                {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ $student->date_of_birth ? date('M d, Y', strtotime($student->date_of_birth)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>
                                        @if($student->gender)
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                <i class="fas fa-{{ $student->gender == 'male' ? 'mars' : 'venus' }} me-1"></i>
                                                {{ ucfirst($student->gender) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td class="text-break">{{ $student->email ?? 'No email' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>
                                        @if($student->phone)
                                            <span class="badge bg-light text-dark d-inline-flex align-items-center">
                                                <i class="fas fa-phone me-1"></i> {{ $student->phone }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alternate Phone</th>
                                    <td>
                                        @if($student->alternate_phone)
                                            <span class="badge bg-light text-dark d-inline-flex align-items-center">
                                                <i class="fas fa-phone me-1"></i> {{ $student->alternate_phone }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $student->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Course</th>
                                    <td>
                                        @if($student->course)
                                            <span class="badge bg-info bg-opacity-10 text-info d-inline-flex align-items-center">
                                                <i class="fas fa-book me-1"></i>
                                                {{ $student->course->course_name ?? $student->course->name ?? 'Not Assigned' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                Not Assigned
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Class</th>
                                    <td>
                                        @if($student->class)
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $student->class->name ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Grade</th>
                                    <td>
                                        @if($student->grade)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-star me-1"></i>
                                                {{ $student->grade->name ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Guardian Name</th>
                                    <td>{{ $student->guardian_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Guardian Phone</th>
                                    <td>{{ $student->guardian_phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Guardian Email</th>
                                    <td>{{ $student->guardian_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $status = $student->status ?? 'active';
                                            $statusColor = match($status) {
                                                'active' => 'success',
                                                'inactive' => 'danger',
                                                'pending' => 'warning',
                                                'graduated' => 'info',
                                                'suspended' => 'danger',
                                                'expelled' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="padding: 6px 12px;">
                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Enrollment Date</th>
                                    <td>{{ $student->enrollment_date ? date('M d, Y', strtotime($student->enrollment_date)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $student->created_at ? $student->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $student->updated_at ? $student->updated_at->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-transparent py-2 py-sm-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fs-6 fs-sm-5">
                        <i class="fas fa-history me-2"></i> Payment History
                    </h5>
                    <a href="{{ route('fees.student', $student->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Record Payment
                    </a>
                </div>
                <div class="card-body p-2 p-sm-3">
                    @if(isset($student->payments) && $student->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->payments->take(10) as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->fee_type ?? 'N/A' }}</td>
                                        <td>KES {{ number_format($payment->amount ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $payment->payment_method ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'overdue' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($payment->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->created_at ? $payment->created_at->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('fees.show', $payment->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('fees.receipt', $payment->id) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($student->payments->count() > 10)
                                <div class="text-center mt-2">
                                    <a href="{{ route('fees.student', $student->id) }}" class="btn btn-link btn-sm">
                                        View all {{ $student->payments->count() }} payments
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-credit-card fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">No payment records found for this student.</p>
                            <a href="{{ route('fees.student', $student->id) }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Record First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fee Structure Summary -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-transparent py-2 py-sm-3">
                    <h5 class="card-title mb-0 fs-6 fs-sm-5">
                        <i class="fas fa-list me-2"></i> Fee Structure Summary
                    </h5>
                </div>
                <div class="card-body p-2 p-sm-3">
                    @if(isset($student->feeStructures) && $student->feeStructures->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Term</th>
                                        <th>Academic Year</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->feeStructures->take(10) as $fee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $fee->fee_type }}</td>
                                        <td>KES {{ number_format($fee->amount ?? 0, 2) }}</td>
                                        <td>{{ $fee->term ?? 'N/A' }}</td>
                                        <td>{{ $fee->academic_year ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $fee->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($fee->status ?? 'inactive') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-file-invoice fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">No fee structures assigned to this student.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Profile Card - Right Column -->
        <div class="col-12 col-lg-4 order-1 order-lg-2">
            <div class="card shadow-sm">
                <div class="card-body text-center p-3 p-md-4">
                    <div class="avatar-lg mx-auto mb-3">
                        @if($student->profile_image)
                            <img src="{{ asset('storage/' . $student->profile_image) }}" alt="{{ $student->first_name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <h5 class="mb-1 text-truncate">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h5>
                    <p class="text-muted small text-break">{{ $student->email ?? 'No email' }}</p>
                    
                    <hr>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">ID</small>
                            <strong>#{{ $student->id }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $statusColor ?? 'secondary' }}">{{ ucfirst($student->status ?? 'Active') }}</span>
                        </div>
                    </div>

                    <!-- Payment Status Quick View -->
                    <hr>
                    <div class="text-start">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Total Fees:</small>
                            <strong>KES {{ number_format($student->total_fees ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Total Paid:</small>
                            <strong class="text-success">KES {{ number_format($student->total_paid ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Balance:</small>
                            <strong class="{{ $student->balance > 0 ? 'text-danger' : 'text-success' }}">
                                KES {{ number_format($student->balance ?? 0, 2) }}
                            </strong>
                        </div>
                    </div>
                    
                    <!-- Quick Actions for Mobile -->
                    <div class="d-block d-lg-none mt-3">
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Student
                            </a>
                            <a href="{{ route('fees.student', $student->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-money-bill-wave me-1"></i> Record Payment
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-transparent py-2 py-sm-3">
                    <h6 class="card-title mb-0 fs-6">
                        <i class="fas fa-chart-pie me-2"></i> Quick Stats
                    </h6>
                </div>
                <div class="card-body p-2 p-sm-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center">
                                <small class="text-muted d-block">Courses</small>
                                <strong>{{ $student->courses_count ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center">
                                <small class="text-muted d-block">Exams</small>
                                <strong>{{ $student->exams_count ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center">
                                <small class="text-muted d-block">Payments</small>
                                <strong>{{ $student->payments_count ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center">
                                <small class="text-muted d-block">Overdue</small>
                                <strong class="text-danger">{{ $student->overdue_count ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions for Desktop -->
            <div class="d-none d-lg-block mt-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Student
                    </a>
                    <a href="{{ route('fees.student', $student->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-money-bill-wave me-1"></i> Record Payment
                    </a>
                    <a href="{{ route('reports.student-statement') }}?student_id={{ $student->id }}" class="btn btn-info btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> Generate Statement
                    </a>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Responsive Styles */
    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .avatar-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 32px;
        margin: 0 auto;
    }
    
    /* Mobile First Table */
    .table th {
        font-weight: 600;
        font-size: 0.8rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 10px 12px;
    }
    
    .table td {
        padding: 10px 12px;
        vertical-align: middle;
        font-size: 0.9rem;
    }
    
    /* Responsive Table */
    @media (max-width: 576px) {
        .table th {
            font-size: 0.75rem;
            padding: 8px 10px;
            min-width: 90px;
        }
        
        .table td {
            font-size: 0.8rem;
            padding: 8px 10px;
            word-break: break-word;
        }
        
        .avatar-lg {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-title {
            font-size: 0.9rem !important;
        }
    }
    
    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) {
        .table th {
            font-size: 0.85rem;
            padding: 12px 16px;
        }
        
        .table td {
            font-size: 0.9rem;
            padding: 12px 16px;
        }
    }
    
    /* Medium devices (tablets, 768px and up) */
    @media (min-width: 768px) {
        .table th {
            font-size: 0.85rem;
        }
    }
    
    /* Button Responsive */
    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
        border-radius: 6px;
        white-space: nowrap;
    }
    
    @media (max-width: 576px) {
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.75rem;
        }
        
        .btn-sm i {
            font-size: 0.8rem;
        }
    }
    
    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .btn-warning {
        background: #ffc107;
        border: none;
        color: #000;
    }
    
    .btn-warning:hover {
        background: #e0a800;
        color: #000;
    }
    
    /* Text truncation for long content */
    .text-truncate {
        max-width: 100%;
        display: inline-block;
    }
    
    /* Badge responsive */
    .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    @media (max-width: 576px) {
        .badge {
            font-size: 0.7rem;
            padding: 3px 6px;
        }
    }
    
    /* Fix for long email on mobile */
    .text-break {
        word-break: break-all;
        overflow-wrap: break-word;
    }
    
    /* Responsive spacing */
    .gap-2 {
        gap: 0.5rem !important;
    }
    
    @media (max-width: 576px) {
        .gap-2 {
            gap: 0.25rem !important;
        }
    }
    
    /* Financial Summary Cards */
    .card.border-primary {
        border-color: #6c8cff !important;
    }
    
    .card.border-success {
        border-color: #28a745 !important;
    }
    
    .card.border-danger {
        border-color: #dc3545 !important;
    }
    
    .card.border-warning {
        border-color: #ffc107 !important;
    }
</style>

@endsection