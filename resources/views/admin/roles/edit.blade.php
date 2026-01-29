@extends('layouts.admin')

@section('header', isset($role) ? 'Edit Role: ' . $role->name : 'Create New Access Role')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('roles.index') }}" class="text-sm font-bold text-gray-400 hover:text-[#2dae9a] flex items-center gap-1 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i> Back to Role Directory
        </a>
    </div>

    <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST" class="space-y-8">
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <!-- Card Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="mb-8">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Role Identification</label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" 
                       placeholder="e.g. Inventory Auditor"
                       class="w-full text-xl font-bold text-gray-900 border-0 border-b-2 border-gray-100 focus:border-[#2dae9a] focus:ring-0 transition-all placeholder:text-gray-200 px-0 pb-2"
                       required @if(isset($role) && in_array($role->name, ['Super Admin', 'Admin'])) readonly @endif>
                @error('name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-6">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Module Permissions</label>
                    <button type="button" @click="toggleAllPermissions()" class="text-[0.7rem] font-bold text-blue-600 hover:underline">Toggle All</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-data="{
                    toggleAllPermissions() {
                        const checkboxes = document.querySelectorAll('.perm-check');
                        const allChecked = Array.from(checkboxes).every(c => c.checked);
                        checkboxes.forEach(c => c.checked = !allChecked);
                    }
                }">
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="p-5 rounded-2xl bg-gray-50/50 border border-gray-100 hover:border-[#2dae9a]/30 transition-all group">
                        <h4 class="text-sm font-black text-gray-900 capitalize mb-4 flex items-center justify-between">
                            {{ $module }}
                            <i class="fas fa-shield-alt text-gray-200 group-hover:text-[#2dae9a]/50 transition-colors"></i>
                        </h4>
                        <div class="space-y-3">
                            @foreach($modulePermissions as $permission)
                            <label class="flex items-center gap-3 cursor-pointer group/label">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                           {{ in_array($permission->name, $rolePermissions ?? []) ? 'checked' : '' }}
                                           class="perm-check w-5 h-5 rounded-lg border-gray-100 text-[#2dae9a] focus:ring-[#2dae9a]/20 transition-all pointer-events-none">
                                </div>
                                <span class="text-xs font-bold text-gray-500 group-hover/label:text-gray-900 transition-colors capitalize">
                                    {{ explode(' ', $permission->name)[0] }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('roles.index') }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">Cancel Changes</a>
            <button type="submit" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold py-3.5 px-10 rounded-xl shadow-lg shadow-emerald-900/10 transform transition hover:-translate-y-0.5">
                {{ isset($role) ? 'Update Access Role' : 'Launch New Role' }}
            </button>
        </div>
    </form>
</div>
@endsection
