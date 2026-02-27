<label class="flex items-center gap-3 p-2 rounded-lg hover:bg-emerald-50 group cursor-pointer transition-all" style="margin-left: {{ $level * 20 }}px;">
    @if($level > 0)
        <span class="text-gray-300 text-[10px] transform -translate-y-0.5"><i class="fas fa-level-up-alt rotate-90"></i></span>
    @endif
    <input type="checkbox" name="ids[]" value="{{ $category->id }}" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 transition-all">
    <span class="text-sm {{ $level === 0 ? 'font-bold text-gray-700' : 'text-gray-500 font-medium' }} group-hover:text-emerald-600 transition-colors">
        {{ $category->name }}
    </span>
</label>

@if($category->children->count() > 0)
    @foreach($category->children as $child)
        @include('admin.menus.partials.category-checkbox', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
