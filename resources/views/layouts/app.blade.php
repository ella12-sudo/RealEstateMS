<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealMS - Real Estate Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { display: flex; margin: 0; background: #f4f7fa; font-family: 'Inter', sans-serif; min-height: 100vh; width: 100%; overflow-x: hidden; }
        
        .sidebar { 
            width: 220px; 
            background-color: #1a3b5c; 
            color: white; 
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            height: 100vh; 
            z-index: 1000; 
            transition: transform 0.3s ease; 
        }

        .sidebar-logo-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            background: rgba(0,0,0,0.1) !important;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            height: 120px;
        }

        .logo-image-container {
            width: 100%; 
            height: 100%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }

        .logo-image-container img {
            width: 100%;
            height: 100%;
            max-width: none;
            object-fit: contain;
        }

        .sidebar-nav { flex: 1; padding-top: 10px; background: transparent !important; }
        
        .nav-item { 
            display: flex; 
            align-items: center; 
            padding: 10px 18px; 
            color: #cbd5e0; 
            text-decoration: none; 
            transition: 0.2s; 
            font-size: 13.5px; 
        }
        
        .nav-item:hover, .nav-item.active { 
            background: rgba(255,255,255,0.08); 
            color: white; 
            border-left: 3px solid #c9952a; 
        }

        .nav-item i { 
            width: 20px; 
            margin-right: 10px; 
            font-size: 15px; 
            text-align: center; 
            opacity: 0.8;
        }

        .main-content { 
            margin-left: 220px; 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            width: calc(100% - 220px); 
            min-width: 0; 
            transition: all 0.3s ease; 
        }

        .topbar { 
            background: white; 
            padding: 12px 25px; 
            display: flex; 
            justify-content: space-between; 
            border-bottom: 1px solid #eee; 
            align-items: center; 
            gap: 16px;
            flex-wrap: wrap;
        }
        .page-body { padding: 25px; flex: 1; }

        .sidebar-footer { padding: 15px 20px; border-top: 1px solid rgba(255,255,255,0.05); }
        .logout-btn { background: none; border: none; color: #fc8181; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; opacity: 0.9; }

        .topbar-global-search { position: relative; }
        .topbar-global-search input {
            padding: 7px 14px 7px 34px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            color: #334155;
            width: 220px;
            outline: none;
            transition: border 0.2s, width 0.2s;
            background: #f8fafc;
        }
        .topbar-global-search input:focus {
            border-color: #1a3b5c;
            width: 280px;
            background: white;
        }
        .topbar-global-search i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 12px;
            pointer-events: none;
        }

        .menu-toggle { display: none; background: none; border: none; font-size: 20px; color: #1a3b5c; cursor: pointer; }

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 999;
        }
        .sidebar-overlay.active { display: block; }

        /* Notification Styles */
        .notification-dropdown { position: relative; cursor: pointer; }
        .dropdown-content { 
            display: none; 
            position: absolute; 
            right: 0; 
            top: 35px; 
            background: white; 
            border: 1px solid #e2e8f0; 
            width: 300px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            border-radius: 8px; 
            z-index: 1001; 
        }
        .dropdown-content.show { display: block; }
        .red-dot {
            position: absolute; 
            top: -8px; 
            right: -8px; 
            background: #e53e3e; 
            color: white; 
            border-radius: 50%; 
            padding: 2px 6px; 
            font-size: 10px; 
            font-weight: bold;
            border: 2px solid white;
        }

        .notif-item {
            display: block;
            padding: 12px 15px;
            text-decoration: none;
            border-bottom: 1px solid #f7fafc;
            transition: background 0.2s;
            background: white;
        }
        .notif-item:hover {
            background: #f8fafc;
            cursor: pointer;
        }

        /* ===================== */
        /* RESPONSIVE BREAKPOINTS */
        /* ===================== */

        /* Tablet (768px - 992px) */
        @media (max-width: 992px) {
            .menu-toggle { display: block; }
            .sidebar { transform: translateX(-100%); width: 220px; }
            .sidebar.active { transform: translateX(0); }
            .main-content { 
                margin-left: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
            }
            .topbar-global-search { display: none; }
            .page-body { padding: 20px; }
        }

        /* Mobile (max 600px) */
        @media (max-width: 600px) {
            .page-body { padding: 12px; }
            .topbar { padding: 10px 14px; gap: 10px; }
            .welcome-text { display: none; }
            .notification-dropdown { margin-left: auto; }

            /* Make tables scroll horizontally on mobile */
            table { min-width: 480px; }
            .table-responsive, .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

            /* Stack summary grids on mobile */
            .summary-grid { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }

            /* Notification dropdown smaller on mobile */
            .dropdown-content { width: 260px; right: -10px; }

            /* Dashboard side-by-side becomes stacked */
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }

        /* Very small phones (max 400px) */
        @media (max-width: 400px) {
            .summary-grid { grid-template-columns: 1fr !important; }
            .page-body { padding: 10px; }
            .topbar { padding: 8px 12px; }
        }
    </style>
</head>
<body>

    {{-- Overlay for mobile sidebar --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo-header">
            <div class="logo-image-container">
                <img src="{{ asset('images/realms-logo-removebg-preview.png') }}" alt="RealMS Logo">
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(Auth::check() && Auth::user()->role === 'admin')
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="{{ route('properties.index') }}" class="nav-item {{ request()->routeIs('properties*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Property Listing
                </a>
                <a href="{{ route('tenants.index') }}" class="nav-item {{ request()->routeIs('tenants*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i> Tenants & Clients
                </a>
                <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i> Payments & Billing
                </a>
                <a href="{{ route('maintenance.index') }}" class="nav-item {{ request()->routeIs('maintenance*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Maintenance
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 style="font-size: 16px; color: #1a3b5c; margin: 0; font-weight: 600; white-space: nowrap;">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div style="display: flex; align-items: center; gap: 20px;">
                @hasSection('topbar-extra')
                    @yield('topbar-extra')
                @endif
                @unless(request()->routeIs('properties*') || request()->routeIs('tenants*'))
                    <div class="topbar-global-search">
                        <i class="fas fa-search"></i>
                        @if(request()->routeIs('payments*'))
                            <form method="GET" action="{{ route('payments.index') }}" style="margin:0;">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tenants...">
                            </form>
                        @elseif(request()->routeIs('maintenance*'))
                            <form method="GET" action="{{ route('maintenance.index') }}" style="margin:0;">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search maintenance...">
                            </form>
                        @else
                            <input type="text" id="globalSearch" placeholder="Search tenants..." oninput="runGlobalSearch()">
                        @endif
                    </div>
                @endunless

                {{-- NOTIFICATION BELL SECTION --}}
                @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="notification-dropdown" onclick="toggleNotifications(event)">
                    <i class="fas fa-bell" style="font-size: 18px; color: #1a3b5c;"></i>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="red-dot">{{ Auth::user()->unreadNotifications->count() }}</span>
                    @endif

                    <div class="dropdown-content" id="notificationDropdown">
                        <div style="padding: 10px 15px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 13px; color: #1a3b5c; display: flex; justify-content: space-between; align-items: center;">
                            Notifications
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notifications.markAsRead') }}" style="font-size: 10px; color: #c9952a; text-decoration: none;">Mark all as read</a>
                            @endif
                        </div>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @forelse(Auth::user()->unreadNotifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}"
                                   class="notif-item"
                                   onclick="event.stopPropagation()">
                                    <div style="font-size: 12px; color: #333; line-height: 1.4;">
                                        <i class="fas fa-money-bill-wave" style="color: #c9952a; font-size: 10px; margin-right: 5px;"></i>
                                        {{ $notification->data['message'] ?? 'New transaction alert' }}
                                    </div>
                                    <div style="font-size: 10px; color: #a0aec0; margin-top: 4px; margin-left: 15px;">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </a>
                            @empty
                                <div style="padding: 20px; text-align: center; color: #a0aec0; font-size: 12px;">
                                    <i class="fas fa-check-circle" style="display: block; font-size: 20px; margin-bottom: 8px; opacity: 0.5;"></i>
                                    All caught up!
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                <div class="welcome-text" style="font-size: 13px; color: #718096; white-space: nowrap;">
                    Welcome, <span style="font-weight: 600; color: #1a3b5c;">{{ Auth::user()->name ?? 'Admin' }}</span>
                </div>
            </div>
        </div>
        <div class="page-body">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function toggleNotifications(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.menu-toggle');
            const overlay = document.getElementById('sidebarOverlay');
            const notifDropdown = document.getElementById('notificationDropdown');
            
            if (window.innerWidth <= 992) {
                if (sidebar && !sidebar.contains(event.target) && toggleBtn && !toggleBtn.contains(event.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                }
            }

            if (notifDropdown && !event.target.closest('.notification-dropdown')) {
                notifDropdown.classList.remove('show');
            }
        });

        function runGlobalSearch() {
            const query = document.getElementById('globalSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.tenant-row');
            if (rows.length > 0) {
                rows.forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
                });
            }
        }
    </script>

</body>
</html>