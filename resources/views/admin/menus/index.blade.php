@extends('layouts.admin')

@section('header', 'Menu Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Navigation Menus</h2>
            <p class="text-gray-500 text-sm mt-1">Create and manage multiple navigation menus for your website.</p>
        </div>
        <button onclick="document.getElementById('createMenuModal').classList.remove('hidden')" 
                class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Create New Menu</span>
        </button>
    </div>

    <!-- Menus Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Menu Name</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Slug</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Location</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Items Count</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $menu->name }}
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $menu->slug }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($menu->location)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg uppercase tracking-wider">
                                    {{ $menu->location }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs italic">Not Set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $menu->allItems->count() }} items
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <button onclick="editMenu({{ $menu->id }}, '{{ $menu->name }}', '{{ $menu->slug }}', '{{ $menu->location }}')" 
                                        class="p-2 rounded-lg bg-blue-50 text-blue-500 hover:text-blue-700 transition-all">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('menus.builder', $menu) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold transition-all text-sm">
                                    <i class="fas fa-tools"></i>
                                    <span>Items</span>
                                </a>
                                <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-400 hover:text-red-600 transition-all" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-list text-gray-300 text-2xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold tracking-tight">No Menus Created</h3>
                            <p class="text-gray-500 text-sm mt-1">Start by creating your first navigation menu.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Menu Modal -->
<div id="editMenuModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 leading-none">Edit Menu Settings</h3>
            <button onclick="document.getElementById('editMenuModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editMenuForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Menu Name</label>
                <input type="text" name="name" id="menu_name" required 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Slug</label>
                <input type="text" name="slug" id="menu_slug" required 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Location</label>
                <select name="location" id="menu_location" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                    <option value="">-- None --</option>
                    <option value="header">Header Menu</option>
                    <option value="footer">Footer Menu</option>
                </select>
            </div>
            <button type="submit" class="w-full py-4 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                <span>Update Menu</span>
                <i class="fas fa-check"></i>
            </button>
        </form>
    </div>
</div>

<script>
    function editMenu(id, name, slug, location) {
        document.getElementById('editMenuForm').action = `/backend/menus/${id}`;
        document.getElementById('menu_name').value = name;
        document.getElementById('menu_slug').value = slug;
        document.getElementById('menu_location').value = location;
        document.getElementById('editMenuModal').classList.remove('hidden');
    }
</script>

<!-- Create Menu Modal -->
<div id="createMenuModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 leading-none">Create New Menu</h3>
            <button onclick="document.getElementById('createMenuModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('menus.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Menu Name</label>
                <input type="text" name="name" required placeholder="e.g., Main Header Menu" 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Location</label>
                <select name="location" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                    <option value="">-- None --</option>
                    <option value="header">Header Menu</option>
                    <option value="footer">Footer Menu</option>
                </select>
            </div>
            <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2">
                <span>Create Menu</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
@endsection
