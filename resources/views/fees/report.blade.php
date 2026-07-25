@extends('layouts.app')

@section('title', 'Payment Report')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Payment Report</h4>
            <p class="text-muted small mb-0">Comprehensive fee collection analysis and reports</p>
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="{{ route('fees.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Back</span>
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-sm-4">
            <form action="{{ route('fees.report') }}" method="GET" class="row g-2 g-sm-3">
                <div class="col-12 col-sm-5 col-md-4">
                    <label for="start_date" class="form-label small fw-bold">Start Date</label>
                    <input type="date" name="start_date" id="start_date" 
                           class="form-control form-control-sm" 
                           value="{{ $startDate ?? now()->startOfMonth()->toDateString() }}">
                </div>
                <div class="col-12 col-sm-5 col-md-4">
                    <label for="end_date" class="form-label small fw-bold">End Date</label>
                    <input type="date" name="end_date" id="end_date" 
                           class="form-control form-control-sm" 
                           value="{{ $endDate ?? now()->toDateString() }}">
                </div>
                <div class="col-12 col-sm-2 col-md-4 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Generate
                    </button>
                    <a href="{{ route('fees.report') }}" class="btn btn-secondary btn-sm flex-grow-1">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                    <button type="submit" name="export" value="csv" class="btn btn-success btn-sm flex-grow-1">
                        <i class="fas fa-file-export me-1"></i> Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 g-sm-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Total Amount</p>
                            <h5 class="fw-bold mb-0 text-primary fs-6 fs-sm-5">KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</h5>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 p-sm-3">
                            <i class="fas fa-money-bill-wave text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Total Payments</p>
                            <h5 class="fw-bold mb-0 text-success fs-6 fs-sm-5">{{ $summary['total_payments'] ?? 0 }}</h5>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 p-sm-3">
                            <i class="fas fa-receipt text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Average Amount</p>
                            <h5 class="fw-bold mb-0 text-info fs-6 fs-sm-5">KES {{ number_format($summary['total_payments'] > 0 ? ($summary['total_amount'] ?? 0) / max(($summary['total_payments'] ?? 1), 1) : 0, 2) }}</h5>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-2 p-sm-3">
                            <i class="fas fa-calculator text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Date Range</p>
                            <h6 class="fw-bold mb-0" style="font-size: 0.75rem;">
                                {{ \Carbon\Carbon::parse($startDate ?? now()->startOfMonth())->format('d M Y') }}
                                -
                                {{ \Carbon\Carbon::parse($endDate ?? now())->format('d M Y') }}
                            </h6>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 p-sm-3">
                            <i class="fas fa-calendar-alt text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-2 g-sm-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-2 py-sm-3">
                    <h6 class="fw-bold mb-0">Payment Methods Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="methodChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-2 py-sm-3">
                    <h6 class="fw-bold mb-0">Fee Types Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakdown Section -->
    <div class="row g-2 g-sm-3 mb-4">
        <!-- Payment Methods Breakdown -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center py-2 py-sm-3">
                    <h6 class="fw-bold mb-0">By Payment Method</h6>
                    <span class="badge bg-secondary">{{ ($summary['by_method'] ?? collect())->count() }} Methods</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Payment Method</th>
                                    <th class="text-center d-none d-sm-table-cell">Transactions</th>
                                    <th class="text-end pe-3">Amount</th>
                                    <th class="text-center d-none d-md-table-cell">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($summary['by_method'] ?? collect()) as $method => $data)
                                    @php
                                        $percentage = ($summary['total_amount'] ?? 0) > 0 
                                            ? round(($data['amount'] / max(($summary['total_amount'] ?? 1), 1)) * 100, 1) 
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $method }}
                                            </span>
                                        </td>
                                        <td class="text-center d-none d-sm-table-cell">{{ $data['count'] }}</td>
                                        <td class="text-end pe-3 fw-bold text-success">
                                            KES {{ number_format($data['amount'], 2) }}
                                        </td>
                                        <td class="text-center d-none d-md-table-cell">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2">{{ $percentage }}%</span>
                                                <div class="progress" style="width: 50px; height: 4px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            No payment method data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(($summary['by_method'] ?? collect())->count() > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th class="ps-3">Total</th>
                                    <th class="text-center d-none d-sm-table-cell">{{ $summary['total_payments'] ?? 0 }}</th>
                                    <th class="text-end pe-3">KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</th>
                                    <th class="text-center d-none d-md-table-cell">100%</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Types Breakdown -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center py-2 py-sm-3">
                    <h6 class="fw-bold mb-0">By Fee Type</h6>
                    <span class="badge bg-secondary">{{ ($summary['by_type'] ?? collect())->count() }} Types</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Fee Type</th>
                                    <th class="text-center d-none d-sm-table-cell">Transactions</th>
                                    <th class="text-end pe-3">Amount</th>
                                    <th class="text-center d-none d-md-table-cell">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($summary['by_type'] ?? collect()) as $type => $data)
                                    @php
                                        $percentage = ($summary['total_amount'] ?? 0) > 0 
                                            ? round(($data['amount'] / max(($summary['total_amount'] ?? 1), 1)) * 100, 1) 
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                                {{ $type ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="text-center d-none d-sm-table-cell">{{ $data['count'] }}</td>
                                        <td class="text-end pe-3 fw-bold text-success">
                                            KES {{ number_format($data['amount'], 2) }}
                                        </td>
                                        <td class="text-center d-none d-md-table-cell">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2">{{ $percentage }}%</span>
                                                <div class="progress" style="width: 50px; height: 4px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            No fee type data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(($summary['by_type'] ?? collect())->count() > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th class="ps-3">Total</th>
                                    <th class="text-center d-none d-sm-table-cell">{{ $summary['total_payments'] ?? 0 }}</th>
                                    <th class="text-end pe-3">KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</th>
                                    <th class="text-center d-none d-md-table-cell">100%</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    @if(isset($summary['by_status']) && ($summary['by_status'] ?? collect())->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-2 py-sm-3">
            <h6 class="fw-bold mb-0">Payment Status Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="row g-2 g-sm-3">
                @foreach(($summary['by_status'] ?? collect()) as $status => $data)
                    @php
                        $statusColor = $status == 'paid' ? 'success' : ($status == 'pending' ? 'warning' : ($status == 'overdue' ? 'danger' : 'secondary'));
                        $icon = $status == 'paid' ? 'fa-check-circle' : ($status == 'pending' ? 'fa-clock' : ($status == 'overdue' ? 'fa-exclamation-circle' : 'fa-times-circle'));
                        $percentage = ($summary['total_amount'] ?? 0) > 0 
                            ? round(($data['amount'] / max(($summary['total_amount'] ?? 1), 1)) * 100, 1) 
                            : 0;
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="card border-0 bg-{{ $statusColor }} bg-opacity-10">
                            <div class="card-body text-center p-2 p-sm-3">
                                <i class="fas {{ $icon }} fa-2x text-{{ $statusColor }} mb-1 mb-sm-2"></i>
                                <h6 class="text-{{ $statusColor }} fw-bold mb-0">{{ ucfirst($status) }}</h6>
                                <h5 class="fw-bold mb-0 fs-6 fs-sm-5">KES {{ number_format($data['amount'], 2) }}</h5>
                                <small class="text-muted">{{ $data['count'] }} transactions ({{ $percentage }}%)</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center py-2 py-sm-3">
            <h6 class="fw-bold mb-0">Transaction Details</h6>
            <span class="badge bg-secondary">{{ $fees->count() ?? 0 }} Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Student</th>
                            <th class="d-none d-sm-table-cell">Admission</th>
                            <th class="text-end">Amount</th>
                            <th class="d-none d-lg-table-cell">Method</th>
                            <th class="d-none d-md-table-cell">Type</th>
                            <th>Status</th>
                            <th class="d-none d-sm-table-cell">Date</th>
                            <th class="d-none d-xl-table-cell">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees ?? [] as $fee)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2 d-none d-sm-flex">
                                            @php
                                                $studentName = $fee->student_name ?? 'N/A';
                                                $initial = !empty($studentName) ? strtoupper(substr($studentName, 0, 1)) : '?';
                                            @endphp
                                            {{ $initial }}
                                        </div>
                                        <span class="fw-semibold" style="font-size: 0.85rem;">
                                            {{ $fee->student_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $fee->student_admission ?? 'N/A' }}</span>
                                </td>
                                <td class="fw-bold text-success text-end">
                                    KES {{ number_format($fee->amount, 2) }}
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @php
                                        $methodColors = [
                                            'M-Pesa' => 'success',
                                            'Mpesa' => 'success',
                                            'Cash' => 'primary',
                                            'Bank Transfer' => 'info',
                                            'Cheque' => 'warning',
                                            'Credit Card' => 'secondary'
                                        ];
                                        $methodColor = $methodColors[$fee->payment_method] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $methodColor }} bg-opacity-10 text-{{ $methodColor }}" style="font-size: 0.65rem;">
                                        @if($fee->isMpesaPayment())
                                            <i class="fas fa-mobile-alt me-1"></i>
                                        @endif
                                        {{ $fee->payment_method ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-warning bg-opacity-10 text-warning" style="font-size: 0.65rem;">
                                        {{ $fee->fee_type ?? 'General' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $status = $fee->status ?? 'pending';
                                        $statusColor = $status == 'paid' ? 'success' : ($status == 'pending' ? 'warning' : ($status == 'overdue' ? 'danger' : 'secondary'));
                                        $statusIcon = $status == 'paid' ? 'fa-check-circle' : ($status == 'pending' ? 'fa-clock' : ($status == 'overdue' ? 'fa-exclamation-circle' : 'fa-circle'));
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="font-size: 0.65rem;">
                                        <i class="fas {{ $statusIcon }} me-1"></i>
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="d-none d-sm-table-cell" style="font-size: 0.75rem;">
                                    @if($fee->payment_date)
                                        {{ $fee->payment_date->format('d M Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="badge bg-dark" style="font-size: 0.65rem;">
                                        {{ $fee->receipt_no ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x d-block mb-3 text-muted"></i>
                                    <h6 class="text-muted">No transactions found for the selected period</h6>
                                    <p class="text-muted small">Try adjusting your date range</p>
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
    .avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 11px;
        flex-shrink: 0;
    }
    
    .card {
        border-radius: 12px !important;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef;
        padding: 6px 8px;
    }
    
    .table td {
        padding: 6px 8px;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .table td {
            font-size: 0.75rem;
            padding: 4px 6px;
        }
        
        .table th {
            font-size: 0.55rem;
            padding: 4px 6px;
        }
    }
    
    @media (max-width: 767.98px) {
        .table td {
            font-size: 0.7rem;
            padding: 4px 4px;
        }
        
        .table th {
            font-size: 0.5rem;
            padding: 4px 4px;
        }
        
        .badge {
            font-size: 0.55rem !important;
            padding: 2px 6px !important;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .table td {
            font-size: 0.6rem;
            padding: 3px 3px;
        }
        
        .table th {
            font-size: 0.45rem;
            padding: 3px 3px;
        }
        
        .badge {
            font-size: 0.5rem !important;
            padding: 2px 4px !important;
        }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment Methods Chart
        const methodCtx = document.getElementById('methodChart')?.getContext('2d');
        if (methodCtx) {
            const methodData = @json(($summary['by_method'] ?? collect())->map(function($data, $key) {
                return [
                    'label' => $key,
                    'value' => $data['amount']
                ];
            })->values());
            
            if (methodData.length > 0) {
                new Chart(methodCtx, {
                    type: 'doughnut',
                    data: {
                        labels: methodData.map(item => item.label),
                        datasets: [{
                            data: methodData.map(item => item.value),
                            backgroundColor: [
                                '#6c8cff',
                                '#28a745',
                                '#ffc107',
                                '#dc3545',
                                '#17a2b8',
                                '#6f42c1'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 12,
                                    usePointStyle: true,
                                    font: { size: 10 },
                                    boxWidth: 10
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }

        // Fee Types Chart
        const typeCtx = document.getElementById('typeChart')?.getContext('2d');
        if (typeCtx) {
            const typeData = @json(($summary['by_type'] ?? collect())->map(function($data, $key) {
                return [
                    'label' => $key ?: 'General',
                    'value' => $data['amount']
                ];
            })->values());
            
            if (typeData.length > 0) {
                new Chart(typeCtx, {
                    type: 'pie',
                    data: {
                        labels: typeData.map(item => item.label),
                        datasets: [{
                            data: typeData.map(item => item.value),
                            backgroundColor: [
                                '#ffc107',
                                '#28a745',
                                '#6c8cff',
                                '#dc3545',
                                '#17a2b8',
                                '#6f42c1',
                                '#fd7e14'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 12,
                                    usePointStyle: true,
                                    font: { size: 10 },
                                    boxWidth: 10
                                }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endpush
@endsection