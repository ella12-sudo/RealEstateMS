<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Models\Transaction;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $tenants  = Tenant::with('user', 'property')->where('status', 'Active')->get();
        
        $query = Payment::with(['tenant.user', 'tenant.property']);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('tenant.user', function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $payments = $query->latest()->paginate(5);
        $payments->appends($request->only('search'));

        $totalCollected = Payment::where('status', 'Paid')->sum('amount');
        $totalPending   = Payment::where('status', 'Pending')->sum('amount');
        $totalOverdue   = Payment::where('status', 'Overdue')->sum('amount');
        $totalRecords   = Payment::count();

        $chartLabels    = [];
        $chartCollected = [];
        $chartPending   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[]    = $month->format('M');
            $chartCollected[] = (float) Payment::where('status', 'Paid')
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');
            $chartPending[]   = (float) Payment::where('status', 'Pending')
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');
        }

        $paymentsChart = [
            'labels'    => $chartLabels,
            'collected' => $chartCollected,
            'pending'   => $chartPending,
        ];

        return view('payments.index', compact(
            'tenants', 'payments', 'totalCollected', 'totalPending', 'totalOverdue', 'totalRecords', 'paymentsChart'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'      => 'required|exists:tenants,id',
            'amount'         => 'required|numeric',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|string', 
        ]);

        $isDuplicate = Payment::where('tenant_id', $request->tenant_id)
            ->where('amount', $request->amount)
            ->whereDate('payment_date', $request->payment_date)
            ->exists();

        if ($isDuplicate) {
            return redirect()->back()->with('error', 'This payment has already been recorded today!');
        }

        $tenant = Tenant::findOrFail($request->tenant_id);

        $payment = new Payment();
        $payment->tenant_id      = $request->tenant_id;
        $payment->property_id    = $tenant->property_id; 
        $payment->lease_id       = $tenant->lease_id; 
        $payment->amount         = $request->amount;
        $payment->payment_date   = $request->payment_date;
        $payment->payment_method = $request->payment_method;
        $payment->due_date       = $request->due_date ?? now();
        
        // Set to Paid immediately since admin is recording cash directly
        $payment->status         = 'Paid';
        $payment->paid_at        = now();
        
        $payment->notes          = $request->notes;
        $payment->save();

        // Create Transaction record
        Transaction::create([
            'payment_id' => $payment->id,
            'amount'     => $payment->amount,
            'net_amount' => $payment->amount / 1.12,
            'vat_amount' => $payment->amount - ($payment->amount / 1.12),
            'status'     => 'completed',
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $tenants = Tenant::with('user')->where('status', 'Active')->get();
        
        return view('payments.edit', compact('payment', 'tenants'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'type'         => 'required|string',
            'tenant_id'    => 'required|exists:tenants,id',
            'amount'       => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status'       => 'required|string',
            'notes'        => 'nullable|string',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update($validatedData);

        return redirect()->route('payments.index')->with('success', 'Transaction updated successfully!');
    }

    public function show($id)
    {
        $payment = Payment::with(['tenant.user', 'tenant.property'])->findOrFail($id);
        return view('payments.show', compact('payment'));
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Transaction deleted successfully!');
    }

    /**
     * Approve a pending payment.
     */
    public function approve(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        $method = $request->input('payment_method', 'Cash'); 

        $payment->update([
            'status'  => 'Paid',
            'paid_at' => now(),
            'method'  => $method,
        ]);

        // Create Transaction
        Transaction::create([
            'payment_id' => $payment->id,
            'amount'     => $payment->amount,
            'net_amount' => $payment->amount / 1.12,
            'vat_amount' => $payment->amount - ($payment->amount / 1.12),
            'status'     => 'completed',
        ]);

        return redirect()->back()->with('success', 'Payment approved successfully!');
    }

    /**
     * Archive a payment.
     */
    public function archive($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'Archived']);

        return redirect()->back()->with('success', 'Payment archived successfully!');
    }
}