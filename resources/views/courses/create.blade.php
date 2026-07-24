@extends('layouts.app') 
@section('title') 
Add New Course 
@endsection 

@section('content') 

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add New Course</h1>
            <p class="text-muted small">Create a new course in the system</p>
        </div>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_name" class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('course_name') is-invalid @enderror" 
                               id="course_name" 
                               name="course_name" 
                               value="{{ old('course_name') }}" 
                               placeholder="Enter course name" 
                               required>
                        @error('course_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}" 
                               placeholder="e.g., CS101" 
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Enter course description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="credits" class="form-label fw-semibold">Credits</label>
                        <input type="number" 
                               class="form-control @error('credits') is-invalid @enderror" 
                               id="credits" 
                               name="credits" 
                               value="{{ old('credits', 3) }}" 
                               min="1" 
                               max="6">
                        @error('credits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Course
                    </button>
                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 3px rgba(108, 140, 255, 0.1);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
    }
    
    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: #6c8cff;
        border: none;
    }
    
    .btn-primary:hover {
        background: #5a7ae6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 140, 255, 0.3);
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

@endsection