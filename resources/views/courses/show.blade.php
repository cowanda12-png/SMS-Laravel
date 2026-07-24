@extends('layouts.app') 
@section('title') 
Course Details 
@endsection 

@section('content') 

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Course Details</h1>
            <p class="text-muted small">View course information</p>
        </div>
        <div>
            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
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
                            <td>{{ $course->id }}</td>
                        </tr>
                        <tr>
                            <th>Course Name</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="course-icon me-2">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <span class="fw-semibold">{{ $course->course_name ?? $course->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Course Code</th>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $course->code }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Credits</th>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info">
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
                                <span class="badge bg-success bg-opacity-10 text-success">
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
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <div class="course-icon-lg mx-auto mb-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <h5 class="mb-1">{{ $course->course_name ?? $course->name ?? 'Unknown' }}</h5>
                            <p class="text-muted small">{{ $course->code }}</p>
                            <hr>
                            <div class="row">
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
                                <small class="text-muted d-block">Status</small>
                                <span class="badge bg-{{ $statusColor ?? 'secondary' }}">
                                    {{ ucfirst($course->status ?? 'Active') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($course->description)
                        <div class="card mt-3">
                            <div class="card-header bg-transparent">
                                <h6 class="mb-0">Description</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $course->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enrolled Students List -->
            @if($course->students->count() > 0)
                <div class="mt-4">
                    <h5 class="mb-3">Enrolled Students ({{ $course->students->count() }})</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span>{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $student->email ?? 'No email' }}</td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
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
    
    .badge {
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 6px;
    }
</style>

@endsection