@extends('layouts.app')

@section('page-title', 'Add Property')

@section('content')

<style>
    .form-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 30px;
        max-width: 800px;
    }
    .form-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a2e4a;
        margin-bottom: 6px;
    }
    .form-subtitle {
        color: #888;
        font-size: 13px;
        margin-bottom: 24px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }
    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-input, .form-select, .form-textarea {
        padding: 11px 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        color: #333;
        outline: none;
        transition: border 0.2s;
        font-family: inherit;
    }
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus { border-color: #1a2e4a; }
    .form-textarea { resize: vertical; min-height: 100px; }
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }
    .btn-save {
        background: #1a2e4a;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover { background: #c9952a; }
    .btn-cancel {
        background: white;
        color: #555;
        padding: 12px 30px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-cancel:hover { background: #f4f6f9; }
    .error-text { color: #e53e3e; font-size: 12px; margin-top: 2px; }
</style>

<!-- Back link -->
<div style="margin-bottom: 20px;">
    <a href="{{ route('properties.index') }}"
       style="color:#888; font-size:14px; text-decoration:none; display:flex; align-items:center; gap:6px;">
        ← Back to Property Listing
    </a>
</div>

<div class="form-card">
    <h2 class="form-title">Add New Property</h2>
    <p class="form-subtitle">Fill in the details to list a new property</p>

    <form method="POST" action="{{ route('properties.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">

            <!-- Property Name -->
            <div class="form-group full">
                <label class="form-label">Property Name</label>
                <input type="text" name="name" class="form-input"
                       placeholder="e.g. Skyview Residences - Unit 3B"
                       value="{{ old('name') }}" required>
                @error('name')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <!-- Type -->
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="">Select type...</option>
                    <option value="Residential" {{ old('type') == 'Residential' ? 'selected' : '' }}>Residential</option>
                    <option value="Commercial" {{ old('type') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                </select>
                @error('type')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <!-- Status -->
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="">Select status...</option>
                    <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Occupied" {{ old('status') == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="Maintenance" {{ old('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('status')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <!-- Location -->
            <div class="form-group full">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-input"
                       placeholder="e.g. Matina, Davao City"
                       value="{{ old('location') }}" required>
                @error('location')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <!-- Rent per Month -->
            <div class="form-group">
                <label class="form-label">Rent per Month (₱)</label>
                <input type="number" name="rent_per_month" class="form-input"
                       placeholder="e.g. 18000"
                       value="{{ old('rent_per_month') }}" required>
                @error('rent_per_month')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <!-- Image -->
            <div class="form-group">
                <label class="form-label">Property Photo</label>
                <input type="file" name="image" class="form-input"
                       accept="image/jpeg,image/png,image/jpg">
                @error('image')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <!-- Description -->
            <div class="form-group full">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-textarea"
                          placeholder="Describe the property...">{{ old('description') }}</textarea>
                @error('description')<span class="error-text">{{ $message }}</span>@enderror
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">Save Property</button>
            <a href="{{ route('properties.index') }}" class="btn-cancel">Cancel</a>
        </div>

    </form>
</div>

@endsection