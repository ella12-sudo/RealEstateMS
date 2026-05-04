@extends('layouts.app')

@section('page-title', 'Transaction Details')

@section('content')

<style>
    .page-body {
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }
    .receipt-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    .detail-container {
        width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .detail-header {
        background: #1a3b5c;
        color: white;
        padding: 20px 16px;
        text-align: center;
    }
    .detail-body { padding: 20px; }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-label { color: #64748b; font-size: 12px; font-weight: 500; }
    .info-value { color: #1e293b; font-weight: 600; font-size: 12px; }
    .status-badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; background: #f1f5f9; }
</style>

<div class="receipt-wrapper">

    <div style="width: 320px; margin-bottom: 12px;">
        <a href="{{ route('payments.index') }}" style="text-decoration: none; color: #64748b; font-size: 12px;">
            <i class="fas fa-arrow-left"></i> Back to Transactions
        </a>
    </div>

    <div class="detail-container">
        <div class="detail-header">
            <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8; margin-bottom: 4px; letter-spacing: 1.5px;">Transaction Amount</div>
            <h1 style="margin: 0; font-size: 26px;">₱{{ number_format($payment->amount, 2) }}</h1>
        </div>

        <div class="detail-body">
            <h3 style="margin-top: 0; margin-bottom: 14px; color: #1a3b5c; font-size: 13px; border-bottom: 2px solid #1a3b5c; display: inline-block; padding-bottom: 4px;">
                Payment Receipt
            </h3>

            <div class="info-row">
                <span class="info-label">Transaction ID</span>
                <span class="info-value">#PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Tenant Name</span>
                <span class="info-value">{{ $payment->tenant->user->first_name ?? 'N/A' }} {{ $payment->tenant->user->last_name ?? '' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Property / Unit</span>
                <span class="info-value">{{ $payment->tenant->property->name ?? 'General' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</span>
            </div>

            {{-- VAT Breakdown (Option A: Inclusive) --}}
            @php
                $baseAmount = $payment->amount / 1.12;
                $vatAmount  = $payment->amount - $baseAmount;
            @endphp

            <div class="info-row">
                <span class="info-label">Base Rent (Net of VAT)</span>
                <span class="info-value">₱{{ number_format($baseAmount, 2) }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">VAT (12%)</span>
                <span class="info-value">₱{{ number_format($vatAmount, 2) }}</span>
            </div>

            <div class="info-row" style="border-bottom: 2px solid #1a3b5c; margin-bottom: 4px;">
                <span class="info-label" style="font-weight: 700; color: #1a2e4a;">Total Amount</span>
                <span class="info-value" style="color: #1a3b5c;">₱{{ number_format($payment->amount, 2) }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Method</span>
                <span class="info-value">{{ $payment->method ?? $payment->payment_method ?? 'N/A' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Transaction Type</span>
                <span class="info-value">{{ $payment->type ?? 'Rent Income' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="status-badge">{{ $payment->status }}</span>
                </span>
            </div>

            <div style="margin-top: 14px;">
                <p class="info-label" style="margin-bottom: 6px;">Notes:</p>
                <div style="background: #f8fafc; padding: 10px; border-radius: 6px; color: #1a2e4a; font-size: 12px; border: 1px solid #e2e8f0;">
                    {{ $payment->notes ?? 'No additional notes provided for this transaction.' }}
                </div>
            </div>

            <div style="margin-top: 16px;">
                <button onclick="window.print()" style="background: #1a3b5c; border: none; padding: 10px; width: 100%; border-radius: 6px; cursor: pointer; color: white; font-weight: 600; font-size: 13px; box-sizing: border-box;">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>

</div>

@endsection