<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealMS - My Maintenance</title>
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
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            height: 120px;
        }

        .logo-image-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }

        .logo-image-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .nav-item { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 20px; 
            color: #cbd5e0; 
            text-decoration: none; 
            font-size: 13.5px; 
            transition: all 0.2s; 
        }

        .nav-item:hover, .nav-item.active { 
            background: rgba(255,255,255,0.08); 
            color: white; 
            border-left: 3px solid #c9952a; 
        }

        .nav-item i { 
            width: 20px; 
            text-align: center; 
            font-size: 16px; 
            opacity: 0.8;
        }

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

        .menu-toggle { display: none; background: none; border: none; font-size: 20px; color: #1a3b5c; cursor: pointer; margin-right: 10px; }

        .main-content { margin-left: 220px; flex: 1; display: flex; flex-direction: column; width: calc(100% - 220px); min-width: 0; }
        .topbar { background: white; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; flex-wrap: wrap; gap: 10px; }
        .topbar h1 { font-size: 16px; color: #1a3b5c; font-weight: 600; margin: 0; }
        .page-body { padding: 28px 25px 20px 25px; }

        .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        table { width: 100%; border-collapse: collapse; min-width: 400px; }
        th { padding: 7px 14px; background: #f8fafc; text-align: left; font-size: 10.5px; text-transform: uppercase; color: #64748b; font-weight: 700; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
        td { padding: 7px 14px; font-size: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; line-height: 1.4; }

        .badge { padding: 2px 8px; border-radius: 20px; font-size: 10.5px; font-weight: 600; display: inline-block; }
        .status-pending    { background: #fef3c7; color: #92400e; }
        .status-inprogress { background: #e0f2fe; color: #075985; }
        .status-completed  { background: #dcfce7; color: #15803d; }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .action-bar h2 {
            font-size: 14px;
            color: #1e293b;
            font-weight: 700;
            margin: 0;
        }
        .btn-request {
            background: #1a3b5c;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .pagination-wrap { padding: 10px 14px; border-top: 1px solid #f0f0f0; }
        .pagination-wrap nav { display: flex; justify-content: flex-end; }
        .pagination-wrap nav > div:first-child { display: none !important; }
        .pagination-wrap nav > div:last-child {
            display: flex;
            justify-content: flex-end;
        }
        .pagination-wrap nav > div:last-child ul,
        .pagination-wrap nav ul {
            display: flex !important;
            justify-content: flex-end !important;
            gap: 5px;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }
        .pagination-wrap nav ul li a,
        .pagination-wrap nav ul li span {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            color: #1a3b5c;
            text-decoration: none;
            background: white;
        }
        .pagination-wrap nav ul li.active span,
        .pagination-wrap nav ul li span[aria-current="page"] {
            background: #1a3b5c;
            color: white;
            border-color: #1a3b5c;
        }

        @media (max-width: 992px) {
            .menu-toggle { display: block; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; }
            .page-body { padding: 15px; }
        }

        @media (max-width: 600px) {
            .page-body { padding: 12px; }
            .topbar { padding: 10px 14px; }
            .welcome-text { display: none; }
            td, th { padding: 6px 10px; font-size: 11px; }
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
        <a href="{{ route('tenant.dashboard') }}" class="nav-item">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="{{ route('tenant.payments') }}" class="nav-item">
            <i class="fas fa-file-invoice-dollar"></i> My Payments
        </a>
        <a href="{{ route('tenant.maintenance') }}" class="nav-item active">
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
            <h1>Maintenance</h1>
        </div>
        <span style="font-size:13px; color:#718096;" class="welcome-text">Welcome, <strong style="color:#1a3b5c;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong></span>
    </div>

    <div class="page-body">
        @if(session('success'))
            <div style="background: #dcfce7; color: #15803d; padding: 12px 14px; border-radius: 8px; margin-bottom: 14px; font-size: 12.5px; border-left: 4px solid #22c55e;">
                {{ session('success') }}
            </div>
        @endif

        <div class="action-bar">
            <h2>Maintenance History</h2>
            <button class="btn-request" onclick="document.getElementById('requestModal').style.display='flex'">
                <i class="fas fa-plus"></i> Request Service
            </button>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date Requested</th>
                            <th>Subject / Issue</th>
                            <th>Status</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td style="color: #64748b; white-space: nowrap;">{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="font-weight: 700; color: #1a3b5c; font-size: 12px;">{{ $request->title }}</div>
                                <div style="font-size: 11px; color: #94a3b8; margin-top: 1px;">{{ Str::limit($request->description, 60) }}</div>
                            </td>
                            <td>
                                <span class="badge {{ 'status-' . strtolower(str_replace(' ', '', $request->status)) }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #1a3b5c; white-space: nowrap;">
                                ₱{{ number_format($request->cost ?? 0, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-folder-open" style="font-size: 22px; display: block; margin-bottom: 8px;"></i>
                                No maintenance requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
            <div class="pagination-wrap">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<div id="requestModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:white; border-radius:12px; padding:25px; width:100%; max-width:500px;">
        <h3 style="margin-bottom: 20px; color: #1a3b5c; font-weight: 700;">New Maintenance Request</h3>
        <form action="/tenant/maintenance/store" method="POST">
            @csrf
            <div>
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Issue Title</label>
                <input type="text" name="title" placeholder="e.g., Leaking Faucet" required 
                       style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size:13px; outline:none;">
            </div>
            <div style="margin-top: 15px;">
                <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Description</label>
                <textarea name="description" rows="4" placeholder="Describe the problem..." required 
                          style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size:13px; outline:none; resize:vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; flex-wrap: wrap;">
                <button type="button" onclick="document.getElementById('requestModal').style.display='none'" 
                        style="background: #f1f5f9; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #475569;">
                    Cancel
                </button>
                <button type="submit" 
                        style="background: #1a3b5c; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    Submit Request
                </button>
            </div>
        </form>
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