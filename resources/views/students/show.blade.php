@extends('layouts.app') 
@section('title') 
Student Details 
@endsection 

@section('content') 

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
        <div class="mb-2 mb-sm-0">
            <h1 class="h4 h-md-3 mb-0">Student Details</h1>
            <p class="text-muted small mb-0">View student information</p>
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

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3 p-md-4">
            <div class="row g-3">
                <!-- Mobile First: Stack columns on small screens -->
                <div class="col-12 col-lg-8 order-2 order-lg-1">
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
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $status = $student->status ?? 'active';
                                            $statusColor = match($status) {
                                                'active' => 'success',
                                                'inactive' => 'danger',
                                                'pending' => 'warning',
                                                'graduated' => 'info',
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
                
                <!-- Profile Card - Moves to top on mobile -->
                <div class="col-12 col-lg-4 order-1 order-lg-2">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center p-3 p-md-4">
                            <div class="avatar-lg mx-auto mb-3">
                                {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
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
                            
                            <!-- Quick Actions for Mobile -->
                            <div class="d-block d-lg-none mt-3">
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i> Edit Student
                                    </a>
                                    <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left me-1"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
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
</style>

@endsection