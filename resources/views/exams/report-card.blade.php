@extends('layouts.app')

@section('title', 'Report Card - ' . $student->first_name . ' ' . $student->last_name)

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-file-alt text-primary me-2"></i> Report Card
            </h4>
            <p class="text-muted small mb-0">
                {{ $student->first_name }} {{ $student->last_name }} • {{ $student->admission_number ?? 'N/A' }}
            </p>
        </div>
        <div class="d-flex gap-1">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('exams.performance-analysis') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Student Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Student Name:</div>
                        <div class="col-8 fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Admission Number:</div>
                        <div class="col-8 fw-semibold">{{ $student->admission_number ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Course:</div>
                        <div class="col-8 fw-semibold">{{ $student->course->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Term:</div>
                        <div class="col-8 fw-semibold">{{ $term ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Academic Year:</div>
                        <div class="col-8 fw-semibold">{{ $academicYear ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Overall Grade:</div>
                        <div class="col-8">
                            @php
                                $gradeColors = [
                                    'A' => 'success',
                                    'B' => 'info',
                                    'C' => 'warning',
                                    'D' => 'warning',
                                    'F' => 'danger'
                                ];
                                $gradeColor = $gradeColors[$performance->overall_grade ?? 'F'] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $gradeColor }} fs-6">
                                {{ $performance->overall_grade ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small mb-1">Average Score</h6>
                    <h3 class="fw-bold text-primary">{{ number_format($performance->average_score ?? 0, 2) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small mb-1">Cumulative Average</h6>
                    <h3 class="fw-bold text-info">{{ number_format($performance->cumulative_average ?? 0, 2) }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small mb-1">Rank</h6>
                    <h3 class="fw-bold text-success">#{{ $performance->rank ?? 'N/A' }}</h3>
                    <small class="text-muted">out of {{ $performance->total_students ?? 0 }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Exam Results</h6>
            @if(isset($results) && $results->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Exam Name</th>
                                <th>Type</th>
                                <th class="text-end">Score</th>
                                <th class="text-end">Percentage</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $index => $result)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $result->exam->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($result->exam->type ?? 'N/A') }}</span></td>
                                    <td class="text-end">{{ number_format($result->score, 2) }}</td>
                                    <td class="text-end">{{ number_format($result->percentage, 2) }}%</td>
                                    <td>
                                        @php
                                            $gradeColor = $gradeColors[$result->grade] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $gradeColor }}">{{ $result->grade ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $result->percentage >= 50 ? 'success' : 'danger' }}">
                                            {{ $result->remarks ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">No exam results found for this student.</p>
            @endif
        </div>
    </div>

    <!-- Teacher Remarks -->
    @if(!empty($performance->teacher_remarks))
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Teacher's Remarks</h6>
                <p class="mb-0">{{ $performance->teacher_remarks }}</p>
            </div>
        </div>
    @endif
</div>

<style>
    @media print {
        .app-header, .sidebar, .app-footer, .btn, .navbar {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 20px !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
        .card-body {
            padding: 15px !important;
        }
        .badge {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection