@extends('layouts.app') 
@section('title') 
Course Details 
@endsection 

@section('content') 

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
        <div class="mb-2 mb-sm-0">
            <h1 class="h4 h-md-3 mb-0">Course Details</h1>
            <p class="text-muted small mb-0">View course information</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> <span class="d-none d-sm-inline">Edit</span>
            </a>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to List</span>
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3 p-md-4">
            <div class="row g-3">
                <!-- Main Content - Moves to bottom on mobile -->
                <div class="col-12 col-lg-8 order-2 order-lg-1">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 35%; min-width: 100px;">ID</th>
                                    <td>{{ $course->id }}</td>
                                </tr>
                                <tr>
                                    <th>Course Name</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-2 flex-shrink-0">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <span class="fw-semibold text-truncate">{{ $course->course_name ?? $course->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Course Code</th>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center">
                                            <i class="fas fa-tag me-1 d-none d-sm-inline"></i>
                                            {{ $course->code }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Credits</th>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info d-inline-flex align-items-center">
                                            <i class="fas fa-star me-1 d-none d-sm-inline"></i>
                                            {{ $course->credits ?? 3 }} Credits
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $status = $course->status ?? 'active';
                                            $statusColor = $status == 'active' ? 'success' : ($status == 'inactive' ? 'danger' : 'warning');
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="padding: 6px 12px;">
                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Students</th>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success d-inline-flex align-items-center">
                                            <i class="fas fa-users me-1"></i> 
                                            {{ $course->students->count() ?? 0 }} Students
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $course->created_at ? $course->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $course->updated_at ? $course->updated_at->format('M d, Y H:i') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Course Description - Mobile Friendly -->
                    @if($course->description)
                        <div class="card mt-3 d-block d-lg-none">
                            <div class="card-header bg-transparent px-3 py-2">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Description</h6>
                            </div>
                            <div class="card-body px-3 py-2">
                                <p class="mb-0 small">{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar Card - Moves to top on mobile -->
                <div class="col-12 col-lg-4 order-1 order-lg-2">
                    <div class="card bg-light h-100">
                        <div class="card-body text-center p-3 p-md-4">
                            <div class="course-icon-lg mx-auto mb-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <h5 class="mb-1 text-truncate">{{ $course->course_name ?? $course->name ?? 'Unknown' }}</h5>
                            <p class="text-muted small">{{ $course->code }}</p>
                            <hr>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Credits</small>
                                    <strong>{{ $course->credits ?? 3 }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Students</small>
                                    <strong>{{ $course->students->count() ?? 0 }}</strong>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <small class="text-muted d-block mb-1">Status</small>
                                <span class="badge bg-{{ $statusColor ?? 'secondary' }}">
                                    {{ ucfirst($course->status ?? 'Active') }}
                                </span>
                            </div>

                            <!-- Quick Actions for Mobile -->
                            <div class="d-block d-lg-none mt-3">
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i> Edit Course
                                    </a>
                                    <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left me-1"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Description - Desktop -->
                    @if($course->description)
                        <div class="card mt-3 d-none d-lg-block">
                            <div class="card-header bg-transparent">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Description</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enrolled Students List - Responsive -->
            @if($course->students->count() > 0)
                <div class="mt-4">
                    <h5 class="mb-3 d-flex align-items-center flex-wrap">
                        <span>Enrolled Students</span>
                        <span class="badge bg-primary ms-2">{{ $course->students->count() }}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="d-none d-sm-table-cell">#</th>
                                    <th>Name</th>
                                    <th class="d-none d-md-table-cell">Email</th>
                                    <th class="d-none d-lg-table-cell">Phone</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                    <tr>
                                        <td class="d-none d-sm-table-cell">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2 flex-shrink-0">
                                                    {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="text-truncate" style="max-width: 120px;">
                                                    {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell text-truncate" style="max-width: 150px;">
                                            {{ $student->email ?? 'No email' }}
                                        </td>
                                        <td class="d-none d-lg-table-cell">{{ $student->phone ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $sStatus = $student->status ?? 'active';
                                                $sColor = $sStatus == 'active' ? 'success' : 'warning';
                                            @endphp
                                            <span class="badge bg-{{ $sColor }} bg-opacity-10 text-{{ $sColor }}">
                                                {{ ucfirst($sStatus) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Responsive Styles */
    .course-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .course-icon-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto;
    }
    
    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
        flex-shrink: 0;
    }
    
    /* Table Styles - Responsive */
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
    
    @media (max-width: 576px) {
        .table th {
            font-size: 0.75rem;
            padding: 8px 10px;
            min-width: 80px;
        }
        
        .table td {
            font-size: 0.8rem;
            padding: 8px 10px;
            word-break: break-word;
        }
        
        .course-icon-lg {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }
        
        .avatar {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }
    }
    
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
    
    /* Badge Responsive */
    .badge {
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    
    @media (max-width: 576px) {
        .badge {
            font-size: 0.7rem;
            padding: 3px 6px;
        }
    }
    
    /* Text truncation */
    .text-truncate {
        max-width: 100%;
        display: inline-block;
    }
    
    /* Spacing utilities */
    .gap-2 {
        gap: 0.5rem !important;
    }
    
    @media (max-width: 576px) {
        .gap-2 {
            gap: 0.25rem !important;
        }
    }
    
    /* Card body padding */
    .card-body {
        padding: 1rem !important;
    }
    
    @media (min-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>

@endsection