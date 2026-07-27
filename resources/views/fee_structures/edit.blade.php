{{-- resources/views/fee_structures/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Fee Structure')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-edit text-primary me-2"></i> Edit Fee Structure
            </h4>
            <p class="text-muted small mb-0">Update fee structure details</p>
        </div>
        <a href="{{ route('fee-structures.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('fee-structures.update', $feeStructure->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Fee Type *</label>
                        <select name="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
                            <option value="">Select fee type</option>
                            @foreach($feeTypes as $type)
                                <option value="{{ $type }}" {{ old('fee_type', $feeStructure->fee_type) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('fee_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Amount (KES) *</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   placeholder="0.00" step="0.01" min="0" 
                                   value="{{ old('amount', $feeStructure->amount) }}" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Class *</label>
                        <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $feeStructure->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Grade *</label>
                        <select name="grade_id" class="form-select @error('grade_id') is-invalid @enderror" required>
                            <option value="">Select grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id', $feeStructure->grade_id) == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Term *</label>
                        <select name="term" class="form-select @error('term') is-invalid @enderror" required>
                            <option value="">Select term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term }}" {{ old('term', $feeStructure->term) == $term ? 'selected' : '' }}>
                                    {{ $term }}
                                </option>
                            @endforeach
                        </select>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Academic Year *</label>
                        <select name="academic_year" class="form-select @error('academic_year') is-invalid @enderror" required>
                            <option value="">Select year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ old('academic_year', $feeStructure->academic_year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                               value="{{ old('due_date', $feeStructure->due_date?->format('Y-m-d')) }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Status *</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $feeStructure->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $feeStructure->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-check mt-3">
                            <input type="checkbox" name="is_compulsory" class="form-check-input" 
                                   id="isCompulsory" value="1" 
                                   {{ old('is_compulsory', $feeStructure->is_compulsory) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isCompulsory">
                                Compulsory Fee
                            </label>
                            <div class="form-text small">Uncheck if this fee is optional</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Additional details about this fee">{{ old('description', $feeStructure->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Fee Structure
                            </button>
                            <a href="{{ route('fee-structures.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection