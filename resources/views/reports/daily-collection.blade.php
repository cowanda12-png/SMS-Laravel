@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="report-card">
        <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="fw-bold mb-0"><i class="bi bi-calendar-day me-2"></i> Daily Collection Report</h2>
            <div class="no-print d-flex gap-2">
                <button onclick="printReport()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print</span>
                </button>
                <button onclick="window.location.href='{{ route('reports.export', 'daily-collection') }}?date={{ $date ?? date('Y-m-d') }}'" class="btn btn-success btn-sm">
                    <i class="bi bi-file-excel me-1"></i> <span class="d-none d-sm-inline">Export CSV</span>
                </button>
            </div>
        </div>

        <!-- Date Selector -->
        <div class="no-print mb-4">
            <form method="GET" action="{{ route('reports.daily-collection') }}" class="row g-2 g-sm-3">
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label small fw-semibold mb-0">Select Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" 
                           value="{{ $date ?? date('Y-m-d') }}" 
                           onchange="this.form.submit()">
                </div>
                <div class="col-6 col-sm-3 col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i> <span class="d-none d-sm-inline">View</span>
                    </button>
                </div>
                <div class="col-6 col-sm-3 col-md-4 d-flex align-items-end justify-content-sm-end">
                    <a href="{{ route('reports.daily-collection') }}" class="btn btn-secondary btn-sm w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> <span class="d-none d-sm-inline">Today</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Date Display -->
        <div class="alert alert-info py-2 py-sm-3">
            <i class="bi bi-calendar me-2"></i> 
            <strong>Report for: {{ date('l, d F Y', strtotime($date ?? date('Y-m-d'))) }}</strong>
        </div>

        <!-- Summary Cards -->
        <div class="row g-2 g-sm-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="summary-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6>Total Collections</h6>
                    <h3>KES {{ number_format($summary['total_collections'] ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6>Transactions</h6>
                    <h3>{{ number_format($summary['total_transactions'] ?? 0) }}</h3>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="summary-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h6>Average Payment</h6>
                    <h3>KES {{ number_format($summary['average_payment'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Daily Collection Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="collectionTable">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No collections found for this date
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="3" class="text-end">Total Collections:</th>
                        <th class="text-end">KES {{ number_format($summary['total_collections'] ?? 0, 2) }}</th>
                        <th colspan="4"></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Total Transactions:</th>
                        <th class="text-end">{{ $summary['total_transactions'] ?? 0 }}</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Status Summary (visible only on small screens) -->
        <div class="row d-md-none mt-3 g-2">
            <div class="col-4">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">Paid</small>
                        <span class="badge bg-success">✓</span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">Pending</small>
                        <span class="badge bg-warning">!</span>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">Overdue</small>
                        <span class="badge bg-danger">!</span>
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

    function exportCSV() {
        const date = '{{ $date ?? date("Y-m-d") }}';
        window.location.href = '{{ route("reports.export", "daily-collection") }}?date=' + date;
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
    
    .alert-info {
        background: #e3f2fd;
        border-color: #bbdefb;
        color: #0d47a1;
        font-size: 0.9rem;
    }
    
    .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #495057;
    }
    
    .form-control-sm {
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 6px;
        border-color: #dee2e6;
    }
    
    .form-control-sm:focus {
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
        
        .alert-info {
            font-size: 0.75rem;
            padding: 8px 12px;
        }
        
        .form-label {
            font-size: 0.7rem;
        }
        
        .form-control-sm {
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
        
        .alert-info {
            font-size: 0.65rem;
            padding: 6px 10px;
        }
        
        .card-body.p-2 {
            padding: 4px !important;
        }
        
        .card-body.p-2 small {
            font-size: 0.5rem !important;
        }
        
        .card-body.p-2 .badge {
            font-size: 0.6rem !important;
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
        .alert-info {
            background: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            color: #212529 !important;
        }
    }
</style>
@endpush
@endsection