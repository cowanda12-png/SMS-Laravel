@extends('layouts.app') 
@section('title') 
Student Details 
@endsection 

@section('content') 

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Student Details</h1>
            <p class="text-muted small">View student information</p>
        </div>
        <div>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%;">ID</th>
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
                                    <div class="avatar me-2">
                                        {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">
                                        {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $student->email ?? 'No email' }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>
                                @if($student->phone)
                                    <span class="badge bg-light text-dark">
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
                                    <span class="badge bg-info bg-opacity-10 text-info">
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
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                            </div>
                            <h5 class="mb-1">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h5>
                            <p class="text-muted small">{{ $student->email ?? 'No email' }}</p>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">ID</small>
                                    <strong>#{{ $student->id }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge bg-{{ $statusColor ?? 'secondary' }}">{{ ucfirst($student->status ?? 'Active') }}</span>
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
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 12px 16px;
    }
    
    .table td {
        padding: 12px 16px;
        vertical-align: middle;
    }
    
    .btn-sm {
        padding: 6px 16px;
        font-size: 0.85rem;
        border-radius: 6px;
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
</style>

@endsection