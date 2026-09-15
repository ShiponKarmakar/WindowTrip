<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Show the staff login screen. */
    public function create()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /** Authenticate a staff member on the separate "admin" guard. */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $user = Auth::guard('admin')->user();

        // Only staff may hold an admin session.
        if (! $user->hasAnyRole(['admin', 'agent'])) {
            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email' => 'This account does not have staff access.',
            ]);
        }

        // Regenerate keeps existing session data (incl. any customer login).
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /** Log out only the admin guard; the customer session stays intact. */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }
}
