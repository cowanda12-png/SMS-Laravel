<!-- resources/views/courses/index.blade.php -->
@extends('layouts.app') 
@section('title') 
Courses 
@endsection 

@section('content') 

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Course List</h1>
            <p class="text-muted small">Manage all courses in the system</p>
        </div>
        <a href="{{ route('courses.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Add New Course
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($courses) && $courses->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course Name</th>
                                <th>Code</th>
                                <th>Credits</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-icon me-2">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $course->course_name ?? $course->name ?? 'Unknown' }}</span>
                                                <br>
                                                <small class="text-muted">ID: #{{ $course->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ $course->code }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ $course->credits ?? 3 }} Credits
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-users me-1"></i> 
                                            {{ $course->students->count() ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $course->status ?? 'active';
                                            $statusColor = $status == 'active' ? 'success' : ($status == 'inactive' ? 'danger' : 'warning');
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}" style="padding: 6px 12px;">
                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-primary" title="View Course">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-outline-success" title="Edit Course">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete({{ $course->id }})" class="btn btn-sm btn-outline-danger" title="Delete Course">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $course->id }}" action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display: none;">
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
                <div class="card-footer bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <small class="text-muted">
                                Showing 
                                <strong>{{ $courses->firstItem() ?? 0 }}</strong> 
                                to 
                                <strong>{{ $courses->lastItem() ?? 0 }}</strong> 
                                of 
                                <strong>{{ $courses->total() ?? $courses->count() }}</strong> 
                                courses
                            </small>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Previous Page Link -->
                                @if ($courses->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->previousPageUrl() }}" rel="prev">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                @endif

                                <!-- Pagination Elements -->
                                @php
                                    $currentPage = $courses->currentPage();
                                    $lastPage = $courses->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                @endphp

                                <!-- First Page -->
                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                <!-- Page Numbers -->
                                @for($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $courses->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor

                                <!-- Last Page -->
                                @if($end < $lastPage)
                                    @if($end < $lastPage - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->url($lastPage) }}">{{ $lastPage }}</a>
                                    </li>
                                @endif

                                <!-- Next Page Link -->
                                @if ($courses->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $courses->nextPageUrl() }}" rel="next">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="card shadow-sm text-center py-5">
            <div class="card-body">
                <i class="fas fa-book" style="font-size: 4rem; color: #dee2e6;"></i>
                <h4 class="mt-3">No Courses Found</h4>
                <p class="text-muted">Click the button below to add your first course.</p>
                <a href="{{ route('courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Course
                </a>
            </div>
        </div>
    @endif
</div>

<style>
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
    }
    
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.8rem;
        border-radius: 6px;
    }
    
    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 12px 12px;
        border-bottom: 2px solid #e9ecef;
        background: #f8f9fa;
    }
    
    .table td {
        padding: 12px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .table tbody tr:hover {
        background: rgba(108, 140, 255, 0.03);
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }
    
    .alert-success {
        border-left-color: #28a745;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
    }
    
    .badge {
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 6px;
    }
    
    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        color: #6c8cff;
        border: none;
        padding: 6px 14px;
        margin: 0 2px;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        background: transparent;
    }
    
    .pagination .page-link:hover {
        background: rgba(108, 140, 255, 0.1);
        color: #6c8cff;
    }
    
    .pagination .page-item.active .page-link {
        background: #6c8cff;
        color: white;
        border-radius: 6px;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #dee2e6;
        background: transparent;
        cursor: not-allowed;
    }
</style>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this course?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>

@endsection