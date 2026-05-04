@extends('layouts.app')

@section('page-title', 'Tenants & Clients')

@section('topbar-extra')
<form method="GET" action="{{ route('tenants.index') }}" style="display:flex; align-items:center;">
    <input type="hidden" name="status" value="{{ $status }}">
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:12px; pointer-events:none;"></i>
        <input type="text" name="search" placeholder="Search tenants..." value="{{ request('search') }}"
            style="padding:7px 14px 7px 34px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; color:#334155; width:180px; outline:none; background:#f8fafc;"
            oninput="clearTimeout(window._st); window._st=setTimeout(()=>this.form.submit(),400)">
    </div>
</form>
@endsection

@section('content')
<style>
    .page-subtitle { font-size: 12px; color: #64748b; margin-bottom: 15px; margin-top: -10px; }

    .actions-container {
        display: flex; justify-content: flex-end; align-items: center;
        gap: 10px; margin-bottom: 15px; flex-wrap: wrap;
    }

    .btn-add {
        background: #1a2e4a; color: white !important; padding: 7px 14px;
        border-radius: 6px; text-decoration: none; font-weight: 600;
        font-size: 12px; display: flex; align-items: center; gap: 6px;
        border: none; cursor: pointer; white-space: nowrap;
    }
    .btn-add:hover { background: #c9952a; }

    .filter-dropdown {
        padding: 6px 28px 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;
        background: #ffffff; font-size: 12px; font-weight: 600; color: #1a2e4a;
        cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 6px center; background-size: 13px;
    }

    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e2e8f0; overflow: hidden; }
    .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

    table { width: 100%; border-collapse: collapse; min-width: 400px; }
    th { text-align: left; padding: 8px 14px; color: #94a3b8; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
    td { padding: 10px 14px; border-bottom: 1px solid #f5f5f5; font-size: 12.5px; color: #334155; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background-color: #f8fafc; }

    .badge-active  { background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; white-space: nowrap; }
    .badge-expired { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; white-space: nowrap; }

    .lease-expiring { color: #991b1b; font-weight: 600; font-size: 12px; }
    .lease-soon     { color: #854d0e; font-weight: 600; font-size: 12px; }
    .lease-ok       { color: #64748b; font-size: 12px; }

    .btn-view {
        background: #f1f5f9; color: #1a2e4a; font-weight: 600;
        font-size: 11px; padding: 4px 10px; border-radius: 6px;
        text-decoration: none; white-space: nowrap;
    }
    .btn-view:hover { background: #1a2e4a; color: white; }

    /* Hide lease end on mobile */
    @media (max-width: 600px) {
        .col-hide { display: none; }
        td, th { padding: 8px 10px; }
    }

    nav[role="navigation"] > div:first-child { display: none !important; }
</style>

<p class="page-subtitle">View and manage active tenants and their lease details</p>

<div class="actions-container">
    <select class="filter-dropdown" onchange="window.location.href=this.value">
        <option value="{{ route('tenants.index', ['status' => 'Active', 'search' => request('search')]) }}" {{ $status === 'Active' ? 'selected' : '' }}>
            Active Tenants
        </option>
        <option value="{{ route('tenants.index', ['status' => 'Expired', 'search' => request('search')]) }}" {{ $status === 'Expired' ? 'selected' : '' }}>
            Expired / Vacated
        </option>
    </select>
    <a href="{{ route('tenants.create') }}" class="btn-add">
        <i class="fas fa-plus"></i> Add New Tenant
    </a>
</div>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Unit / Property</th>
                    <th class="col-hide">Lease End</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                @php
                    $leaseEnd    = $tenant->lease_end ? \Carbon\Carbon::parse($tenant->lease_end) : null;
                    $daysLeft    = $leaseEnd ? now()->diffInDays($leaseEnd, false) : null;
                    $leaseClass  = 'lease-ok';
                    $leaseLabel  = '';
                    if ($daysLeft !== null) {
                        if ($daysLeft <= 30 && $daysLeft >= 0) { $leaseClass = 'lease-expiring'; $leaseLabel = ' — expires soon'; }
                        elseif ($daysLeft <= 60 && $daysLeft >= 0) { $leaseClass = 'lease-soon'; $leaseLabel = ' — 60 days'; }
                    }
                    $firstName   = $tenant->user->first_name ?? 'Unknown';
                    $lastName    = $tenant->user->last_name ?? '';
                    $email       = $tenant->user->email ?? 'N/A';
                    $property    = $tenant->property->name ?? 'N/A';
                    $leaseEndFmt = $leaseEnd ? $leaseEnd->format('M d, Y') : 'N/A';
                @endphp
                <tr class="tenant-row">
                    <td>
                        <div style="font-weight:600; color:#1a2e4a; font-size:12.5px;">{{ $firstName }} {{ $lastName }}</div>
                        <div style="font-size:11px; color:#94a3b8;">{{ $email }}</div>
                    </td>
                    <td style="color:#64748b; font-size:12px;">{{ $property }}</td>
                    <td class="col-hide">
                        <span class="{{ $leaseClass }}">{{ $leaseEndFmt }}{{ $leaseLabel }}</span>
                    </td>
                    <td>
                        @if($tenant->status === 'Active')
                            <span class="badge-active">Active</span>
                        @else
                            <span class="badge-expired">{{ $tenant->status }}</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('tenants.show', $tenant->id) }}" class="btn-view">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:40px; color:#94a3b8;">No tenants found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
    <div style="padding: 10px 20px; border-top: 1px solid #f0f0f0;">
        {{ $tenants->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection