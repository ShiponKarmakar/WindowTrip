<?php

namespace Database\Seeders;

use App\Support\Features;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Features::keys() as $key) {
            Permission::findOrCreate($key, 'web');
        }

        // Admin: full access.
        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(Features::keys());

        // Agent: sensible default subset (only if the role has no perms yet,
        // so we don't clobber a customised agent role on re-run).
        $agent = Role::findOrCreate('agent', 'web');
        if ($agent->permissions()->count() === 0) {
            $agent->syncPermissions(Features::AGENT_DEFAULTS);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
