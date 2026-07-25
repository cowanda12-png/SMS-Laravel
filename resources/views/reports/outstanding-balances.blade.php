@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="report-card">
        <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i> Outstanding Balances Report</h2>
            <div class="no-print d-flex gap-2">
                <button onclick="printReport()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print</span>
                </button>
                <button onclick="exportCSV()" class="btn btn-success btn-sm">
                    <i class="bi bi-file-excel me-1"></i> <span class="d-none d-sm-inline">Export CSV</span>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('reports.outstanding-balances') }}" class="no-print row g-2 g-sm-3 mb-4">
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label small fw-semibold mb-0">Course</label>
                <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    @foreach($courses ?? [] as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_name ?? $course->name ?? 'Course #' . $course->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-sm-6 col-md-4">
                <label class="form-label small fw-semibold mb-0">Balance Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Balances</option>
                    <option value="critical" {{ request('status') == 'critical' ? 'selected' : '' }}>
                        Critical (&gt; 50k)
                    </option>
                    <option value="high" {{ request('status') == 'high' ? 'selected' : '' }}>
                        High (20k - 50k)
                    </option>
                    <option value="medium" {{ request('status') == 'medium' ? 'selected' : '' }}>
                        Medium (5k - 20k)
                    </option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>
                        Low (&lt; 5k)
                    </option>
                </select>
            </div>
            <div class="col-6 col-sm-6 col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-filter me-1"></i> <span class="d-none d-sm-inline">Apply Filters</span>
                </button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row g-2 g-sm-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="card-title small">Total Outstanding</h6>
                        <h3 class="card-text fw-bold fs-5 fs-sm-4">KES {{ number_format($totalOutstanding ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="card-title small">Students with Balance</h6>
                        <h3 class="card-text fw-bold fs-5 fs-sm-4">{{ $outstandingStudents->count() ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card text-white bg-info h-100">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="card-title small">Average Balance</h6>
                        <h3 class="card-text fw-bold fs-5 fs-sm-4">
                            KES {{ number_format(($totalOutstanding ?? 0) / max(($outstandingStudents->count() ?? 1), 1), 2) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert -->
        <div class="alert alert-warning py-2 py-sm-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>{{ $outstandingStudents->count() ?? 0 }}</strong> students have outstanding balances.
            @if(($totalOutstanding ?? 0) > 0)
                Total outstanding: <strong>KES {{ number_format($totalOutstanding ?? 0, 2) }}</strong>
            @endif
        </div>

        <!-- Outstanding Balances Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th class="d-none d-sm-table-cell">Admission No</th>
                        <th class="d-none d-md-table-cell">Course</th>
                        <th class="text-end d-none d-lg-table-cell">Expected Fees</th>
                        <th class="text-end d-none d-lg-table-cell">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outstandingStudents ?? [] as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $student->full_name ?? $student->name ?? 'N/A' }}</strong>
                            </td>
                            <td class="d-none d-sm-table-cell">{{ $student->admission_number ?? 'N/A' }}</td>
                            <td class="d-none d-md-table-cell">{{ $student->course->course_name ?? $student->course->name ?? 'N/A' }}</td>
                            <td class="text-end d-none d-lg-table-cell">KES {{ number_format($student->total_fees ?? 0, 2) }}</td>
                            <td class="text-end text-success d-none d-lg-table-cell">KES {{ number_format($student->total_paid ?? 0, 2) }}</td>
                            <td class="text-end">
                                <strong class="text-danger">KES {{ number_format($student->balance ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                @php
                                    $balance = $student->balance ?? 0;
                                    if ($balance > 50000) {
                                        $badge = 'danger';
                                        $label = 'Critical';
                                    } elseif ($balance > 20000) {
                                        $badge = 'warning';
                                        $label = 'High';
                                    } elseif ($balance > 5000) {
                                        $badge = 'info';
                                        $label = 'Medium';
                                    } else {
                                        $badge = 'secondary';
                                        $label = 'Low';
                                    }
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle"></i> No outstanding balances found!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="6" class="text-end">Total Outstanding:</th>
                        <th class="text-end">KES {{ number_format($totalOutstanding ?? 0, 2) }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-end">Number of Students:</th>
                        <th class="text-end">{{ $outstandingStudents->count() ?? 0 }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Summary (visible only on small screens) -->
        <div class="row d-md-none mt-3 g-2">
            <div class="col-6">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">Total Fees</small>
                        <div class="fw-bold text-primary">KES {{ number_format($outstandingStudents->sum('total_fees') ?? 0, 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card bg-light">
                    <div class="card-body text-center p-2">
                        <small class="text-muted d-block">Total Paid</small>
                        <div class="fw-bold text-success">KES {{ number_format($outstandingStudents->sum('total_paid') ?? 0, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Distribution Chart -->
        <div class="row g-3 mt-4">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i> Balance Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="balanceChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i> Balance Status Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="250"></canvas>
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
        const courseId = document.querySelector('[name="course_id"]')?.value || '';
        const status = document.querySelector('[name="status"]')?.value || '';
        window.location.href = '{{ route("reports.export", "outstanding") }}?course_id=' + courseId + '&status=' + status;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Balance Distribution Chart
        const ctx1 = document.getElementById('balanceChart').getContext('2d');
        const studentNames = @json($outstandingStudents->map(function($s) {
            return $s->full_name ?? $s->name ?? 'Student';
        })->toArray());
        const balances = @json($outstandingStudents->pluck('balance')->toArray());
        
        if (studentNames.length > 0) {
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: studentNames,
                    datasets: [{
                        label: 'Outstanding Balance',
                        data: balances,
                        backgroundColor: balances.map(b => 
                            b > 50000 ? 'rgba(255, 99, 132, 0.7)' :
                            b > 20000 ? 'rgba(255, 206, 86, 0.7)' :
                            b > 5000 ? 'rgba(54, 162, 235, 0.7)' :
                            'rgba(108, 117, 125, 0.7)'
                        ),
                        borderColor: balances.map(b =>
                            b > 50000 ? 'rgba(255, 99, 132, 1)' :
                            b > 20000 ? 'rgba(255, 206, 86, 1)' :
                            b > 5000 ? 'rgba(54, 162, 235, 1)' :
                            'rgba(108, 117, 125, 1)'
                        ),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'KES ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Status Breakdown Chart
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        const critical = {{ $outstandingStudents->filter(function($s) { return ($s->balance ?? 0) > 50000; })->count() }};
        const high = {{ $outstandingStudents->filter(function($s) { return ($s->balance ?? 0) > 20000 && ($s->balance ?? 0) <= 50000; })->count() }};
        const medium = {{ $outstandingStudents->filter(function($s) { return ($s->balance ?? 0) > 5000 && ($s->balance ?? 0) <= 20000; })->count() }};
        const low = {{ $outstandingStudents->filter(function($s) { return ($s->balance ?? 0) <= 5000 && ($s->balance ?? 0) > 0; })->count() }};

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Critical (>50k)', 'High (20k-50k)', 'Medium (5k-20k)', 'Low (<5k)'],
                datasets: [{
                    data: [critical, high, medium, low],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
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
                                return context.label + ': ' + context.parsed + ' students (' + percentage + '%)';
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
    
    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #495057;
    }
    
    .form-select-sm {
        font-size: 0.82rem;
        padding: 6px 10px;
        border-radius: 6px;
        border-color: #dee2e6;
    }
    
    .form-select-sm:focus {
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
        
        .form-label {
            font-size: 0.7rem;
        }
        
        .form-select-sm {
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
        
        .card .card-body h3 {
            font-size: 1rem !important;
        }
        
        .card .card-body h6 {
            font-size: 0.6rem !important;
        }
        
        .card-body.p-2 {
            padding: 4px !important;
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