@extends('layouts.app')

@section('content')
<div class="report-card">
    <div class="report-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-graph-up"></i> Course Revenue Report</h2>
        <div class="no-print">
            <button onclick="printReport()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="summary-box" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h3>Grand Total Revenue</h3>
                <h2>KES {{ number_format($grandTotal, 2) }}</h2>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Course</th>
                <th class="text-center">Number of Students</th>
                <th class="text-end">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courseData as $data)
                <tr>
                    <td>{{ $data['course'] }}</td>
                    <td class="text-center">{{ $data['students_count'] }}</td>
                    <td class="text-end">KES {{ number_format($data['revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-secondary">
            <tr>
                <th colspan="2" class="text-end">Grand Total:</th>
                <th class="text-end">KES {{ number_format($grandTotal, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection