<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        $view = Permission::firstOrCreate(['name' => 'view dashboard']);
        $edit = Permission::firstOrCreate(['name' => 'edit settings']);

        $admin->givePermissionTo([$view, $edit]);
        $viewer->givePermissionTo([$view]);

        if ($u = User::where('email', 'vincent@test.local')->first()) {
            $u->assignRole('admin');
        }
    }
}
