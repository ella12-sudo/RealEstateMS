@extends('layouts.app')

@section('page-title', 'Add New Tenant')

@section('content')
<style>
    .form-card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 420px; margin: 0 auto; overflow: hidden; }
    .form-header { padding: 12px 18px; border-bottom: 1px solid #f0f0f0; background: #f8fafc; }
    .form-header h2 { font-size: 14px; font-weight: 700; color: #1a2e4a; margin: 0; }
    .form-body { padding: 16px 18px; }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-size: 11px; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .form-group input, .form-group select { width: 100%; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #1e293b; outline: none; }
    .alert-danger { background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 6px; font-size: 12px; margin-bottom: 15px; border: 1px solid #fca5a5; }
    .btn-save { background: #1a2e4a; color: white; padding: 7px 16px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-save:hover { background: #c9952a; }
    .btn-cancel { color: #64748b; text-decoration: none; font-size: 13px; margin-left: 8px; }
</style>

<div class="form-card">
    <div class="form-header">
        <h2>Add New Tenant</h2>
    </div>

    <div class="form-body">
        {{-- ADDED: Error Display --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin: 0; padding-left: 15px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tenants.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                {{-- ADDED: Phone Field (Required by your Controller) --}}
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0912..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Property Unit</label>
                <select name="property_id" required>
                    <option value="">-- Select Available Unit --</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }} (₱{{ number_format($property->rent_per_month, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label>Lease Start</label>
                    <input type="date" name="lease_start" value="{{ old('lease_start') }}" required>
                </div>
                <div class="form-group">
                    <label>Lease End</label>
                    <input type="date" name="lease_end" value="{{ old('lease_end') }}" required>
                </div>
            </div>

            <div style="margin-top: 8px; display: flex; align-items: center;">
                <button type="submit" class="btn-save">Save Tenant</button>
                <a href="{{ route('tenants.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection