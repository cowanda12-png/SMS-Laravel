@extends('layouts.app')

@section('title', 'Fee Structure')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Fee Structure
            </h4>
            <p class="text-muted small mb-0">Overview of all student fee payments</p>
        </div>
        <a href="{{ route('fees.create') }}" class="btn btn-primary btn-sm btn-md-lg px-3 px-md-4">
            <i class="fas fa-plus-circle me-1 me-md-2"></i> <span class="d-none d-sm-inline">Record Payment</span>
            <span class="d-inline d-sm-none">Add Fee</span>
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> Please fix the following errors:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-2 g-sm-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Total Collected</p>
                            <h5 class="fw-bold mb-0 fs-6 fs-md-5">KES {{ number_format($totalFees ?? 0, 0) }}</h5>
                        </div>
                        <span class="icon-circle bg-success-subtle text-success">
                            <i class="fas fa-coins"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Today</p>
                            <h5 class="fw-bold mb-0 fs-6 fs-md-5">KES {{ number_format($todayFees ?? 0, 0) }}</h5>
                        </div>
                        <span class="icon-circle bg-primary-subtle text-primary">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Pending ({{ $pendingCount ?? 0 }})</p>
                            <h5 class="fw-bold mb-0 fs-6 fs-md-5">KES {{ number_format($pendingFees ?? 0, 0) }}</h5>
                        </div>
                        <span class="icon-circle bg-warning-subtle text-warning">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Overdue ({{ $overdueCount ?? 0 }})</p>
                            <h5 class="fw-bold mb-0 fs-6 fs-md-5">KES {{ number_format($overdueFees ?? 0, 0) }}</h5>
                        </div>
                        <span class="icon-circle bg-danger-subtle text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2 p-sm-3">
            <form action="{{ route('fees.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold mb-0">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Receipt no, student..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Student</label>
                    <select name="student_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($statuses ?? [] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Fee Type</label>
                    <select name="fee_type" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($feeTypes ?? [] as $type)
                            <option value="{{ $type }}" {{ request('fee_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Term</label>
                    <select name="term" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($terms ?? [] as $term)
                            <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>
                                {{ $term }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Apply filters">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'student_id', 'status', 'fee_type', 'term']))
                        <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-secondary flex-fill" title="Clear filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Fee Records Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if(isset($fees) && $fees->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No fee records found.</p>
                    <a href="{{ route('fees.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Record the first payment
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Receipt</th>
                                <th>Student</th>
                                <th class="text-end">Amount</th>
                                <th class="d-none d-md-table-cell">Method</th>
                                <th class="d-none d-lg-table-cell">Fee Type</th>
                                <th class="d-none d-xl-table-cell">Term</th>
                                <th class="d-none d-sm-table-cell">Date</th>
                                <th>Status</th>
                                <th class="text-end pe-3" style="min-width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees as $fee)
                                @php
                                    // Get student name with fallback
                                    $studentName = $fee->student_name ?? 'N/A';
                                    if ($studentName === 'N/A' || $studentName === 'Unknown Student' || $studentName === 'Student Not Found') {
                                        try {
                                            $student = \App\Models\Students::find($fee->student_id);
                                            $studentName = $student->name ?? 'N/A';
                                        } catch (\Exception $e) {
                                            $studentName = 'N/A';
                                        }
                                    }
                                    
                                    // Get admission number with fallback
                                    $admission = $fee->student_admission ?? 'N/A';
                                    if ($admission === 'N/A') {
                                        try {
                                            $student = \App\Models\Students::find($fee->student_id);
                                            $admission = $student->admission_number ?? 'N/A';
                                        } catch (\Exception $e) {
                                            $admission = 'N/A';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-semibold small">{{ $fee->receipt_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $studentName }}</div>
                                        <div class="text-muted small d-none d-sm-block">{{ $admission }}</div>
                                    </td>
                                    <td class="text-end fw-semibold">KES {{ number_format($fee->amount, 0) }}</td>
                                    <td class="d-none d-md-table-cell">
                                        @if($fee->payment_method)
                                            <span class="badge bg-light text-dark border">
                                                @if(str_contains($fee->payment_method, 'Pesa') || str_contains($fee->payment_method, 'M-Pesa'))
                                                    <i class="fas fa-mobile-alt me-1 text-success"></i>
                                                @endif
                                                {{ $fee->payment_method }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $fee->fee_type ?? '—' }}</td>
                                    <td class="d-none d-xl-table-cell small text-muted">
                                        {{ $fee->term ?? '—' }}
                                        @if($fee->academic_year)
                                            <br><small>{{ $fee->academic_year }}</small>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell small">
                                        {{ $fee->formatted_payment_date ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $fee->status_badge['color'] ?? 'secondary' }}">
                                            <i class="fas {{ $fee->status_badge['icon'] ?? 'fa-circle' }} me-1"></i>
                                            {{ $fee->status_badge['label'] ?? ucfirst($fee->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                                            <a href="{{ route('fees.show', $fee->id) }}" 
                                               class="btn btn-sm btn-primary action-btn" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(isset($fee->status) && $fee->status === 'paid')
                                                <a href="{{ route('fees.receipt', $fee->id) }}" 
                                                   class="btn btn-sm btn-success action-btn" 
                                                   title="Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            @endif
                                            @if(isset($fee->status) && $fee->status !== 'paid')
                                                <a href="{{ route('fees.edit', $fee->id) }}" 
                                                   class="btn btn-sm btn-warning action-btn" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($fees, 'links'))
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-3">
                        <div>
                            <span class="text-muted small">
                                Showing <strong>{{ $fees->firstItem() ?? 0 }}</strong> 
                                to <strong>{{ $fees->lastItem() ?? 0 }}</strong> 
                                of <strong>{{ $fees->total() ?? $fees->count() }}</strong>
                            </span>
                        </div>
                        <div class="pagination-wrapper">
                            {{ $fees->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .icon-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    
    .table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6c757d;
        font-weight: 600;
        padding: 8px 8px;
    }
    
    .table td {
        padding: 8px 8px;
        vertical-align: middle;
    }
    
    .report-link-card {
        transition: all 0.15s ease;
        background: #fff;
        border-color: #e9ecef !important;
    }
    
    .report-link-card:hover {
        border-color: #6c8cff !important;
        background: #f8f9ff;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(108, 140, 255, 0.1);
    }
    
    [data-bs-toggle="collapse"] .fa-chevron-down {
        transition: transform 0.2s ease;
    }
    
    [data-bs-toggle="collapse"][aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }
    
    /* Action Buttons */
    .action-btn {
        padding: 3px 6px;
        font-size: 0.65rem;
        border-radius: 6px;
        min-width: 24px;
        min-height: 24px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    
    .action-btn i {
        font-size: 0.7rem;
    }
    
    /* Badge Styling */
    .badge {
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
    }
    
    .badge.bg-light {
        background: #f8f9fa !important;
        border: 1px solid #e9ecef;
    }
    
    /* Pagination */
    .pagination-wrapper {
        overflow-x: auto;
        max-width: 100%;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    
    .pagination-wrapper::-webkit-scrollbar {
        display: none;
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
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .table td, .table th {
            padding: 6px 6px;
        }
        
        .action-btn {
            padding: 2px 5px;
            min-width: 22px;
            min-height: 22px;
            font-size: 0.6rem;
        }
        
        .action-btn i {
            font-size: 0.6rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .table td, .table th {
            padding: 5px 4px;
            font-size: 0.75rem;
        }
        
        .action-btn {
            padding: 2px 4px;
            min-width: 20px;
            min-height: 20px;
            font-size: 0.55rem;
            border-radius: 4px;
        }
        
        .action-btn i {
            font-size: 0.55rem;
        }
        
        .badge {
            font-size: 0.6rem;
            padding: 3px 6px;
        }
        
        .form-control-sm, .form-select-sm {
            font-size: 0.7rem;
            padding: 4px 6px;
            height: 28px;
        }
        
        .icon-circle {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
            border-radius: 8px;
        }
        
        .pagination .page-link {
            padding: 3px 8px;
            font-size: 0.7rem;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .table td, .table th {
            padding: 4px 3px;
            font-size: 0.65rem;
        }
        
        .action-btn {
            padding: 1px 3px;
            min-width: 18px;
            min-height: 18px;
            font-size: 0.5rem;
            border-radius: 3px;
        }
        
        .action-btn i {
            font-size: 0.5rem;
        }
        
        .badge {
            font-size: 0.5rem;
            padding: 2px 5px;
        }
        
        .icon-circle {
            width: 28px;
            height: 28px;
            font-size: 0.7rem;
        }
        
        .pagination .page-link {
            padding: 2px 6px;
            font-size: 0.6rem;
        }
        
        .btn-sm {
            font-size: 0.65rem;
            padding: 3px 6px;
        }
        
        .text-muted.small {
            font-size: 0.6rem !important;
        }
    }
    
    @media (max-width: 400px) {
        .table td, .table th {
            padding: 3px 2px;
            font-size: 0.55rem;
        }
        
        .action-btn {
            min-width: 16px;
            min-height: 16px;
            font-size: 0.45rem;
        }
        
        .action-btn i {
            font-size: 0.45rem;
        }
    }
</style>
@endsection