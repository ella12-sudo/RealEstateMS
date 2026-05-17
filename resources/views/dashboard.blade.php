@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('page-subtitle', 'Overview of properties, tenants, and revenue')

@section('content')
<style>
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (max-width: 1024px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  { .summary-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }

    .summary-card {
        background: white;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .summary-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.10);
        transform: translateY(-2px);
        cursor: pointer;
    }
    .summary-card h3 {
        color: #94a3b8;
        font-size: 10px;
        margin: 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .summary-card .value {
        font-size: 20px;
        font-weight: 700;
        word-break: break-word;
        text-align: right;
        white-space: nowrap;
    }
    .summary-card .label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 400;
        display: block;
        margin-top: 2px;
    }

    .value-green  { color: #16a34a; }
    .value-dark   { color: #1a3b5c; }
    .value-red    { color: #dc2626; }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 900px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }

    .detail-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .table-scroll {
        max-height: 280px;
        overflow-y: auto;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
    .table-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .detail-box h2 {
        font-size: 14px; color: #1a3b5c; margin-top: 0; margin-bottom: 14px; font-weight: 600;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;
    }
    .styled-table { width: 100%; border-collapse: collapse; font-size: 12px; min-width: 300px; }
    .styled-table th {
        text-align: left; padding: 8px 10px; color: #64748b; font-size: 10px;
        font-weight: 600; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;
        position: sticky; top: 0; background: white; z-index: 1;
    }
    .styled-table td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .styled-table tbody tr:last-child td { border-bottom: none; }

    /* Flat text status — no bubble */
    .status-badge   { font-size: 10px; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .status-paid    { color: #16a34a; }
    .status-pending { color: #dc2626; }

    .empty-state { text-align: center; color: #94a3b8; padding: 30px 0; font-size: 13px; }
    .view-all-link { font-size: 11px; color: #c9952a; text-decoration: none; font-weight: 600; white-space: nowrap; }
    .view-all-link:hover { text-decoration: underline; }

    /* Pagination */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .pagination-info { font-size: 11px; color: #94a3b8; }
    .pagination-btns { display: flex; gap: 4px; }
    .page-btn {
        padding: 4px 9px;
        font-size: 11px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: white;
        color: #1a3b5c;
        transition: all 0.15s ease;
    }
    .page-btn:hover:not(:disabled) { background: #f1f5f9; }
    .page-btn.active { background: #1a3b5c; color: white; border-color: #1a3b5c; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    @media (max-width: 600px) {
        .col-hide-mobile { display: none; }
        .detail-box { padding: 14px; }
    }
</style>

{{-- TOP STAT CARDS --}}
<div class="summary-grid">
    <a href="{{ route('properties.index') }}" style="text-decoration: none;">
        <div class="summary-card">
            <h3>Total Properties</h3>
            <span class="value value-dark">{{ $totalProperties ?? '6' }}</span>
        </div>
    </a>
    <a href="{{ route('tenants.index') }}" style="text-decoration: none;">
        <div class="summary-card">
            <h3>Active Tenants</h3>
            <span class="value value-dark">{{ $activeTenants ?? '6' }}</span>
        </div>
    </a>
    <a href="{{ route('payments.index') }}" style="text-decoration: none;">
        <div class="summary-card">
            <h3>Monthly Revenue</h3>
            <span class="value value-green" style="font-size:16px;">₱{{ number_format($monthlyRevenue ?? 100500, 2) }}</span>
        </div>
    </a>
    <a href="{{ route('maintenance.index') }}" style="text-decoration: none;">
        <div class="summary-card">
            <h3>Open Requests</h3>
            <span class="value {{ ($openRequests ?? 0) > 0 ? 'value-red' : 'value-dark' }}">{{ $openRequests ?? '5' }}</span>
        </div>
    </a>
</div>

{{-- GRAPH + RECENT PAYMENTS --}}
<div class="dashboard-grid">
    @if(Auth::user()->role === 'admin')
    <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <h3 style="font-size: 14px; color: #1a3b5c; margin: 0 0 15px 0; font-weight: 700;">Revenue Trends (Monthly)</h3>
        <div style="height: 200px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    @endif

    <div class="detail-box">
        <h2>
            Recent Payments
            <a href="{{ route('payments.index') }}" class="view-all-link">VIEW ALL</a>
        </h2>
        <div class="table-scroll">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th class="col-hide-mobile">Property</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments ?? [] as $payment)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1e293b; font-size: 12px;">
                                {{ $payment->tenant->user->first_name ?? '' }} {{ $payment->tenant->user->last_name ?? '' }}
                            </div>
                        </td>
                        <td class="col-hide-mobile" style="color: #64748b; font-size: 12px;">{{ $payment->property->name ?? '' }}</td>
                        <td style="font-weight: 700; color: #1e293b; font-size: 12px; white-space: nowrap;">₱{{ number_format($payment->amount ?? 0, 2) }}</td>
                        <td>
                            <span class="status-badge {{ ($payment->status ?? 'Paid') === 'Paid' ? 'status-paid' : 'status-pending' }}">
                                {{ $payment->status ?? 'Paid' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state">No recent payments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MAINTENANCE REQUESTS --}}
<div class="detail-box">
    <h2>
        Maintenance Requests
        <a href="{{ route('maintenance.index') }}" class="view-all-link">VIEW ALL</a>
    </h2>
    @if(isset($maintenanceRequests) && $maintenanceRequests->count() > 0)
    <div class="table-scroll">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th class="col-hide-mobile">Property</th>
                    <th>Title</th>
                    <th class="col-hide-mobile">Priority</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="maintenanceTableBody">
                @foreach($maintenanceRequests as $index => $request)
                <tr class="maintenance-row" data-index="{{ $index }}">
                    <td>
                        <div style="font-weight: 600; color: #1e293b; font-size: 12px;">
                            {{ $request->tenant->user->first_name ?? '' }} {{ $request->tenant->user->last_name ?? '' }}
                        </div>
                    </td>
                    <td class="col-hide-mobile" style="color: #64748b; font-size: 12px;">{{ $request->property->name ?? 'N/A' }}</td>
                    <td style="color: #1e293b; font-size: 12px;">{{ Str::limit($request->title, 30) }}</td>
                    <td class="col-hide-mobile" style="color: #64748b; font-size: 12px;">{{ $request->priority ?? 'Medium' }}</td>
                    <td>
                        <span class="status-badge status-pending">{{ $request->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap" id="maintenancePagination">
        <span class="pagination-info" id="paginationInfo"></span>
        <div class="pagination-btns">
            <button class="page-btn" id="prevPage" onclick="changePage(-1)">← Prev</button>
            <div id="pageNumbers" style="display:flex; gap:4px;"></div>
            <button class="page-btn" id="nextPage" onclick="changePage(1)">Next →</button>
        </div>
    </div>

    @else
    <div class="empty-state">
        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 40px; opacity: 0.2; margin-bottom: 10px;">
        <p>No pending requests</p>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        @if(Auth::user()->role === 'admin')
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueChart['labels'] ?? []) !!},
                datasets: [{
                    label: 'Revenue ₱',
                    data: {!! json_encode($revenueChart['data'] ?? []) !!},
                    borderColor: '#1a3b5c',
                    backgroundColor: 'rgba(26, 59, 92, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1a3b5c',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#64748b', font: { size: 10 } } },
                    x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
        @endif

        // ── Maintenance Requests Pagination ──
        const ROWS_PER_PAGE = 5;
        let currentPage = 1;
        const rows = document.querySelectorAll('.maintenance-row');
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / ROWS_PER_PAGE);

        function renderPage(page) {
            const start = (page - 1) * ROWS_PER_PAGE;
            const end   = start + ROWS_PER_PAGE;

            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });

            // Info text
            document.getElementById('paginationInfo').textContent =
                `Showing ${start + 1}–${Math.min(end, totalRows)} of ${totalRows} requests`;

            // Prev / Next
            document.getElementById('prevPage').disabled = page === 1;
            document.getElementById('nextPage').disabled = page === totalPages;

            // Numbered buttons
            const container = document.getElementById('pageNumbers');
            container.innerHTML = '';
            for (let p = 1; p <= totalPages; p++) {
                const btn = document.createElement('button');
                btn.textContent = p;
                btn.className = 'page-btn' + (p === page ? ' active' : '');
                btn.onclick = () => { currentPage = p; renderPage(p); };
                container.appendChild(btn);
            }

            // Hide pagination if only one page
            document.getElementById('maintenancePagination').style.display =
                totalPages <= 1 ? 'none' : 'flex';
        }

        window.changePage = function (dir) {
            const next = currentPage + dir;
            if (next >= 1 && next <= totalPages) {
                currentPage = next;
                renderPage(currentPage);
            }
        };

        if (totalRows > 0) renderPage(currentPage);
    });
</script>
@endsection