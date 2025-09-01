<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $viewDashboard = Permission::findOrCreate('view dashboard');
        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo($viewDashboard);

        // make first user an admin for convenience
        if ($u = User::first()) {
            $u->assignRole('admin');
        }
    }
}
