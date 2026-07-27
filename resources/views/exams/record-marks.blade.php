@extends('layouts.app')

@section('title', 'Record Marks - ' . $exam->name)

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-pencil-alt text-primary me-2"></i> Record Marks
            </h4>
            <p class="text-muted small mb-0">
                {{ $exam->name }} ({{ $exam->code }}) - Max Score: {{ $exam->max_score }}
            </p>
        </div>
        <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('exams.record-marks', $exam->id) }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Admission</th>
                                <th>Score ({{ $exam->max_score }})</th>
                                <th>Status</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students ?? [] as $index => $student)
                                @php
                                    $result = $existingResults[$student->id] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    </td>
                                    <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                    <td>
                                        <input type="hidden" name="results[{{ $index }}][student_id]" value="{{ $student->id }}">
                                        <input type="number" 
                                               name="results[{{ $index }}][score]" 
                                               class="form-control form-control-sm @error('results.'.$index.'.score') is-invalid @enderror"
                                               style="width: 100px;"
                                               value="{{ old('results.'.$index.'.score', $result->score ?? '') }}"
                                               min="0" max="{{ $exam->max_score }}"
                                               step="0.01"
                                               placeholder="Score">
                                        <small class="text-muted">Max: {{ $exam->max_score }}</small>
                                    </td>
                                    <td>
                                        <select name="results[{{ $index }}][status]" class="form-select form-select-sm" style="width: 130px;">
                                            <option value="submitted" {{ old('results.'.$index.'.status', $result->status ?? '') == 'submitted' ? 'selected' : '' }}>
                                                Submitted
                                            </option>
                                            <option value="graded" {{ old('results.'.$index.'.status', $result->status ?? '') == 'graded' ? 'selected' : '' }}>
                                                Graded
                                            </option>
                                            <option value="absent" {{ old('results.'.$index.'.status', $result->status ?? '') == 'absent' ? 'selected' : '' }}>
                                                Absent
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="results[{{ $index }}][feedback]" 
                                               class="form-control form-control-sm"
                                               placeholder="Feedback..."
                                               value="{{ old('results.'.$index.'.feedback', $result->feedback ?? '') }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-end pt-3 border-top">
                    <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Marks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection