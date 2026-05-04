<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        /* REMOVED: The role mismatch check that was causing login errors.
           This allows you to log in directly if your credentials are correct.
        */

        // ✅ Role-based redirect
        if ($user->role === 'admin') {
            // FIXED: Pointing back to your original 'dashboard' route name
            return redirect()->route('dashboard'); 
        } elseif ($user->role === 'tenant') {
            return redirect()->route('tenant.dashboard');
        }

        // Fallback
        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
