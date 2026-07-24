@extends('layouts.app')

@section('title', 'Student List')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Student List</h1>
            <p class="text-muted small">Manage all students in the system</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('students.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-export me-1"></i> Export
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add New Student
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
        <div class="card-body">
            <form action="{{ route('students.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Search by name, email, or admission number..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="course" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}" 
                                    {{ request('course') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name ?? $course->name ?? 'Course' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
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
                                <th>Admission No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Course</th>
                                <th>Fees</th>
                                <th>Last Payment</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    // Calculate fee statistics
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
                                @endphp
                                <tr>
                                    <td class="ps-3">{{ $students->firstItem() + $loop->index }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark fw-normal">
                                            {{ $student->admission_number ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                {{ strtoupper(substr($student->first_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-semibold">
                                                    {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}
                                                </span>
                                                <br>
                                                <small class="text-muted">ID: #{{ $student->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.85rem;">
                                            {{ $student->email ?? 'No email' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($student->phone)
                                            <span class="badge bg-light text-dark fw-normal">
                                                <i class="fas fa-phone me-1"></i> {{ $student->phone }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.85rem;">N/A</span>
                                        @endif
                                    </td>
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
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">Total:</span>
                                                <strong class="text-primary">KES {{ number_format($totalFees, 2) }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">Paid:</span>
                                                <strong class="text-success">KES {{ number_format($paidFees, 2) }}</strong>
                                            </div>
                                            @if($pendingFees > 0)
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted small">Pending:</span>
                                                    <strong class="text-warning">KES {{ number_format($pendingFees, 2) }}</strong>
                                                </div>
                                            @endif
                                            @if($overdueFees > 0)
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted small">Overdue:</span>
                                                    <strong class="text-danger">KES {{ number_format($overdueFees, 2) }}</strong>
                                                </div>
                                            @endif
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
                                            <small class="text-muted">{{ $paymentCount }} payments • {{ $paymentPercentage }}% paid</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($lastPayment)
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold text-success">
                                                    KES {{ number_format($lastAmount, 2) }}
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ $lastPayment->payment_date ? $lastPayment->payment_date->format('d M Y') : 'N/A' }}
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-receipt me-1"></i>
                                                    Receipt: <strong>{{ $lastReceipt }}</strong>
                                                </span>
                                                @if($lastPayment->payment_method)
                                                    <span class="badge bg-{{ $lastPayment->payment_method == 'M-Pesa' || $lastPayment->payment_method == 'Mpesa' ? 'success' : 'info' }} bg-opacity-10 text-{{ $lastPayment->payment_method == 'M-Pesa' || $lastPayment->payment_method == 'Mpesa' ? 'success' : 'info' }} small mt-1">
                                                        <i class="fas {{ $lastPayment->payment_method == 'M-Pesa' || $lastPayment->payment_method == 'Mpesa' ? 'fa-mobile-alt' : 'fa-money-bill-wave' }} me-1"></i>
                                                        {{ $lastPayment->payment_method }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No payments</span>
                                        @endif
                                    </td>
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
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="padding: 6px 12px;">
                                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                                {{ ucfirst($status) }}
                                            </span>
                                            @if($hasOutstanding)
                                                <span class="badge bg-danger bg-opacity-10 text-danger" style="padding: 4px 8px; font-size: 0.65rem;">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Outstanding Balance
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                                            <a href="{{ route('students.show', $student->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="View Student">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('students.edit', $student->id) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Edit Student">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('fees.create', ['student_id' => $student->id]) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Record Payment">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </a>
                                            <button onclick="confirmDelete({{ $student->id }})" 
                                                    class="btn btn-sm btn-danger" 
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
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                Showing 
                                <strong>{{ $students->firstItem() ?? 0 }}</strong> 
                                to 
                                <strong>{{ $students->lastItem() ?? 0 }}</strong> 
                                of 
                                <strong>{{ $students->total() ?? $students->count() }}</strong> 
                                students
                            </span>
                        </div>
                        <div>
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
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    /* Button Styling */
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.78rem;
        border-radius: 6px;
        font-weight: 500;
        min-width: 32px;
        transition: all 0.2s ease;
    }
    
    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
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
    
    /* Table Styling */
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 12px 12px;
        border-bottom: 2px solid #e9ecef;
        background: #f8f9fa;
        white-space: nowrap;
    }
    
    .table td {
        padding: 12px 12px;
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
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
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
    }
    
    .progress-bar {
        transition: width 0.6s ease;
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
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
        font-size: 0.85rem;
        padding: 8px 12px;
        border-color: #e9ecef;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    
    .input-group-text {
        background: #f8f9fa;
        border-color: #e9ecef;
    }
    
    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    
    .pagination .page-link {
        color: #6c8cff;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.85rem;
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
    @media (max-width: 991.98px) {
        .table-responsive {
            overflow-x: auto;
        }
        
        .table th,
        .table td {
            white-space: nowrap;
        }
        
        .btn-sm {
            padding: 4px 8px;
            min-width: 28px;
            font-size: 0.7rem;
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
            padding: 4px 10px;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
        }
        
        .d-flex.gap-2 .btn {
            width: 100%;
        }
        
        .table td {
            padding: 8px 8px;
        }
        
        .table th {
            padding: 8px 8px;
            font-size: 0.65rem;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
    }
</style>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
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

<!-- SweetAlert2 for better confirm dialogs -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
<style>
    /* Additional hover effect for table rows */
    .table-hover tbody tr:hover {
        background: rgba(108, 140, 255, 0.04);
        cursor: default;
    }
    
    /* Smooth transition for card */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
    }
    
    /* Status dot animation */
    .fa-circle {
        display: inline-block;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
    
    /* Progress bar animation */
    .progress-bar {
        transition: width 1s ease-in-out;
    }
</style>
@endpush

@endsection