<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = Tenant::with('property')
            ->where('user_id', Auth::id())
            ->first();

        if (!$tenant) {
            return view('tenant.dashboard')->with('error', 'Profile not found.');
        }

        $payments = Payment::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();

        $maintenanceRequests = Maintenance::where('tenant_id', $tenant->id)
            ->latest()
            ->get();

        return view('tenant.dashboard', compact('tenant', 'payments', 'maintenanceRequests'));
    }

    public function paymentStore(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();

        // Prevent duplicate payment same day
        $isDuplicate = Payment::where('tenant_id', $tenant->id)
            ->where('amount', $request->amount)
            ->whereDate('payment_date', now())
            ->exists();

        if ($isDuplicate) {
            return redirect()->back()->with('error', 'Payment already recorded today!');
        }

        Payment::create([
            'lease_id'       => $tenant->lease_id,
            'tenant_id'      => $tenant->id,
            'property_id'    => $tenant->property_id,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'status'         => 'Paid',
            'payment_date'   => now(),
            'due_date'       => now()->startOfMonth(),
        ]);

        return redirect()->back()->with('success', 'Payment submitted successfully!');
    }
}