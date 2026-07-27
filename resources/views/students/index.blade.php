@extends('layouts.app')

@section('title', 'Student List')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Student List</h1>
            <p class="text-muted small">Manage all students in the system</p>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
            
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Add New Student</span>
                <span class="d-inline d-sm-none">Add</span>
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-sm-4">
            <form action="{{ route('students.index') }}" method="GET" class="row g-2 g-sm-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Search..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <select name="course" class="form-select form-select-sm">
                        <option value="">All Courses</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}" 
                                    {{ request('course') == $course->id ? 'selected' : '' }}>
                                {{ Str::limit($course->course_name ?? $course->name ?? 'Course', 20) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">All Payment</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    @if(isset($students) && $students->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">#</th>
                                <th class="d-none d-sm-table-cell">Admission</th>
                                <th>Name</th>
                                <th class="d-none d-md-table-cell">Email</th>
                                <th class="d-none d-lg-table-cell">Phone</th>
                                <th class="d-none d-xl-table-cell">Course</th>
                                <th class="d-none d-xxl-table-cell">Fees</th>
                                <th class="d-none d-xxl-table-cell">Last Payment</th>
                                <th>Status</th>
                                <th class="text-center" style="min-width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $totalFees = $student->fees()->sum('amount') ?? 0;
                                    $paidFees = $student->fees()->where('status', 'paid')->sum('amount') ?? 0;
                                    $pendingFees = $student->fees()->where('status', 'pending')->sum('amount') ?? 0;
                                    $overdueFees = $student->fees()->where('status', 'overdue')->sum('amount') ?? 0;
                                    $paymentCount = $student->fees()->where('status', 'paid')->count();
                                    $lastPayment = $student->fees()->where('status', 'paid')->latest('payment_date')->first();
                                    $lastReceipt = $lastPayment ? $lastPayment->receipt_no : 'N/A';
                                    $lastAmount = $lastPayment ? $lastPayment->amount : 0;
                                    $paymentPercentage = $totalFees > 0 ? round(($paidFees / $totalFees) * 100, 1) : 0;
                                    $hasOutstanding = $pendingFees > 0 || $overdueFees > 0;
                                    $status = $student->status ?? 'active';
                                    $statusColor = match($status) {
                                        'active' => 'success',
                                        'inactive' => 'danger',
                                        'pending' => 'warning',
                                        'graduated' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-3">{{ $students->firstItem() + $loop->index }}</td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge bg-light text-dark fw-normal">
                                            {{ $student->admission_number ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2 d-none d-sm-flex">
                                                {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-semibold" style="font-size: 0.85rem;">
                                                    {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                                                </span>
                                                <br class="d-sm-none">
                                                <small class="text-muted d-sm-none">
                                                    {{ $student->admission_number ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell" style="font-size: 0.82rem;">
                                        {{ Str::limit($student->email ?? 'No email', 20) }}
                                    </td>
                                    <td class="d-none d-lg-table-cell" style="font-size: 0.82rem;">
                                        {{ $student->phone ?? 'N/A' }}
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        @if($student->course)
                                            <span class="badge bg-info bg-opacity-10 text-info" style="font-size: 0.7rem;">
                                                {{ Str::limit($student->course->course_name ?? $student->course->name ?? 'N/A', 15) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7rem;">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td class="d-none d-xxl-table-cell">
                                        <div class="d-flex flex-column gap-1" style="min-width: 100px;">
                                            <div class="d-flex justify-content-between" style="font-size: 0.75rem;">
                                                <span class="text-muted">Total:</span>
                                                <strong class="text-primary">KES {{ number_format($totalFees, 0) }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between" style="font-size: 0.75rem;">
                                                <span class="text-muted">Paid:</span>
                                                <strong class="text-success">KES {{ number_format($paidFees, 0) }}</strong>
                                            </div>
                                            <div class="progress mt-1" style="height: 4px;">
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
                                            <small class="text-muted" style="font-size: 0.6rem;">{{ $paymentPercentage }}% paid</small>
                                        </div>
                                    </td>
                                    <td class="d-none d-xxl-table-cell">
                                        @if($lastPayment)
                                            <div style="font-size: 0.75rem;">
                                                <div class="fw-semibold text-success">
                                                    KES {{ number_format($lastAmount, 0) }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.6rem;">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ $lastPayment->payment_date ? $lastPayment->payment_date->format('d M Y') : 'N/A' }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size: 0.75rem;">No payments</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="font-size: 0.65rem; padding: 4px 8px;">
                                                <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                                                {{ ucfirst($status) }}
                                            </span>
                                            @if($hasOutstanding)
                                                <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 0.55rem; padding: 2px 6px;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Balance
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                                            <a href="{{ route('students.show', $student->id) }}" 
                                               class="btn btn-sm btn-primary action-btn" 
                                               title="View Student">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.edit', $student->id) }}" 
                                               class="btn btn-sm btn-success action-btn" 
                                               title="Edit Student">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('fees.create', ['student_id' => $student->id]) }}" 
                                               class="btn btn-sm btn-warning action-btn" 
                                               title="Add Payment">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </a>
                                            <button onclick="confirmDelete({{ $student->id }})" 
                                                    class="btn btn-sm btn-danger action-btn" 
                                                    title="Delete Student">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $student->id }}" 
                                                  action="{{ route('students.destroy', $student->id) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            @if(method_exists($students, 'links'))
                <div class="card-footer bg-transparent border-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                Showing 
                                <strong>{{ $students->firstItem() ?? 0 }}</strong> 
                                to 
                                <strong>{{ $students->lastItem() ?? 0 }}</strong> 
                                of 
                                <strong>{{ $students->total() ?? $students->count() }}</strong> 
                                students
                            </span>
                        </div>
                        <div class="pagination-wrapper">
                            {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="fas fa-user-graduate" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 fw-bold">No Students Found</h4>
                <p class="text-muted">Click the "Add New Student" button to get started.</p>
                <a href="{{ route('students.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Student
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    /* Avatar Styling */
    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
        flex-shrink: 0;
    }
    
    /* Action Buttons */
    .action-btn {
        padding: 4px 6px;
        font-size: 0.7rem;
        border-radius: 6px;
        min-width: 28px;
        min-height: 28px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .action-btn i {
        font-size: 0.75rem;
    }
    
    /* Button Colors */
    .btn-primary {
        background: #6c8cff;
        border: none;
    }
    .btn-primary:hover {
        background: #5a7ae6;
    }
    
    .btn-success {
        background: #28a745;
        border: none;
    }
    .btn-success:hover {
        background: #218838;
    }
    
    .btn-warning {
        background: #ffc107;
        border: none;
        color: #212529;
    }
    .btn-warning:hover {
        background: #e0a800;
        color: #212529;
    }
    
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    
    .btn-secondary {
        background: #6c757d;
        border: none;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    /* Table Styling */
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 10px 10px;
        border-bottom: 2px solid #e9ecef;
        background: #f8f9fa;
        white-space: nowrap;
    }
    
    .table td {
        padding: 10px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .table tbody tr {
        transition: all 0.15s ease;
    }
    
    .table tbody tr:hover {
        background: rgba(108, 140, 255, 0.04);
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Badge Styling */
    .badge {
        font-weight: 500;
        border-radius: 6px;
    }
    
    .badge.bg-light {
        background: #f8f9fa !important;
        border: 1px solid #e9ecef;
    }
    
    /* Progress Bar */
    .progress {
        border-radius: 4px;
        background-color: #e9ecef;
        overflow: hidden;
        height: 4px;
    }
    
    .progress-bar {
        transition: width 0.6s ease;
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
        padding: 10px 15px;
    }
    
    .alert-success {
        border-left-color: #28a745;
        background: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
        background: #f8d7da;
        color: #721c24;
    }
    
    /* Card Styling */
    .card {
        border-radius: 12px !important;
        overflow: hidden;
        border: none !important;
    }
    
    .card-body {
        padding: 0;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
        background: transparent;
    }
    
    /* Form Styling */
    .form-control, .form-select {
        border-radius: 6px;
        font-size: 0.8rem;
        padding: 6px 10px;
        border-color: #e9ecef;
        height: 36px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    
    .form-select-sm {
        height: 36px;
        padding: 4px 24px 4px 10px;
        font-size: 0.78rem;
    }
    
    .input-group-text {
        background: #f8f9fa;
        border-color: #e9ecef;
        padding: 6px 10px;
        font-size: 0.8rem;
    }
    
    /* Pagination Styling */
    .pagination-wrapper {
        overflow-x: auto;
        max-width: 100%;
    }
    
    .pagination {
        margin-bottom: 0;
        gap: 3px;
        flex-wrap: nowrap;
    }
    
    .pagination .page-link {
        color: #6c8cff;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.8rem;
        background: transparent;
    }
    
    .pagination .page-link:hover {
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        transform: translateY(-1px);
    }
    
    .pagination .page-item.active .page-link {
        background: #6c8cff;
        color: white;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(108, 140, 255, 0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #dee2e6;
        background: transparent;
        cursor: not-allowed;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        border-radius: 6px;
    }
    
    /* Responsive Styling */
    @media (max-width: 1199.98px) {
        .table th,
        .table td {
            padding: 8px 6px;
        }
    }
    
    @media (max-width: 991.98px) {
        .table th,
        .table td {
            padding: 6px 5px;
            font-size: 0.8rem;
        }
        
        .action-btn {
            padding: 3px 5px;
            min-width: 24px;
            min-height: 24px;
            font-size: 0.6rem;
        }
        
        .action-btn i {
            font-size: 0.65rem;
        }
        
        .badge {
            font-size: 0.6rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .container-fluid {
            padding: 0.5rem !important;
        }
        
        .card-footer .d-flex {
            flex-direction: column;
            align-items: center !important;
            gap: 10px;
        }
        
        .pagination .page-link {
            padding: 3px 8px;
            font-size: 0.7rem;
        }
        
        .table td {
            padding: 5px 4px;
            font-size: 0.7rem;
        }
        
        .table th {
            padding: 5px 4px;
            font-size: 0.55rem;
        }
        
        .action-btn {
            padding: 2px 4px;
            min-width: 20px;
            min-height: 20px;
            font-size: 0.55rem;
        }
        
        .action-btn i {
            font-size: 0.55rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .d-flex.gap-2 {
            gap: 0.25rem !important;
        }
        
        .table td {
            padding: 4px 3px;
            font-size: 0.65rem;
        }
        
        .table th {
            padding: 4px 3px;
            font-size: 0.5rem;
            letter-spacing: 0.3px;
        }
        
        .action-btn {
            padding: 2px 3px;
            min-width: 18px;
            min-height: 18px;
            font-size: 0.5rem;
            border-radius: 4px;
        }
        
        .action-btn i {
            font-size: 0.5rem;
        }
        
        .badge {
            font-size: 0.5rem;
            padding: 2px 5px;
        }
        
        .btn-sm {
            font-size: 0.65rem;
            padding: 3px 6px;
        }
        
        .form-control, .form-select {
            font-size: 0.7rem;
            padding: 4px 6px;
            height: 30px;
        }
        
        .input-group-text {
            padding: 4px 6px;
            font-size: 0.7rem;
        }
        
        .pagination .page-link {
            padding: 2px 6px;
            font-size: 0.65rem;
        }
        
        .card-footer .text-muted {
            font-size: 0.65rem !important;
        }
    }
    
    @media (max-width: 400px) {
        .table td {
            padding: 3px 2px;
            font-size: 0.55rem;
        }
        
        .table th {
            padding: 3px 2px;
            font-size: 0.45rem;
        }
        
        .action-btn {
            padding: 1px 2px;
            min-width: 16px;
            min-height: 16px;
            font-size: 0.45rem;
            border-radius: 3px;
        }
        
        .action-btn i {
            font-size: 0.45rem;
        }
        
        .avatar {
            width: 20px;
            height: 20px;
            font-size: 8px;
        }
    }
</style>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this student? This action cannot be undone!')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background: rgba(108, 140, 255, 0.04);
        cursor: default;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
    }
    
    .fa-circle {
        display: inline-block;
    }
    
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    /* Scrollable pagination on mobile */
    .pagination-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    
    .pagination-wrapper::-webkit-scrollbar {
        display: none;
    }
</style>
@endpush

@endsection