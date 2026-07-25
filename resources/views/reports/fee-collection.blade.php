@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="report-card">
        <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2"></i> Fee Collection Report</h2>
            <div class="no-print d-flex gap-2">
                <button onclick="printReport()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print Report</span>
                </button>
                <button onclick="exportCSV()" class="btn btn-success btn-sm">
                    <i class="bi bi-file-excel me-1"></i> <span class="d-none d-sm-inline">Export CSV</span>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('reports.fee-collection') }}" class="no-print row g-2 g-sm-3 mb-4">
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-0">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-0">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
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
            <div class="col-12 col-sm-4 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter me-1"></i> <span class="d-none d-sm-inline">Apply Filters</span>
                </button>
            </div>
        </form>

        <!-- Summary Section -->
        <div class="row g-2 g-sm-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="summary-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6>Total Collected</h6>
                    <h3>KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6>Transactions</h6>
                    <h3>{{ number_format($summary['total_transactions'] ?? 0) }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h6>Average Payment</h6>
                    <h3>KES {{ number_format($summary['average_payment'] ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <h6>Collection Rate</h6>
                    <h3>
                        @php
                            $total = $summary['total_amount'] ?? 0;
                            $paid = $summary['total_paid'] ?? 0;
                            $rate = $total > 0 ? round(($paid / $total) * 100, 1) : 0;
                        @endphp
                        {{ $rate }}%
                    </h3>
                </div>
            </div>
        </div>

        <!-- Status Breakdown Cards -->
        <div class="row g-2 g-sm-3 mb-4">
            <div class="col-4 col-md-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="card-title small">Paid</h6>
                        <h4 class="card-text fw-bold fs-6 fs-sm-5">KES {{ number_format($summary['total_paid'] ?? 0, 2) }}</h4>
                        <small>{{ $fees->where('status', 'paid')->count() ?? 0 }} transactions</small>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="card-title small">Pending</h6>
                        <h4 class="card-text fw-bold fs-6 fs-sm-5">KES {{ number_format($summary['total_pending'] ?? 0, 2) }}</h4>
                        <small>{{ $fees->where('status', 'pending')->count() ?? 0 }} transactions</small>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="card-title small">Overdue</h6>
                        <h4 class="card-text fw-bold fs-6 fs-sm-5">KES {{ number_format($summary['total_overdue'] ?? 0, 2) }}</h4>
                        <small>{{ $fees->where('status', 'overdue')->count() ?? 0 }} transactions</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Collection Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th class="d-none d-sm-table-cell">Course</th>
                        <th class="text-end">Amount</th>
                        <th class="d-none d-md-table-cell">Status</th>
                        <th class="d-none d-lg-table-cell">Term</th>
                        <th class="d-none d-xl-table-cell">Academic Year</th>
                        <th class="d-none d-sm-table-cell">Payment Date</th>
                        <th class="d-none d-xxl-table-cell">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees ?? [] as $index => $fee)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $fee->student->full_name ?? $fee->student->name ?? 'N/A' }}</strong>
                            </td>
                            <td class="d-none d-sm-table-cell">{{ $fee->student->course->course_name ?? $fee->student->course->name ?? 'N/A' }}</td>
                            <td class="text-end">
                                <strong>KES {{ number_format($fee->amount, 2) }}</strong>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($fee->status) }}
                                </span>
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $fee->term ?? 'N/A' }}</td>
                            <td class="d-none d-xl-table-cell">{{ $fee->academic_year ?? 'N/A' }}</td>
                            <td class="d-none d-sm-table-cell">{{ $fee->payment_date ? date('d-m-Y', strtotime($fee->payment_date)) : 'N/A' }}</td>
                            <td class="d-none d-xxl-table-cell">{{ $fee->due_date ? date('d-m-Y', strtotime($fee->due_date)) : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No fee records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="3" class="text-end">Grand Total:</th>
                        <th class="text-end">KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</th>
                        <th colspan="5"></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Average Payment:</th>
                        <th class="text-end">KES {{ number_format($summary['average_payment'] ?? 0, 2) }}</th>
                        <th colspan="5"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Status Breakdown Pie Chart & Summary -->
        <div class="row g-3 mt-4">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i> Payment Status Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="bi bi-table me-2"></i> Status Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = $summary['total_amount'] ?? 1;
                                    @endphp
                                    @foreach(['paid', 'pending', 'overdue'] as $status)
                                        @php
                                            $amount = $summary['total_' . $status] ?? 0;
                                            $percentage = $total > 0 ? round(($amount / $total) * 100, 1) : 0;
                                            $color = $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : 'danger');
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $color }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">{{ $fees->where('status', $status)->count() ?? 0 }}</td>
                                            <td class="text-end">KES {{ number_format($amount, 2) }}</td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $color }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $percentage }}%"
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">{{ $fees->count() ?? 0 }}</th>
                                        <th class="text-end">KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</th>
                                        <th class="text-center">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function printReport() {
        window.print();
    }

    function exportCSV() {
        const startDate = document.querySelector('[name="start_date"]')?.value || '';
        const endDate = document.querySelector('[name="end_date"]')?.value || '';
        const status = document.querySelector('[name="status"]')?.value || '';
        const term = document.querySelector('[name="term"]')?.value || '';
        window.location.href = '{{ route("reports.export", "fee-collection") }}?start_date=' + startDate + '&end_date=' + endDate + '&status=' + status + '&term=' + term;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        const data = {
            labels: ['Paid', 'Pending', 'Overdue'],
            datasets: [{
                data: [
                    {{ $summary['total_paid'] ?? 0 }},
                    {{ $summary['total_pending'] ?? 0 }},
                    {{ $summary['total_overdue'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 2
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': KES ' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

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
        font-size: 1.3rem;
    }
    
    .summary-box {
        padding: 15px 10px;
        border-radius: 10px;
        color: white;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        height: 100%;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .summary-box h6 {
        margin-bottom: 5px;
        opacity: 0.9;
        font-weight: 300;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .summary-box h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .table th {
        white-space: nowrap;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 10px;
    }
    
    .table td {
        padding: 8px 10px;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    
    .badge {
        padding: 4px 10px;
        font-size: 0.7rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .progress {
        height: 20px;
        border-radius: 10px;
    }
    
    .progress-bar {
        line-height: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 15px;
    }
    
    .card .card-header h5 {
        font-size: 0.95rem;
    }
    
    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #495057;
    }
    
    .form-control-sm, .form-select-sm {
        font-size: 0.82rem;
        padding: 6px 10px;
        border-radius: 6px;
        border-color: #dee2e6;
    }
    
    .form-control-sm:focus, .form-select-sm:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .report-card {
            padding: 12px;
        }
        
        .report-header h2 {
            font-size: 1.1rem;
        }
        
        .summary-box h3 {
            font-size: 0.95rem;
        }
        
        .summary-box {
            min-height: 70px;
            padding: 12px 8px;
        }
    }
    
    @media (max-width: 767.98px) {
        .report-card {
            padding: 10px;
        }
        
        .report-header h2 {
            font-size: 0.95rem;
        }
        
        .report-header .btn-sm {
            font-size: 0.7rem;
            padding: 3px 8px;
        }
        
        .report-header .btn-sm i {
            font-size: 0.7rem;
        }
        
        .summary-box {
            min-height: 60px;
            padding: 10px 6px;
        }
        
        .summary-box h6 {
            font-size: 0.6rem;
        }
        
        .summary-box h3 {
            font-size: 0.8rem;
        }
        
        .table td {
            font-size: 0.75rem;
            padding: 6px 6px;
        }
        
        .table th {
            font-size: 0.6rem;
            padding: 6px 6px;
        }
        
        .badge {
            font-size: 0.6rem;
            padding: 3px 8px;
        }
        
        .card .card-header h5 {
            font-size: 0.8rem;
        }
        
        .form-label {
            font-size: 0.7rem;
        }
        
        .form-control-sm, .form-select-sm {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
    }
    
    @media (max-width: 575.98px) {
        .report-card {
            padding: 8px;
        }
        
        .report-header h2 {
            font-size: 0.85rem;
        }
        
        .report-header .btn-sm {
            font-size: 0.6rem;
            padding: 2px 6px;
        }
        
        .report-header .btn-sm i {
            font-size: 0.6rem;
        }
        
        .summary-box {
            min-height: 50px;
            padding: 8px 4px;
        }
        
        .summary-box h6 {
            font-size: 0.5rem;
            margin-bottom: 2px;
        }
        
        .summary-box h3 {
            font-size: 0.7rem;
        }
        
        .table td {
            font-size: 0.65rem;
            padding: 4px 4px;
        }
        
        .table th {
            font-size: 0.5rem;
            padding: 4px 4px;
            letter-spacing: 0.3px;
        }
        
        .badge {
            font-size: 0.5rem;
            padding: 2px 6px;
        }
        
        .card .card-body {
            padding: 8px !important;
        }
        
        .card .card-body h4 {
            font-size: 0.8rem !important;
        }
        
        .card .card-body small {
            font-size: 0.5rem !important;
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
        .summary-box {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        .card {
            border: 1px solid #ddd;
        }
        .table-hover tbody tr:hover {
            background: transparent !important;
        }
    }
</style>
@endpush
@endsection