@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-exclamation-triangle"></i> Outstanding Balances Report</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
            <button class="btn btn-success">
                <i class="bi bi-file-pdf"></i> Export PDF
            </button>
            <button class="btn btn-info">
                <i class="bi bi-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.outstanding-balances') }}" class="no-print row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Course</label>
            <select name="course" class="form-select">
                <option value="">All</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Term</label>
            <select name="term" class="form-select">
                <option value="">All</option>
                @foreach($terms as $term)
                    <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>
                        {{ $term }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Academic Year</label>
            <select name="academic_year" class="form-select">
                <option value="">All</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter"></i> Apply Filters
            </button>
        </div>
    </form>

    <div class="alert alert-warning">
        <i class="bi bi-info-circle"></i>
        <strong>{{ $outstandingStudents->count() }}</strong> students have outstanding balances.
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th class="text-end">Expected Fees</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outstandingStudents as $student)
                <tr>
                    <td>{{ $student->name }}<br><small class="text-muted">{{ $student->admission_number }}</small></td>
                    <td>{{ $student->course->name }}</td>
                    <td class="text-end">KES {{ number_format($student->total_fees, 2) }}</td>
                    <td class="text-end text-success">KES {{ number_format($student->total_paid, 2) }}</td>
                    <td class="text-end text-danger fw-bold">KES {{ number_format($student->balance, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No outstanding balances found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-secondary">
            <tr>
                <th colspan="4" class="text-end">Total Outstanding:</th>
                <th class="text-end">KES {{ number_format($outstandingStudents->sum('balance'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection