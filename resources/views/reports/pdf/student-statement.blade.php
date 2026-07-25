<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Fee Statement</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 5px 10px;
            font-size: 14px;
        }
        .student-info .label {
            font-weight: bold;
            width: 150px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .summary-box {
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-box.total { background: #e3f2fd; }
        .summary-box.paid { background: #e8f5e9; }
        .summary-box.balance { background: #ffebee; }
        .summary-box.pending { background: #fff3e0; }
        .summary-box.overdue { background: #fce4ec; }
        .summary-box .amount {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }
        .summary-box .label-text {
            font-size: 13px;
            color: #666;
        }
        table.fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
        }
        table.fee-table th {
            background: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table.fee-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        table.fee-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        table.fee-table .text-end {
            text-align: right;
        }
        .status-badge {
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status-paid { background: #4caf50; }
        .status-pending { background: #ff9800; }
        .status-overdue { background: #f44336; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .tfoot-total {
            font-weight: bold;
            background: #e3f2fd !important;
        }
        .tfoot-paid {
            font-weight: bold;
            background: #e8f5e9 !important;
        }
        .tfoot-balance {
            font-weight: bold;
            background: #ffebee !important;
        }
        .text-danger { color: #f44336; }
        .text-success { color: #4caf50; }
        .text-warning { color: #ff9800; }
        .fw-bold { font-weight: bold; }
        .mt-2 { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>STUDENT FEE STATEMENT</h1>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td><strong>{{ $selectedStudent->full_name ?? $selectedStudent->name ?? 'N/A' }}</strong></td>
                <td class="label">Admission No:</td>
                <td><strong>{{ $selectedStudent->admission_number ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Course:</td>
                <td>{{ $selectedStudent->course->course_name ?? $selectedStudent->course->name ?? 'N/A' }}</td>
                <td class="label">Academic Year:</td>
                <td>{{ $selectedStudent->academic_year ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <h3>Fee Summary</h3>
    <div class="summary-grid">
        <div class="summary-box total">
            <div class="label-text">Total Fees Expected</div>
            <div class="amount">KES {{ number_format($totalFees, 2) }}</div>
        </div>
        <div class="summary-box paid">
            <div class="label-text">Total Amount Paid</div>
            <div class="amount text-success">KES {{ number_format($totalPaid, 2) }}</div>
        </div>
        <div class="summary-box balance">
            <div class="label-text">Outstanding Balance</div>
            <div class="amount {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                KES {{ number_format($balance, 2) }}
            </div>
        </div>
        <div class="summary-box pending">
            <div class="label-text">Pending Amount</div>
            <div class="amount text-warning">KES {{ number_format($pendingAmount, 2) }}</div>
        </div>
        <div class="summary-box overdue">
            <div class="label-text">Overdue Amount</div>
            <div class="amount text-danger">KES {{ number_format($overdueAmount, 2) }}</div>
        </div>
    </div>

    <h3>Payment History</h3>
    <table class="fee-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Term</th>
                <th>Academic Year</th>
                <th class="text-end">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentFees as $fee)
                <tr>
                    <td>{{ $fee->payment_date ? date('d-m-Y', strtotime($fee->payment_date)) : 'N/A' }}</td>
                    <td>{{ $fee->description ?? 'Fee Payment' }}</td>
                    <td>{{ $fee->term ?? 'N/A' }}</td>
                    <td>{{ $fee->academic_year ?? 'N/A' }}</td>
                    <td class="text-end">KES {{ number_format($fee->amount, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $fee->status }}">
                            {{ ucfirst($fee->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No fee records found</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="tfoot-total">
                <td colspan="4" class="text-end">Total Fees:</td>
                <td class="text-end">KES {{ number_format($totalFees, 2) }}</td>
                <td></td>
            </tr>
            <tr class="tfoot-paid">
                <td colspan="4" class="text-end">Total Paid:</td>
                <td class="text-end">KES {{ number_format($totalPaid, 2) }}</td>
                <td></td>
            </tr>
            <tr class="tfoot-balance">
                <td colspan="4" class="text-end">Balance:</td>
                <td class="text-end">KES {{ number_format($balance, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a system-generated statement. Please verify the details with the finance office.</p>
        <p>Generated by {{ Auth::user()->name ?? 'System' }} on {{ $generatedDate }}</p>
    </div>
</body>
</html>