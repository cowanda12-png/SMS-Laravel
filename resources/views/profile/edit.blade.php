@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-user-circle text-primary me-2"></i> My Profile
                    </h4>
                    <p class="text-muted small mb-0">Manage your account settings and preferences</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                        <i class="fas fa-user me-1"></i> {{ Auth::user()->name ?? 'User' }}
                    </span>
                </div>
            </div>

            <div class="row g-3">
                <!-- Left Column -->
                <div class="col-12 col-lg-6">
                    <!-- Profile Information -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center py-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                <i class="fas fa-user-edit text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center py-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Delete Account</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-12 col-lg-6">
                    <!-- Change Password -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center py-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                                <i class="fas fa-key text-warning"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Change Password</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-transparent border-0 d-flex align-items-center py-3">
                            <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2">
                                <i class="fas fa-bolt text-info"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-grid gap-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-th-large me-2"></i> Go to Dashboard
                                </a>
                                <a href="{{ route('profile.activity') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-clock me-2"></i> View Activity Log
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 12px !important;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .card-header .rounded-circle {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border-color: #e9ecef;
        font-size: 0.9rem;
        padding: 8px 14px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    
    .form-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #495057;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 18px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .btn-sm {
        padding: 6px 14px;
        font-size: 0.8rem;
    }
    
    .btn-primary {
        background: #6c8cff;
        border: none;
    }
    
    .btn-primary:hover {
        background: #5a7ae6;
    }
    
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    
    .btn-danger:hover {
        background: #c82333;
    }
    
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }
    
    .alert-success {
        border-left-color: #28a745;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
    
    .alert-warning {
        border-left-color: #ffc107;
    }
    
    .alert-info {
        border-left-color: #17a2b8;
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .card-body {
            padding: 1.25rem !important;
        }
    }
    
    @media (max-width: 767.98px) {
        .container-fluid {
            padding: 0.5rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem 1rem !important;
        }
        
        .card-header h5 {
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            font-size: 0.82rem;
            padding: 6px 12px;
        }
        
        .btn {
            font-size: 0.8rem;
            padding: 6px 14px;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 5px 10px;
        }
    }
    
    @media (max-width: 575.98px) {
        .container-fluid {
            padding: 0.25rem !important;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem !important;
        }
        
        .card-header h5 {
            font-size: 0.85rem;
        }
        
        .card-header .rounded-circle {
            width: 28px;
            height: 28px;
        }
        
        .card-header .rounded-circle i {
            font-size: 0.75rem !important;
        }
        
        .form-control, .form-select {
            font-size: 0.75rem;
            padding: 5px 10px;
        }
        
        .form-label {
            font-size: 0.75rem;
        }
        
        .btn {
            font-size: 0.75rem;
            padding: 5px 12px;
        }
        
        .btn-sm {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
        
        .badge {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
        }
    }
</style>
@endpush
@endsection