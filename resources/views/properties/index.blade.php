@extends('layouts.app')

@section('page-title', 'Property Listing')

@section('page-subtitle', 'View and manage all listed properties')

@section('topbar-extra')
<form id="filterForm" method="GET" action="{{ route('properties.index') }}" style="display:flex; align-items:center;">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" name="location" placeholder="Search location..." value="{{ request('location') }}">
    </div>
</form>
@endsection

@section('topbar-sub')
<select name="status" class="filter-select" form="filterForm">
    <option value="">All statuses</option>
    <option value="Occupied"    {{ request('status') == 'Occupied'    ? 'selected' : '' }}>Occupied</option>
    <option value="Available"   {{ request('status') == 'Available'   ? 'selected' : '' }}>Available</option>
    <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
</select>
<button type="submit" class="btn-filter" form="filterForm">Filter</button>
@if(request('location') || request('status'))
    <a href="{{ route('properties.index') }}" class="btn-reset">Reset</a>
@endif
@endsection

@section('content')
<style>
    .page-subtitle { font-size: 12px; color: #64748b; margin-bottom: 15px; margin-top: -10px; }

    .btn-add-top {
        background: #1a2e4a; color: white !important; padding: 7px 16px;
        border-radius: 6px; text-decoration: none; font-weight: 600;
        font-size: 12px; display: flex; align-items: center; gap: 6px;
        border: none; cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .btn-add-top:hover { background: #c9952a; }

    .search-wrap { position: relative; }
    .search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 12px; pointer-events: none; }
    .search-wrap input {
        padding: 7px 12px 7px 32px; border: 1px solid #e2e8f0; border-radius: 6px;
        font-size: 13px; color: #334155; width: 160px; outline: none; background: white;
    }

    .filter-select { padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; color: #334155; background: white; cursor: pointer; }
    .btn-filter { background: #1a2e4a; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-reset { padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; color: #64748b; background: #f8fafc; text-decoration: none; }

    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden; border: 1px solid #e2e8f0; }
    .table-header { padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; gap: 10px; }
    .table-header h3 { font-size: 14px; font-weight: 700; color: #1a2e4a; margin: 0; }
    .total-badge { background: #f4f6f9; color: #64748b; padding: 2px 8px; border-radius: 20px; font-size: 11px; margin-left: 8px; }

    .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 500px; }
    th { text-align: left; padding: 12px 16px; color: #94a3b8; font-size: 10px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
    td { padding: 14px 16px; border-bottom: 1px solid #f5f5f5; font-size: 12.5px; color: #334155; }
    tbody tr:hover { background-color: #f8fafc; }

    .action-cell { display: flex; gap: 8px; justify-content: center; align-items: center; }
    .icon-btn { cursor: pointer; background: none; border: none; padding: 4px; display: flex; align-items: center; border-radius: 5px; transition: 0.2s; text-decoration: none; }
    .icon-view { color: #1a2e4a !important; }
    .icon-edit { color: #c9952a !important; }
    .icon-delete { color: #dc3545 !important; }
    .icon-btn:hover { background: #f1f5f9; opacity: 0.8; }

    /* Flat text status — no pill */
    .badge { font-size: 10px; font-weight: 700; white-space: nowrap; display: inline-block; }
    .badge-occupied    { color: #0369a1; }
    .badge-available   { color: #15803d; }
    .badge-maintenance { color: #856404; }

    @media (max-width: 600px) {
        .col-hide { display: none; }
    }

    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 16px; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: white; border-radius: 10px; padding: 20px; width: 100%; max-width: 460px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); max-height: 90vh; overflow-y: auto; }
    .modal-title { font-size: 15px; font-weight: 700; color: #1a2e4a; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; }
    .modal-form-group { margin-bottom: 12px; text-align: left; }
    .modal-form-group label { display: block; font-size: 11px; font-weight: 600; color: #475569; margin-bottom: 4px; text-transform: uppercase; }
    .modal-form-group input, .modal-form-group select { width: 100%; padding: 9px 11px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #334155; outline: none; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; flex-wrap: wrap; }
    .btn-save { background: #1a2e4a; color: white; padding: 8px 18px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .btn-cancel { background: #f1f5f9; color: #475569; padding: 8px 18px; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }

    .pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; justify-content: flex-end; flex-wrap: wrap; }
    nav[role="navigation"] > div:first-child { display: none !important; }
</style>

@if(session('success'))
    <div style="background: #dcfce7; color: #15803d; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 12px; border: 1px solid #bdf0cc;">
        {{ session('success') }}
    </div>
@endif

@if(auth()->user()->role === 'admin')
<div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
    <button class="btn-add-top" onclick="openModal('addModal')">
        <i class="fas fa-plus"></i> Add Property
    </button>
</div>
@endif

<div class="table-card">
    <div class="table-header">
        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:6px;">
            <h3>All Properties</h3>
            <span class="total-badge">{{ $properties->total() }} Total</span>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Property Name</th>
                    <th class="col-hide">Type</th>
                    <th>Location</th>
                    <th>Rent/Mo</th>
                    <th>Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr>
                    <td style="font-weight:600; color:#1a2e4a; font-size:12.5px;">{{ $property->name }}</td>
                    <td class="col-hide" style="color:#64748b;">{{ $property->type }}</td>
                    <td style="color:#64748b;">{{ $property->location }}</td>
                    <td style="font-weight:600; white-space:nowrap;">₱{{ number_format($property->rent_per_month, 0) }}</td>
                    <td>
                        <span class="badge {{ $property->status == 'Occupied' ? 'badge-occupied' : ($property->status == 'Available' ? 'badge-available' : 'badge-maintenance') }}">
{{ $property->status }}
                        </span>
                    </td>
                    <td>
                        <div class="action-cell">
                            <a href="{{ route('properties.show', $property->id) }}" class="icon-btn icon-view" title="View">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if(auth()->user()->role === 'admin')
                                <button class="icon-btn icon-edit" onclick="openEditModal({{ $property->id }}, '{{ addslashes($property->name) }}', '{{ addslashes($property->location) }}', '{{ $property->rent_per_month }}', '{{ $property->status }}', '{{ $property->type }}')">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('properties.destroy', $property->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn icon-delete" onclick="return confirm('Archive this property?')">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">No properties found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 10px 20px; border-top: 1px solid #f0f0f0; display:flex; justify-content:flex-end;">{{ $properties->links() }}</div>
</div>

<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <div class="modal-title">Add New Property</div>
        <form action="{{ route('properties.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="Residential">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div class="modal-form-group" style="grid-column: span 2;">
                    <label>Property Name</label>
                    <select name="name" id="add_name" required onchange="autoFillLocation(this)">
                        <option value="">-- Select Property --</option>
                        <option value="Davao Heights" data-location="Davao City">Davao Heights</option>
                        <option value="Matina Grand Residences" data-location="Matina, Davao City">Matina Grand Residences</option>
                        <option value="Buhangin Riverside Homes" data-location="Buhangin, Davao City">Buhangin Riverside Homes</option>
                        <option value="Lanang Premier Villas" data-location="Lanang, Davao City">Lanang Premier Villas</option>
                        <option value="Talomo Hillside Terraces" data-location="Talomo, Davao City">Talomo Hillside Terraces</option>
                        <option value="Toril Garden Homes" data-location="Toril, Davao City">Toril Garden Homes</option>
                        <option value="Calinan Countryside Estates" data-location="Calinan, Davao City">Calinan Countryside Estates</option>
                        <option value="Mintal Greenview Villas" data-location="Mintal, Davao City">Mintal Greenview Villas</option>
                        <option value="Ma-a Hillcrest Heights" data-location="Ma-a, Davao City">Ma-a Hillcrest Heights</option>
                        <option value="Tibungco Bayside Estates" data-location="Tibungco, Davao City">Tibungco Bayside Estates</option>
                    </select>
                </div>
                <div class="modal-form-group"><label>Location</label><input type="text" name="location" id="add_location" required></div>
                <div class="modal-form-group"><label>Rent (₱)</label><input type="number" name="rent_per_month" required></div>
                <div class="modal-form-group">
                    <label>Status</label>
                    <select name="status"><option value="Available">Available</option><option value="Occupied">Occupied</option></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn-save">Save Property</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-title">Edit Property</div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="type" id="edit_type" value="Residential">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div class="modal-form-group"><label>Name</label><input type="text" name="name" id="edit_name" required></div>
                <div class="modal-form-group"><label>Location</label><input type="text" name="location" id="edit_location" required></div>
                <div class="modal-form-group"><label>Rent</label><input type="number" name="rent_per_month" id="edit_rent" required></div>
                <div class="modal-form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status"><option value="Available">Available</option><option value="Occupied">Occupied</option><option value="Maintenance">Maintenance</option></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-save">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function autoFillLocation(select) {
        const selected = select.options[select.selectedIndex];
        document.getElementById('add_location').value = selected.dataset.location || '';
    }
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    function openEditModal(id, name, location, rent, status, type) {
        document.getElementById('editForm').action = '/properties/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_location').value = location;
        document.getElementById('edit_rent').value = rent;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_type').value = type;
        openModal('editModal');
    }
    window.onclick = function(event) { if (event.target.className === 'modal-overlay active') { closeModal(event.target.id); } }
</script>
@endsection