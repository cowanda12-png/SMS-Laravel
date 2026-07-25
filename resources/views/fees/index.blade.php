@extends('layouts.app')

@section('title', 'Fee Structure')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Fee Structure
            </h4>
            <p class="text-muted small mb-0">Overview of all student fee payments</p>
        </div>
        <a href="{{ route('fees.create') }}" class="btn btn-primary btn-lg px-4">
            <i class="fas fa-plus-circle me-2"></i> Record Payment
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
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
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Collected</p>
                            <h5 class="fw-bold mb-0">KES {{ number_format($totalFees, 2) }}</h5>
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
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Collected Today</p>
                            <h5 class="fw-bold mb-0">KES {{ number_format($todayFees, 2) }}</h5>
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
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending ({{ $pendingCount }})</p>
                            <h5 class="fw-bold mb-0">KES {{ number_format($pendingFees, 2) }}</h5>
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
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Overdue ({{ $overdueCount }})</p>
                            <h5 class="fw-bold mb-0">KES {{ number_format($overdueFees, 2) }}</h5>
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
        <div class="card-body">
            <form action="{{ route('fees.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Receipt no, student name..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Student</label>
                    <select name="student_id" class="form-select form-select-sm">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                                {{ $method }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Fee Type</label>
                    <select name="fee_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach($feeTypes as $type)
                            <option value="{{ $type }}" {{ request('fee_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Apply filters">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'student_id', 'payment_method', 'status', 'fee_type', 'start_date', 'end_date']))
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
            @if($fees->isEmpty())
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
                                <th>Receipt No.</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Fee Type</th>
                                <th>Term / Year</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees as $fee)
                                <tr>
                                    <td>
                                        <span class="fw-semibold small">{{ $fee->receipt_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $fee->student_name }}</div>
                                        <div class="text-muted small">{{ $fee->student_admission }}</div>
                                    </td>
                                    <td class="fw-semibold">KES {{ number_format($fee->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            @if($fee->payment_method === 'M-Pesa')
                                                <i class="fas fa-mobile-alt me-1 text-success"></i>
                                            @endif
                                            {{ $fee->payment_method ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $fee->fee_type ?? 'N/A' }}</td>
                                    <td class="small text-muted">
                                        {{ $fee->term ?? '—' }} @if($fee->academic_year) / {{ $fee->academic_year }} @endif
                                    </td>
                                    <td class="small">{{ $fee->formatted_payment_date }}</td>
                                    <td>
                                        <span class="badge bg-{{ $fee->status_badge['color'] }}">
                                            <i class="fas {{ $fee->status_badge['icon'] }} me-1"></i>
                                            {{ $fee->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('fees.show', $fee->id) }}" class="btn btn-outline-secondary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($fee->status === 'paid')
                                                <a href="{{ route('fees.receipt', $fee->id) }}" class="btn btn-outline-success" title="Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center py-3">
                    {{ $fees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #6c757d;
        font-weight: 600;
    }
</style>
@endsection
