@extends('layouts.app')

@section('page-title', 'Property Details')

@section('content')
<style>
    .detail-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        max-width: 480px;
        margin: 0 auto;
        overflow: hidden;
    }
    .detail-header {
        padding: 12px 18px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    .detail-header h2 { font-size: 14px; font-weight: 700; color: #1a2e4a; margin: 0; }
    .detail-header span { font-size: 11px; color: #94a3b8; }
    .detail-body { padding: 16px 18px; }
    .detail-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
    .detail-value { font-size: 13px; font-weight: 600; color: #1e293b; }
    .detail-footer { padding: 12px 18px; border-top: 1px solid #f0f0f0; }
    .btn-back { background: #f1f5f9; color: #1a2e4a; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.2s; }
    .btn-back:hover { background: #e2e8f0; }
    .badge-occupied { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .badge-available { background: #dbeafe; color: #1e40af; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; }
</style>

<div class="detail-card">
    <div class="detail-header">
        <h2>Property Details</h2>
        <span>ID: #{{ $property->id }}</span>
    </div>

    <div class="detail-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div>
                <div class="detail-label">Property Name</div>
                <div class="detail-value">{{ $property->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="detail-label">Location</div>
                <div class="detail-value">{{ $property->location ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="detail-label">Monthly Rent</div>
                <div class="detail-value" style="color: #1a2e4a;">
                    ₱{{ number_format($property->rent_per_month ?? 0, 2) }}
                </div>
            </div>
            <div>
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="{{ $property->status == 'Occupied' ? 'badge-occupied' : 'badge-available' }}">
                        {{ strtoupper($property->status ?? 'N/A') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-footer">
        <a href="/properties" class="btn-back">← Back to Listing</a>
    </div>
</div>

@endsection