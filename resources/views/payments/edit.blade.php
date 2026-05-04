@extends('layouts.app')

@section('page-title', 'Edit Transaction')

@section('content')

<style>
    .edit-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 30px;
    }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #1a2e4a; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; }
    .btn-update { background: #1a2e4a; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .btn-cancel { color: #64748b; text-decoration: none; margin-left: 15px; font-size: 14px; }
</style>

<div class="edit-container">
    <h3 style="margin-top:0; color:#1a2e4a; font-size:18px; margin-bottom:25px; border-bottom:1px solid #f1f5f9; padding-bottom:15px;">
        Update Transaction Details
    </h3>

    <form action="{{ route('payments.update', $payment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Transaction Type</label>
                <select name="type" class="form-control" required>
                    <option value="Rent" {{ ($payment->type ?? 'Rent') == 'Rent' ? 'selected' : '' }}>Rent Income</option>
                    <option value="Maintenance" {{ ($payment->type ?? 'Rent') == 'Maintenance' ? 'selected' : '' }}>Maintenance Expense</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tenant / Reference</label>
                <select name="tenant_id" class="form-control" required>
                    <option value="">Select Tenant</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ $payment->tenant_id == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->user->first_name ?? 'N/A' }} {{ $tenant->user->last_name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Amount (₱)</label>
                <input type="number" name="amount" class="form-control" value="{{ $payment->amount }}" step="0.01" required>
            </div>

            <div class="form-group">
                <label class="form-label">Transaction Date</label>
                <input type="date" name="payment_date" class="form-control" value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Payment Status</label>
            <select name="status" class="form-control" required>
                <option value="Paid" {{ $payment->status == 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ $payment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Overdue" {{ $payment->status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Notes / Description</label>
            <textarea name="notes" class="form-control" rows="4">{{ $payment->notes }}</textarea>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-update">Update Record</button>
            <a href="{{ route('payments.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

@endsection