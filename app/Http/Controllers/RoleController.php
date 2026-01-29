<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:Super Admin|Admin'),
        ];
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($item) {
            return explode(' ', $item->name)[1]; // Group by module name
        });
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Super Admin' && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('roles.index')->with('error', 'Unauthorized');
        }

        $permissions = Permission::all()->groupBy(function($item) {
            $parts = explode(' ', $item->name);
            return isset($parts[1]) ? $parts[1] : 'other';
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin' && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('roles.index')->with('error', 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Cashier'])) {
            return back()->with('error', 'Default roles cannot be deleted.');
        }

        $role->delete();
        return back()->with('success', 'Role deleted successfully.');
    }
}
