@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="report-card">
        <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i> Payment Status Analysis</h2>
            <div class="no-print d-flex gap-2">
                <button onclick="printReport()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print</span>
                </button>
                <button onclick="exportCSV()" class="btn btn-success btn-sm">
                    <i class="bi bi-file-excel me-1"></i> <span class="d-none d-sm-inline">Export CSV</span>
                </button>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(!session('error'))
            <!-- Summary Cards -->
            <div class="row g-2 g-sm-3 mb-4">
                <div class="col-6 col-lg-4">
                    <div class="summary-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h6>Total Transactions</h6>
                        <h3>{{ number_format($paymentMethods->sum('transactions') ?? 0) }}</h3>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="summary-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <h6>Total Amount</h6>
                        <h3>KES {{ number_format($grandTotal ?? 0, 2) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h6>Average per Status</h6>
                        <h3>KES {{ number_format(($grandTotal ?? 0) / max(($paymentMethods->count() ?? 1), 1), 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown Table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Payment Status</th>
                            <th class="text-center d-none d-sm-table-cell">Transactions</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end d-none d-md-table-cell">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = $paymentMethods->sum('total_amount') ?? 0;
                        @endphp
                        @forelse($paymentMethods ?? [] as $index => $method)
                            @php
                                $percentage = $grandTotal > 0 ? ($method->total_amount / $grandTotal) * 100 : 0;
                                $colors = ['primary', 'success', 'warning', 'danger', 'info'];
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>
                                        <span class="badge bg-{{ $method->payment_method === 'paid' ? 'success' : ($method->payment_method === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($method->payment_method) }}
                                        </span>
                                    </strong>
                                </td>
                                <td class="text-center d-none d-sm-table-cell">{{ number_format($method->transactions) }}</td>
                                <td class="text-end">KES {{ number_format($method->total_amount, 2) }}</td>
                                <td class="text-end d-none d-md-table-cell">{{ number_format($percentage, 1) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox"></i> No payment data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="2" class="text-end">Total</th>
                            <th class="text-center d-none d-sm-table-cell">{{ number_format($paymentMethods->sum('transactions') ?? 0) }}</th>
                            <th class="text-end">KES {{ number_format($grandTotal, 2) }}</th>
                            <th class="text-end d-none d-md-table-cell">100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Mobile Summary (visible only on small screens) -->
            <div class="row d-sm-none mt-3 g-2">
                <div class="col-6">
                    <div class="card bg-light">
                        <div class="card-body text-center p-2">
                            <small class="text-muted d-block">Transactions</small>
                            <div class="fw-bold">{{ number_format($paymentMethods->sum('transactions') ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-light">
                        <div class="card-body text-center p-2">
                            <small class="text-muted d-block">Avg per Status</small>
                            <div class="fw-bold text-primary">KES {{ number_format(($grandTotal ?? 0) / max(($paymentMethods->count() ?? 1), 1), 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bars -->
            @if($paymentMethods->count() > 0)
                <div class="mt-4">
                    <h5 class="fw-bold"><i class="bi bi-bar-chart me-2"></i> Distribution by Status</h5>
                    <div class="row g-2 g-sm-3">
                        @foreach($paymentMethods ?? [] as $index => $method)
                            @php
                                $percentage = $grandTotal > 0 ? ($method->total_amount / $grandTotal) * 100 : 0;
                                $colors = ['primary', 'success', 'warning', 'danger'];
                                $color = $colors[$index % count($colors)];
                                $badgeColor = $method->payment_method === 'paid' ? 'success' : ($method->payment_method === 'pending' ? 'warning' : 'danger');
                            @endphp
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="card mb-2 mb-sm-3">
                                    <div class="card-body text-center p-2 p-sm-3">
                                        <h6>
                                            <span class="badge bg-{{ $badgeColor }}">
                                                {{ ucfirst($method->payment_method) }}
                                            </span>
                                        </h6>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $color }}" 
                                                 style="width: {{ $percentage }}%;"
                                                 role="progressbar"
                                                 aria-valuenow="{{ $percentage }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                        <small class="d-block mt-1">
                                            KES {{ number_format($method->total_amount, 0) }} 
                                            ({{ number_format($method->transactions) }})
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pie Chart -->
                <div class="row mt-4">
                    <div class="col-12 col-md-8 col-lg-6 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i> Payment Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="statusChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function printReport() {
        window.print();
    }

    function exportCSV() {
        window.location.href = '{{ route("reports.export", "fee-collection") }}';
    }

    @if(!session('error') && isset($paymentMethods) && $paymentMethods->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            const labels = @json($paymentMethods->pluck('payment_method')->map(function($method) {
                return ucfirst($method);
            })->toArray());
            const amounts = @json($paymentMethods->pluck('total_amount')->toArray());
            const colors = [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)'
            ];
            
            if (labels.length > 0) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: amounts,
                            backgroundColor: colors.slice(0, labels.length),
                            borderColor: colors.map(c => c.replace('0.8', '1')),
                            borderWidth: 2
                        }]
                    },
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
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                        return context.label + ': KES ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    @endif
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
    
    .alert-warning {
        background: #fff3cd;
        border-color: #ffc107;
        color: #856404;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
        font-size: 0.9rem;
    }
    
    .card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 15px;
    }
    
    .card .card-header h5 {
        font-size: 0.95rem;
    }
    
    .progress {
        height: 20px;
        border-radius: 8px;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        line-height: 20px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 8px;
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
        
        .alert-warning {
            font-size: 0.75rem;
            padding: 8px 12px;
        }
        
        .card .card-header h5 {
            font-size: 0.8rem;
        }
        
        .progress {
            height: 16px;
        }
        
        .progress-bar {
            line-height: 16px;
            font-size: 9px;
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
        
        .alert-warning {
            font-size: 0.65rem;
            padding: 6px 10px;
        }
        
        .card .card-body {
            padding: 8px !important;
        }
        
        .card .card-body h6 {
            font-size: 0.7rem !important;
        }
        
        .card .card-body small {
            font-size: 0.55rem !important;
        }
        
        .card-body.p-2 {
            padding: 4px !important;
        }
        
        .card-body.p-2 .fw-bold {
            font-size: 0.65rem !important;
        }
        
        .card-body.p-2 small {
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
        .alert-warning {
            background: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            color: #212529 !important;
        }
    }
</style>
@endpush
@endsection