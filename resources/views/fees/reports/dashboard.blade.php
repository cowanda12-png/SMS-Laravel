@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="report-card">
            <h2><i class="bi bi-speedometer2"></i> Reports Dashboard</h2>
            <p class="text-muted">Welcome to the School Management System Reports & Analytics Module</p>
            
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <h2 class="card-text">{{ \App\Models\Students::count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <h2 class="card-text">KES {{ number_format(\App\Models\Fee::sum('amount'), 0) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Outstanding Balance</h5>
                            <h2 class="card-text">KES {{ number_format(\App\Models\Fee::where('status', 'pending')->orWhere('status', 'overdue')->sum('amount'), 0) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Today's Collections</h5>
                            <h2 class="card-text">KES {{ number_format(\App\Models\Fee::whereDate('payment_date', date('Y-m-d'))->sum('amount'), 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="row mt-4">
                <h5>Quick Reports</h5>
                @php
                    $reports = [
                        ['route' => route('reports.student-statement'), 'icon' => 'bi-person-vcard', 'color' => 'primary', 'title' => 'Student Statement'],
                        ['route' => route('reports.fee-collection'), 'icon' => 'bi-cash-stack', 'color' => 'success', 'title' => 'Fee Collection'],
                        ['route' => route('reports.outstanding-balances'), 'icon' => 'bi-exclamation-triangle', 'color' => 'warning', 'title' => 'Outstanding Balances'],
                        ['route' => route('reports.course-revenue'), 'icon' => 'bi-graph-up', 'color' => 'info', 'title' => 'Course Revenue'],
                        ['route' => route('reports.daily-collection'), 'icon' => 'bi-calendar-day', 'color' => 'danger', 'title' => 'Daily Collection'],
                        ['route' => route('reports.monthly-collection'), 'icon' => 'bi-calendar-month', 'color' => 'dark', 'title' => 'Monthly Collection'],
                        ['route' => route('reports.mpesa-transactions'), 'icon' => 'bi-phone', 'color' => 'success', 'title' => 'M-Pesa Transactions'],
                        ['route' => route('reports.payment-method-analysis'), 'icon' => 'bi-pie-chart', 'color' => 'primary', 'title' => 'Payment Method Analysis'],
                    ];
                @endphp
                @foreach($reports as $report)
                    <div class="col-md-3">
                        <a href="{{ $report['route'] }}" class="text-decoration-none">
                            <div class="card text-center mb-3 shadow-sm">
                                <div class="card-body">
                                    <i class="bi {{ $report['icon'] }} fs-1 text-{{ $report['color'] }}"></i>
                                    <h6 class="mt-2">{{ $report['title'] }}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection