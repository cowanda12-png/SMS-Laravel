{{-- resources/views/fee_structures/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Fee Structure Management')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Fee Structure
            </h4>
            <p class="text-muted small mb-0">Manage fees by class, grade, term, and academic year</p>
        </div>
        <a href="{{ route('fee-structures.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Add Fee Structure
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
            <form action="{{ route('fee-structures.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold mb-0">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Fee type, year..."
                           value="{{ request('search') }}">
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
                    <label class="form-label small fw-semibold mb-0">Grade</label>
                    <select name="grade_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Term</label>
                    <select name="term" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($terms as $term)
                            <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>
                                {{ $term }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label small fw-semibold mb-0">Year</label>
                    <select name="academic_year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'class_id', 'grade_id', 'term', 'academic_year']))
                        <a href="{{ route('fee-structures.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Fee Structures Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($feeStructures->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No fee structures found.</p>
                    <a href="{{ route('fee-structures.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Create fee structure
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Fee Type</th>
                                <th>Class</th>
                                <th>Grade</th>
                                <th>Term</th>
                                <th>Year</th>
                                <th class="text-end">Amount (KES)</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feeStructures as $structure)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-semibold">{{ $structure->fee_type }}</span>
                                        @if($structure->description)
                                            <br><small class="text-muted">{{ Str::limit($structure->description, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $structure->class->name ?? 'N/A' }}</td>
                                    <td>{{ $structure->grade->name ?? 'N/A' }}</td>
                                    <td>{{ $structure->term }}</td>
                                    <td>{{ $structure->academic_year }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($structure->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $structure->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($structure->status) }}
                                        </span>
                                        @if($structure->is_compulsory)
                                            <span class="badge bg-info">Compulsory</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('fee-structures.edit', $structure->id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('fee-structures.destroy', $structure->id) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Delete this fee structure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
                        Showing {{ $feeStructures->firstItem() ?? 0 }} to {{ $feeStructures->lastItem() ?? 0 }} 
                        of {{ $feeStructures->total() }}
                    </span>
                    {{ $feeStructures->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection