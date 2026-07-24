@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-person-vcard"></i> Student Fee Statement</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print Statement
            </button>
            <button onclick="window.location.href='{{ route('reports.student-statement') }}?export=pdf&student_id=' + document.getElementById('studentSelect').value" class="btn btn-success">
                <i class="bi bi-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Student Selection -->
    <div class="no-print mb-4">
        <form method="GET" action="{{ route('reports.student-statement') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Select Student</label>
                <select name="student_id" id="studentSelect" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Student --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $selectedStudent && $selectedStudent->id == $student->id ? 'selected' : '' }}>
                            {{ $student->admission_number }} - {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedStudent)
        <!-- Student Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Student Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Name</th>
                        <td>{{ $selectedStudent->name }}</td>
                    </tr>
                    <tr>
                        <th>Admission Number</th>
                        <td>{{ $selectedStudent->admission_number }}</td>
                    </tr>
                    <tr>
                        <th>Course</th>
                        <td>{{ $selectedStudent->course->name }}</td>
                    </tr>
                    <tr>
                        <th>Academic Year</th>
                        <td>{{ $selectedStudent->academic_year }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Fee Summary</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Total Fees Expected</th>
                        <td class="fw-bold">KES {{ number_format($selectedStudent->total_fees, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Amount Paid</th>
                        <td class="fw-bold text-success">KES {{ number_format($selectedStudent->total_paid, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Outstanding Balance</th>
                        <td class="fw-bold {{ $selectedStudent->balance > 0 ? 'text-danger' : 'text-success' }}">
                            KES {{ number_format($selectedStudent->balance, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment History -->
        <h5>Payment History</h5>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Receipt No</th>
                    <th>Payment Method</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($selectedStudent->payments as $payment)
                    <tr>
                        <td>{{ date('d-m-Y', strtotime($payment->payment_date)) }}</td>
                        <td>{{ $payment->receipt_no }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td class="text-end">KES {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No payments recorded</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-secondary">
                <tr>
                    <th colspan="3" class="text-end">Total Paid:</th>
                    <th class="text-end">KES {{ number_format($selectedStudent->total_paid, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> Please select a student to view their fee statement.
        </div>
    @endif
</div>
@endsection