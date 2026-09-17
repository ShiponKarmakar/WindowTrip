<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staff = User::staff()
            ->with('roles:id,name')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")))
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $u->roles->first()?->name,
                'is_self' => $u->id === Auth::guard('admin')->id(),
            ]);

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Form', [
            'staff' => null,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function edit(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        return Inertia::render('Admin/Staff/Form', [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'role' => $staff->roles->first()?->name,
            ],
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member '.$user->name.' added.');
    }

    public function update(Request $request, User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Don't let the last admin drop their own admin role and lock everyone out.
        if ($staff->hasRole('admin') && $data['role'] !== 'admin' && $this->adminCount() <= 1) {
            return back()->with('error', 'You cannot remove the last administrator.');
        }

        $staff->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
        if (! empty($data['password'])) {
            $staff->update(['password' => Hash::make($data['password'])]);
        }
        $staff->syncRoles([$data['role']]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(User $staff)
    {
        abort_unless($staff->isStaff(), 404);

        if ($staff->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        if ($staff->hasRole('admin') && $this->adminCount() <= 1) {
            return back()->with('error', 'You cannot delete the last administrator.');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member removed.');
    }

    private function roleOptions()
    {
        return Role::where('guard_name', 'web')->orderBy('name')->pluck('name');
    }

    private function adminCount(): int
    {
        return User::role('admin')->count();
    }
}
