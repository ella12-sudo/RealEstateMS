<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Payment; 
use App\Models\Maintenance; 
use App\Models\User; // Added this
use App\Notifications\NewPaymentSubmitted; // Added this
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role === 'tenant') {
            return $this->tenant();
        }

       // Build last 6 months labels + revenue data
$revenueLabels = [];
$revenueData   = [];
for ($i = 5; $i >= 0; $i--) {
    $month = now()->subMonths($i);
    $revenueLabels[] = $month->format('M');
    $revenueData[]   = (float) Payment::where('status', 'Paid')
        ->whereYear('payment_date', $month->year)
        ->whereMonth('payment_date', $month->month)
        ->sum('amount');
}

$data = [
    'totalProperties' => Property::count(),
    'activeTenants'   => Tenant::where('status', 'Active')->count(),
    'monthlyRevenue'  => Payment::where('status', 'Paid')
                            ->whereYear('payment_date', now()->year)
                            ->whereMonth('payment_date', now()->month)
                            ->sum('amount'),
    'openRequests'    => Maintenance::where('status', 'Pending')->count(),
    'recentPayments'  => Payment::with(['tenant.user', 'property'])
                            ->whereHas('tenant')
                            ->latest()
                            ->take(5)
                            ->get(),
    'maintenanceRequests' => Maintenance::with(['tenant.user', 'property'])
                            ->where('status', 'Pending')
                            ->latest()
                            ->paginate(5),
    'revenueChart' => [
        'labels' => $revenueLabels,
        'data'   => $revenueData,
    ],
];

        return view('dashboard', $data);
    }

    public function tenant()
    {
        $tenant = Tenant::with(['user', 'property', 'payments' => function($query) {
            $query->latest()->take(5);
        }])->where('user_id', Auth::id())->first();

        if (!$tenant) {
            Auth::logout(); // ← changed
            return redirect()->route('login')->with('error', 'Your account is not yet assigned to a property. Please contact your admin.');
        }

        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'payments' => $tenant->payments
        ]);
    }

    public function payments()
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) { return back()->with('error', 'Profile not found.'); }
        
        $totalPaid      = Payment::where('tenant_id', $tenant->id)->where('status', 'Paid')->sum('amount');
        $pendingBalance = Payment::where('tenant_id', $tenant->id)->where('status', 'Pending')->sum('amount');
        $monthlyRent    = $tenant->property->rent_per_month ?? 0;

        $payments = Payment::where('tenant_id', $tenant->id)->latest()->paginate(6);

        return view('tenant.payments', compact('payments', 'totalPaid', 'pendingBalance', 'monthlyRent'));
    }

    public function maintenance()
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) { return back()->with('error', 'Tenant profile not found.'); }

        $requests = Maintenance::where('tenant_id', $tenant->id)->latest()->paginate(6);
        return view('tenant.maintenance', compact('requests'));
    }

    public function storeMaintenance(Request $request)
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) { return back()->with('error', 'Tenant profile not found.'); }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Maintenance::create([
            'tenant_id' => $tenant->id,
            'property_id' => $tenant->property_id, 
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'Pending', 
            'priority' => 'Medium', 
        ]);

        return redirect()->route('tenant.maintenance')->with('success', 'Submitted successfully!');
    }

    public function storePayment(Request $request)
    {
        $tenant = Auth::user()->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant profile not found.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        // We store the created payment into a variable $payment
        $payment = Payment::create([
            'lease_id'       => $tenant->lease_id ?? null,
            'tenant_id'      => $tenant->id,
            'property_id'    => $tenant->property_id,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'status'         => 'Pending',
            'payment_date'   => now(),
            'due_date'       => now()->startOfMonth(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Logic to notify the Admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewPaymentSubmitted($payment));
        }

        return redirect()->route('tenant.payments')->with('success', 'Payment submitted! Please wait for admin verification.');
    }
    
    public function showReceipt(Payment $payment)
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant || $payment->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access.');
        }
        $payment->load(['tenant.user', 'property']);
        return view('tenant.receipt', compact('payment'));
    }
}