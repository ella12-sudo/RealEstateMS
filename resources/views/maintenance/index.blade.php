@extends('layouts.app')

@section('page-title', 'Maintenance')

@section('content')
<style>
    .page-subtitle { font-size: 12px; color: #64748b; margin-bottom: 16px; margin-top: -10px; }
    
    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
    .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

    table { width: 100%; border-collapse: collapse; min-width: 500px; }
    th { padding: 8px 14px; text-align: left; color: #94a3b8; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #fafafa; border-bottom: 1px solid #f0f0f0; white-space: nowrap; }
    td { padding: 10px 14px; border-bottom: 1px solid #f5f5f5; font-size: 12.5px; color: #334155; vertical-align: middle; }

    /* Column widths */
    th:nth-child(1), td:nth-child(1) { width: 90px; }
    th:nth-child(2), td:nth-child(2) { width: 280px; max-width: 280px; }
    th:nth-child(3), td:nth-child(3) { width: 110px; }
    th:nth-child(4), td:nth-child(4) { width: 120px; }
    th:nth-child(5), td:nth-child(5) { width: 120px; }
    th:nth-child(6), td:nth-child(6) { width: 80px; padding-right: 20px; }

    .priority-badge { padding: 2px 8px; border-radius: 50px; font-size: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; white-space: nowrap; }
    .p-Emergency { background: #fee2e2; color: #b91c1c; }
    .p-High { background: #ffedd5; color: #9a3412; }
    .p-Medium { background: #f0f9ff; color: #075985; }
    .p-Low { background: #f1f5f9; color: #475569; }

    .cost-box { margin-top: 5px; display: flex; gap: 4px; align-items: center; }
    .cost-input { border: 1px solid #c9952a; border-radius: 4px; padding: 3px; width: 70px; font-size: 11px; outline: none; }
    .btn-save { background: #1a2e4a; color: white; border: none; padding: 3px 8px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600; }

    .status-select {
        font-size: 10px; padding: 2px 8px; border-radius: 50px; font-weight: 700;
        cursor: pointer; border: none; outline: none; appearance: none;
        -webkit-appearance: none; text-align: center; height: 22px;
    }
    .status-Pending { background: #fef3c7; color: #92400e; }
    .status-InProgress { background: #e0f2fe; color: #075985; }
    .status-Completed { background: #dcfce7; color: #15803d; }

    /* FIX 2: Single-line title with ellipsis */
    .issue-title { font-weight: 600; color: #1a2e4a; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }
    /* FIX 3: Single-line description with ellipsis */
    .issue-desc  { font-size: 11px; color: #94a3b8; margin-top: 2px; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .tab-container { display: flex; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
    .tabs { display: flex; background: #f1f5f9; padding: 3px; border-radius: 8px; gap: 2px; }
    .tab-link { padding: 5px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; color: #64748b; transition: 0.2s; white-space: nowrap; }
    .tab-link.active { background: white; color: #1a3b5c; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .tab-link.archived-tab.active { background: white; color: #c9952a; }

    @media (max-width: 600px) {
        .col-hide { display: none; }
        table { min-width: 360px; }
    }

    .pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; justify-content: center; flex-wrap: wrap; }
    .pagination li a, .pagination li span { display: inline-block; padding: 5px 11px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0; color: #1a3b5c; text-decoration: none; background: white; }
    .pagination li.active span { background: #1a3b5c; color: white; border-color: #1a3b5c; }
    nav[role="navigation"] > div:first-child { display: none !important; }

    /* ===== MODAL STYLES ===== */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 9999;
        align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white; border-radius: 12px; padding: 24px;
        width: 90%; max-width: 400px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: modalIn 0.2s ease;
    }
    @keyframes modalIn {
        from { transform: translateY(-16px); opacity: 0; }
        to   { transform: translateY(0);     opacity: 1; }
    }
    .modal-title { margin: 0 0 4px; color: #1a2e4a; font-size: 15px; font-weight: 700; }
    .modal-sub   { margin: 0 0 16px; font-size: 12px; color: #64748b; }
    .modal-info  {
        background: #f8fafc; border-radius: 8px; padding: 10px 12px;
        margin-bottom: 16px; font-size: 12px; color: #334155;
        border: 1px solid #e2e8f0; line-height: 1.8;
    }
    .modal-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .modal-input-wrap {
        display: flex; align-items: center;
        border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 8px 12px; margin-top: 6px; margin-bottom: 20px;
    }
    .modal-input-wrap span { color: #64748b; font-size: 13px; margin-right: 6px; }
    .modal-input-wrap input {
        border: none; outline: none; font-size: 13px;
        width: 100%; color: #1a2e4a;
    }
    .modal-actions { display: flex; gap: 8px; justify-content: flex-end; }
    .btn-cancel {
        padding: 8px 16px; border-radius: 8px; border: 1px solid #e2e8f0;
        background: white; color: #64748b; font-size: 12px;
        font-weight: 600; cursor: pointer;
    }
    .btn-confirm {
        padding: 8px 16px; border-radius: 8px; border: none;
        background: #1a2e4a; color: white; font-size: 12px;
        font-weight: 600; cursor: pointer;
    }
    .btn-cancel:hover { background: #f1f5f9; }
    .btn-confirm:hover { background: #243d5e; }
</style>

<p class="page-subtitle">Update task status and record repair expenses automatically.</p>

@if(session('success'))
    <div style="background: #dcfce7; color: #15803d; padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; font-size: 12px; border: 1px solid #bdf0cc;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="tab-container">
    <div class="tabs">
        <a href="{{ route('maintenance.index', ['tab' => 'active']) }}"
           class="tab-link {{ $tab === 'active' ? 'active' : '' }}">
            <i class="fas fa-tools" style="margin-right: 4px;"></i> Active
        </a>
        <a href="{{ route('maintenance.index', ['tab' => 'archived']) }}"
           class="tab-link archived-tab {{ $tab === 'archived' ? 'active' : '' }}">
            <i class="fas fa-archive" style="margin-right: 4px;"></i> Archived
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Priority</th>
                    <th>Issue Details</th>
                    <th>Tenant</th>
                    <th>Status</th>
                    <th class="col-hide">Date</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td><span class="priority-badge p-{{ $req->priority }}">{{ $req->priority }}</span></td>
                    <td>
                        <div class="issue-title">{{ Str::limit($req->title, 30) }}</div>
                        <div class="issue-desc">{{ Str::limit($req->description, 50) }}</div>
                    </td>
                    <td style="white-space: nowrap;">{{ $req->tenant->user->first_name ?? 'N/A' }}</td>
                    <td>
                        @if($tab === 'active')
                        <form action="{{ route('maintenance.update', $req->id) }}" method="POST" id="form-{{ $req->id }}">
                            @csrf @method('PUT')
                            <select name="status"
                                onchange="checkStatus(this, {{ $req->id }}, '{{ addslashes(Str::limit($req->title, 30)) }}', '{{ addslashes($req->tenant->user->first_name ?? 'N/A') }}')"
                                class="status-select status-{{ str_replace(' ', '', $req->status) }}"
                                id="select-{{ $req->id }}">
                                <option value="Pending" {{ $req->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ $req->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ $req->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <div id="cost-area-{{ $req->id }}" class="cost-box" style="display:none;">
                                <input type="number" name="cost" class="cost-input" placeholder="₱" step="0.01">
                            </div>
                        </form>
                        @else
                            <span class="status-select status-{{ str_replace(' ', '', $req->status) }}" style="cursor:default;">{{ $req->status }}</span>
                            @if($req->archived_at)
                                <div style="font-size:10px; color:#94a3b8; margin-top:3px;">{{ $req->archived_at->format('M d, Y') }}</div>
                            @endif
                        @endif
                    </td>
                    <td class="col-hide" style="color:#64748b; white-space:nowrap;">{{ $req->created_at->format('M d, Y') }}</td>
                    <td style="text-align:right;">
                        <div style="display:flex; justify-content:flex-end; gap:5px; align-items:center;">
                            @if($tab === 'active')
                                @if($req->status === 'Completed')
                                    <form action="{{ route('maintenance.archive', $req->id) }}" method="POST" onsubmit="return confirm('Archive this?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" style="background:none; border:none; color:#c9952a; cursor:pointer; font-size:13px;" title="Archive">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('maintenance.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:13px;" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('maintenance.restore', $req->id) }}" method="POST" onsubmit="return confirm('Restore this?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="background:none; border:none; color:#16a34a; cursor:pointer; font-size:13px;" title="Restore">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('maintenance.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Permanently delete?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:13px;" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8; font-size:13px;">
                        {{ $tab === 'archived' ? 'No archived requests found.' : 'No maintenance requests found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div style="padding: 10px 14px; border-top: 1px solid #f0f0f0;">
        {{ $requests->links() }}
    </div>
    @endif
</div>

<!-- ===== COMPLETION MODAL ===== -->
<div id="completionModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title"><i class="fas fa-check-circle" style="color:#15803d; margin-right:6px;"></i>Mark as Completed</h3>
        <p class="modal-sub">Optionally enter the repair cost before confirming.</p>
        <div class="modal-info">
            <div><strong>Issue:</strong> <span id="modal-issue-title"></span></div>
            <div><strong>Tenant:</strong> <span id="modal-issue-tenant"></span></div>
        </div>
        <label class="modal-label">Repair Cost (Optional)</label>
        <div class="modal-input-wrap">
            <span>₱</span>
            <input type="number" id="modal-cost-input" placeholder="0.00" step="0.01" min="0">
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-confirm" onclick="submitCompletion()">Confirm & Save</button>
        </div>
    </div>
</div>

<script>
let activeFormId = null;
let activeSelect = null;

function checkStatus(select, id, issueTitle, tenantName) {
    const statusMap = { 'Pending': 'status-Pending', 'In Progress': 'status-InProgress', 'Completed': 'status-Completed' };
    select.className = 'status-select ' + (statusMap[select.value] || '');

    if (select.value === 'Completed') {
        activeFormId = id;
        activeSelect = select;
        document.getElementById('modal-issue-title').textContent = issueTitle;
        document.getElementById('modal-issue-tenant').textContent = tenantName;
        document.getElementById('modal-cost-input').value = '';
        document.getElementById('completionModal').classList.add('show');
    } else {
        const costArea = document.getElementById('cost-area-' + id);
        costArea.style.display = 'none';
        document.getElementById('form-' + id).submit();
    }
}

function closeModal() {
    document.getElementById('completionModal').classList.remove('show');
    if (activeSelect) {
        activeSelect.value = activeSelect.dataset.previous || 'Pending';
        const statusMap = { 'Pending': 'status-Pending', 'In Progress': 'status-InProgress', 'Completed': 'status-Completed' };
        activeSelect.className = 'status-select ' + (statusMap[activeSelect.value] || '');
    }
    activeFormId = null;
    activeSelect = null;
}

function submitCompletion() {
    if (!activeFormId) return;
    const cost = document.getElementById('modal-cost-input').value;
    const form = document.getElementById('form-' + activeFormId);
    const costInput = form.querySelector('input[name="cost"]');
    if (costInput) costInput.value = cost;
    document.getElementById('completionModal').classList.remove('show');
    form.submit();
}

document.querySelectorAll('.status-select').forEach(function(select) {
    select.dataset.previous = select.value;
    select.addEventListener('focus', function() {
        this.dataset.previous = this.value;
    });
});
</script>
@endsection