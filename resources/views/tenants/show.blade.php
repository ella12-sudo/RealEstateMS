@extends('layouts.app')

@section('page-title', 'Tenant Profile')

@section('content')
<style>
    .page-body {
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }
    .detail-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        max-width: 620px;
        width: 100%;
        margin: 0 auto;
        overflow: hidden;
    }
    .detail-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    .detail-header h2 { font-size: 15px; font-weight: 700; color: #1a2e4a; margin: 0; }
    .detail-body { padding: 20px; }
    .detail-section-title { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
    .detail-row { margin-bottom: 8px; font-size: 13px; color: #1e293b; }
    .detail-row strong { color: #475569; }
    .detail-footer { padding: 12px 20px; border-top: 1px solid #f0f0f0; }
    .btn-back { color: #1a2e4a; text-decoration: none; font-size: 13px; font-weight: 600; }
    .btn-back:hover { color: #c9952a; }
    .badge-active { color: #166534; font-size: 12px; font-weight: 600; }
    .badge-expired { color: #991b1b; font-size: 12px; font-weight: 600; }
</style>

<div class="detail-card">
    <div class="detail-header">
        <h2>Tenant Details</h2>
        <span class="{{ $tenant->status == 'Active' ? 'badge-active' : 'badge-expired' }}">
            {{ $tenant->status }}
        </span>
    </div>

    <div class="detail-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div class="detail-section-title">Personal Information</div>
                <div class="detail-row"><strong>Full Name:</strong> {{ $tenant->user->first_name }} {{ $tenant->user->last_name }}</div>
                <div class="detail-row"><strong>Email:</strong> {{ $tenant->user->email }}</div>
                <div class="detail-row"><strong>Phone:</strong> {{ $tenant->phone ?? 'Not Provided' }}</div>
            </div>
            <div>
                <div class="detail-section-title">Lease Details</div>
                <div class="detail-row"><strong>Property:</strong> {{ $tenant->property->name ?? 'N/A' }}</div>
                <div class="detail-row"><strong>Lease Start:</strong> {{ \Carbon\Carbon::parse($tenant->lease_start)->format('M d, Y') }}</div>
                <div class="detail-row"><strong>Lease End:</strong> {{ \Carbon\Carbon::parse($tenant->lease_end)->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="detail-footer">
        <a href="{{ route('tenants.index') }}" class="btn-back">← Back to Directory</a>
    </div>
</div>

@endsection