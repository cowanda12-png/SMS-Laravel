@extends('layouts.app')

@section('title')
Courses
@endsection

@section('content')

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Course List</h1>
            <p class="text-muted small">Manage all courses in the system</p>
        </div>
        <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Add New Course</span>
            <span class="d-inline d-sm-none">Add</span>
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($courses) && $courses->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">#</th>
                                <th>Course Name</th>
                                <th class="d-none d-sm-table-cell">Code</th>
                                <th class="d-none d-md-table-cell">Credits</th>
                                <th class="d-none d-lg-table-cell">Students</th>
                                <th class="d-none d-xl-table-cell">Status</th>
                                <th class="text-center pe-3" style="min-width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                @php
                                    $status = $course->status ?? 'active';
                                    $statusColor = $status == 'active' ? 'success' : ($status == 'inactive' ? 'danger' : 'warning');
                                    $studentCount = $course->students->count() ?? 0;
                                @endphp
                                <tr>
                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-2">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold" style="font-size: 0.9rem;">
                                                    {{ $course->course_name ?? $course->name ?? 'Unknown' }}
                                                </span>
                                                <br>
                                                <small class="text-muted">ID: #{{ $course->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ $course->code ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ $course->credits ?? 3 }} Credits
                                        </span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-users me-1"></i> 
                                            {{ $studentCount }}
                                        </span>
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="padding: 4px 10px;">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                                            <a href="{{ route('courses.show', $course->id) }}" 
                                               class="btn btn-sm btn-primary action-btn" 
                                               title="View Course">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('courses.edit', $course->id) }}" 
                                               class="btn btn-sm btn-success action-btn" 
                                               title="Edit Course">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('courses.students', $course->id) }}" 
                                               class="btn btn-sm btn-info action-btn" 
                                               title="View Students">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <button onclick="confirmDelete({{ $course->id }})" 
                                                    class="btn btn-sm btn-danger action-btn" 
                                                    title="Delete Course">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $course->id }}" 
                                                  action="{{ route('courses.destroy', $course->id) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            @if(method_exists($courses, 'links'))
                <div class="card-footer bg-transparent border-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                Showing 
                                <strong>{{ $courses->firstItem() ?? 0 }}</strong> 
                                to 
                                <strong>{{ $courses->lastItem() ?? 0 }}</strong> 
                                of 
                                <strong>{{ $courses->total() ?? $courses->count() }}</strong> 
                                courses
                            </span>
                        </div>
                        <div class="pagination-wrapper">
                            {{ $courses->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="fas fa-book" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3 fw-bold">No Courses Found</h4>
                <p class="text-muted">Click the button below to add your first course.</p>
                <a href="{{ route('courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Course
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    /* Course Icon */
    .course-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    
    .course-icon:hover {
        background: rgba(108, 140, 255, 0.2);
        transform: scale(1.05);
    }
    
    /* Action Buttons */
    .action-btn {
        padding: 4px 6px;
        font-size: 0.7rem;
        border-radius: 6px;
        min-width: 28px;
        min-height: 28px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .action-btn i {
        font-size: 0.75rem;
    }
    
    /* Button Colors */
    .btn-primary {
        background: #6c8cff;
        border: none;
    }
    .btn-primary:hover {
        background: #5a7ae6;
    }
    
    .btn-success {
        background: #28a745;
        border: none;
    }
    .btn-success:hover {
        background: #218838;
    }
    
    .btn-info {
        background: #17a2b8;
        border: none;
        color: white;
    }
    .btn-info:hover {
        background: #138496;
        color: white;
    }
    
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.78rem;
        border-radius: 6px;
        font-weight: 500;
    }
    
    /* Table Styling */
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 10px 10px;
        border-bottom: 2px solid #e9ecef;
        background: #f8f9fa;
        white-space: nowrap;
    }
    
    .table td {
        padding: 10px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .table tbody tr {
        transition: all 0.15s ease;
    }
    
    .table tbody tr:hover {
        background: rgba(108, 140, 255, 0.04);
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Badge Styling */
    .badge {
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    
    /* Card Styling */
    .card {
        border-radius: 12px !important;
        overflow: hidden;
        border: none !important;
    }
    
    .card-body {
        padding: 0;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
        background: transparent;
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
        padding: 10px 15px;
    }
    
    .alert-success {
        border-left-color: #28a745;
        background: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
        background: #f8d7da;
        color: #721c24;
    }
    
    /* Pagination Styling */
    .pagination-wrapper {
        overflow-x: auto;
        max-width: 100%;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    
    .pagination-wrapper::-webkit-scrollbar {
        display: none;
    }
    
    .pagination {
        margin-bottom: 0;
        gap: 3px;
        flex-wrap: nowrap;
    }
    
    .pagination .page-link {
        color: #6c8cff;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.8rem;
        background: transparent;
    }
    
    .pagination .page-link:hover {
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
        transform: translateY(-1px);
    }
    
    .pagination .page-item.active .page-link {
        background: #6c8cff;
        color: white;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(108, 140, 255, 0.3);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #dee2e6;
        background: transparent;
        cursor: not-allowed;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        border-radius: 6px;
    }
    
    /* Responsive */
    @media (max-width: 1199.98px) {
        .table th, .table td {
            padding: 8px 6px;
        }
    }
    
    @media (max-width: 991.98px) {
        .table th, .table td {
            padding: 6px 5px;
            font-size: 0.8rem;
        }
        
        .action-btn {
            padding: 3px 5px;
            min-width: 24px;
            min-height: 24px;
            font-size: 0.6rem;
        }
        
        .action-btn i {
            font-size: 0.65rem;
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 3px 8px;
        }
    }
    
    @media (max-width: 767.98px) {
        .container-fluid {
            padding: 0.5rem !important;
        }
        
        .card-footer .d-flex {
            flex-direction: column;
            align-items: center !important;
            gap: 10px;
        }
        
        .pagination .page-link {
            padding: 3px 8px;
            font-size: 0.7rem;
        }
        
        .table td {
            padding: 5px 4px;
            font-size: 0.75rem;
        }
        
        .table th {
            padding: 5px 4px;
            font-size: 0.6rem;
        }
        
        .action-btn {
            padding: 2px 4px;
            min-width: 20px;
            min-height: 20px;
            font-size: 0.55rem;
        }
        
        .action-btn i {
            font-size: 0.55rem;
        }
        
        .course-icon {
            width: 28px;
            height: 28px;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .table td {
            padding: 4px 3px;
            font-size: 0.7rem;
        }
        
        .table th {
            padding: 4px 3px;
            font-size: 0.55rem;
            letter-spacing: 0.3px;
        }
        
        .action-btn {
            padding: 2px 3px;
            min-width: 18px;
            min-height: 18px;
            font-size: 0.5rem;
            border-radius: 4px;
        }
        
        .action-btn i {
            font-size: 0.5rem;
        }
        
        .badge {
            font-size: 0.6rem;
            padding: 2px 6px;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 3px 8px;
        }
        
        .pagination .page-link {
            padding: 2px 6px;
            font-size: 0.65rem;
        }
        
        .card-footer .text-muted {
            font-size: 0.65rem !important;
        }
    }
    
    @media (max-width: 400px) {
        .table td {
            padding: 3px 2px;
            font-size: 0.6rem;
        }
        
        .table th {
            padding: 3px 2px;
            font-size: 0.5rem;
        }
        
        .action-btn {
            padding: 1px 2px;
            min-width: 16px;
            min-height: 16px;
            font-size: 0.45rem;
            border-radius: 3px;
        }
        
        .action-btn i {
            font-size: 0.45rem;
        }
        
        .course-icon {
            width: 22px;
            height: 22px;
            font-size: 0.6rem;
        }
    }
</style>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background: rgba(108, 140, 255, 0.04);
        cursor: default;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
    }
    
    .fa-circle {
        display: inline-block;
    }
</style>
@endpush

@endsection