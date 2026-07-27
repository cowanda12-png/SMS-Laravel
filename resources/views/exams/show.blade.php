@extends('layouts.app')

@section('title', $exam->name)

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-info-circle text-primary me-2"></i> {{ $exam->name }}
            </h4>
            <p class="text-muted small mb-0">
                {{ $exam->code }} • {{ ucfirst($exam->type) }} • {{ $exam->exam_date ? $exam->exam_date->format('d M Y') : 'N/A' }}
            </p>
        </div>
        <div class="d-flex gap-1">
            <a href="{{ route('exams.record-marks', $exam->id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt me-1"></i> Record Marks
            </a>
            <a href="{{ route('exams.edit', $exam->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-2 g-sm-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Total Students</h6>
                    <h4 class="fw-bold mb-0">{{ $totalStudents ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Submitted</h6>
                    <h4 class="fw-bold mb-0 text-info">{{ $submitted ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Graded</h6>
                    <h4 class="fw-bold mb-0 text-success">{{ $graded ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Absent</h6>
                    <h4 class="fw-bold mb-0 text-danger">{{ $absent ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Average Score</h6>
                    <h4 class="fw-bold mb-0 text-primary">{{ number_format($averageScore ?? 0, 2) }}%</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-sm-3 text-center">
                    <h6 class="text-muted small mb-1">Pass Rate</h6>
                    <h4 class="fw-bold mb-0 text-success">{{ number_format($passRate ?? 0, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Exam Information</h6>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Course:</div>
                        <div class="col-7 fw-semibold">{{ $exam->course->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Class:</div>
                        <div class="col-7 fw-semibold">{{ $exam->class->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Type:</div>
                        <div class="col-7 fw-semibold">{{ ucfirst($exam->type) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Exam Date:</div>
                        <div class="col-7 fw-semibold">{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : 'N/A' }}</div>
                    </div>
                    @if($exam->submission_date)
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Submission Date:</div>
                        <div class="col-7 fw-semibold">{{ $exam->submission_date->format('d M Y') }}</div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Max Score:</div>
                        <div class="col-7 fw-semibold">{{ $exam->max_score }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Passing Score:</div>
                        <div class="col-7 fw-semibold">{{ $exam->passing_score }}%</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Weight:</div>
                        <div class="col-7 fw-semibold">{{ $exam->weight }}%</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Status:</div>
                        <div class="col-7">
                            <span class="badge bg-{{ $exam->status_badge['color'] ?? 'secondary' }}">
                                {{ $exam->status_badge['label'] ?? ucfirst($exam->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Grade Distribution</h6>
                    @if(!empty($gradeDistribution))
                        <div class="row">
                            @foreach($gradeDistribution as $grade => $count)
                                <div class="col-2 text-center">
                                    <div class="fw-bold" style="font-size: 1.2rem;">{{ $grade }}</div>
                                    <div class="text-muted small">{{ $count }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between">
                                @foreach($gradeDistribution as $grade => $count)
                                    @php
                                        $total = array_sum($gradeDistribution);
                                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                        $color = $grade == 'A' ? '#28a745' : ($grade == 'B' ? '#17a2b8' : ($grade == 'C' ? '#ffc107' : ($grade == 'D' ? '#fd7e14' : '#dc3545')));
                                    @endphp
                                    <div style="width: {{ $percentage }}%; height: 8px; background: {{ $color }}; border-radius: 4px;"></div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No grades recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Description & Instructions -->
    @if($exam->description || $exam->instructions)
    <div class="row g-3 mb-4">
        @if($exam->description)
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Description</h6>
                    <p class="mb-0">{{ $exam->description }}</p>
                </div>
            </div>
        </div>
        @endif
        @if($exam->instructions)
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Instructions</h6>
                    <p class="mb-0">{{ $exam->instructions }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Top Performers -->
    @if(isset($topPerformers) && $topPerformers->isNotEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Top Performers</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th class="text-end">Score</th>
                            <th class="text-end">%</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPerformers as $index => $result)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $result->student->first_name ?? 'N/A' }} {{ $result->student->last_name ?? '' }}</td>
                            <td class="text-end">{{ number_format($result->score, 1) }}</td>
                            <td class="text-end">{{ number_format($result->percentage, 1) }}%</td>
                            <td><span class="badge bg-success">{{ $result->grade }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection