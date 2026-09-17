<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Features;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'web')
            ->with('permissions:id,name')
            ->withCount('users')
            ->orderBy('name')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'permissions' => $r->name === 'admin' ? Features::keys() : $r->permissions->pluck('name'),
                'permission_count' => $r->name === 'admin' ? count(Features::keys()) : $r->permissions->count(),
                'users_count' => $r->users_count,
                'locked' => $r->name === 'admin',
            ]);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'features' => Features::options(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Roles/Form', [
            'role' => null,
            'features' => Features::options(),
        ]);
    }

    public function edit(Role $role)
    {
        abort_if($role->name === 'admin', 403, 'The administrator role has full access and cannot be edited.');

        return Inertia::render('Admin/Roles/Form', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
            'features' => Features::options(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($this->validPermissions($data['permissions'] ?? []));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role "'.$role->name.'" created.');
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'admin', 403);
        $data = $this->validated($request, $role->id);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($this->validPermissions($data['permissions'] ?? []));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->name === 'admin', 403);

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Reassign the staff on this role before deleting it.');
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    private function validated(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:40', 'not_in:admin', Rule::unique('roles', 'name')->ignore($ignore)],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);
    }

    /** Keep only known feature permissions and ensure they exist. */
    private function validPermissions(array $perms): array
    {
        $valid = array_values(array_intersect($perms, Features::keys()));
        foreach ($valid as $p) {
            Permission::findOrCreate($p, 'web');
        }

        return $valid;
    }
}
