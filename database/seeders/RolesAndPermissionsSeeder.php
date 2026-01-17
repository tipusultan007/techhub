<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- DEFINE PERMISSIONS ---
        Permission::create(['name' => 'use pos']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'manage purchases']);
        Permission::create(['name' => 'manage suppliers']);
        Permission::create(['name' => 'manage customers']);
        Permission::create(['name' => 'manage returns']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'manage settings']);
        Permission::create(['name' => 'manage users']);

        // --- DEFINE ROLES ---
        $cashierRole = Role::create(['name' => 'Cashier']);
        $cashierRole->givePermissionTo('use pos');

        $managerRole = Role::create(['name' => 'Manager']);
        $managerRole->givePermissionTo([
            'use pos',
            'manage products',
            'manage purchases',
            'manage suppliers',
            'manage customers',
            'manage returns',
        ]);

        // Super Admin gets all permissions by default (in AuthServiceProvider)
        $adminRole = Role::create(['name' => 'Admin']);

        // --- ASSIGN ROLE TO EXISTING ADMIN ---
        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}