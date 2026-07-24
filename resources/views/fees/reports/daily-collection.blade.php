@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-calendar-day"></i> Daily Collection Report</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-calendar"></i> Report for: <strong>{{ date('l, d-m-Y', strtotime($today)) }}</strong>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="summary-box">
                <h3>Today's Total Collections</h3>
                <h2>KES {{ number_format($summary['total_collections'], 2) }}</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>Number of Transactions</h3>
                <h2>{{ number_format($summary['total_transactions']) }}</h2>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Student</th>
                <th>Receipt No</th>
                <th class="text-end">Amount</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->student->name ?? 'N/A' }}</td>
                    <td>{{ $payment->receipt_no }}</td>
                    <td class="text-end">KES {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->payment_method }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No payments recorded today</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection