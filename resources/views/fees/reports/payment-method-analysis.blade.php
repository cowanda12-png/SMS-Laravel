@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-pie-chart"></i> Payment Method Analysis</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Payment Method</th>
                <th class="text-center">Transactions</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = $paymentMethods->sum('total_amount');
            @endphp
            @forelse($paymentMethods as $method)
                @php
                    $percentage = $grandTotal > 0 ? ($method->total_amount / $grandTotal) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ $method->payment_method }}</strong></td>
                    <td class="text-center">{{ number_format($method->transactions) }}</td>
                    <td class="text-end">KES {{ number_format($method->total_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($percentage, 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No payment data available</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-secondary">
            <tr>
                <th>Total</th>
                <th class="text-center">{{ number_format($paymentMethods->sum('transactions')) }}</th>
                <th class="text-end">KES {{ number_format($grandTotal, 2) }}</th>
                <th class="text-end">100%</th>
            </tr>
        </tfoot>
    </table>

    @if($paymentMethods->count() > 0)
        <div class="mt-4">
            <h5>Summary</h5>
            <div class="row">
                @foreach($paymentMethods as $method)
                    @php
                        $percentage = $grandTotal > 0 ? ($method->total_amount / $grandTotal) * 100 : 0;
                    @endphp
                    <div class="col-md-3">
                        <div class="card mb-2">
                            <div class="card-body text-center">
                                <h6>{{ $method->payment_method }}</h6>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ ['primary', 'success', 'warning', 'info'][$loop->index % 4] }}" 
                                         style="width: {{ $percentage }}%;">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                                <small>KES {{ number_format($method->total_amount, 0) }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection