@extends('layouts.app')

@section('page-title', 'Maintenance')
@section('page-subtitle', 'Update task status and record repair expenses automatically.')

@section('content')
<style>
    .page-subtitle { font-size: 12px; color: #64748b; margin-bottom: 16px; margin-top: -10px; }
    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0; }
    .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 500px; }
    th { padding: 6px 14px; text-align: left; color: #94a3b8; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #fafafa; border-bottom: 1px solid #f0f0f0; white-space: nowrap; }
    td { padding: 10px 14px; border-bottom: 1px solid #f5f5f5; font-size: 12.5px; color: #334155; vertical-align: middle; line-height: 1; }
    th:nth-child(1), td:nth-child(1) { width: 90px; }
    th:nth-child(2), td:nth-child(2) { width: 220px; max-width: 220px; overflow: hidden; }
    th:nth-child(3), td:nth-child(3) { width: 110px; }
    th:nth-child(4), td:nth-child(4) { width: 120px; }
    th:nth-child(5), td:nth-child(5) { width: 120px; }
    th:nth-child(6), td:nth-child(6) { width: 100px; text-align: center !important; padding-right: 0; }
    .priority-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; white-space: nowrap; }
    .p-Emergency { color: #b91c1c; }
    .p-High { color: #9a3412; }
    .p-Medium { color: #075985; }
    .p-Low { color: #475569; }
    .status-badge { font-size: 10px; font-weight: 700; display: inline-block; white-space: nowrap; }
    .status-Pending { color: #92400e; }
    .status-InProgress { color: #075985; }
    .status-Completed { color: #15803d; }
    .issue-title { font-weight: 600; color: #1a2e4a; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; line-height: 1.2; }
    .issue-desc { font-size: 11px; color: #94a3b8; margin-top: 1px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2; }
    .pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; justify-content: flex-end; flex-wrap: wrap; }
    .pagination li a, .pagination li span { display: inline-block; padding: 5px 11px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0; color: #1a3b5c; text-decoration: none; background: white; }
    .pagination li.active span { background: #1a3b5c; color: white; border-color: #1a3b5c; }
    nav[role="navigation"] > div:first-child { display: none !important; }

    /* ===== MODAL STYLES ===== */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 9999; align-items: center; justify-content: center; }
    .modal-overlay.show { display: flex; }
    .modal-box { background: white; border-radius: 12px; padding: 24px; width: 90%; max-width: 420px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalIn 0.2s ease; }
    @keyframes modalIn { from { transform: translateY(-16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-title { margin: 0 0 4px; color: #1a2e4a; font-size: 15px; font-weight: 700; }
    .modal-sub { margin: 0 0 16px; font-size: 12px; color: #64748b; }
    .modal-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 5px; margin-top: 12px; }
    .modal-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; font-size: 13px; width: 100%; box-sizing: border-box; color: #1a2e4a; outline: none; background: white; }
    .modal-input-wrap { display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; margin-top: 6px; }
    .modal-input-wrap span { color: #64748b; font-size: 13px; margin-right: 6px; }
    .modal-input-wrap input { border: none; outline: none; font-size: 13px; width: 100%; color: #1a2e4a; }
    .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; }
    .btn-cancel { padding: 8px 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #64748b; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-confirm { padding: 8px 16px; border-radius: 8px; border: none; background: #1a2e4a; color: white; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-cancel:hover { background: #f1f5f9; }
    .btn-confirm:hover { background: #243d5e; }

    @media (max-width: 600px) { .col-hide { display: none; } table { min-width: 360px; } }
</style>

@if(session('success'))
    <div style="background:#dcfce7;color:#15803d;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:12px;border:1px solid #bdf0cc;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
    @if($tab === 'active')
        <a href="{{ route('maintenance.index', ['tab' => 'archived']) }}"
           style="font-size:12px;font-weight:600;color:#c9952a;text-decoration:none;display:flex;align-items:center;gap:5px;background:#fff8ed;border:1px solid #f0d9a8;padding:5px 12px;border-radius:7px;">
            <i class="fas fa-archive"></i> View Archived
        </a>
    @else
        <a href="{{ route('maintenance.index', ['tab' => 'active']) }}"
           style="font-size:12px;font-weight:600;color:#1a3b5c;text-decoration:none;display:flex;align-items:center;gap:5px;background:#f0f5ff;border:1px solid #c7d7ee;padding:5px 12px;border-radius:7px;">
            <i class="fas fa-tools"></i> View Active
        </a>
    @endif
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
                    <th style="text-align:center;">Actions</th>
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
                    <td style="white-space:nowrap;">{{ $req->tenant->user->first_name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ str_replace(' ', '', $req->status) }}">
                            {{ $req->status }}
                        </span>
                        @if($tab === 'archived' && $req->archived_at)
                            <div style="font-size:10px;color:#94a3b8;margin-top:3px;">{{ $req->archived_at->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td class="col-hide" style="color:#64748b;white-space:nowrap;">{{ $req->created_at->format('M d, Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;justify-content:center;gap:6px;align-items:center;">
                            @if($tab === 'active')
                                <button type="button"
                                    onclick="openEditModal({{ $req->id }}, '{{ $req->priority }}', '{{ $req->status }}', '{{ $req->cost }}')"
                                    style="background:none;border:none;color:#1a3b5c;cursor:pointer;font-size:13px;" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form action="{{ route('maintenance.archive', $req->id) }}" method="POST" onsubmit="return confirm('Archive this maintenance request?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="background:none;border:none;color:#c9952a;cursor:pointer;font-size:13px;" title="Archive">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('maintenance.restore', $req->id) }}" method="POST" onsubmit="return confirm('Restore this?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="background:none;border:none;color:#16a34a;cursor:pointer;font-size:13px;" title="Restore">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('maintenance.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Permanently delete?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:13px;" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;font-size:13px;">
                        {{ $tab === 'archived' ? 'No archived requests found.' : 'No maintenance requests found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div style="padding:10px 14px;border-top:1px solid #f0f0f0;display:flex;justify-content:flex-end;">
        {{ $requests->links() }}
    </div>
    @endif
</div>

<!-- ===== EDIT MODAL ===== -->
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <h3 class="modal-title"><i class="fas fa-pen" style="color:#1a3b5c;margin-right:6px;"></i>Edit Maintenance Request</h3>
        <p class="modal-sub">Update the priority and status of this request.</p>
        <form id="editForm" method="POST">
            @csrf @method('PUT')

            <label class="modal-label">Priority</label>
            <select name="priority" id="edit-priority" class="modal-select">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
                <option value="Emergency">Emergency</option>
            </select>

            <label class="modal-label">Status</label>
            <select name="status" id="edit-status" class="modal-select" onchange="toggleCostField()">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>

            <div id="edit-cost-wrap" style="display:none;">
                <label class="modal-label">Repair Cost (Optional)</label>
                <div class="modal-input-wrap">
                    <span>₱</span>
                    <input type="number" name="cost" id="edit-cost" placeholder="0.00" step="0.01" min="0">
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-confirm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCostField() {
    const status = document.getElementById('edit-status').value;
    document.getElementById('edit-cost-wrap').style.display = status === 'Completed' ? 'block' : 'none';
}

function openEditModal(id, priority, status, cost) {
    document.getElementById('editForm').action = '/maintenance/' + id;
    document.getElementById('edit-priority').value = priority;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-cost').value = cost > 0 ? cost : '';
    toggleCostField();
    document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('show');
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection