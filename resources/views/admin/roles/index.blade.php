@extends('layouts.admin')

@section('header', 'System Roles & Permissions')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Access Roles</h3>
            <p class="text-sm text-gray-500">Manage user groups and their granular permissions.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition transform hover:-translate-y-0.5">
            <i class="fas fa-plus-circle mr-2"></i> Create New Role
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Role Name</th>
                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Permissions</th>
                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $role->name === 'Super Admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $role->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5 max-w-xl">
                            @forelse($role->permissions->take(8) as $permission)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase tracking-tight">
                                    {{ $permission->name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">No permissions assigned</span>
                            @endforelse
                            @if($role->permissions->count() > 8)
                                <span class="text-[10px] font-bold text-blue-500 font-mono">+{{ $role->permissions->count() - 8 }} more</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 text-gray-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        @if(!in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Cashier']))
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Permanent delete this role?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 text-gray-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
