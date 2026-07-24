{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Student Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ===== GLOBAL ===== */
        * {
            box-sizing: border-box;
            margin: 0;
        }
        body {
            background: #f4f6fa;
            font-family: 'Figtree', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100%;
            background: #1a1a1a;
            color: #e2e8f0;
            padding: 1.5rem 1rem;
            overflow-y: auto;
            z-index: 1060;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid #2a2a2a;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #2a2a2a;
            margin-bottom: 1.5rem;
        }
        .sidebar-brand i {
            font-size: 28px;
            color: #818cf8;
        }
        .sidebar-brand span {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            letter-spacing: 0.3px;
        }
        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 16px;
            border-radius: 10px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        .sidebar-nav a i {
            width: 22px;
            font-size: 1rem;
            color: #64748b;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: #2a2a2a;
            color: white;
        }
        .sidebar-nav a:hover i,
        .sidebar-nav a.active i {
            color: #a5b4fc;
        }
        .sidebar-footer {
            border-top: 1px solid #2a2a2a;
            padding-top: 1rem;
            margin-top: auto;
        }
        .sidebar-footer .user-mini {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 4px;
        }
        .sidebar-footer .user-mini .avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }
        .sidebar-footer .user-mini .user-detail {
            line-height: 1.4;
        }
        .sidebar-footer .user-mini .user-detail .name {
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
        }
        .sidebar-footer .user-mini .user-detail .email {
            font-size: 0.75rem;
            color: #94a3b8;
        }
        .sidebar-footer .logout-sidebar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin-top: 8px;
            border-radius: 10px;
            color: #f87171;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            font-size: 0.95rem;
        }
        .sidebar-footer .logout-sidebar:hover {
            background: #2a2a2a;
            color: #fca5a5;
        }
        .sidebar-footer .logout-sidebar i {
            width: 22px;
            color: #f87171;
        }

        /* ===== TOP NAVBAR ===== */
        .top-navbar {
            background: #1a1a1a;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
            padding: 0.6rem 0;
            position: sticky;
            top: 0;
            z-index: 1050;
            border-bottom: 1px solid #2a2a2a;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }
        .top-navbar .navbar-brand .brand-text {
            font-weight: 500;
            font-size: 1.1rem;
            letter-spacing: 0.3px;
            color: #ffffff;
        }
        .top-navbar .nav-link {
            color: rgba(255,255,255,0.7) !important;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 400;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .top-navbar .nav-link i {
            font-size: 0.85rem;
        }
        .top-navbar .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #ffffff !important;
        }
        .top-navbar .nav-link.active {
            background: rgba(255,255,255,0.08);
            color: #ffffff !important;
        }
        .top-navbar .navbar-toggler {
            color: #ffffff;
            padding: 6px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .top-navbar .navbar-toggler:focus {
            box-shadow: none;
        }
        .nav-divider {
            width: 1px;
            height: 30px;
            background: rgba(255,255,255,0.08);
            margin: 0 15px;
        }
        .bell-wrapper {
            position: relative;
            cursor: pointer;
            color: rgba(255,255,255,0.6);
            padding: 5px;
            transition: color 0.25s ease;
        }
        .bell-wrapper:hover {
            color: rgba(255,255,255,0.9);
        }
        .bell-wrapper .badge-notif {
            position: absolute;
            top: -2px;
            right: -4px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.55rem;
            border: 2px solid #1a1a1a;
            line-height: 1;
            min-width: 18px;
            text-align: center;
        }
        .admin-dropdown-toggle {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 4px 12px 4px 4px;
            color: rgba(255,255,255,0.8);
            transition: all 0.25s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .admin-dropdown-toggle:hover {
            background: rgba(255,255,255,0.08);
        }
        .admin-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            color: #ffffff;
            flex-shrink: 0;
        }
        .admin-name {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            font-weight: 400;
            white-space: nowrap;
        }
        .admin-chevron {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.4);
            transition: transform 0.2s;
        }
        .dropdown-menu-dark-custom {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 12px;
            padding: 0.5rem 0;
            min-width: 220px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin-top: 8px;
        }
        .dropdown-menu-dark-custom .dropdown-item {
            color: rgba(255,255,255,0.7);
            padding: 8px 20px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .dropdown-menu-dark-custom .dropdown-item i {
            width: 18px;
            color: rgba(255,255,255,0.4);
        }
        .dropdown-menu-dark-custom .dropdown-item:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
        }
        .dropdown-menu-dark-custom .dropdown-divider {
            border-color: #2a2a2a;
            margin: 0.3rem 0;
        }
        .dropdown-menu-dark-custom .user-header {
            padding: 12px 20px;
            border-bottom: 1px solid #2a2a2a;
        }
        .dropdown-menu-dark-custom .user-header .name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff;
        }
        .dropdown-menu-dark-custom .user-header .email {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
        }
        .dropdown-menu-dark-custom .logout-item {
            color: #dc3545 !important;
        }
        .dropdown-menu-dark-custom .logout-item i {
            color: #dc3545 !important;
        }
        .dropdown-menu-dark-custom .logout-item:hover {
            background: rgba(220,53,69,0.1) !important;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: scale(0.95) translateY(-10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .dropdown-menu-dark-custom {
            animation: slideDown 0.2s ease forwards;
            transform-origin: top right;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 260px;
            padding: 1.5rem 1.5rem 2rem;
            flex: 1;
            transition: margin-left 0.3s ease;
        }

        /* ===== DASHBOARD SPECIFIC ===== */
        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
        }
        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .page-header h1 small {
            font-size: 1rem;
            font-weight: 400;
            color: #64748b;
            margin-left: 0.5rem;
        }
        .header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .btn-export {
            background: white;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-export:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .btn-add {
            background: #1a1a1a;
            border: none;
            color: white;
            padding: 0.5rem 1.4rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-add:hover {
            background: #2d2d2d;
            color: white;
        }
        .filter-bar {
            background: white;
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 1.8rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }
        .filter-bar .search-wrap {
            flex: 2 1 260px;
            display: flex;
            align-items: center;
            background: #f1f4f9;
            border-radius: 30px;
            padding: 0.2rem 0.2rem 0.2rem 1.2rem;
            border: 1px solid #e9edf2;
            transition: border 0.2s;
        }
        .filter-bar .search-wrap:focus-within {
            border-color: #667eea;
        }
        .filter-bar .search-wrap input {
            border: none;
            background: transparent;
            padding: 0.55rem 0;
            flex: 1;
            outline: none;
            font-size: 0.9rem;
            color: #1e293b;
            min-width: 120px;
        }
        .filter-bar .search-wrap input::placeholder {
            color: #94a3b8;
        }
        .filter-bar .search-wrap button {
            background: #1a1a1a;
            border: none;
            color: white;
            padding: 0.5rem 1.4rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .filter-bar .search-wrap button:hover {
            background: #2d2d2d;
        }
        .filter-select-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            flex: 1 1 auto;
        }
        .filter-select-group select {
            background: #f1f4f9;
            border: 1px solid #e9edf2;
            border-radius: 30px;
            padding: 0.5rem 1.8rem 0.5rem 1.2rem;
            font-size: 0.85rem;
            color: #1e293b;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            background-size: 10px;
            min-width: 130px;
            transition: border 0.2s;
        }
        .filter-select-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-filter {
            background: #f1f4f9;
            border: 1px solid #e9edf2;
            border-radius: 30px;
            padding: 0.5rem 1.4rem;
            font-weight: 500;
            font-size: 0.85rem;
            color: #1e293b;
            transition: all 0.2s;
        }
        .btn-filter:hover {
            background: #e9edf2;
        }
        .table-card {
            background: white;
            border-radius: 16px;
            padding: 0 0 0.5rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow-x: auto;
        }
        .table-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 480px;
        }
        .table-card table th {
            text-align: left;
            padding: 1rem 1.2rem;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e9edf2;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .table-card table td {
            padding: 0.9rem 1.2rem;
            border-bottom: 1px solid #f1f4f9;
            color: #1e293b;
            vertical-align: middle;
        }
        .table-card table tr:last-child td {
            border-bottom: none;
        }
        .table-card table tr:hover td {
            background: #fafbfc;
        }
        .student-id {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 400;
            margin-left: 4px;
        }
        .badge-status {
            background: #e6f7e6;
            color: #0b6e0b;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-status.inactive {
            background: #fee9e9;
            color: #b91c1c;
        }
        .action-link {
            color: #64748b;
            margin-right: 12px;
            transition: color 0.2s;
            text-decoration: none;
        }
        .action-link:hover {
            color: #1a1a1a;
        }
        .action-link i {
            font-size: 0.9rem;
        }

        /* ===== MOBILE OVERLAY ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1055;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active {
            display: block;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .top-navbar {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .top-navbar .navbar-nav {
                margin: 10px 0;
                gap: 2px !important;
            }
            .top-navbar .nav-link {
                padding: 10px 16px !important;
                border-radius: 6px;
            }
            .nav-divider {
                display: none !important;
            }
            .top-navbar .navbar-brand .brand-text {
                font-size: 1rem !important;
            }
            .admin-name {
                display: none;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }
        @media (max-width: 575.98px) {
            .top-navbar .navbar-brand .brand-text {
                font-size: 0.85rem !important;
            }
            .top-navbar .container-fluid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            .admin-dropdown-toggle {
                padding: 4px 8px 4px 4px;
            }
            .bell-wrapper .badge-notif {
                font-size: 0.5rem;
                min-width: 16px;
                padding: 1px 5px;
            }
            .main-content {
                padding: 1rem 0.75rem 2rem;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
            .page-header h1 {
                font-size: 1.4rem;
            }
            .page-header h1 small {
                font-size: 0.85rem;
                display: block;
                margin-left: 0;
                margin-top: 2px;
            }
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                padding: 1rem;
            }
            .filter-bar .search-wrap {
                flex: 1 1 auto;
            }
            .filter-select-group {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .filter-select-group select {
                flex: 1 1 120px;
            }
            .header-actions {
                width: 100%;
                justify-content: stretch;
            }
            .header-actions .btn {
                flex: 1;
                text-align: center;
            }
            .table-card table {
                font-size: 0.8rem;
                min-width: 380px;
            }
            .table-card table th,
            .table-card table td {
                padding: 0.6rem 0.8rem;
            }
            .filter-bar .search-wrap input {
                font-size: 0.8rem;
                min-width: 80px;
            }
            .filter-bar .search-wrap button {
                padding: 0.4rem 1rem;
                font-size: 0.8rem;
            }
        }
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0) !important;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- ===== SIDEBAR OVERLAY (mobile) ===== -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            <span>Student MS</span>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> Students
            </a>
            <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Courses
            </a>
            <a href="{{ route('fees.index') }}" class="{{ request()->routeIs('fees.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Fees
            </a>
            <div style="border-top:1px solid #2a2a2a; margin:0.5rem 0;"></div>
            <a href="{{ route('profile.edit') }}">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="{{ route('profile.change-password') }}">
                <i class="fas fa-key"></i> Change Password
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-mini">
                <div class="avatar-sm">
                    {{ strtoupper(substr(Auth::check() ? Auth::user()->name : 'A', 0, 1)) }}
                </div>
                <div class="user-detail">
                    <div class="name">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                    <div class="email">{{ Auth::check() ? Auth::user()->email : 'guest@example.com' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-sidebar">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- ===== TOP NAVBAR ===== -->
    <nav class="top-navbar navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <!-- Mobile hamburger (opens sidebar) -->
            <button class="navbar-toggler" type="button" id="sidebarToggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <span class="brand-text">Student Management System</span>
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center" style="gap:2px;">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-th-large"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                            <i class="fas fa-user-graduate"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                            <i class="fas fa-book"></i> Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}" href="{{ route('fees.index') }}">
                            <i class="fas fa-money-bill-wave"></i> Fees
                        </a>
                    </li>
                </ul>
                <div class="nav-divider d-none d-lg-block"></div>
                <div class="d-flex align-items-center gap-3 mt-2 mt-lg-0">
                    <div class="bell-wrapper">
                        <i class="fas fa-bell"></i>
                        <span class="badge-notif">3</span>
                    </div>
                    <div class="dropdown">
                        <button class="admin-dropdown-toggle dropdown-toggle" type="button" id="adminDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="admin-avatar">
                                {{ strtoupper(substr(Auth::check() ? Auth::user()->name : 'A', 0, 1)) }}
                            </div>
                            <span class="admin-name">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</span>
                            <i class="fas fa-chevron-down admin-chevron"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark-custom" aria-labelledby="adminDropdown">
                            <li class="user-header">
                                <div class="name">{{ Auth::check() ? Auth::user()->name : 'Admin User' }}</div>
                                <div class="email">{{ Auth::check() ? Auth::user()->email : 'admin@example.com' }}</div>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.change-password') }}"><i class="fas fa-key"></i> Change Password</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="display:block; margin:0; padding:0;">
                                    @csrf
                                    <button type="submit" class="dropdown-item logout-item">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
        @if (isset($header))
            <div class="page-header">
                <h1>
                    <i class="fas fa-users" style="color: #667eea;"></i>
                    {{ $header }}
                    <small>{{ $slot ?? 'Manage all students in the system' }}</small>
                </h1>
                @if (isset($actions))
                    <div class="header-actions">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        (function() {
            'use strict';

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (sidebar.classList.contains('open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (sidebar.classList.contains('open')) {
                        closeSidebar();
                    }
                    // close any open Bootstrap dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                        const parent = menu.closest('.dropdown');
                        if (parent) {
                            const toggle = parent.querySelector('.dropdown-toggle');
                            if (toggle) {
                                const instance = bootstrap.Dropdown.getInstance(toggle);
                                if (instance) instance.hide();
                            }
                        }
                    });
                }
            });

            // Close sidebar on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992 && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });

            // Close sidebar when a link is clicked (mobile)
            document.querySelectorAll('.sidebar-nav a, .sidebar-footer .logout-sidebar').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });

        })();
    </script>

    @stack('scripts')
</body>
</html>