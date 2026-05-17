@extends('layouts.app')

@section('page-title', 'Payments & Billing')

@section('page-subtitle', 'Monitor collected payments, pending and overdue balances')

@section('content')
<style>
    .page-subtitle { font-size: 13px; color: #64748b; margin-bottom: 20px; margin-top: -10px; }

    /* Summary cards */
    .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
    @media (max-width: 900px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .summary-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }

    .summary-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .summary-card .label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }
    .summary-card .amount {
        font-size: 18px;
        font-weight: 700;
        color: #1a2e4a;
        white-space: nowrap;
    }
    .summary-card.green .amount { color: #28a745; }
    .summary-card.red .amount { color: #dc3545; }

    /* Graph + table stack on mobile */
    .payments-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 900px) { .payments-grid { grid-template-columns: 1fr; } }

    .graph-card { background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
    .table-header { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 6px; }
    .table-header h3 { font-size: 13px; font-weight: 600; color: #1a2e4a; margin: 0; }

    .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 380px; }
    th { padding: 8px 10px; text-align: left; color: #94a3b8; font-size: 10px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
    td { padding: 9px 10px; border-bottom: 1px solid #f5f5f5; font-size: 12px; color: #334155; }
    tbody tr:hover { background-color: #f8fafc; }

    .badge { font-size: 10px; font-weight: 700; display: inline-block; white-space: nowrap; }
.bg-rent { color: #15803d; }
.bg-maint { color: #b91c1c; }

    .btn-view-receipt { color: #1a2e4a; background: #f1f5f9; padding: 4px 7px; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .btn-approve { color: #15803d; background: #dcfce7; padding: 4px 7px; border-radius: 6px; border: none; cursor: pointer; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; }

    /* Hide entity column on mobile */
    @media (max-width: 600px) {
        .col-hide { display: none; }
    }

    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; padding: 16px; }
    .modal-box { background: white; padding: 20px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto; }
    .modal-title { font-size: 15px; font-weight: 700; color: #1a2e4a; margin-bottom: 16px; }
    .modal-form-group { margin-bottom: 14px; }
    .modal-form-group label { display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px; }
    .modal-form-group input { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; flex-wrap: wrap; }

    .pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; justify-content: flex-end; flex-wrap: wrap; }
    .pagination li a, .pagination li span { display: inline-block; padding: 5px 11px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0; color: #1a3b5c; text-decoration: none; background: white; }
    .pagination li.active span { background: #1a3b5c; color: white; border-color: #1a3b5c; }
    nav[role="navigation"] > div:first-child { display: none !important; }
</style>

{{-- CHANGED: Removed <p class="page-subtitle"> — subtitle moved to topbar via @section('page-subtitle') above --}}

<div class="summary-grid">
    <div class="summary-card green">
        <span class="label">Total Collected</span>
        <span class="amount">₱{{ number_format($totalCollected, 2) }}</span>
    </div>
    <div class="summary-card">
        <span class="label">Pending</span>
        <span class="amount">₱{{ number_format($totalPending, 2) }}</span>
    </div>
    <div class="summary-card red">
        <span class="label">Overdue</span>
        <span class="amount">₱{{ number_format($totalOverdue, 2) }}</span>
    </div>
    <div class="summary-card">
        <span class="label">Total Transactions</span>
        <span class="amount">{{ $totalRecords }}</span>
    </div>
</div>

<div class="payments-grid">
    <div class="graph-card">
        <h3 style="font-size: 13px; color: #1a3b5c; margin-bottom: 12px; font-weight: 600;">Annual Collection Overview</h3>
        <div style="height: 220px;">
            <canvas id="paymentHistoryChart"></canvas>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h3>Recent Transactions</h3>
            <span style="font-size:11px; color:#94a3b8;">{{ $payments->total() }} records</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th class="col-hide">Entity</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <span class="badge {{ ($payment->type ?? 'Rent') == 'Maintenance' ? 'bg-maint' : 'bg-rent' }}">
                                {{ $payment->type ?? 'Rent' }}
                            </span>
                        </td>
                        <td class="col-hide" style="font-weight:600; color:#1a2e4a; font-size:12px;">
                            {{ $payment->tenant->user->first_name ?? 'N/A' }} {{ $payment->tenant->user->last_name ?? '' }}
                        </td>
                        <td style="color: {{ ($payment->type ?? 'Rent') == 'Maintenance' ? '#dc3545' : '#1a2e4a' }}; font-weight:700; white-space:nowrap;">
                            {{ ($payment->type ?? 'Rent') == 'Maintenance' ? '-' : '+' }} ₱{{ number_format($payment->amount, 2) }}
                        </td>
                        <td>
                            <span class="badge" style="color:#475569;">{{ $payment->status }}</span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 4px; align-items: center;">
                                @if(auth()->user()->role === 'admin' && $payment->status === 'Pending')
                                    <button type="button" class="btn-approve" style="background: #fef9c3; color: #a16207;" 
                                            onclick="openCashModal({{ $payment->id }}, {{ $payment->amount }})" title="Receive Cash">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <form action="{{ route('payments.approve', $payment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Approve this payment?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-approve" title="Approve Payment">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('payments.show', $payment->id) }}" class="btn-view-receipt" title="View Receipt">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
       @if($payments->hasPages())
<div style="padding: 12px 14px; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end;">
    {{ $payments->links() }}
</div>
@endif
    </div>
</div>

<div class="modal-overlay" id="cashModal">
    <div class="modal-box">
        <div class="modal-title">Process Payment</div>
        <form action="" id="cashForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-form-group">
                <label>Payment Method</label>
                <select name="payment_method" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; background: white;" onchange="toggleCalculator(this.value)">
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="Online Banking">Online Banking</option>
                </select>
            </div>
            <div class="modal-form-group">
                <label>Total Bill Amount</label>
                <input type="text" id="bill_amount_display" readonly style="background: #f8fafc; font-weight: 700;">
            </div>
            <div id="calculator_fields">
                <div class="modal-form-group">
                    <label>Amount Received from Tenant</label>
                    <input type="number" name="amount_received" id="amount_received" placeholder="e.g. 15000" oninput="calculateChange()">
                </div>
                <div class="modal-form-group">
                    <label>Change (Sukli)</label>
                    <input type="text" id="change_amount" readonly style="font-weight: 700; background: #f8fafc;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('cashModal')" style="padding: 8px 14px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 13px;">Cancel</button>
                <button type="submit" style="padding: 8px 14px; border-radius: 6px; border: none; background: #1a2e4a; color: white; cursor: pointer; font-weight: 600; font-size: 13px;">Confirm & Paid</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCalculator(method) {
    const calcFields = document.getElementById('calculator_fields');
    const amountReceived = document.getElementById('amount_received');
    if (method === 'Cash') { calcFields.style.display = 'block'; amountReceived.required = true; }
    else { calcFields.style.display = 'none'; amountReceived.required = false; amountReceived.value = ''; }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function openCashModal(id, amount) {
    const modal = document.getElementById('cashModal');
    const form = document.getElementById('cashForm');
    form.action = `/payments/${id}/approve`;
    document.getElementById('bill_amount_display').value = '₱' + parseFloat(amount).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('bill_amount_display').dataset.raw = amount;
    document.getElementById('amount_received').value = '';
    document.getElementById('change_amount').value = '₱0.00';
    modal.style.display = 'flex';
}
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
function calculateChange() {
    const bill = parseFloat(document.getElementById('bill_amount_display').dataset.raw);
    const received = parseFloat(document.getElementById('amount_received').value) || 0;
    const change = received - bill;
    const changeInput = document.getElementById('change_amount');
    if (received > 0) {
        changeInput.value = '₱' + (change >= 0 ? change : 0).toLocaleString(undefined, {minimumFractionDigits: 2});
        changeInput.style.color = change >= 0 ? '#15803d' : '#dc3545';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('paymentHistoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($paymentsChart['labels'] ?? []) !!},
            datasets: [
                {
                    label: 'Collected ₱',
                    data: {!! json_encode($paymentsChart['collected'] ?? []) !!},
                    borderColor: '#1a3b5c',
                    backgroundColor: 'rgba(26, 59, 92, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1a3b5c',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Pending ₱',
                    data: {!! json_encode($paymentsChart['pending'] ?? []) !!},
                    borderColor: '#94a3b8',
                    backgroundColor: 'rgba(148, 163, 184, 0.06)',
                    borderWidth: 2,
                    pointBackgroundColor: '#94a3b8',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { boxWidth: 10, font: { size: 9 } }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 9 } } },
                x: { grid: { display: false }, ticks: { font: { size: 9 } } }
            }
        }
    });
});
</script>
@endsection