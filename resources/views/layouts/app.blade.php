<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c7cd;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8b0b8;
        }
        
        /* Header styles */
        .app-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
        }
        
        .app-header .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .app-header .brand-icon {
            background: rgba(255,255,255,0.2);
            padding: 6px 10px;
            border-radius: 8px;
        }
        
        .app-header .brand-icon i {
            font-size: 20px;
        }
        
        .app-header .brand-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        
        /* Mobile menu toggle */
        .menu-toggle {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 6px 10px;
            color: white;
            cursor: pointer;
            display: none;
            transition: all 0.25s ease;
        }
        
        .menu-toggle:hover {
            background: rgba(255,255,255,0.2);
        }
        
        /* Navigation */
        .app-nav {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .app-nav .nav-link {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .app-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateY(-1px);
        }
        
        .app-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .app-nav .nav-divider {
            width: 1px;
            height: 25px;
            background: rgba(255,255,255,0.2);
            margin: 0 5px;
        }
        
        /* User Dropdown */
        .user-dropdown-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 4px 12px 4px 4px;
            color: rgba(255,255,255,0.9);
            transition: all 0.25s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .user-dropdown-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            color: #ffffff;
        }
        
        .user-name {
            font-weight: 500;
            font-size: 0.85rem;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #1a1a1a;
            color: #ffffff;
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            border-right: 1px solid #2a2a2a;
            flex-shrink: 0;
            z-index: 999;
            transition: transform 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 1rem 1rem 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 1rem;
        }
        
        .sidebar-brand-icon {
            background: rgba(108, 140, 255, 0.15);
            padding: 6px 10px;
            border-radius: 8px;
        }
        
        .sidebar-brand-icon i {
            font-size: 1rem;
            color: #6c8cff;
        }
        
        .sidebar-brand-title {
            margin: 0;
            font-weight: 600;
            font-size: 0.9rem;
            color: #ffffff;
        }
        
        .sidebar-brand-sub {
            color: rgba(255,255,255,0.35);
            font-size: 0.6rem;
            letter-spacing: 0.3px;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0.5rem 0.8rem;
            margin: 0;
        }
        
        .sidebar-nav li {
            margin-bottom: 2px;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.82rem;
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background: rgba(108, 140, 255, 0.12);
            color: #6c8cff;
        }
        
        .sidebar-link.active {
            background: rgba(108, 140, 255, 0.12);
            color: #6c8cff;
        }
        
        .sidebar-link i {
            width: 18px;
            text-align: center;
        }
        
        /* Sidebar Divider */
        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 0.5rem 0.8rem;
        }
        
        /* Sidebar Dropdown Styles */
        .sidebar-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.82rem;
            transition: all 0.2s ease;
            cursor: pointer;
            width: 100%;
            background: none;
            border: none;
            font-family: inherit;
            text-align: left;
        }
        
        .sidebar-dropdown-toggle:hover {
            background: rgba(108, 140, 255, 0.12);
            color: #6c8cff;
        }
        
        .sidebar-dropdown-toggle.active {
            background: rgba(108, 140, 255, 0.12);
            color: #6c8cff;
        }
        
        .sidebar-dropdown-toggle .arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 0.7rem;
        }
        
        .sidebar-dropdown-toggle .arrow.open {
            transform: rotate(180deg);
        }
        
        .sidebar-dropdown-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }
        
        .sidebar-dropdown-menu.open {
            max-height: 500px;
            opacity: 1;
        }
        
        .sidebar-dropdown-menu .sidebar-link {
            padding-left: 40px;
            font-size: 0.78rem;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 240px;
            padding-top: 60px;
            background: #f8f9fa;
            min-height: 100vh;
            padding: 70px 1.5rem 1.5rem 1.5rem;
            transition: margin-left 0.3s ease;
        }
        
        /* Footer */
        .app-footer {
            background: #1a1a1a;
            color: rgba(255,255,255,0.5);
            padding: 1rem 0;
            border-top: 1px solid #2a2a2a;
            margin-left: 240px;
            transition: margin-left 0.3s ease;
        }
        
        .app-footer .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0 1.5rem;
        }
        
        .app-footer .footer-text {
            font-size: 0.8rem;
        }
        
        .app-footer .footer-links {
            display: flex;
            gap: 1.2rem;
            flex-wrap: wrap;
        }
        
        .app-footer .footer-link {
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            font-size: 0.75rem;
            transition: color 0.25s ease;
        }
        
        .app-footer .footer-link:hover {
            color: rgba(255,255,255,0.6);
        }
        
        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 998;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .app-header .brand-title {
                font-size: 0.95rem;
            }
            
            .app-nav .nav-link {
                font-size: 0.78rem;
                padding: 5px 10px;
            }
            
            .app-nav .nav-link i {
                margin-right: 3px;
            }
        }
        
        @media (max-width: 767.98px) {
            .app-header {
                padding: 0 0.8rem;
                height: 55px;
            }
            
            .app-header .brand-title {
                font-size: 0.85rem;
            }
            
            .app-header .brand-icon {
                padding: 4px 8px;
            }
            
            .app-header .brand-icon i {
                font-size: 16px;
            }
            
            /* Hide navigation links on mobile */
            .app-nav .nav-link {
                display: none;
            }
            
            .app-nav .nav-divider {
                display: none;
            }
            
            /* Show menu toggle on mobile */
            .menu-toggle {
                display: block;
            }
            
            .user-name {
                display: none;
            }
            
            .user-dropdown-btn {
                padding: 4px 8px 4px 4px;
            }
            
            .user-avatar {
                width: 26px;
                height: 26px;
                font-size: 10px;
            }
            
            /* Sidebar slides in from left */
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                top: 55px;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            /* Main content full width on mobile */
            .main-content {
                margin-left: 0;
                padding: 65px 1rem 1rem 1rem;
            }
            
            .app-footer {
                margin-left: 0;
                padding: 0.8rem 0;
            }
            
            .app-footer .footer-content {
                flex-direction: column;
                text-align: center;
                padding: 0 1rem;
            }
            
            .app-footer .footer-links {
                justify-content: center;
            }
        }
        
        @media (max-width: 575.98px) {
            .app-header {
                padding: 0 0.6rem;
                height: 50px;
            }
            
            .app-header .brand-title {
                font-size: 0.75rem;
            }
            
            .app-header .brand-icon {
                padding: 3px 6px;
            }
            
            .app-header .brand-icon i {
                font-size: 14px;
            }
            
            .sidebar {
                width: 100%;
                max-width: 300px;
                top: 50px;
            }
            
            .main-content {
                padding: 60px 0.8rem 0.8rem 0.8rem;
            }
            
            .user-avatar {
                width: 24px;
                height: 24px;
                font-size: 9px;
            }
        }
        
        /* Dropdown animations */
        .dropdown-menu {
            animation: slideDown 0.25s ease forwards;
            transform-origin: top right;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        /* Dark dropdown */
        .dropdown-menu-dark-custom {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 12px;
            padding: 0.5rem 0;
            min-width: 200px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            margin-top: 8px;
        }
        
        .dropdown-menu-dark-custom .dropdown-item {
            color: rgba(255,255,255,0.7);
            padding: 8px 20px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-menu-dark-custom .dropdown-item:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
        }
        
        .dropdown-menu-dark-custom .dropdown-divider {
            border-color: #2a2a2a;
            margin: 0.3rem 0;
        }
        
        .dropdown-menu-dark-custom .dropdown-header {
            padding: 10px 20px;
            border-bottom: 1px solid #2a2a2a;
        }
        
        .dropdown-menu-dark-custom .dropdown-header .name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #ffffff;
        }
        
        .dropdown-menu-dark-custom .dropdown-header .email {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
        }
        
        .dropdown-menu-dark-custom .logout-btn {
            color: #dc3545;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .dropdown-menu-dark-custom .logout-btn:hover {
            background: rgba(220,53,69,0.1);
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Fixed Header -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2 class="brand-title">Student Management</h2>
        </div>
        
        <nav class="app-nav">
            <!-- Mobile Menu Toggle -->
            <button class="menu-toggle" id="menuToggle" onclick="toggleSidebar()" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Desktop Navigation -->
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> Students
            </a>
            <a href="{{ route('courses.index') }}" class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="fas fa-book"></i> Courses
            </a>
            <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Fees
            </a>
            
            <span class="nav-divider"></span>
            
            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="user-dropdown-btn" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        @php
                            $userName = Auth::check() ? Auth::user()->name : 'A';
                        @endphp
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                    <span class="user-name">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 10px; opacity: 0.7;"></i>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-dark-custom dropdown-menu-end">
                    <!-- User Info -->
                    <li class="dropdown-header">
                        <div class="name">{{ Auth::check() ? Auth::user()->name : 'Guest User' }}</div>
                        <div class="email">{{ Auth::check() ? Auth::user()->email : 'guest@example.com' }}</div>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <!-- Profile -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user" style="width: 18px; margin-right: 10px; color: rgba(255,255,255,0.4);"></i>
                            Profile
                        </a>
                    </li>
                    
                    <!-- Change Password -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                            <i class="fas fa-key" style="width: 18px; margin-right: 10px; color: rgba(255,255,255,0.4);"></i>
                            Change Password
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <!-- Logout -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: block; margin: 0; padding: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn">
                                <i class="fas fa-sign-out-alt" style="width: 18px; margin-right: 10px;"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content Area with Sidebar -->
    <div style="display: flex; min-height: 100vh;">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h5 class="sidebar-brand-title">SMS</h5>
                    <small class="sidebar-brand-sub">Student Management</small>
                </div>
            </div>

            <ul class="sidebar-nav">
                <!-- Main Modules -->
                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                </li>
                <li>
                    <a href="{{ route('courses.index') }}" class="sidebar-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>
                
                <!-- Fees Dropdown Section -->
                <li>
                    <button class="sidebar-dropdown-toggle" id="feesDropdownToggle" onclick="toggleFeesDropdown()">
                        <i class="fas fa-coins"></i> 
                        <span>Fees Management</span>
                        <span class="arrow" id="feesArrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <ul class="sidebar-dropdown-menu" id="feesDropdownMenu">
                        <li>
                            <a href="{{ route('fee-structures.index') }}" class="sidebar-link {{ request()->routeIs('fee-structures.*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar"></i> Fee Structure
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('fees.index') }}" class="sidebar-link {{ request()->routeIs('fees.index') ? 'active' : '' }}">
                                <i class="fas fa-receipt"></i> Fee Records
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('fees.create') }}" class="sidebar-link {{ request()->routeIs('fees.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i> Record Payment
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Sidebar Divider -->
                <li><hr class="sidebar-divider"></li>
                
                <!-- Reports Dropdown Section -->
                <li>
                    <button class="sidebar-dropdown-toggle" id="reportsDropdownToggle" onclick="toggleReportsDropdown()">
                        <i class="fas fa-chart-pie"></i> 
                        <span>Reports &amp; Analytics</span>
                        <span class="arrow" id="reportsArrow"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <ul class="sidebar-dropdown-menu" id="reportsDropdownMenu">
                        <li>
                            <a href="{{ route('reports.student-statement') }}" class="sidebar-link {{ request()->routeIs('reports.student-statement') ? 'active' : '' }}">
                                <i class="fas fa-id-card"></i> Student Statement
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.fee-collection') }}" class="sidebar-link {{ request()->routeIs('reports.fee-collection') ? 'active' : '' }}">
                                <i class="fas fa-money-bill-wave"></i> Fee Collection
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.outstanding-balances') }}" class="sidebar-link {{ request()->routeIs('reports.outstanding-balances') ? 'active' : '' }}">
                                <i class="fas fa-exclamation-triangle"></i> Outstanding Balances
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.course-revenue') }}" class="sidebar-link {{ request()->routeIs('reports.course-revenue') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i> Course Revenue
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.daily-collection') }}" class="sidebar-link {{ request()->routeIs('reports.daily-collection') ? 'active' : '' }}">
                                <i class="fas fa-calendar-day"></i> Daily Collection
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.monthly-collection') }}" class="sidebar-link {{ request()->routeIs('reports.monthly-collection') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt"></i> Monthly Collection
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.mpesa-transactions') }}" class="sidebar-link {{ request()->routeIs('reports.mpesa-transactions') ? 'active' : '' }}">
                                <i class="fas fa-mobile-alt"></i> M-Pesa Transactions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.payment-method-analysis') }}" class="sidebar-link {{ request()->routeIs('reports.payment-method-analysis') ? 'active' : '' }}">
                                <i class="fas fa-credit-card"></i> Payment Method Analysis
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.fee-summary') }}" class="sidebar-link {{ request()->routeIs('reports.fee-summary') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i> Fee Summary
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="app-footer" id="appFooter">
        <div class="footer-content">
            <span class="footer-text">
                &copy; {{ date('Y') }} Student Management System. All Rights Reserved.
            </span>
            <div class="footer-links">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Support</a>
                <a href="#" class="footer-link" onclick="scrollToTop(event)">
                    <i class="fas fa-arrow-up"></i> Back to Top
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            
            // Prevent body scroll when sidebar is open
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        // Toggle Fees Dropdown
        function toggleFeesDropdown() {
            const menu = document.getElementById('feesDropdownMenu');
            const arrow = document.getElementById('feesArrow');
            const toggle = document.getElementById('feesDropdownToggle');
            
            menu.classList.toggle('open');
            arrow.classList.toggle('open');
        }
        
        // Toggle Reports Dropdown
        function toggleReportsDropdown() {
            const menu = document.getElementById('reportsDropdownMenu');
            const arrow = document.getElementById('reportsArrow');
            const toggle = document.getElementById('reportsDropdownToggle');
            
            menu.classList.toggle('open');
            arrow.classList.toggle('open');
        }
        
        // Close sidebar when clicking on a link (mobile)
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 767) {
                    closeSidebar();
                }
            });
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
        
        // Scroll to top
        function scrollToTop(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Auto-close alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                const bsAlert = bootstrap.Alert.getInstance(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            });
        }, 5000);
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                dropdowns.forEach(function(dropdown) {
                    const toggle = dropdown.closest('.dropdown').querySelector('.dropdown-toggle');
                    if (toggle) {
                        const bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                        if (bsDropdown) {
                            bsDropdown.hide();
                        }
                    }
                });
            }
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Close sidebar on resize to desktop
                if (window.innerWidth > 767) {
                    closeSidebar();
                }
            }, 250);
        });
        
        // Highlight current page in sidebar
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
        
        // Auto-expand fees dropdown if a fees page is active
        document.addEventListener('DOMContentLoaded', function() {
            const feesLinks = document.querySelectorAll('#feesDropdownMenu .sidebar-link');
            let hasActiveFees = false;
            feesLinks.forEach(function(link) {
                if (link.classList.contains('active')) {
                    hasActiveFees = true;
                }
            });
            
            if (hasActiveFees) {
                const menu = document.getElementById('feesDropdownMenu');
                const arrow = document.getElementById('feesArrow');
                menu.classList.add('open');
                arrow.classList.add('open');
            }
            
            // Auto-expand reports dropdown if a reports page is active
            const reportsLinks = document.querySelectorAll('#reportsDropdownMenu .sidebar-link');
            let hasActiveReports = false;
            reportsLinks.forEach(function(link) {
                if (link.classList.contains('active')) {
                    hasActiveReports = true;
                }
            });
            
            if (hasActiveReports) {
                const menu = document.getElementById('reportsDropdownMenu');
                const arrow = document.getElementById('reportsArrow');
                menu.classList.add('open');
                arrow.classList.add('open');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>