@extends('layouts.app')

@section('title', 'Fee Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fee Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('fees.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Fee
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="feesTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fees as $key => $fee)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            {{ $fee->student->name ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">ID: {{ $fee->student->student_id ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $fee->course->course_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="font-weight-bold">
                                                ${{ number_format($fee->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ $fee->due_date ? date('M d, Y', strtotime($fee->due_date)) : 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'paid' => 'success',
                                                    'unpaid' => 'danger',
                                                    'partial' => 'warning',
                                                    'overdue' => 'danger',
                                                    'pending' => 'info'
                                                ];
                                                $color = $statusColors[$fee->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ ucfirst($fee->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $fee->payment_date ? date('M d, Y', strtotime($fee->payment_date)) : 'Not Paid' }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('fees.show', $fee->id) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('fees.edit', $fee->id) }}" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('fees.destroy', $fee->id) }}" 
                                                      method="POST" 
                                                      style="display: inline-block;"
                                                      onsubmit="return confirm('Are you sure you want to delete this fee record?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle"></i> No fee records found.
                                                <a href="{{ route('fees.create') }}" class="alert-link">Add your first fee record</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right font-weight-bold">Total:</th>
                                    <th class="font-weight-bold">
                                        ${{ number_format($fees->sum('amount') ?? 0, 2) }}
                                    </th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($fees, 'links'))
                        <div class="d-flex justify-content-center mt-3">
                            {{ $fees->links() }}
                        </div>
                    @endif
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .card-header .card-title {
        color: white;
        font-weight: 600;
    }
    .card-header .card-tools a {
        color: white;
        border-color: rgba(255,255,255,0.5);
    }
    .card-header .card-tools a:hover {
        background: rgba(255,255,255,0.2);
        border-color: white;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .badge {
        font-size: 12px;
        padding: 5px 10px;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    tfoot {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    tfoot th {
        border-top: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#feesTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "order": [[0, 'desc']],
            "language": {
                "emptyTable": "No fee records found"
            }
        });
    });
</script>
@endpush