@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="report-card">
        <div class="report-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="fw-bold mb-0"><i class="bi bi-person-vcard me-2"></i> Student Fee Statement</h2>
            <div class="no-print d-flex gap-2">
                <button onclick="printReport()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> <span class="d-none d-sm-inline">Print Statement</span>
                </button>
                <button onclick="exportPDF()" class="btn btn-success btn-sm">
                    <i class="bi bi-file-pdf me-1"></i> <span class="d-none d-sm-inline">Export PDF</span>
                </button>
            </div>
        </div>

        <!-- Student Selection -->
        <div class="no-print mb-4">
            <form method="GET" action="{{ route('reports.student-statement') }}" class="row g-2 g-sm-3">
                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold mb-0">Select Student</label>
                    <select name="student_id" id="studentSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Select Student --</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ $selectedStudent && $selectedStudent->id == $student->id ? 'selected' : '' }}>
                                {{ $student->admission_number ?? $student->id }} - {{ Str::limit($student->full_name ?? $student->name ?? 'Student', 25) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($selectedStudent)
            <!-- Student Information -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Student Information</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th width="120" class="bg-light">Name</th>
                                    <td>{{ $selectedStudent->full_name ?? $selectedStudent->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Admission No</th>
                                    <td>{{ $selectedStudent->admission_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Course</th>
                                    <td>{{ $selectedStudent->course->course_name ?? $selectedStudent->course->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Academic Year</th>
                                    <td>{{ $selectedStudent->academic_year ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Fee Summary</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tr>
                                    <th width="140" class="bg-light">Total Fees Expected</th>
                                    <td class="fw-bold">KES {{ number_format($studentFees->sum('amount') ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Total Amount Paid</th>
                                    <td class="fw-bold text-success">KES {{ number_format($studentFees->where('status', 'paid')->sum('amount') ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Outstanding Balance</th>
                                    <td class="fw-bold {{ ($studentFees->sum('amount') - $studentFees->where('status', 'paid')->sum('amount')) > 0 ? 'text-danger' : 'text-success' }}">
                                        KES {{ number_format(($studentFees->sum('amount') - $studentFees->where('status', 'paid')->sum('amount')) ?? 0, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Pending Amount</th>
                                    <td class="fw-bold text-warning">KES {{ number_format($studentFees->where('status', 'pending')->sum('amount') ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Overdue Amount</th>
                                    <td class="fw-bold text-danger">KES {{ number_format($studentFees->where('status', 'overdue')->sum('amount') ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i> Fee Payment History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="d-none d-sm-table-cell">Term</th>
                                    <th class="d-none d-md-table-cell">Academic Year</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($studentFees as $fee)
                                    <tr>
                                        <td>{{ $fee->payment_date ? date('d-m-Y', strtotime($fee->payment_date)) : 'N/A' }}</td>
                                        <td>{{ $fee->description ?? 'Fee Payment' }}</td>
                                        <td class="d-none d-sm-table-cell">{{ $fee->term ?? 'N/A' }}</td>
                                        <td class="d-none d-md-table-cell">{{ $fee->academic_year ?? 'N/A' }}</td>
                                        <td class="text-end">KES {{ number_format($fee->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($fee->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox"></i> No fee records found for this student
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="4" class="text-end">Total Fees:</th>
                                    <th class="text-end">KES {{ number_format($studentFees->sum('amount') ?? 0, 2) }}</th>
                                    <th></th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="4" class="text-end">Total Paid:</th>
                                    <th class="text-end">KES {{ number_format($studentFees->where('status', 'paid')->sum('amount') ?? 0, 2) }}</th>
                                    <th></th>
                                </tr>
                                <tr class="table-danger">
                                    <th colspan="4" class="text-end">Balance:</th>
                                    <th class="text-end">KES {{ number_format(($studentFees->sum('amount') - $studentFees->where('status', 'paid')->sum('amount')) ?? 0, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                <h5>Please select a student to view their fee statement.</h5>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function printReport() {
        window.print();
    }

    function exportPDF() {
        var studentId = document.getElementById('studentSelect').value;
        if (!studentId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Student Selected',
                text: 'Please select a student first.',
                confirmButtonColor: '#6c8cff'
            });
            return;
        }
        window.location.href = '{{ route("reports.student-statement.pdf") }}?student_id=' + studentId;
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
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    
    .card {
        border-radius: 10px !important;
        overflow: hidden;
        border: 1px solid #e9ecef !important;
    }
    
    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 10px 15px;
    }
    
    .card-header h5 {
        font-size: 0.95rem;
    }
    
    .table th {
        white-space: nowrap;
        font-size: 0.75rem;
        padding: 8px 12px;
        vertical-align: middle;
    }
    
    .table td {
        padding: 8px 12px;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    
    .table .bg-light {
        background-color: #f8f9fa !important;
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
        border-radius: 8px;
    }
    
    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #495057;
    }
    
    .form-select-sm {
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 6px;
        border-color: #dee2e6;
    }
    
    .form-select-sm:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    
    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }
        .report-card {
            margin: 0;
            padding: 10px;
            box-shadow: none;
            border: none;
        }
        .report-header {
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .table {
            font-size: 11px;
        }
        .table th {
            font-size: 10px;
            padding: 4px 8px;
        }
        .table td {
            padding: 4px 8px;
        }
        .badge {
            border: 1px solid #000;
            background: transparent !important;
            color: #000 !important;
        }
        .card {
            border: 1px solid #ddd !important;
            margin-bottom: 10px;
        }
        .card-header {
            background: #f8f9fa !important;
        }
        .text-success {
            color: #28a745 !important;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .text-warning {
            color: #ffc107 !important;
        }
        .alert-info {
            background: #f8f9fa !important;
            border: 1px solid #ddd !important;
            color: #212529 !important;
        }
        .table-secondary {
            background: #f8f9fa !important;
        }
        .table-success {
            background: #d4edda !important;
        }
        .table-danger {
            background: #f8d7da !important;
        }
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
            padding: 6px 8px;
        }
        
        .table th {
            font-size: 0.65rem;
            padding: 6px 8px;
        }
        
        .badge {
            font-size: 0.6rem;
            padding: 3px 8px;
        }
        
        .card-header h5 {
            font-size: 0.85rem;
        }
        
        .form-label {
            font-size: 0.7rem;
        }
        
        .form-select-sm {
            font-size: 0.8rem;
            padding: 5px 8px;
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
            padding: 4px 6px;
        }
        
        .table th {
            font-size: 0.55rem;
            padding: 4px 6px;
        }
        
        .badge {
            font-size: 0.5rem;
            padding: 2px 6px;
        }
        
        .card-body {
            padding: 8px !important;
        }
        
        .card-header {
            padding: 6px 10px;
        }
        
        .card-header h5 {
            font-size: 0.75rem;
        }
        
        .alert-info {
            padding: 10px !important;
        }
        
        .alert-info h5 {
            font-size: 0.9rem;
        }
        
        .alert-info .fs-3 {
            font-size: 2rem !important;
        }
    }
</style>
@endpush
@endsection