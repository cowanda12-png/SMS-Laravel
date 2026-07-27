@extends('layouts.app')

@section('title', 'Exams')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-book-open text-primary me-2"></i> Exams
            </h4>
            <p class="text-muted small mb-0">Manage all exams and assessments</p>
        </div>
        <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Create Exam
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2 p-sm-3">
            <form action="{{ route('exams.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold mb-0">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Exam name, code..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Course</label>
                    <select name="course_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Class</label>
                    <select name="class_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'course_id', 'class_id', 'status']))
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($exams->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No exams found.</p>
                    <a href="{{ route('exams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Create Exam
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Code</th>
                                <th>Exam Name</th>
                                <th>Course</th>
                                <th>Class</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Max Score</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-semibold">{{ $exam->code }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $exam->name }}</div>
                                        @if($exam->description)
                                            <small class="text-muted">{{ Str::limit($exam->description, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $exam->course->name ?? 'N/A' }}</td>
                                    <td>{{ $exam->class->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($exam->type) }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $exam->formatted_date }}</div>
                                        @if($exam->submission_date)
                                            <small class="text-muted">Due: {{ $exam->submission_date->format('d M Y') }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $exam->max_score }}</td>
                                    <td>
                                        <span class="badge bg-{{ $exam->status_badge['color'] }}">
                                            {{ $exam->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                                            <a href="{{ route('exams.show', $exam->id) }}" 
                                               class="btn btn-sm btn-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('exams.edit', $exam->id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('exams.record-marks', $exam->id) }}" 
                                               class="btn btn-sm btn-success" title="Record Marks">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('exams.destroy', $exam->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        title="Delete" onclick="return confirm('Delete this exam?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-3">
                    <span class="text-muted small">
                        Showing {{ $exams->firstItem() ?? 0 }} to {{ $exams->lastItem() ?? 0 }} 
                        of {{ $exams->total() }}
                    </span>
                    {{ $exams->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection