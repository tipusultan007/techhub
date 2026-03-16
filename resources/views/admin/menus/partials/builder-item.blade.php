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
            @include('admin.menus.partials.builder-item', ['item' => $child])
        @endforeach
    </div>
</div>
