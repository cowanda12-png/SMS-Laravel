@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="row">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h2 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i> Reports Dashboard</h2>
                        <p class="text-muted small mb-0">Welcome to the School Management System Reports & Analytics Module</p>
                    </div>
                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print Dashboard</span>
                            <span class="d-inline d-sm-none">Print</span>
                        </button>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="row g-2 g-sm-3 mt-4">
                    <div class="col-6 col-lg-3">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body p-2 p-sm-3">
                                <h6 class="card-title small mb-1">Total Students</h6>
                                <h2 class="card-text fw-bold fs-4 fs-md-3">{{ number_format(\App\Models\Students::count()) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body p-2 p-sm-3">
                                <h6 class="card-title small mb-1">Total Revenue</h6>
                                <h2 class="card-text fw-bold fs-4 fs-md-3">KES {{ number_format(\App\Models\Fee::sum('amount') ?? 0, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body p-2 p-sm-3">
                                <h6 class="card-title small mb-1">Outstanding Balance</h6>
                                <h2 class="card-text fw-bold fs-4 fs-md-3">KES {{ number_format(\App\Models\Fee::where('status', 'pending')->orWhere('status', 'overdue')->sum('amount') ?? 0, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card text-white bg-danger h-100">
                            <div class="card-body p-2 p-sm-3">
                                <h6 class="card-title small mb-1">Today's Collections</h6>
                                <h2 class="card-text fw-bold fs-4 fs-md-3">KES {{ number_format(\App\Models\Fee::whereDate('payment_date', date('Y-m-d'))->sum('amount') ?? 0, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Reports Links -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="fw-bold"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Quick Reports</h5>
                        <hr>
                    </div>
                    @php
                        $reports = [
                            ['route' => route('reports.student-statement'), 'icon' => 'bi-person-vcard', 'color' => 'primary', 'title' => 'Student Statement', 'description' => 'View individual student fee statements'],
                            ['route' => route('reports.fee-collection'), 'icon' => 'bi-cash-stack', 'color' => 'success', 'title' => 'Fee Collection', 'description' => 'View all fee collections with filters'],
                            ['route' => route('reports.outstanding-balances'), 'icon' => 'bi-exclamation-triangle', 'color' => 'warning', 'title' => 'Outstanding Balances', 'description' => 'Students with pending balances'],
                            ['route' => route('reports.course-revenue'), 'icon' => 'bi-graph-up', 'color' => 'info', 'title' => 'Course Revenue', 'description' => 'Revenue breakdown by course'],
                            ['route' => route('reports.daily-collection'), 'icon' => 'bi-calendar-day', 'color' => 'danger', 'title' => 'Daily Collection', 'description' => 'Collections for a specific day'],
                            ['route' => route('reports.monthly-collection'), 'icon' => 'bi-calendar-month', 'color' => 'dark', 'title' => 'Monthly Collection', 'description' => 'Collections for a specific month'],
                            ['route' => route('reports.fee-summary'), 'icon' => 'bi-file-earmark-text', 'color' => 'secondary', 'title' => 'Fee Summary', 'description' => 'Comprehensive fee summary'],
                        ];
                    @endphp
                    @foreach($reports as $report)
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="{{ $report['route'] }}" class="text-decoration-none">
                                <div class="card h-100 text-center shadow-sm hover-card">
                                    <div class="card-body p-2 p-sm-3">
                                        <i class="bi {{ $report['icon'] }} fs-1 fs-sm-2 text-{{ $report['color'] }}"></i>
                                        <h6 class="mt-2 fw-bold small">{{ $report['title'] }}</h6>
                                        <small class="text-muted d-none d-sm-block">{{ $report['description'] }}</small>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0 pb-2">
                                        <span class="badge bg-{{ $report['color'] }}">View →</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Recent Activity -->
                <div class="row g-3 mt-4">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i> Recent Transactions</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">Student</th>
                                                <th class="text-end">Amount</th>
                                                <th class="pe-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $recent = \App\Models\Fee::with('student')
                                                    ->orderBy('payment_date', 'desc')
                                                    ->limit(5)
                                                    ->get();
                                            @endphp
                                            @forelse($recent as $fee)
                                                <tr>
                                                    <td class="ps-3">{{ $fee->student->full_name ?? $fee->student->name ?? 'N/A' }}</td>
                                                    <td class="text-end fw-semibold">KES {{ number_format($fee->amount, 0) }}</td>
                                                    <td class="pe-3">
                                                        <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($fee->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">No recent transactions</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i> Quick Stats</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $totalFees = \App\Models\Fee::sum('amount') ?? 0;
                                    $totalPaid = \App\Models\Fee::where('status', 'paid')->sum('amount') ?? 0;
                                    $totalPending = \App\Models\Fee::where('status', 'pending')->sum('amount') ?? 0;
                                    $totalOverdue = \App\Models\Fee::where('status', 'overdue')->sum('amount') ?? 0;
                                    $paidPercent = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 0;
                                @endphp
                                <div class="mb-3">
                                    <label class="fw-bold small">Collection Rate: {{ $paidPercent }}%</label>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: {{ $paidPercent }}%"
                                             aria-valuenow="{{ $paidPercent }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $paidPercent }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Paid</small>
                                            <div class="fw-bold text-success small">KES {{ number_format($totalPaid, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Pending</small>
                                            <div class="fw-bold text-warning small">KES {{ number_format($totalPending, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Overdue</small>
                                            <div class="fw-bold text-danger small">KES {{ number_format($totalOverdue, 0) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <small class="text-muted d-block">Total Fees</small>
                                            <div class="fw-bold text-primary small">KES {{ number_format($totalFees, 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .report-card {
        padding: 15px 15px 25px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .report-header {
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .report-header h2 {
        color: #2c3e50;
        margin-bottom: 2px;
        font-size: 1.4rem;
    }
    
    .report-header p {
        font-size: 0.85rem;
    }
    
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .hover-card .card-footer {
        padding: 4px 8px 8px;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
        border: none !important;
    }
    
    .card .card-body .fs-1 {
        font-size: 2.2rem !important;
    }
    
    .card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 15px;
    }
    
    .card .card-header h5 {
        font-size: 0.95rem;
    }
    
    .table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
    }
    
    .table td {
        font-size: 0.85rem;
        padding: 6px 8px;
    }
    
    .badge {
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
    }
    
    .progress {
        border-radius: 8px;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 20px;
    }
    
    .border.rounded {
        border-color: #e9ecef !important;
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .report-card {
            padding: 12px;
        }
        
        .report-header h2 {
            font-size: 1.2rem;
        }
        
        .card .card-body .fs-1 {
            font-size: 1.8rem !important;
        }
    }
    
    @media (max-width: 767.98px) {
        .report-card {
            padding: 10px;
        }
        
        .report-header h2 {
            font-size: 1rem;
        }
        
        .report-header p {
            font-size: 0.75rem;
        }
        
        .card .card-body .fs-1 {
            font-size: 1.5rem !important;
        }
        
        .card .card-body {
            padding: 10px !important;
        }
        
        .card .card-header h5 {
            font-size: 0.8rem;
        }
        
        .table td {
            font-size: 0.75rem;
            padding: 4px 6px;
        }
        
        .badge {
            font-size: 0.6rem;
            padding: 3px 8px;
        }
        
        .progress {
            height: 16px !important;
        }
        
        .progress-bar {
            font-size: 0.6rem;
            line-height: 16px;
        }
    }
    
    @media (max-width: 575.98px) {
        .report-card {
            padding: 8px;
        }
        
        .report-header h2 {
            font-size: 0.9rem;
        }
        
        .report-header p {
            font-size: 0.65rem;
        }
        
        .card .card-body .fs-1 {
            font-size: 1.2rem !important;
        }
        
        .card .card-body h6 {
            font-size: 0.65rem !important;
        }
        
        .card .card-body h2 {
            font-size: 1.1rem !important;
        }
        
        .table td {
            font-size: 0.65rem;
            padding: 3px 4px;
        }
        
        .table th {
            font-size: 0.55rem;
            padding: 4px 4px;
        }
        
        .badge {
            font-size: 0.5rem;
            padding: 2px 6px;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 3px 8px;
        }
        
        .border.rounded {
            padding: 6px !important;
        }
        
        .border.rounded .fw-bold {
            font-size: 0.75rem !important;
        }
        
        .border.rounded small {
            font-size: 0.55rem !important;
        }
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        .report-card {
            box-shadow: none;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .hover-card:hover {
            transform: none !important;
        }
        .card {
            break-inside: avoid;
        }
    }
</style>
@endpush
@endsection