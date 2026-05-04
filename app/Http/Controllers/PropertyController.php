<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // Show all properties
    public function index(Request $request)
    {
        $properties = Property::query()
            ->when($request->filled('location'), function ($q) use ($request) {
                // ← FIXED: now searches both property name AND location
                $q->where(function($q2) use ($request) {
                    $q2->where('location', 'like', '%' . $request->location . '%')
                       ->orWhere('name', 'like', '%' . $request->location . '%');
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->paginate(5)
            ->withQueryString();

        return view('properties.index', compact('properties'));
    }

    // Show form to add new property
    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
        return view('properties.create');
    }

    // Save new property to database
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:Residential,Commercial,Industrial',
            'location'       => 'required|string|max:255',
            'rent_per_month' => 'required|numeric|min:0',
            'status'         => 'required|in:Available,Occupied,Maintenance',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        Property::create($data);

        return redirect()->route('properties.index')
                         ->with('success', 'Property added successfully!');
    }

    // Show single property
    public function show(Property $property)
    {
        return view('properties.show', compact('property'));
    }

    // Show edit form
    public function edit(Property $property)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
        return view('properties.edit', compact('property'));
    }

    // Update property
    public function update(Request $request, Property $property)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:Residential,Commercial,Industrial',
            'location'       => 'required|string|max:255',
            'rent_per_month' => 'required|numeric|min:0',
            'status'         => 'required|in:Available,Occupied,Maintenance',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        $property->update($data);

        return redirect()->route('properties.index')
                         ->with('success', 'Property updated successfully!');
    }

    // Soft delete property
    public function destroy(Property $property)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $property->delete();

        return redirect()->route('properties.index')
                         ->with('success', 'Property archived successfully!');
    }
}