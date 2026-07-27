@extends('layouts.app')

@section('title', 'Performance Analysis')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-chart-line text-primary me-2"></i> Performance Analysis
            </h4>
            <p class="text-muted small mb-0">Track and analyze student academic performance</p>
        </div>
        <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Exams
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-2 g-sm-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Total Students</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['total_students'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Average Performance</h6>
                    <h4 class="fw-bold mb-0 text-primary">{{ number_format($stats['average_performance'] ?? 0, 2) }}%</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Highest Performance</h6>
                    <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['highest_performance'] ?? 0, 2) }}%</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Lowest Performance</h6>
                    <h4 class="fw-bold mb-0 text-danger">{{ number_format($stats['lowest_performance'] ?? 0, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2 p-sm-3">
            <form action="{{ route('exams.performance-analysis') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold mb-0">Student</label>
                    <select name="student_id" class="form-select form-select-sm">
                        <option value="">All Students</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Course</label>
                    <select name="course_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Term</label>
                    <select name="term" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($terms ?? [] as $term)
                            <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>
                                {{ $term }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Academic Year</label>
                    <select name="academic_year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($academicYears ?? [] as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request()->anyFilled(['student_id', 'course_id', 'term', 'academic_year']))
                        <a href="{{ route('exams.performance-analysis') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Records -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if(isset($performanceRecords) && $performanceRecords->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No performance records found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Student</th>
                                <th>Course</th>
                                <th>Class</th>
                                <th>Term</th>
                                <th>Academic Year</th>
                                <th class="text-end">Average</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performanceRecords ?? [] as $record)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $record->student->first_name ?? 'N/A' }} {{ $record->student->last_name ?? '' }}</div>
                                        <small class="text-muted">{{ $record->student->admission_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $record->course->name ?? 'N/A' }}</td>
                                    <td>{{ $record->class->name ?? 'N/A' }}</td>
                                    <td>{{ $record->term ?? 'N/A' }}</td>
                                    <td>{{ $record->academic_year ?? 'N/A' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($record->average_score, 2) }}%</td>
                                    <td>
                                        @php
                                            $gradeColors = [
                                                'A' => '#28a745',
                                                'B' => '#17a2b8',
                                                'C' => '#ffc107',
                                                'D' => '#fd7e14',
                                                'F' => '#dc3545'
                                            ];
                                            $gradeColor = $gradeColors[$record->overall_grade] ?? '#6c757d';
                                        @endphp
                                        <span class="badge" style="background: {{ $gradeColor }};">
                                            {{ $record->overall_grade ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $record->status_badge['color'] ?? 'secondary' }}">
                                            {{ $record->status_badge['label'] ?? ucfirst($record->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('exams.report-card', ['studentId' => $record->student_id, 'term' => $record->term ?? 'Term 1', 'academicYear' => $record->academic_year ?? date('Y')]) }}" 
                                           class="btn btn-sm btn-info" title="Report Card">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-3">
                    <span class="text-muted small">
                        Showing {{ $performanceRecords->firstItem() ?? 0 }} to {{ $performanceRecords->lastItem() ?? 0 }} 
                        of {{ $performanceRecords->total() ?? 0 }}
                    </span>
                    {{ $performanceRecords->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection