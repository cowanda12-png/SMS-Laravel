@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-phone"></i> M-Pesa Transactions Report</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.mpesa-transactions') }}" class="no-print row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Student</label>
            <select name="student" class="form-select">
                <option value="">All</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ request('student') == $student->id ? 'selected' : '' }}>
                        {{ $student->admission_number }} - {{ $student->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date Range</label>
            <input type="text" name="date_range" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD" value="{{ request('date_range') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter"></i> Apply Filters
            </button>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Receipt No</th>
                <th>Student</th>
                <th>Phone</th>
                <th class="text-end">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->receipt_no }}</td>
                    <td>{{ $transaction->student->name ?? 'N/A' }}</td>
                    <td>{{ $transaction->phone_number }}</td>
                    <td class="text-end">KES {{ number_format($transaction->amount, 2) }}</td>
                    <td>
                        @if($transaction->status == 'Completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($transaction->status == 'Pending')
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No M-Pesa transactions found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection