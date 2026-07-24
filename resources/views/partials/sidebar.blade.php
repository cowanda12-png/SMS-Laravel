<div class="sidebar" style="background: #1a1a1a; color: #ffffff; height: 100vh; position: sticky; top: 0; overflow-y: auto; border-right: 1px solid #2a2a2a;">
    <!-- Brand/Logo -->
    <div style="padding: 1.2rem 1rem;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;">
            <div style="background: rgba(108, 140, 255, 0.15); padding: 8px 12px; border-radius: 8px;">
                <i class="fas fa-graduation-cap" style="font-size: 1.2rem; color: #6c8cff;"></i>
            </div>
            <div>
                <h5 style="margin: 0; font-weight: 600; font-size: 0.95rem; color: #ffffff;">SMS</h5>
                <small style="color: rgba(255,255,255,0.35); font-size: 0.65rem; letter-spacing: 0.3px;">Student Management</small>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul style="list-style: none; padding: 0; margin: 0;">
            <!-- Dashboard -->
            <li style="margin-bottom: 4px;">
                <a href="{{ route('dashboard') }}" 
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; 
                          color: {{ request()->routeIs('dashboard') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; 
                          text-decoration: none; border-radius: 8px; font-size: 0.85rem; 
                          transition: all 0.2s ease; 
                          background: {{ request()->routeIs('dashboard') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                    <i class="fas fa-th-large" style="width: 20px; text-align: center; font-size: 0.95rem;"></i>
                    <span>Dashboard</span>
                    @if(request()->routeIs('dashboard'))
                        <span style="margin-left: auto; width: 6px; height: 6px; background: #6c8cff; border-radius: 50%;"></span>
                    @endif
                </a>
            </li>

            <!-- Students -->
            <li style="margin-bottom: 4px;">
                <a href="{{ route('students.index') }}" 
                   class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; 
                          color: {{ request()->routeIs('students.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; 
                          text-decoration: none; border-radius: 8px; font-size: 0.85rem; 
                          transition: all 0.2s ease; 
                          background: {{ request()->routeIs('students.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                    <i class="fas fa-user-graduate" style="width: 20px; text-align: center; font-size: 0.95rem;"></i>
                    <span>Students</span>
                    @if(request()->routeIs('students.*'))
                        <span style="margin-left: auto; width: 6px; height: 6px; background: #6c8cff; border-radius: 50%;"></span>
                    @endif
                </a>
            </li>

            <!-- Courses -->
            <li style="margin-bottom: 4px;">
                <a href="{{ route('courses.index') }}" 
                   class="sidebar-link {{ request()->routeIs('courses.*') ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; 
                          color: {{ request()->routeIs('courses.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; 
                          text-decoration: none; border-radius: 8px; font-size: 0.85rem; 
                          transition: all 0.2s ease; 
                          background: {{ request()->routeIs('courses.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                    <i class="fas fa-book" style="width: 20px; text-align: center; font-size: 0.95rem;"></i>
                    <span>Courses</span>
                    @if(request()->routeIs('courses.*'))
                        <span style="margin-left: auto; width: 6px; height: 6px; background: #6c8cff; border-radius: 50%;"></span>
                    @endif
                </a>
            </li>

            

            <!-- Fees -->
            <li style="margin-bottom: 4px;">
                <a href="{{ route('fees.index') }}" 
                   class="sidebar-link {{ request()->routeIs('fees.*') ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; 
                          color: {{ request()->routeIs('fees.*') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; 
                          text-decoration: none; border-radius: 8px; font-size: 0.85rem; 
                          transition: all 0.2s ease; 
                          background: {{ request()->routeIs('fees.*') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                    <i class="fas fa-money-bill-wave" style="width: 20px; text-align: center; font-size: 0.95rem;"></i>
                    <span>Fees</span>
                    @if(request()->routeIs('fees.*'))
                        <span style="margin-left: auto; width: 6px; height: 6px; background: #6c8cff; border-radius: 50%;"></span>
                    @endif
                </a>
            </li>

            <!-- Divider -->
            <li style="margin: 15px 0;">
                <hr style="border-color: rgba(255,255,255,0.05); margin: 0;">
            </li>

            <!-- Reports -->
            <li style="margin-bottom: 4px;">
                <a href="{{ route('fees.report') }}" 
                   class="sidebar-link {{ request()->routeIs('fees.report') ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; 
                          color: {{ request()->routeIs('fees.report') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; 
                          text-decoration: none; border-radius: 8px; font-size: 0.85rem; 
                          transition: all 0.2s ease; 
                          background: {{ request()->routeIs('fees.report') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                    <i class="fas fa-chart-bar" style="width: 20px; text-align: center; font-size: 0.95rem;"></i>
                    <span>Reports</span>
                </a>
            </li>

            <!-- Settings -->
            <li style="margin-bottom: 4px;">
                <a href="{{ route('profile.edit') }}" 
                   class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; 
                          color: {{ request()->routeIs('profile.edit') ? '#6c8cff' : 'rgba(255,255,255,0.6)' }}; 
                          text-decoration: none; border-radius: 8px; font-size: 0.85rem; 
                          transition: all 0.2s ease; 
                          background: {{ request()->routeIs('profile.edit') ? 'rgba(108, 140, 255, 0.12)' : 'transparent' }};">
                    <i class="fas fa-cog" style="width: 20px; text-align: center; font-size: 0.95rem;"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <!-- Bottom Section -->
        <div style="position: absolute; bottom: 20px; left: 0; right: 0; padding: 0 1rem;">
            <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px;">
                <div style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px; background: rgba(255,255,255,0.03);">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(108, 140, 255, 0.2); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; color: #6c8cff;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.8);">
                            {{ Auth::user()->name ?? 'Guest' }}
                        </div>
                        <div style="font-size: 0.65rem; color: rgba(255,255,255,0.35);">
                            {{ Auth::user()->email ?? 'guest@example.com' }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: rgba(255,255,255,0.3); cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: all 0.2s ease;" 
                                onmouseover="this.style.color='#dc3545'" 
                                onmouseout="this.style.color='rgba(255,255,255,0.3)'" 
                                title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Sidebar scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.02);
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(108, 140, 255, 0.3);
        border-radius: 10px;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(108, 140, 255, 0.5);
    }
    
    /* Sidebar link hover */
    .sidebar-link:hover {
        background: rgba(108, 140, 255, 0.08) !important;
        color: #6c8cff !important;
        transform: translateX(4px);
    }
    
    .sidebar-link.active {
        background: rgba(108, 140, 255, 0.12) !important;
        color: #6c8cff !important;
        border-right: 3px solid #6c8cff;
    }
    
    .sidebar-link {
        position: relative;
        overflow: hidden;
    }
    
    .sidebar-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #6c8cff;
        transform: scaleY(0);
        transition: transform 0.2s ease;
    }
    
    .sidebar-link:hover::before,
    .sidebar-link.active::before {
        transform: scaleY(1);
    }
    
    /* Mobile responsive */
    @media (max-width: 991.98px) {
        .sidebar {
            display: none;
            position: fixed !important;
            width: 280px !important;
            z-index: 1000;
            top: 70px;
            left: 0;
            bottom: 0;
            background: #1a1a1a;
            overflow-y: auto;
            box-shadow: 2px 0 20px rgba(0,0,0,0.3);
        }
        
        .sidebar.show {
            display: block !important;
        }
    }
</style>

<script>
    // Toggle sidebar on mobile
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('show');
        }
    }
    
    // Close sidebar on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991.98) {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.remove('show');
            }
        }
    });
    
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98) {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.navbar-toggler');
            if (sidebar && toggleBtn) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        }
    });
</script>