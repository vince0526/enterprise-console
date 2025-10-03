<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CoreDatabasePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'core.view',
            'core.create',
            'core.update',
            'core.delete',
            'core.manage-owners',
            'core.manage-lifecycle',
            'core.manage-links',
        ];

        foreach ($perms as $p) {
            Permission::findOrCreate($p);
        }

        // Attach to admin if exists
        if ($role = Role::where('name', 'admin')->first()) {
            $role->givePermissionTo($perms);
        }
    }
}
