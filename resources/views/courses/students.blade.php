@extends('layouts.app')

@section('title', 'Course Students')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4">
        <div class="mb-2 mb-sm-0">
            <h1 class="h4 h-md-3 mb-0 text-truncate" style="max-width: 250px;">{{ $course->course_name }}</h1>
            <p class="text-muted small mb-0">Students enrolled in this course</p>
        </div>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back to Courses</span>
        </a>
    </div>

    <!-- Statistics Cards - Responsive Grid -->
    <div class="row g-2 g-sm-3 mb-3 mb-md-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-2 p-sm-3">
                    <h6 class="text-muted small mb-1">Total Students</h6>
                    <h3 class="fw-bold mb-0 fs-4 fs-md-3">{{ $stats['total_students'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-2 p-sm-3">
                    <h6 class="text-muted small mb-1">Total Fees</h6>
                    <h3 class="fw-bold text-primary mb-0 fs-6 fs-md-3" style="font-size: clamp(0.9rem, 2.5vw, 1.5rem);">
                        KES <span class="d-inline d-sm-none">{{ number_format(($stats['total_fees'] ?? 0) / 1000, 1) }}K</span>
                        <span class="d-none d-sm-inline">{{ number_format($stats['total_fees'] ?? 0, 0) }}</span>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-2 p-sm-3">
                    <h6 class="text-muted small mb-1">Total Paid</h6>
                    <h3 class="fw-bold text-success mb-0 fs-6 fs-md-3" style="font-size: clamp(0.9rem, 2.5vw, 1.5rem);">
                        KES <span class="d-inline d-sm-none">{{ number_format(($stats['total_paid'] ?? 0) / 1000, 1) }}K</span>
                        <span class="d-none d-sm-inline">{{ number_format($stats['total_paid'] ?? 0, 0) }}</span>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-2 p-sm-3">
                    <h6 class="text-muted small mb-1">Collection Rate</h6>
                    <h3 class="fw-bold text-info mb-0 fs-4 fs-md-3">{{ $stats['collection_rate'] ?? 0 }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="d-none d-sm-table-cell">#</th>
                            <th style="min-width: 100px;">Student</th>
                            <th class="d-none d-md-table-cell">Admission</th>
                            <th class="d-none d-lg-table-cell">Email</th>
                            <th class="d-none d-xl-table-cell">Phone</th>
                            <th class="d-none d-sm-table-cell">Total Fees</th>
                            <th class="d-none d-sm-table-cell">Paid</th>
                            <th>Balance</th>
                            <th style="min-width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2 flex-shrink-0 d-none d-sm-flex">
                                            {{ strtoupper(substr($student->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-truncate" style="max-width: 120px;">
                                            {{ $student->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-light text-dark">
                                        {{ $student->admission_number ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="d-none d-lg-table-cell text-truncate" style="max-width: 150px;">
                                    {{ $student->email ?? 'N/A' }}
                                </td>
                                <td class="d-none d-xl-table-cell">{{ $student->phone ?? 'N/A' }}</td>
                                <td class="d-none d-sm-table-cell text-nowrap" style="font-size: 0.85rem;">
                                    KES {{ number_format($student->total_fees ?? 0, 0) }}
                                </td>
                                <td class="d-none d-sm-table-cell text-success text-nowrap" style="font-size: 0.85rem;">
                                    KES {{ number_format($student->total_paid ?? 0, 0) }}
                                </td>
                                <td class="text-danger text-nowrap" style="font-size: 0.85rem;">
                                    <span class="d-sm-none">KES </span>
                                    {{ number_format($student->outstanding_balance ?? 0, 0) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $student->status_color ?? 'secondary' }} status-badge">
                                        {{ ucfirst($student->status ?? 'N/A') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No students enrolled in this course</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Avatar */
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
    
    /* Responsive Table */
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        color: #6c757d;
        background: #f8f9fa;
        padding: 10px 8px;
        white-space: nowrap;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table td {
        padding: 10px 8px;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    
    /* Status Badge */
    .status-badge {
        padding: 4px 8px;
        font-size: 0.7rem;
        border-radius: 4px;
        white-space: nowrap;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .table th {
            font-size: 0.65rem;
            padding: 6px 4px;
        }
        
        .table td {
            font-size: 0.75rem;
            padding: 6px 4px;
        }
        
        .status-badge {
            font-size: 0.6rem;
            padding: 2px 6px;
        }
        
        .avatar {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }
    }
    
    @media (min-width: 576px) and (max-width: 767px) {
        .table th {
            font-size: 0.7rem;
            padding: 8px 6px;
        }
        
        .table td {
            font-size: 0.8rem;
            padding: 8px 6px;
        }
    }
    
    /* Statistics Cards Responsive */
    @media (max-width: 576px) {
        .card-body {
            padding: 0.5rem !important;
        }
        
        .card-body h6 {
            font-size: 0.65rem !important;
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
    
    /* Badge Responsive */
    .badge {
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    
    @media (max-width: 576px) {
        .badge {
            font-size: 0.65rem;
            padding: 3px 6px;
        }
    }
    
    /* Text truncation for long content */
    .text-truncate {
        max-width: 100%;
        display: inline-block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Make table scrollable on very small screens */
    @media (max-width: 480px) {
        .table-responsive {
            border: 0;
        }
    }
    
    /* Hover effect for table rows */
    .table-hover tbody tr:hover {
        background-color: rgba(108, 140, 255, 0.04);
        cursor: pointer;
    }
    
    /* Number formatting for small screens */
    .text-nowrap {
        white-space: nowrap;
    }
</style>

<script>
    // Optional: Add tooltips for truncated content
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.text-truncate');
        elements.forEach(el => {
            if (el.scrollWidth > el.clientWidth) {
                el.setAttribute('data-bs-toggle', 'tooltip');
                el.setAttribute('data-bs-placement', 'top');
                el.setAttribute('title', el.textContent);
            }
        });
    });
</script>
@endsection