@extends('layouts.app')

@section('title', 'Exams')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-book-open text-primary me-2"></i> Exams Management
            </h4>
            <p class="text-muted small mb-0">Manage all exams in the system</p>
        </div>
        <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Add New Exam
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2 p-sm-3">
            <form action="{{ route('exams.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label small fw-semibold mb-0">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search exams..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold mb-0">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold mb-0">Sort By</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'sort']))
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Exam management module is under development.</p>
                <p class="text-muted small">This feature will be available soon.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection