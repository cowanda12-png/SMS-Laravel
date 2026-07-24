@extends('layouts.app') 
@section('title') 
Dashboard 
@endsection 

@section('content') 
<div class="dashboard-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Dashboard Overview</h2>
            <p class="text-muted small">Welcome back! Here's what's happening with your school today.</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('F d, Y') }}
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
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
                <div class="card-body p-3">
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
                <div class="card-body p-3">
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
                <div class="card-body p-3">
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
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Enrollment Trends</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="updateChart('weekly')">Weekly</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateChart('monthly')">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Fee Collection Overview</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="updateFeeChart('weekly')">Weekly</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateFeeChart('monthly')">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="feeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Fee Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Student Fee Status</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success">Paid</span>
                        <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger">Overdue</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($studentsWithFees ?? [] as $student)
                            @php
                                $totalFees = $student->fees()->sum('amount') ?? 0;
                                $paidFees = $student->fees()->where('status', 'paid')->sum('amount') ?? 0;
                                $pendingFees = $student->fees()->where('status', 'pending')->sum('amount') ?? 0;
                                $overdueFees = $student->fees()->where('status', 'overdue')->sum('amount') ?? 0;
                                $paymentCount = $student->fees()->where('status', 'paid')->count();
                                $paymentPercentage = $totalFees > 0 ? round(($paidFees / $totalFees) * 100, 1) : 0;
                                $statusClass = $overdueFees > 0 ? 'danger' : ($pendingFees > 0 ? 'warning' : 'success');
                                $statusText = $overdueFees > 0 ? 'Overdue' : ($pendingFees > 0 ? 'Pending' : 'Paid');
                                $lastPayment = $student->fees()->where('status', 'paid')->latest('payment_date')->first();
                            @endphp
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="fee-card card border-0 shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar-lg me-2">
                                                {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-0">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h6>
                                                <small class="text-muted">{{ $student->admission_number ?? 'N/A' }}</small>
                                            </div>
                                            <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                        
                                        <div class="fee-stats">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Total Fees</span>
                                                <span class="fw-bold">KES {{ number_format($totalFees, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Paid</span>
                                                <span class="fw-bold text-success">KES {{ number_format($paidFees, 2) }}</span>
                                            </div>
                                            @if($pendingFees > 0)
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted small">Pending</span>
                                                    <span class="fw-bold text-warning">KES {{ number_format($pendingFees, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($overdueFees > 0)
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted small">Overdue</span>
                                                    <span class="fw-bold text-danger">KES {{ number_format($overdueFees, 2) }}</span>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Payments</span>
                                                <span class="fw-bold">{{ $paymentCount }}</span>
                                            </div>
                                            <div class="mt-2">
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $paymentPercentage }}%"></div>
                                                    @if($pendingFees > 0)
                                                        <div class="progress-bar bg-warning" role="progressbar" 
                                                             style="width: {{ $totalFees > 0 ? round(($pendingFees / $totalFees) * 100, 1) : 0 }}%"></div>
                                                    @endif
                                                    @if($overdueFees > 0)
                                                        <div class="progress-bar bg-danger" role="progressbar" 
                                                             style="width: {{ $totalFees > 0 ? round(($overdueFees / $totalFees) * 100, 1) : 0 }}%"></div>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $paymentPercentage }}% paid</small>
                                            </div>
                                            @if($lastPayment)
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-receipt me-1"></i> Last: 
                                                        <strong>{{ $lastPayment->receipt_no ?? 'N/A' }}</strong>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-alt me-1"></i> 
                                                        {{ $lastPayment->payment_date ? $lastPayment->payment_date->format('d M Y') : 'N/A' }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2 d-flex gap-1">
                                            <a href="{{ route('fees.create', ['student_id' => $student->id]) }}" 
                                               class="btn btn-sm btn-primary flex-grow-1">
                                                <i class="fas fa-plus me-1"></i> Add Fee
                                            </a>
                                            <a href="{{ route('fees.index', ['student_id' => $student->id]) }}" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No students with fees found</h6>
                                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Add Student
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
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
                        <div class="d-flex justify-content-between">
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
                        <div class="d-flex gap-2">
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $totalStudents > 0 ? round(($activeStudents ?? 0) / $totalStudents * 100, 1) : 0 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Active</small>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $totalStudents > 0 ? round(($pendingStudents ?? 0) / $totalStudents * 100, 1) : 0 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $totalStudents > 0 ? round(($graduatedStudents ?? 0) / $totalStudents * 100, 1) : 0 }}%">
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
                        <a href="{{ route('fees.report') }}" class="btn btn-outline-success">
                            <i class="fas fa-file-pdf me-2"></i> Generate Report
                        </a>
                        <a href="{{ route('students.create') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-plus me-2"></i> Add New Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Students & Fee Payments -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Recent Students</h5>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">#</th>
                                    <th class="border-0 py-2">Name</th>
                                    <th class="border-0 py-2">Course</th>
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
                                        <td>
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
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Recent Fee Payments</h5>
                    <a href="{{ route('fees.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-2 ps-3">Student</th>
                                    <th class="border-0 py-2">Amount</th>
                                    <th class="border-0 py-2">Receipt</th>
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
                                        <td>
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
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Fee Status Distribution</h5>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success me-1">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Paid
                        </span>
                        <span class="badge bg-warning bg-opacity-10 text-warning me-1">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Pending
                        </span>
                        <span class="badge bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Overdue
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
    
    .avatar-lg {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
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
    
    .fee-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    
    .fee-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
        border-color: rgba(108, 140, 255, 0.2) !important;
    }
    
    .fee-stats {
        background: #f8f9fa;
        padding: 10px 12px;
        border-radius: 8px;
        margin: 8px 0;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 12px;
        border-radius: 6px;
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
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.78rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .btn-outline-primary:hover {
        background: #6c8cff;
        color: white;
    }
    
    .btn-outline-success:hover {
        background: #28a745;
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-content {
            padding: 10px;
        }
        
        .card:hover {
            transform: none !important;
        }
        
        .fee-status-card {
            margin-bottom: 10px;
        }
        
        .fee-card {
            margin-bottom: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .btn-group {
            flex-direction: column;
            gap: 5px;
        }
        
        .btn-group .btn {
            width: 100%;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let enrollmentChart, feeChart;

    document.addEventListener('DOMContentLoaded', function() {
        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
        
        enrollmentChart = new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'New Enrollments',
                    data: [12, 19, 15, 22, 28, 35, 42],
                    borderColor: '#6c8cff',
                    backgroundColor: 'rgba(108, 140, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4
                }, {
                    label: 'Total Students',
                    data: [65, 72, 80, 85, 90, 100, 115],
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

        // Fee Collection Chart
        const feeCtx = document.getElementById('feeChart').getContext('2d');
        
        feeChart = new Chart(feeCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Collected',
                    data: [5000, 6500, 7200, 8500, 9200, 10500, 12000],
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: '#28a745',
                    borderWidth: 1
                }, {
                    label: 'Pending',
                    data: [2000, 1800, 1500, 1200, 1000, 800, 600],
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
    });

    function updateChart(type) {
        const data = {
            weekly: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                new: [5, 8, 6, 12, 15, 10, 7],
                total: [50, 58, 64, 76, 91, 101, 108]
            },
            monthly: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                new: [12, 19, 15, 22, 28, 35, 42],
                total: [65, 72, 80, 85, 90, 100, 115]
            }
        };

        const selected = data[type];
        
        enrollmentChart.data.labels = selected.labels;
        enrollmentChart.data.datasets[0].data = selected.new;
        enrollmentChart.data.datasets[1].data = selected.total;
        enrollmentChart.update();
    }

    function updateFeeChart(type) {
        const data = {
            weekly: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                collected: [1200, 1500, 1800, 2200, 2800, 1500, 900],
                pending: [500, 400, 300, 200, 150, 100, 80]
            },
            monthly: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                collected: [5000, 6500, 7200, 8500, 9200, 10500, 12000],
                pending: [2000, 1800, 1500, 1200, 1000, 800, 600]
            }
        };

        const selected = data[type];
        
        feeChart.data.labels = selected.labels;
        feeChart.data.datasets[0].data = selected.collected;
        feeChart.data.datasets[1].data = selected.pending;
        feeChart.update();
    }
</script>

@endsection