@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-calendar-month"></i> Monthly Collection Report</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <!-- Month/Year Selector -->
    <form method="GET" action="{{ route('reports.monthly-collection') }}" class="no-print row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Month</label>
            <select name="month" class="form-select">
                @foreach($months as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Year</label>
            <select name="year" class="form-select">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-calendar"></i> View Report
            </button>
        </div>
    </form>

    <div class="alert alert-info">
        <i class="bi bi-calendar"></i> Report for: <strong>{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</strong>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="summary-box">
                <h3>Total Collected</h3>
                <h2>KES {{ number_format($summary['total_collected'], 2) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>Transactions</h3>
                <h2>{{ number_format($summary['total_transactions']) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>Highest Payment</h3>
                <h2>KES {{ number_format($summary['highest_payment'], 2) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3>Average Payment</h3>
                <h2>KES {{ number_format($summary['average_payment'], 2) }}</h2>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Receipt No</th>
                <th>Student</th>
                <th class="text-end">Amount</th>
                <th>Method</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->receipt_no }}</td>
                    <td>{{ $payment->student->name ?? 'N/A' }}</td>
                    <td class="text-end">KES {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td>{{ date('d-m-Y', strtotime($payment->payment_date)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No payments for this month</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection