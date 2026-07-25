@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="report-card">
        <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i> Course Revenue Report</h2>
            <div class="no-print d-flex gap-2">
                <button onclick="printReport()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print</span>
                </button>
                <button onclick="window.location.href='{{ route('reports.export', 'course-revenue') }}'" class="btn btn-success btn-sm">
                    <i class="bi bi-file-excel me-1"></i> <span class="d-none d-sm-inline">Export CSV</span>
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-2 g-sm-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="summary-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6>Total Revenue</h6>
                    <h3>KES {{ number_format($grandTotal ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="summary-box" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <h6>Total Paid</h6>
                    <h3>KES {{ number_format($grandPaid ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6>Total Pending</h6>
                    <h3>KES {{ number_format($grandPending ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Course Revenue Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course</th>
                        <th class="text-center">Students</th>
                        <th class="text-end">Total Revenue</th>
                        <th class="text-end d-none d-md-table-cell">Paid</th>
                        <th class="text-end d-none d-md-table-cell">Pending</th>
                        <th class="text-center">Collection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courseData ?? [] as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $data['name'] ?? 'N/A' }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $data['students_count'] ?? 0 }}</span>
                            </td>
                            <td class="text-end">
                                <strong>KES {{ number_format($data['revenue'] ?? 0, 2) }}</strong>
                            </td>
                            <td class="text-end text-success d-none d-md-table-cell">
                                KES {{ number_format($data['paid_revenue'] ?? 0, 2) }}
                            </td>
                            <td class="text-end text-warning d-none d-md-table-cell">
                                KES {{ number_format($data['pending_revenue'] ?? 0, 2) }}
                            </td>
                            <td class="text-center">
                                @php
                                    $rate = $data['collection_rate'] ?? 0;
                                    $badgeClass = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}" style="font-size: 0.75rem; min-width: 50px; display: inline-block;">
                                    {{ $rate }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No course data available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="3" class="text-end">Grand Total:</th>
                        <th class="text-end">KES {{ number_format($grandTotal ?? 0, 2) }}</th>
                        <th class="text-end d-none d-md-table-cell">KES {{ number_format($grandPaid ?? 0, 2) }}</th>
                        <th class="text-end d-none d-md-table-cell">KES {{ number_format($grandPending ?? 0, 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Summary Row (visible only on small screens) -->
        <div class="row d-md-none mt-3">
            <div class="col-6">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted">Paid</small>
                        <div class="fw-bold text-success">KES {{ number_format($grandPaid ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted">Pending</small>
                        <div class="fw-bold text-warning">KES {{ number_format($grandPending ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printReport() {
        window.print();
    }
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
    
    .badge.bg-info {
        background: #e3f2fd !important;
        color: #0d6efd !important;
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
        
        .card-body.p-2 {
            padding: 6px !important;
        }
        
        .card-body.p-2 .fw-bold {
            font-size: 0.7rem !important;
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
        .table-hover tbody tr:hover {
            background: transparent !important;
        }
    }
</style>
@endpush
@endsection