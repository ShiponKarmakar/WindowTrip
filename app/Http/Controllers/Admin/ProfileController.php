<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        $u = Auth::guard('admin')->user();

        return Inertia::render('Admin/Profile', [
            'profile' => [
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $u = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($u->id)],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $u->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::guard('admin')->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password updated.');
    }
}
