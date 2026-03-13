<li class="{{ request()->url() == $item->url ? 'active' : '' }}" x-data="{ open: false }">
    <div class="mobile-nav-item" :class="{ 'submenu-active': open }">
        <a href="{{ $item->url ?? route('category.show', ['slug' => $item->slug]) }}" target="{{ $item->target ?? '_self' }}">
            {{ $item->label ?? $item->name }}
        </a>
        @if($item->children->count() > 0)
            <div class="submenu-toggle" @click="open = !open">
                <i class="ri-arrow-down-s-line"></i>
            </div>
        @endif
    </div>
    @if($item->children->count() > 0)
        <ul class="mobile-submenu" :class="{ 'open': open }" x-show="open" x-collapse>
            @foreach($item->children as $child)
                @include('frontend.partials.mobile-menu-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>
