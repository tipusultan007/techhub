@extends('layouts.admin')

@section('header', 'Menu Builder: ' . $menu->name)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .menu-item-handle { cursor: move; }
    .nested-sortable {
        min-height: 10px;
        padding-top: 10px;
    }
    .nested-sortable .menu-item-row {
        margin-bottom: 10px;
    }
    .menu-item-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s;
    }
    .menu-item-card:hover {
        border-color: #2dae9a;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .nested-sortable .nested-sortable {
        padding-left: 2.5rem;
        border-left: 2px dashed #e5e7eb;
        margin-left: 1rem;
        margin-top: 0.5rem;
    }
    .accordion-btn.active i { transform: rotate(180deg); }
    .accordion-content.hidden { display: none; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('menus.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 text-gray-400 hover:text-emerald-500 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Menu Builder</h2>
            <p class="text-gray-500 text-sm mt-1">Manage items for <strong>{{ $menu->name }}</strong>. Drag to reorder or nest.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Sidebar: Add Items -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Add Menu Items</h3>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <!-- Categories Accordion -->
                    <div class="accordion-item">
                        <button class="accordion-btn w-full p-4 flex justify-between items-center hover:bg-gray-50 transition-all group active" data-target="cat-content">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-600"><i class="fas fa-th-large mr-3 text-gray-400"></i>Categories</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                        </button>
                        <div id="cat-content" class="accordion-content p-4 space-y-3">
                            <form action="{{ route('menus.items.store', $menu) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="category">
                                <div class="max-h-48 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                    @foreach($categories as $category)
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-sm text-gray-700 font-medium">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button type="submit" class="w-full mt-4 py-2.5 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all">Add to Menu</button>
                            </form>
                        </div>
                    </div>

                    <!-- Brands Accordion -->
                    <div class="accordion-item">
                        <button class="accordion-btn w-full p-4 flex justify-between items-center hover:bg-gray-50 transition-all group" data-target="brand-content">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-600"><i class="fas fa-award mr-3 text-gray-400"></i>Brands</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                        </button>
                        <div id="brand-content" class="accordion-content p-4 space-y-3 hidden">
                            <form action="{{ route('menus.items.store', $menu) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="brand">
                                <div class="max-h-48 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                    @foreach($brands as $brand)
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="ids[]" value="{{ $brand->id }}" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-sm text-gray-700 font-medium">{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button type="submit" class="w-full mt-4 py-2.5 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all">Add to Menu</button>
                            </form>
                        </div>
                    </div>

                    <!-- Pages Accordion -->
                    <div class="accordion-item">
                        <button class="accordion-btn w-full p-4 flex justify-between items-center hover:bg-gray-50 transition-all group" data-target="page-content">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-600"><i class="fas fa-file mr-3 text-gray-400"></i>Pages</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                        </button>
                        <div id="page-content" class="accordion-content p-4 space-y-3 hidden">
                            <form action="{{ route('menus.items.store', $menu) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="page">
                                <div class="max-h-48 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                    @foreach($pages as $page)
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="ids[]" value="{{ $page->id }}" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-sm text-gray-700 font-medium">{{ $page->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <button type="submit" class="w-full mt-4 py-2.5 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all">Add to Menu</button>
                            </form>
                        </div>
                    </div>

                    <!-- Custom Link Accordion -->
                    <div class="accordion-item">
                        <button class="accordion-btn w-full p-4 flex justify-between items-center hover:bg-gray-50 transition-all group" data-target="custom-content">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-emerald-600"><i class="fas fa-link mr-3 text-gray-400"></i>Custom Link</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                        </button>
                        <div id="custom-content" class="accordion-content p-4 hidden">
                            <form action="{{ route('menus.items.store', $menu) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="type" value="custom">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Link Label</label>
                                    <input type="text" name="label" required placeholder="Display Text" 
                                           class="w-full px-3 py-2 rounded-xl border-gray-200 text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">URL</label>
                                    <input type="text" name="url" required placeholder="https://..." 
                                           class="w-full px-3 py-2 rounded-xl border-gray-200 text-sm">
                                </div>
                                <button type="submit" class="w-full py-2.5 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all">Add to Menu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Workspace: Reorder Items -->
        <div class="lg:col-span-8 flex flex-col h-full bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[600px]">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Menu Structure</h3>
                <button type="submit" form="menu-order-form" class="inline-flex items-center gap-2 px-6 py-2 bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition-all">
                    <i class="fas fa-save"></i>
                    <span>Save Order</span>
                </button>
            </div>

            <div class="p-6 flex-1 overflow-y-auto">
                <form id="menu-order-form" class="h-full">
                    <div id="menu-builder-list" class="nested-sortable h-full space-y-3">
                        @foreach($menu->menuItems as $item)
                            <div class="menu-item-row" data-id="{{ $item->id }}">
                                <div class="menu-item-card group">
                                    <div class="menu-item-handle p-2 -ml-2 text-gray-300 group-hover:text-gray-400 transition-colors">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block text-sm font-bold text-gray-900 truncate">{{ $item->label }}</span>
                                        <span class="block text-[10px] uppercase font-black tracking-widest text-gray-300 mt-0.5">{{ $item->type }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="edit-item-btn p-1.5 text-gray-400 hover:text-emerald-500 transition-colors" data-id="{{ $item->id }}" data-label="{{ $item->label }}" data-url="{{ $item->url }}" data-target="{{ $item->target }}">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <form action="{{ route('menus.items.destroy', $item) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" onclick="return confirm('Remove this item?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="nested-sortable mt-2">
                                    @foreach($item->children as $child)
                                        <div class="menu-item-row" data-id="{{ $child->id }}">
                                            <div class="menu-item-card group">
                                                <div class="menu-item-handle p-2 -ml-2 text-gray-300 group-hover:text-gray-400 transition-colors">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <span class="block text-sm font-bold text-gray-900 truncate">{{ $child->label }}</span>
                                                    <span class="block text-[10px] uppercase font-black tracking-widest text-gray-300 mt-0.5">{{ $child->type }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" class="edit-item-btn p-1.5 text-gray-400 hover:text-emerald-500 transition-colors" data-id="{{ $child->id }}" data-label="{{ $child->label }}" data-url="{{ $child->url }}" data-target="{{ $child->target }}">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <form action="{{ route('menus.items.destroy', $child) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" onclick="return confirm('Remove this item?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- Potential sub-children if needed -->
                                            <div class="nested-sortable"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div id="editItemModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 leading-none">Edit Menu Item</h3>
            <button onclick="document.getElementById('editItemModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editItemForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Label</label>
                <input type="text" name="label" id="edit-label" required 
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
            </div>
            <div class="space-y-2" id="url-field">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">URL</label>
                <input type="text" name="url" id="edit-url"
                       class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Target</label>
                <select name="target" id="edit-target" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                    <option value="_self">Same Window</option>
                    <option value="_blank">New Tab</option>
                </select>
            </div>
            <button type="submit" class="w-full py-4 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                <span>Update Item</span>
                <i class="fas fa-check"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@1.0.0/jquery-sortablejs.min.js"></script>
<script>
$(document).ready(function() {
    // Accordion Logic
    $('.accordion-btn').on('click', function() {
        const target = $(this).data('target');
        $(this).toggleClass('active');
        $('#' + target).toggleClass('hidden');
    });

    // Initialize Nested Sortable
    const nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));
    nestedSortables.forEach(function(el) {
        new Sortable(el, {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.menu-item-handle',
            ghostClass: 'bg-emerald-50',
            chosenClass: 'scale-[1.02]',
            dragClass: 'opacity-0',
            onEnd: function() {
                // Reordering logic if needed on end (optional, we use save button)
            }
        });
    });

    // Save Order Logic
    $('#menu-order-form').on('submit', function(e) {
        e.preventDefault();
        
        const serialize = (el) => {
            const items = [];
            [].slice.call(el.children).forEach(function(row) {
                const item = { id: row.dataset.id };
                const sublist = row.querySelector('.nested-sortable');
                if (sublist && sublist.children.length > 0) {
                    item.children = serialize(sublist);
                }
                items.push(item);
            });
            return items;
        };

        const result = serialize(document.getElementById('menu-builder-list'));
        
        const saveBtn = $(this).find('button[type="submit"]');
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');

        $.ajax({
            url: "{{ route('menus.update-order') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                items: JSON.stringify(result)
            },
            success: function(response) {
                toastr.success('Menu structure saved successfully.');
                saveBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Save Order');
            },
            error: function() {
                toastr.error('Failed to save menu structure.');
                saveBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Save Order');
            }
        });
    });

    // Edit Item Logic
    $('.edit-item-btn').on('click', function() {
        const id = $(this).data('id');
        const label = $(this).data('label');
        const url = $(this).data('url');
        const target = $(this).data('target');

        $('#edit-label').val(label);
        $('#edit-url').val(url);
        $('#edit-target').val(target);
        $('#editItemForm').attr('action', `/backend/menu-items/${id}`);
        
        $('#editItemModal').removeClass('hidden');
    });
});
</script>
@endpush
