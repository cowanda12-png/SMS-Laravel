@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-cash-stack"></i> Fee Collection Report</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.fee-collection') }}" class="no-print row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="">All</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
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
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter"></i> Apply Filters
            </button>
        </div>
    </form>

    <!-- Summary Section -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="summary-box">
                <h3>Total Amount Collected</h3>
                <h2>KES {{ number_format($summary['total_amount'], 2) }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>Total Transactions</h3>
                <h2>{{ number_format($summary['total_transactions']) }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>Average Payment</h3>
                <h2>KES {{ number_format($summary['average_payment'], 2) }}</h2>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Receipt No</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->receipt_no }}</td>
                    <td>{{ $payment->student->name ?? 'N/A' }}</td>
                    <td>KES {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ date('d-m-Y', strtotime($payment->payment_date)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No payments found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection