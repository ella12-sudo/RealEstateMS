@extends('layouts.app')

@section('page-title', 'Edit Property')

@section('content')
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="color: #1a2e4a; margin-bottom: 20px;">Edit Property: {{ $property->name }}</h2>

    <form action="{{ route('properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size: 14px; margin-bottom: 5px;">Property Name</label>
            <input type="text" name="name" value="{{ old('name', $property->name) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size: 14px; margin-bottom: 5px;">Type</label>
            <select name="type" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                <option value="Residential" {{ $property->type == 'Residential' ? 'selected' : '' }}>Residential</option>
                <option value="Commercial" {{ $property->type == 'Commercial' ? 'selected' : '' }}>Commercial</option>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size: 14px; margin-bottom: 5px;">Location</label>
            <input type="text" name="location" value="{{ old('location', $property->location) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size: 14px; margin-bottom: 5px;">Rent per Month (₱)</label>
            <input type="number" name="rent_per_month" value="{{ old('rent_per_month', $property->rent_per_month) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size: 14px; margin-bottom: 5px;">Status</label>
            <select name="status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                <option value="Available" {{ $property->status == 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Occupied" {{ $property->status == 'Occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="Maintenance" {{ $property->status == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-size: 14px; margin-bottom: 5px;">Update Image (Optional)</label>
            <input type="file" name="image" style="font-size: 13px;">
            @if($property->image)
                <p style="font-size: 12px; color: #888; mt-2">Current image: {{ $property->image }}</p>
            @endif
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #1a2e4a; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Update Property</button>
            <a href="{{ route('properties.index') }}" style="background: #f4f6f9; color: #555; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-size: 14px;">Cancel</a>
        </div>
    </form>
</div>
@endsection