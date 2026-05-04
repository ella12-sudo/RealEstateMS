<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealMS - Tenant Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f4f7fa; display: flex; min-height: 100vh; width: 100%; overflow-x: hidden; }

        .sidebar { 
            width: 220px; 
            background: #1a3b5c; 
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

        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #cbd5e0; text-decoration: none; font-size: 13.5px; transition: all 0.2s; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.08); color: white; border-left: 3px solid #c9952a; }
        .nav-item i { width: 20px; text-align: center; font-size: 16px; opacity: 0.8; }

        .sidebar-footer { margin-top: auto; padding: 15px 20px; border-top: 1px solid rgba(255,255,255,0.05); }
        .logout-btn { background: none; border: none; cursor: pointer; color: #fc8181; font-size: 13px; opacity: 0.9; display: flex; align-items: center; gap: 8px; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 999;
        }
        .sidebar-overlay.active { display: block; }

        .main-content { margin-left: 220px; flex: 1; display: flex; flex-direction: column; width: calc(100% - 220px); min-width: 0; }
        .topbar { background: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; flex-wrap: wrap; gap: 10px; }
        .topbar h1 { font-size: 16px; color: #1a3b5c; font-weight: 600; margin: 0; }
        .page-body { padding: 25px; }

        .menu-toggle { display: none; background: none; border: none; font-size: 20px; color: #1a3b5c; cursor: pointer; margin-right: 10px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px 22px; border: 1px solid #e2e8f0; }
        .stat-card .label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .stat-card .value { font-size: 22px; font-weight: 700; color: #1a2e4a; }

        .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-header { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; gap: 10px; }
        .table-header h3 { font-size: 15px; font-weight: 600; color: #1a2e4a; margin: 0; }

        table { width: 100%; border-collapse: collapse; min-width: 400px; }
        th { padding: 10px 20px; text-align: left; color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        td { padding: 13px 20px; border-bottom: 1px solid #f5f5f5; font-size: 13px; color: #334155; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-red { background: #f8d7da; color: #721c24; }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .menu-toggle { display: block; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .page-body { padding: 18px; }
        }

        @media (max-width: 600px) {
            .stats-grid { grid-template-columns: 1fr; gap: 10px; }
            .page-body { padding: 12px; }
            .topbar { padding: 10px 14px; }
            .welcome-text { display: none; }
            td, th { padding: 10px 12px; font-size: 12px; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo-header">
        <div class="logo-image-container">
            <img src="{{ asset('images/realms-logo-removebg-preview.png') }}" alt="RealMS Logo">
        </div>
    </div>
    
    <nav style="padding-top: 10px;">
        <a href="{{ route('tenant.dashboard') }}" class="nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="{{ route('tenant.payments') }}" class="nav-item">
            <i class="fas fa-file-invoice-dollar"></i> My Payments
        </a>
        <a href="{{ route('tenant.maintenance') }}" class="nav-item">
            <i class="fas fa-tools"></i> Maintenance
        </a>
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
        <div style="display: flex; align-items: center;">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1>My Dashboard</h1>
        </div>
        <span style="font-size:13px; color:#718096;" class="welcome-text">Welcome, <strong style="color:#1a3b5c;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong></span>
    </div>
    <div class="page-body">

        <div class="stats-grid">
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <div>
                        <div class="label">My Property</div>
                        <div style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ $tenant->property->location ?? 'N/A' }}</div>
                    </div>
                    <div class="value" style="font-size:15px; text-align:right;">{{ $tenant->property->name ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <div>
                        <div class="label">Monthly Rent</div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:2px;">Due every 1st</div>
                    </div>
                    <div class="value">₱{{ number_format($tenant->property->rent_per_month ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <div class="label">Lease Status</div>
                    <span class="badge {{ ($tenant->status ?? 'Active') == 'Active' ? 'badge-green' : 'badge-red' }}">
                        {{ $tenant->status ?? 'Active' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>My Recent Payments</h3>
                <a href="{{ route('tenant.payments') }}" style="color:#c9952a; font-size:13px; text-decoration:none; font-weight:600;">View All</a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Paid On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments ?? [] as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                            <td style="font-weight:600;">₱{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') : 'Apr 19, 2026' }}</td>
                            <td><span class="badge badge-green">Paid</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding:40px; color:#94a3b8;">No recent payments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.querySelector('.menu-toggle');
        const overlay = document.getElementById('sidebarOverlay');
        if (window.innerWidth <= 992) {
            if (!sidebar.contains(event.target) && menuBtn && !menuBtn.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        }
    });
</script>

</body>
</html>