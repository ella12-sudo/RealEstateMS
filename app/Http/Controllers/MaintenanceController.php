<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Maintenance::with(['tenant.user', 'property']);

        if (Auth::user()->role === 'tenant') {
            $tenant = Auth::user()->tenant;
            if (!$tenant) {
                return back()->with('error', 'Tenant profile not found.');
            }
            $query->where('tenant_id', $tenant->id);
        }

        // ADDED: Filter by tab (active or archived)
        $tab = $request->get('tab', 'active');
        if ($tab === 'archived') {
            $query->archived();
        } else {
            $query->active();
        }

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $requests = $query->latest()->paginate(5);
        $tenants = Tenant::with('user')->where('status', 'Active')->get();
        $properties = Property::all();

        if (Auth::user()->role === 'tenant') {
            return view('tenant.maintenance', compact('requests'));
        }

        return view('maintenance.index', compact('requests', 'tenants', 'properties', 'tab'));
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return redirect()->back()->with('error', 'Tenant record not found.');
        }

        try {
            Maintenance::create([
                'tenant_id'   => $tenant->id,
                'property_id' => $tenant->property_id,
                'title'       => $request->title,
                'description' => $request->description,
                'priority'    => $request->priority ?? 'Medium',
                'status'      => 'Pending'
            ]);

            $route = Auth::user()->role === 'tenant' ? 'tenant.maintenance' : 'maintenance.index';
            return redirect()->route($route)->with('success', 'Maintenance request logged!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

   public function update(Request $request, $id)
{
    $maintenance = Maintenance::findOrFail($id);

    $maintenance->title       = $request->title;
    $maintenance->description = $request->description;
    $maintenance->priority    = $request->priority;
    $maintenance->status      = $request->status;

    if ($request->status === 'Completed' && $request->filled('cost')) {
        $maintenance->cost = $request->cost;
    }

    $maintenance->save();

    return redirect()->route('maintenance.index')->with('success', 'Maintenance request updated successfully.');
}
    // ADDED: Archive a maintenance record
    public function archive($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update(['archived_at' => now()]);
        return redirect()->route('maintenance.index')->with('success', 'Request archived successfully!');
    }

    // ADDED: Restore an archived record
    public function restore($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update(['archived_at' => null]);
        return redirect()->route('maintenance.index', ['tab' => 'archived'])->with('success', 'Request restored successfully!');
    }

    public function destroy($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Record deleted.');
    }
}