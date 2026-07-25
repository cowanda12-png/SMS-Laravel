@extends('layouts.app')

@section('title', 'Course Students')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $course->course_name }}</h1>
            <p class="text-muted small">Students enrolled in this course</p>
        </div>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Students</h6>
                    <h3 class="fw-bold">{{ $stats['total_students'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Fees</h6>
                    <h3 class="fw-bold text-primary">KES {{ number_format($stats['total_fees'] ?? 0, 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Paid</h6>
                    <h3 class="fw-bold text-success">KES {{ number_format($stats['total_paid'] ?? 0, 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Collection Rate</h6>
                    <h3 class="fw-bold text-info">{{ $stats['collection_rate'] ?? 0 }}%</h3>
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
                            <th>#</th>
                            <th>Student</th>
                            <th>Admission</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Fees</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                <td>{{ $student->email ?? 'N/A' }}</td>
                                <td>{{ $student->phone ?? 'N/A' }}</td>
                                <td>KES {{ number_format($student->total_fees ?? 0, 0) }}</td>
                                <td class="text-success">KES {{ number_format($student->total_paid ?? 0, 0) }}</td>
                                <td class="text-danger">KES {{ number_format($student->outstanding_balance ?? 0, 0) }}</td>
                                <td>
                                    <span class="badge bg-{{ $student->status_color ?? 'secondary' }}">
                                        {{ ucfirst($student->status ?? 'N/A') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No students enrolled in this course</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection