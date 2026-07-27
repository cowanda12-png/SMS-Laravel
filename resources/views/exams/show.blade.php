@extends('layouts.app')

@section('title', 'Exam Details')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-info-circle text-primary me-2"></i> Exam Details
            </h4>
            <p class="text-muted small mb-0">View exam information</p>
        </div>
        <div class="d-flex gap-1">
            <a href="{{ route('exams.edit', $id ?? 1) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <div class="text-center py-5">
                <i class="fas fa-construction fa-3x text-warning mb-3"></i>
                <h5 class="fw-bold">Exam Details</h5>
                <p class="text-muted mb-3">This feature is currently under development.</p>
                <p class="text-muted small">Exam details will be displayed here soon.</p>
                <a href="{{ route('exams.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Exams
                </a>
            </div>
        </div>
    </div>
</div>
@endsection