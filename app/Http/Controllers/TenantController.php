<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    // Show all tenants
    public function index(Request $request)
    {
        // Auto-expire tenants whose lease_end date has passed
        Tenant::where('status', 'Active')
              ->whereNotNull('lease_end')
              ->where('lease_end', '<', now()->toDateString())
              ->each(function ($tenant) {
                  $tenant->update(['status' => 'Expired']);
                  if ($tenant->property) {
                      $tenant->property->update(['status' => 'Available']);
                  }
              });

        $query = Tenant::with(['user', 'property']);

        // Search by first_name or last_name
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        // Filter by status tab
        if ($request->status === 'Active') {
            $query->where('status', 'Active');
        } elseif ($request->status === 'Expired') {
            $query->whereIn('status', ['Expired', 'Vacated']);
        } else {
            // Default: show Active tenants
            $query->where('status', 'Active');
        }

        $tenants    = $query->orderBy('created_at', 'desc')->paginate(5); // ← changed
        $properties = Property::where('status', 'Available')->get();
        $status     = $request->status ?? 'Active';

        return view('tenants.index', compact('tenants', 'properties', 'status'));
    }

    public function create()
    {
        $properties = Property::where('status', 'Available')->get();
        return view('tenants.create', compact('properties')); 
    }

    // Save new tenant to database
    public function store(Request $request)
    {
        $request->validate([
            'first_name'  => 'required|string|max:255',
            'last_name'   => 'required|string|max:255',
            'email'       => 'required|email',
            'phone'       => 'required|string|max:20',
            'property_id' => 'required|exists:properties,id',
            'lease_start' => 'required|date',
            'lease_end'   => 'required|date|after:lease_start',
        ]);

        // Check if user already exists, if not create a new one
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'password'   => Hash::make('tenant123'),
                'role'       => 'tenant',
                'contact_number' => $request->phone,
            ]);
        }

        // Create tenant record
        Tenant::create([
            'user_id'     => $user->id,
            'property_id' => $request->property_id,
            'lease_start' => $request->lease_start,
            'lease_end'   => $request->lease_end,
            'status'      => 'Active',
        ]);

        // Mark property as Occupied
        Property::find($request->property_id)
                ->update(['status' => 'Occupied']);

        return redirect()->route('tenants.index')
                         ->with('success', 'Tenant added successfully!');
    }

    // Show single tenant
    public function show(Tenant $tenant)
    {
        $tenant->load(['user', 'property', 'payments']);
        return view('tenants.show', compact('tenant'));
    }

    // Mark tenant as Vacated manually
    public function vacate(Tenant $tenant)
    {
        $tenant->update(['status' => 'Vacated']);

        if ($tenant->property) {
            $tenant->property->update(['status' => 'Available']);
        }

        return redirect()->back()
                         ->with('success', 'Tenant marked as vacated. Property is now available.');
    }

    // Delete tenant
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('tenants.index')
                         ->with('success', 'Tenant removed successfully!');
    }

    // Tenant: My Payments page
    public function payments()
    {
        $tenant = auth()->user()->tenant;

        $totalPaid      = $tenant->payments()->where('status', 'Paid')->sum('amount');
        $pendingBalance = $tenant->payments()->where('status', 'Pending')->sum('amount');
        $monthlyRent    = $tenant->property->monthly_rent ?? 0;

        $payments = $tenant->payments()
                           ->orderBy('created_at', 'desc')
                           ->paginate(6);

        return view('tenant.payments', compact('payments', 'totalPaid', 'pendingBalance', 'monthlyRent'));
    }

    // Tenant: My Maintenance page
    public function maintenance()
    {
        $tenant = auth()->user()->tenant;

        $requests = $tenant->maintenanceRequests()
                           ->orderBy('created_at', 'desc')
                           ->paginate(6);

        return view('tenant.maintenance', compact('requests'));
    }
}