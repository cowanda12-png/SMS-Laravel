@extends('layouts.app')

@section('title', 'Create Exam')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-plus-circle text-primary me-2"></i> Create New Exam
            </h4>
            <p class="text-muted small mb-0">Add a new exam to the system</p>
        </div>
        <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> Please fix the following errors:
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- Exam Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Exam Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" placeholder="e.g., Midterm Examination" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Exam Code -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Exam Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                               value="{{ old('code') }}" placeholder="e.g., EXM-2024-001" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Course -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                            <option value="">Select Course</option>
                            @foreach($courses ?? [] as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Class -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">Select Class</option>
                            @foreach($classes ?? [] as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Exam Type -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Exam Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            @foreach($examTypes ?? [] as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Exam Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Exam Date <span class="text-danger">*</span></label>
                        <input type="date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror" 
                               value="{{ old('exam_date', date('Y-m-d')) }}" required>
                        @error('exam_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submission Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Submission Date</label>
                        <input type="date" name="submission_date" class="form-control @error('submission_date') is-invalid @enderror" 
                               value="{{ old('submission_date') }}">
                        <small class="text-muted">Optional - Date when students should submit</small>
                        @error('submission_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Max Score -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Max Score <span class="text-danger">*</span></label>
                        <input type="number" name="max_score" class="form-control @error('max_score') is-invalid @enderror" 
                               value="{{ old('max_score', 100) }}" step="0.01" min="1" required>
                        <small class="text-muted">Maximum possible score for this exam</small>
                        @error('max_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Passing Score -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Passing Score (%) <span class="text-danger">*</span></label>
                        <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                               value="{{ old('passing_score', 50) }}" step="0.01" min="0" max="100" required>
                        <small class="text-muted">Minimum percentage required to pass</small>
                        @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Weight -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Weight (%)</label>
                        <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" 
                               value="{{ old('weight', 100) }}" step="0.01" min="0" max="100">
                        <small class="text-muted">Weight of this exam in overall grade (default: 100%)</small>
                        @error('weight')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="graded" {{ old('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                        </select>
                        <small class="text-muted">Draft: Not visible, Published: Visible to students, Completed: Exam done, Graded: Results ready</small>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ====== NEW: Term and Academic Year ====== -->
                    <!-- Term -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
                        <select name="term" class="form-select @error('term') is-invalid @enderror" required>
                            <option value="">Select Term</option>
                            <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                            <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                            <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                        </select>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Academic Year -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year" class="form-select @error('academic_year') is-invalid @enderror" required>
                            <option value="">Select Academic Year</option>
                            @php
                                $currentYear = date('Y');
                                $years = [
                                    ($currentYear - 1) . '/' . $currentYear,
                                    $currentYear . '/' . ($currentYear + 1),
                                    ($currentYear + 1) . '/' . ($currentYear + 2)
                                ];
                            @endphp
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Describe the exam, topics covered, etc.">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Instructions</label>
                        <textarea name="instructions" class="form-control @error('instructions') is-invalid @enderror" 
                                  rows="3" placeholder="Instructions for students (e.g., duration, allowed materials, etc.)">{{ old('instructions') }}</textarea>
                        @error('instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap justify-content-end pt-3 border-top">
                            <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Exam
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection