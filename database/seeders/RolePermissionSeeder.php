<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Modules and Permissions
        $modules = [
            'dashboard' => ['access'],
            'pos' => ['access'],
            'orders' => ['manage', 'view'],
            'products' => ['manage', 'view'],
            'categories' => ['manage'],
            'brands' => ['manage'],
            'attributes' => ['manage'],
            'purchases' => ['manage', 'view'],
            'suppliers' => ['manage'],
            'customers' => ['manage'],
            'returns' => ['manage'],
            'quotations' => ['manage', 'view'],
            'expenses' => ['manage', 'view'],
            'coupons' => ['manage'],
            'banners' => ['manage'],
            'popups' => ['manage'],
            'solutions' => ['manage'],
            'pages' => ['manage'],
            'stock' => ['manage'],
            'reports' => ['view'],
            'users' => ['manage'],
            'roles' => ['manage'],
            'settings' => ['manage'],
            'notifications' => ['manage'],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$action} {$module}";
                Permission::firstOrCreate(['name' => $permissionName]);
                $allPermissions[] = $permissionName;
            }
        }

        // Create Roles and Assign Permissions
        
        // Super Admin (Full Access always)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions($allPermissions);

        // Admin (Almost everything)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($allPermissions);

        // Manager
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'access dashboard',
            'manage products', 'view products',
            'manage categories', 'manage brands',
            'manage purchases', 'view purchases',
            'manage customers', 'manage suppliers',
            'manage quotations', 'view quotations',
            'manage stock'
        ]);

        // Cashier
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier']);
        $cashierRole->syncPermissions([
            'access dashboard',
            'access pos',
            'view products',
            'manage customers',
            'manage orders'
        ]);

        // Assign Super Admin to User ID 1 (assuming it's the main dev/admin)
        $user = User::find(1);
        if ($user) {
            $user->assignRole($superAdminRole);
        }
    }
}
