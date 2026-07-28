@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Dashboard Overview</h2>
            <p class="text-muted small">Welcome back! Here's what's happening with your school today.</p>
        </div>
        <div class="mt-2 mt-sm-0">
            <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('F d, Y') }}
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Students</p>
                            <h3 class="fw-bold mb-0">{{ $totalStudents ?? 0 }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i> {{ $studentGrowth ?? 0 }}%
                            </small>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-user-graduate text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Courses</p>
                            <h3 class="fw-bold mb-0">{{ $totalCourses ?? 0 }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i> {{ $courseGrowth ?? 0 }}%
                            </small>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-book text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Revenue</p>
                            <h3 class="fw-bold mb-0">KES {{ number_format($totalRevenue ?? 0, 2) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i> {{ $revenueGrowth ?? 0 }}%
                            </small>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-money-bill-wave text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-sm-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Pending Fees</p>
                            <h3 class="fw-bold mb-0">KES {{ number_format($pendingFees ?? 0, 2) }}</h3>
                            <small class="text-danger">
                                <i class="fas fa-arrow-up me-1"></i> {{ $pendingGrowth ?? 0 }}%
                            </small>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-2 mb-sm-0">Enrollment Trends</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary active" onclick="updateChart('daily')">Daily</button>
                        <button class="btn btn-outline-secondary" onclick="updateChart('yearly')">Yearly</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-2 mb-sm-0">Fee Collection Overview</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary active" onclick="updateFeeChart('daily')">Daily</button>
                        <button class="btn btn-outline-secondary" onclick="updateFeeChart('yearly')">Yearly</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="feeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Fee Summary -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="fw-bold mb-0">Fee Collection Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">Total Fees Collected</span>
                        <span class="fw-bold text-success">KES {{ number_format($totalCollected ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">Pending Fees</span>
                        <span class="fw-bold text-danger">KES {{ number_format($pendingFeesTotal ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">Overdue Fees</span>
                        <span class="fw-bold text-warning">KES {{ number_format($overdueFeesTotal ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Collection Rate</span>
                        <span class="fw-bold text-primary">{{ $collectionRate ?? 0 }}%</span>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $collectionRate ?? 0 }}%" 
                                 aria-valuenow="{{ $collectionRate ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex flex-wrap justify-content-between">
                            <span class="text-muted small">Paid: {{ $paidCount ?? 0 }}</span>
                            <span class="text-muted small">Pending: {{ $pendingCount ?? 0 }}</span>
                            <span class="text-muted small">Overdue: {{ $overdueCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="fw-bold mb-0">Student Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">Active Students</span>
                        <span class="fw-bold text-success">{{ $activeStudents ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">Inactive Students</span>
                        <span class="fw-bold text-danger">{{ $inactiveStudents ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted">Pending Students</span>
                        <span class="fw-bold text-warning">{{ $pendingStudents ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Graduated</span>
                        <span class="fw-bold text-info">{{ $graduatedStudents ?? 0 }}</span>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $totalStudents > 0 ? round(($activeStudents ?? 0) / max($totalStudents, 1) * 100, 1) : 0 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Active</small>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $totalStudents > 0 ? round(($pendingStudents ?? 0) / max($totalStudents, 1) * 100, 1) : 0 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $totalStudents > 0 ? round(($graduatedStudents ?? 0) / max($totalStudents, 1) * 100, 1) : 0 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Graduated</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="fw-bold mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('fees.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i> Add New Fee
                        </a>
                        <a href="{{ route('fees.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i> View All Fees
                        </a>
                        <a href="{{ route('students.create') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-plus me-2"></i> Add New Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exams & Reports Section -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-2 mb-sm-0">
                        <i class="fas fa-pencil-alt text-primary me-2"></i>Exams Overview
                    </h5>
                    <a href="{{ route('exams.index') }}" class="btn btn-sm btn-primary">Manage Exams</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <h6 class="text-muted mb-1">Upcoming Exams</h6>
                                <h3 class="fw-bold text-primary mb-0">{{ $upcomingExams ?? 0 }}</h3>
                                <small class="text-muted">Next: {{ $nextExamDate ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <h6 class="text-muted mb-1">Completed Exams</h6>
                                <h3 class="fw-bold text-success mb-0">{{ $completedExams ?? 0 }}</h3>
                                <small class="text-muted">This semester</small>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            @if(isset($recentExams) && $recentExams->count() > 0)
                                @foreach($recentExams as $exam)
                                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                        <span class="text-muted small">
                                            📝 {{ $exam->subject ?? $exam->name ?? 'Untitled' }}
                                            @if(isset($exam->exam_date))
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}</small>
                                            @endif
                                        </span>
                                        <span class="badge bg-{{ $exam->status == 'upcoming' ? 'warning' : ($exam->status == 'completed' ? 'success' : ($exam->status == 'ongoing' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($exam->status ?? 'Upcoming') }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-3 text-muted">
                                    <small>No exams scheduled</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
            <h5 class="fw-bold mb-2 mb-sm-0">
                <i class="fas fa-chart-bar text-info me-2"></i>Reports
            </h5>
            <a href="{{ route('reports.dashboard') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-6 col-md-4">
                    <a href="{{ route('reports.student-statement') }}" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 text-center hover-card">
                            <i class="fas fa-user-graduate fa-2x text-primary mb-2"></i>
                            <h6 class="mb-0 small">Student Statement</h6>
                            <small class="text-muted">{{ $reportStats['student_reports'] ?? 0 }}</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4">
                    <a href="{{ route('reports.fee-collection') }}" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 text-center hover-card">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <h6 class="mb-0 small">Fee Collection</h6>
                            <small class="text-muted">{{ $reportStats['fee_reports'] ?? 0 }}</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4">
                    <a href="{{ route('reports.fee-summary') }}" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 text-center hover-card">
                            <i class="fas fa-chart-pie fa-2x text-warning mb-2"></i>
                            <h6 class="mb-0 small">Fee Summary</h6>
                            <small class="text-muted">Overview</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4">
                    <a href="{{ route('reports.course-revenue') }}" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 text-center hover-card">
                            <i class="fas fa-trophy fa-2x text-danger mb-2"></i>
                            <h6 class="mb-0 small">Course Revenue</h6>
                            <small class="text-muted">{{ $reportStats['performance_reports'] ?? 0 }}</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4">
                    <a href="{{ route('reports.daily-collection') }}" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 text-center hover-card">
                            <i class="fas fa-calendar-day fa-2x text-info mb-2"></i>
                            <h6 class="mb-0 small">Daily Collection</h6>
                            <small class="text-muted">Today</small>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4">
                    <a href="{{ route('reports.outstanding-balances') }}" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 text-center hover-card">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                            <h6 class="mb-0 small">Outstanding</h6>
                            <small class="text-muted">Balances</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- Recent Students & Fee Payments -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-2 mb-sm-0">Recent Students</h5>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">#</th>
                                    <th class="border-0 py-2">Name</th>
                                    <th class="border-0 py-2 d-none d-sm-table-cell">Course</th>
                                    <th class="border-0 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents ?? [] as $student)
                                    <tr>
                                        <td class="ps-3">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="fw-semibold">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $student->course->course_name ?? $student->course->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $student->status ?? 'active';
                                                $statusColor = $status == 'active' ? 'success' : ($status == 'inactive' ? 'danger' : 'warning');
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No recent students</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-2 mb-sm-0">Recent Fee Payments</h5>
                    <a href="{{ route('fees.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">Student</th>
                                    <th class="border-0 py-2">Amount</th>
                                    <th class="border-0 py-2 d-none d-sm-table-cell">Receipt</th>
                                    <th class="border-0 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments ?? [] as $payment)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    {{ strtoupper(substr($payment->student->first_name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="fw-semibold">{{ $payment->student->first_name ?? '' }} {{ $payment->student->last_name ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-success">KES {{ number_format($payment->amount ?? 0, 2) }}</td>
                                        <td class="d-none d-sm-table-cell">
                                            <span class="badge bg-dark">{{ $payment->receipt_no ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $status = $payment->status ?? 'pending';
                                                $statusColor = $status == 'paid' ? 'success' : ($status == 'pending' ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No recent payments</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Status Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-2 mb-sm-0">Fee Status Distribution</h5>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Paid
                        </span>
                        <span class="badge bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Pending
                        </span>
                        <span class="badge bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Overdue
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <div class="fee-status-card text-center p-3 border rounded-3">
                                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                    <h6 class="text-muted mb-0">Paid Fees</h6>
                                </div>
                                <h3 class="text-success fw-bold mb-0">KES {{ number_format($paidFeesTotal ?? 0, 2) }}</h3>
                                <small class="text-muted">{{ $paidCount ?? 0 }} payments</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $paidPercentage ?? 0 }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $paidPercentage ?? 0 }}% of total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="fee-status-card text-center p-3 border rounded-3">
                                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                    <h6 class="text-muted mb-0">Pending Fees</h6>
                                </div>
                                <h3 class="text-warning fw-bold mb-0">KES {{ number_format($pendingFeesTotal ?? 0, 2) }}</h3>
                                <small class="text-muted">{{ $pendingCount ?? 0 }} payments</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $pendingPercentage ?? 0 }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $pendingPercentage ?? 0 }}% of total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="fee-status-card text-center p-3 border rounded-3">
                                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                    <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                                    <h6 class="text-muted mb-0">Overdue Fees</h6>
                                </div>
                                <h3 class="text-danger fw-bold mb-0">KES {{ number_format($overdueFeesTotal ?? 0, 2) }}</h3>
                                <small class="text-muted">{{ $overdueCount ?? 0 }} payments</small>
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: {{ $overduePercentage ?? 0 }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $overduePercentage ?? 0 }}% of total</small>
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
    .dashboard-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 5px;
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
    
    .card {
        border-radius: 12px !important;
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .fee-status-card {
        transition: all 0.3s ease;
        background: #f8f9fa;
        border-color: #e9ecef !important;
    }
    
    .fee-status-card:hover {
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .hover-card:hover {
        background: #ffffff !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transform: translateY(-3px);
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 12px;
        border-radius: 6px;
    }
    
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.78rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .btn-group-sm .btn {
        padding: 3px 10px;
        font-size: 0.75rem;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .dashboard-content {
            padding: 0;
        }
    }
    
    @media (max-width: 768px) {
        .dashboard-content {
            padding: 0;
        }
        
        .card:hover {
            transform: none !important;
        }
        
        .fee-status-card {
            margin-bottom: 10px;
        }
        
        .fee-status-card:hover {
            transform: none !important;
        }
        
        .table-responsive {
            font-size: 0.85rem;
        }
        
        .btn-group-sm {
            flex-wrap: wrap;
        }
        
        .btn-group-sm .btn {
            font-size: 0.7rem;
            padding: 2px 8px;
        }
    }
    
    @media (max-width: 576px) {
        .dashboard-content {
            padding: 0;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-body .p-3 {
            padding: 0.75rem !important;
        }
        
        .table-responsive {
            font-size: 0.75rem;
        }
        
        .table-responsive .badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        
        .fee-status-card .fa-2x {
            font-size: 1.5rem !important;
        }
        
        .fee-status-card h3 {
            font-size: 1.2rem !important;
        }
        
        .fee-status-card h6 {
            font-size: 0.8rem !important;
        }
        
        .btn-group-sm .btn {
            font-size: 0.65rem;
            padding: 2px 6px;
        }
    }
    
    @media (max-width: 400px) {
        .avatar {
            width: 24px;
            height: 24px;
            font-size: 10px;
        }
        
        .table-responsive {
            font-size: 0.7rem;
        }
        
        .table-responsive .badge {
            font-size: 0.6rem;
            padding: 2px 6px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let enrollmentChart, feeChart;

    // Prepare data with safe defaults using PHP
    <?php
        // Default arrays
        $defaultDailyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $defaultDailyEnrollment = [5, 8, 6, 12, 15, 10, 7];
        $defaultDailyTotal = [50, 58, 64, 76, 91, 101, 108];
        $defaultYearlyLabels = ['2019', '2020', '2021', '2022', '2023', '2024', '2025'];
        $defaultYearlyEnrollment = [120, 150, 180, 220, 280, 350, 420];
        $defaultYearlyTotal = [650, 720, 800, 850, 900, 1000, 1150];
        $defaultDailyCollected = [1200, 1500, 1800, 2200, 2800, 1500, 900];
        $defaultDailyPending = [500, 400, 300, 200, 150, 100, 80];
        $defaultYearlyCollected = [50000, 65000, 72000, 85000, 92000, 105000, 120000];
        $defaultYearlyPending = [20000, 18000, 15000, 12000, 10000, 8000, 6000];
    ?>

    const chartData = {
        dailyLabels: <?php echo json_encode($dailyLabels ?? $defaultDailyLabels); ?>,
        dailyEnrollment: <?php echo json_encode($dailyEnrollment ?? $defaultDailyEnrollment); ?>,
        dailyTotal: <?php echo json_encode($dailyTotal ?? $defaultDailyTotal); ?>,
        yearlyLabels: <?php echo json_encode($yearlyLabels ?? $defaultYearlyLabels); ?>,
        yearlyEnrollment: <?php echo json_encode($yearlyEnrollment ?? $defaultYearlyEnrollment); ?>,
        yearlyTotal: <?php echo json_encode($yearlyTotal ?? $defaultYearlyTotal); ?>,
        dailyCollected: <?php echo json_encode($dailyCollected ?? $defaultDailyCollected); ?>,
        dailyPending: <?php echo json_encode($dailyPending ?? $defaultDailyPending); ?>,
        yearlyCollected: <?php echo json_encode($yearlyCollected ?? $defaultYearlyCollected); ?>,
        yearlyPending: <?php echo json_encode($yearlyPending ?? $defaultYearlyPending); ?>
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        if (enrollmentCtx) {
            enrollmentChart = new Chart(enrollmentCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartData.dailyLabels,
                    datasets: [{
                        label: 'New Enrollments',
                        data: chartData.dailyEnrollment,
                        borderColor: '#6c8cff',
                        backgroundColor: 'rgba(108, 140, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }, {
                        label: 'Total Students',
                        data: chartData.dailyTotal,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: { font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Fee Collection Chart
        const feeCtx = document.getElementById('feeChart');
        if (feeCtx) {
            feeChart = new Chart(feeCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.dailyLabels,
                    datasets: [{
                        label: 'Collected',
                        data: chartData.dailyCollected,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: '#28a745',
                        borderWidth: 1
                    }, {
                        label: 'Pending',
                        data: chartData.dailyPending,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: '#ffc107',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                font: { size: 11 },
                                callback: function(value) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }
    });

    function updateChart(type) {
        if (!enrollmentChart) return;
        
        const data = {
            daily: {
                labels: chartData.dailyLabels,
                new: chartData.dailyEnrollment,
                total: chartData.dailyTotal
            },
            yearly: {
                labels: chartData.yearlyLabels,
                new: chartData.yearlyEnrollment,
                total: chartData.yearlyTotal
            }
        };

        const selected = data[type] || data.daily;
        
        enrollmentChart.data.labels = selected.labels;
        enrollmentChart.data.datasets[0].data = selected.new;
        enrollmentChart.data.datasets[1].data = selected.total;
        enrollmentChart.update();

        // Update button states
        const buttons = document.querySelector('#enrollmentChart').closest('.card').querySelectorAll('.btn-group .btn');
        buttons.forEach(btn => {
            btn.classList.remove('active', 'btn-outline-primary', 'btn-outline-secondary');
            if (btn.textContent.toLowerCase() === type) {
                btn.classList.add('active', 'btn-outline-primary');
            } else {
                btn.classList.add('btn-outline-secondary');
            }
        });
    }

    function updateFeeChart(type) {
        if (!feeChart) return;
        
        const data = {
            daily: {
                labels: chartData.dailyLabels,
                collected: chartData.dailyCollected,
                pending: chartData.dailyPending
            },
            yearly: {
                labels: chartData.yearlyLabels,
                collected: chartData.yearlyCollected,
                pending: chartData.yearlyPending
            }
        };

        const selected = data[type] || data.daily;
        
        feeChart.data.labels = selected.labels;
        feeChart.data.datasets[0].data = selected.collected;
        feeChart.data.datasets[1].data = selected.pending;
        feeChart.update();

        // Update button states
        const buttons = document.querySelector('#feeChart').closest('.card').querySelectorAll('.btn-group .btn');
        buttons.forEach(btn => {
            btn.classList.remove('active', 'btn-outline-primary', 'btn-outline-secondary');
            if (btn.textContent.toLowerCase() === type) {
                btn.classList.add('active', 'btn-outline-primary');
            } else {
                btn.classList.add('btn-outline-secondary');
            }
        });
    }
</script>

@endsection