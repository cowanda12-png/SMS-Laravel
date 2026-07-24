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
        
        /* Header hover effects */
        header nav a:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: white !important;
            transform: translateY(-1px);
        }
        
        header nav a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }
        
        /* Sidebar link hover */
        .sidebar-link:hover {
            background: rgba(108, 140, 255, 0.12) !important;
            color: #6c8cff !important;
        }
        
        .sidebar-link.active {
            background: rgba(108, 140, 255, 0.12);
            color: #6c8cff !important;
        }
        
        /* Dropdown menu */
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
        
        /* Footer link hover */
        .footer-link:hover {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            header {
                flex-direction: column;
                height: auto;
                padding: 0.8rem 1rem;
                gap: 8px;
            }
            
            header nav {
                flex-wrap: wrap;
                justify-content: center;
                width: 100%;
            }
            
            header nav a {
                font-size: 0.8rem !important;
                padding: 6px 12px !important;
            }
            
            .sidebar {
                display: none !important;
            }
            
            main {
                margin-left: 0 !important;
                padding: 1rem !important;
            }
            
            footer {
                margin-left: 0 !important;
            }
        }
        
        @media (max-width: 575.98px) {
            header h2 {
                font-size: 1rem !important;
            }
            
            header .fa-bell,
            header .fa-chevron-down {
                display: none;
            }
            
            .user-name {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Fixed Header -->
    <header style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; height: 70px; box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3); position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="background: rgba(255,255,255,0.2); padding: 8px 12px; border-radius: 8px;">
                <i class="fas fa-graduation-cap" style="font-size: 24px;"></i>
            </div>
            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 600; letter-spacing: 0.5px;">
                Student Management System
            </h2>
        </div>
        
        <nav style="display: flex; align-items: center; gap: 5px;">
            <a href="{{ route('dashboard') }}" style="color: rgba(255,255,255,0.9); text-decoration: none; padding: 8px 18px; border-radius: 8px; font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease; {{ request()->routeIs('dashboard') ? 'background: rgba(255,255,255,0.2);' : '' }}">
                <i class="fas fa-th-large" style="margin-right: 6px;"></i> Dashboard
            </a>
            <a href="{{ route('students.index') }}" style="color: rgba(255,255,255,0.9); text-decoration: none; padding: 8px 18px; border-radius: 8px; font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease; {{ request()->routeIs('students.*') ? 'background: rgba(255,255,255,0.2);' : '' }}">
                <i class="fas fa-user-graduate" style="margin-right: 6px;"></i> Students
            </a>
            <a href="{{ route('courses.index') }}" style="color: rgba(255,255,255,0.9); text-decoration: none; padding: 8px 18px; border-radius: 8px; font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease; {{ request()->routeIs('courses.*') ? 'background: rgba(255,255,255,0.2);' : '' }}">
                <i class="fas fa-book" style="margin-right: 6px;"></i> Courses
            </a>
            <a href="{{ route('fees.index') }}" style="color: rgba(255,255,255,0.9); text-decoration: none; padding: 8px 18px; border-radius: 8px; font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease; {{ request()->routeIs('fees.*') ? 'background: rgba(255,255,255,0.2);' : '' }}">
                <i class="fas fa-money-bill-wave" style="margin-right: 6px;"></i> Fees
            </a>
            
            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2); margin: 0 10px;"></div>
            
            <!-- User Dropdown -->
            <div class="dropdown" style="display: inline-block;">
                <button class="dropdown-toggle d-flex align-items-center gap-2" 
                        type="button" 
                        id="userDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); 
                               border-radius: 20px; padding: 4px 12px 4px 4px; color: rgba(255,255,255,0.9);
                               transition: all 0.25s ease; cursor: pointer;"
                        onmouseover="this.style.background='rgba(255,255,255,0.2)'" 
                        onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); 
                                display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;
                                color: #ffffff;">
                        @php
                            $userName = Auth::check() ? Auth::user()->name : 'A';
                            $userEmail = Auth::check() ? Auth::user()->email : 'guest@example.com';
                        @endphp
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                    <span class="user-name" style="font-weight: 500; font-size: 0.9rem;">
                        {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                    </span>
                    <i class="fas fa-chevron-down" style="font-size: 10px; opacity: 0.7;"></i>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end" 
                    style="background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; 
                           padding: 0.5rem 0; min-width: 220px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); margin-top: 8px;">
                    
                    <!-- User Info -->
                    <li style="padding: 12px 20px; border-bottom: 1px solid #2a2a2a;">
                        <div style="font-weight: 600; font-size: 0.95rem; color: #ffffff;">
                            {{ Auth::check() ? Auth::user()->name : 'Guest User' }}
                        </div>
                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.4); margin-top: 2px;">
                            {{ Auth::check() ? Auth::user()->email : 'guest@example.com' }}
                        </div>
                    </li>
                    
                    <!-- Profile -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}" 
                           style="color: rgba(255,255,255,0.7); padding: 8px 20px; font-size: 0.9rem; 
                                  transition: all 0.2s ease; display: flex; align-items: center; gap: 12px;
                                  {{ request()->routeIs('profile.edit') ? 'background: rgba(255,255,255,0.05); color: #ffffff;' : '' }}">
                            <i class="fas fa-user" style="width: 18px; color: rgba(255,255,255,0.4);"></i>
                            Profile
                        </a>
                    </li>
                    
                    <!-- Change Password -->
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.change-password') }}" 
                           style="color: rgba(255,255,255,0.7); padding: 8px 20px; font-size: 0.9rem; 
                                  transition: all 0.2s ease; display: flex; align-items: center; gap: 12px;
                                  {{ request()->routeIs('profile.change-password') ? 'background: rgba(255,255,255,0.05); color: #ffffff;' : '' }}">
                            <i class="fas fa-key" style="width: 18px; color: rgba(255,255,255,0.4);"></i>
                            Change Password
                        </a>
                    </li>
                    
                    <!-- Divider -->
                    <li><hr class="dropdown-divider" style="border-color: #2a2a2a; margin: 0.3rem 0;"></li>
                    
                    <!-- Logout (Breeze) -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: block; margin: 0; padding: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item" 
                                    style="color: #dc3545; padding: 8px 20px; font-size: 0.9rem; 
                                           transition: all 0.2s ease; display: flex; align-items: center; gap: 12px;
                                           background: none; border: none; width: 100%; text-align: left; cursor: pointer;"
                                    onmouseover="this.style.background='rgba(220,53,69,0.1)'" 
                                    onmouseout="this.style.background='transparent'">
                                <i class="fas fa-sign-out-alt" style="width: 18px;"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content Area with Sidebar -->
    <div style="display: flex; padding-top: 70px; min-height: 100vh;">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 250px; background: #1a1a1a; color: #ffffff; position: fixed; top: 70px; left: 0; bottom: 0; overflow-y: auto; border-right: 1px solid #2a2a2a; flex-shrink: 0; z-index: 999;">
            <div style="padding: 1.2rem 1rem;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;">
                    <div style="background: rgba(108, 140, 255, 0.15); padding: 6px 10px; border-radius: 8px;">
                        <i class="fas fa-graduation-cap" style="font-size: 1.1rem; color: #6c8cff;"></i>
                    </div>
                    <div>
                        <h5 style="margin: 0; font-weight: 600; font-size: 0.95rem; color: #ffffff;">SMS</h5>
                        <small style="color: rgba(255,255,255,0.35); font-size: 0.65rem; letter-spacing: 0.3px;">Student Management</small>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 4px;">
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                           style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: {{ request()->routeIs('dashboard') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; text-decoration: none; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s ease; background: {{ request()->routeIs('dashboard') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                            <i class="fas fa-th-large" style="width: 18px; text-align: center;"></i> Dashboard
                        </a>
                    </li>
                    <li style="margin-bottom: 4px;">
                        <a href="{{ route('students.index') }}" class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                           style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: {{ request()->routeIs('students.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; text-decoration: none; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s ease; background: {{ request()->routeIs('students.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                            <i class="fas fa-user-graduate" style="width: 18px; text-align: center;"></i> Students
                        </a>
                    </li>
                    <li style="margin-bottom: 4px;">
                        <a href="{{ route('courses.index') }}" class="sidebar-link {{ request()->routeIs('courses.*') ? 'active' : '' }}"
                           style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: {{ request()->routeIs('courses.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; text-decoration: none; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s ease; background: {{ request()->routeIs('courses.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                            <i class="fas fa-book" style="width: 18px; text-align: center;"></i> Courses
                        </a>
                    </li>
                    <li style="margin-bottom: 4px;">
                        <a href="{{ route('exams.index') }}" class="sidebar-link {{ request()->routeIs('exams.*') ? 'active' : '' }}"
                           style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: {{ request()->routeIs('exams.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; text-decoration: none; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s ease; background: {{ request()->routeIs('exams.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                            <i class="fas fa-pencil-alt" style="width: 18px; text-align: center;"></i> Exams
                        </a>
                    </li>
                    <li style="margin-bottom: 4px;">
                        <a href="{{ route('fees.index') }}" class="sidebar-link {{ request()->routeIs('fees.*') ? 'active' : '' }}"
                           style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: {{ request()->routeIs('fees.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; text-decoration: none; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s ease; background: {{ request()->routeIs('fees.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                            <i class="fas fa-money-bill-wave" style="width: 18px; text-align: center;"></i> Fees
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main style="flex: 1; margin-left: 250px; background: #f8f9fa; min-height: 100vh; padding: 1.5rem; overflow-y: auto;">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer style="background: #1a1a1a; color: rgba(255,255,255,0.5); padding: 1.5rem 0; border-top: 1px solid #2a2a2a; margin-left: 250px;">
        <div class="container-fluid px-4">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <span style="font-size: 0.85rem;">
                    &copy; {{ date('Y') }} Student Management System. All Rights Reserved.
                </span>
                <div style="display: flex; gap: 1.5rem;">
                    <a href="#" class="footer-link" style="color: rgba(255,255,255,0.3); text-decoration: none; font-size: 0.8rem; transition: color 0.25s ease;">
                        Privacy Policy
                    </a>
                    <a href="#" class="footer-link" style="color: rgba(255,255,255,0.3); text-decoration: none; font-size: 0.8rem; transition: color 0.25s ease;">
                        Terms of Service
                    </a>
                    <a href="#" class="footer-link" style="color: rgba(255,255,255,0.3); text-decoration: none; font-size: 0.8rem; transition: color 0.25s ease;">
                        Support
                    </a>
                    <a href="#" class="footer-link" style="color: rgba(255,255,255,0.3); text-decoration: none; font-size: 0.8rem; transition: color 0.25s ease;">
                        <i class="fas fa-arrow-up"></i> Back to Top
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-close alerts after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    const bsAlert = bootstrap.Alert.getInstance(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                });
            }, 5000);

            // Back to top button functionality
            document.querySelector('.footer-link:last-child')?.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

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

            // Highlight current page in sidebar
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar-link').forEach(function(link) {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>