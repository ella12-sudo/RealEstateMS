<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - #{{ $payment->id }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7fa; padding: 40px; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .receipt-card { background: white; width: 320px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #1a3b5c; color: white; padding: 20px 16px; text-align: center; }
        .body { padding: 20px; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; font-size: 12px; }
        .value { color: #1e293b; font-weight: 600; font-size: 12px; }
        .btn-print { display: block; width: 100%; padding: 10px; background: #1a3b5c; color: white; text-align: center; text-decoration: none; border-radius: 6px; margin-top: 16px; border: none; cursor: pointer; font-size: 13px; box-sizing: border-box; }
        @media print { .btn-print, .back-link { display: none; } }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="header">
            <p style="font-size: 11px; opacity: 0.8; margin-bottom: 5px; letter-spacing: 1.5px;">TRANSACTION AMOUNT</p>
            <h2 style="margin: 0; font-size: 26px;">₱{{ number_format($payment->amount, 2) }}</h2>
        </div>
        <div class="body">
            <h4 style="margin-top: 0; color: #1a3b5c; border-bottom: 2px solid #1a3b5c; display: inline-block; font-size: 13px;">Payment Receipt</h4>
            <div class="row"><span class="label">Transaction ID</span> <span class="value">#PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span></div>
            <div class="row"><span class="label">Tenant Name</span> <span class="value">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span></div>
            <div class="row"><span class="label">Property / Unit</span> <span class="value">{{ $payment->property->name ?? 'N/A' }}</span></div>

            {{-- FIXED: Null-safe date, falls back to created_at --}}
            <div class="row">
                <span class="label">Payment Date</span>
                <span class="value">
                    {{ $payment->payment_date 
                        ? \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') 
                        : ($payment->created_at ? $payment->created_at->format('F d, Y') : 'N/A') }}
                </span>
            </div>

            {{-- VAT Breakdown (Option A: Inclusive) --}}
            @php
                $baseAmount = $payment->amount / 1.12;
                $vatAmount  = $payment->amount - $baseAmount;
            @endphp
            <div class="row">
                <span class="label">Base Rent (Net of VAT)</span>
                <span class="value">₱{{ number_format($baseAmount, 2) }}</span>
            </div>
            <div class="row">
                <span class="label">VAT (12%)</span>
                <span class="value">₱{{ number_format($vatAmount, 2) }}</span>
            </div>
            <div class="row" style="border-bottom: 2px solid #1a3b5c; margin-bottom: 4px;">
                <span class="label" style="font-weight: 700; color: #1a2e4a;">Total Amount</span>
                <span class="value" style="color: #1a3b5c;">₱{{ number_format($payment->amount, 2) }}</span>
            </div>

            {{-- FIXED: Shows method column, falls back to payment_method --}}
            <div class="row">
                <span class="label">Method</span>
                <span class="value">{{ $payment->method ?? $payment->payment_method ?? 'N/A' }}</span>
            </div>

            <div class="row">
                <span class="label">Status</span>
                <span class="value" style="color: #10b981;">{{ $payment->status }}</span>
            </div>

            <button onclick="window.print()" class="btn-print">Print Receipt</button>
            <a href="{{ route('tenant.payments') }}" class="back-link" style="display: block; text-align: center; margin-top: 12px; color: #64748b; font-size: 11px; text-decoration: none;">← Back to Payments</a>
        </div>
    </div>
</body>
</html>