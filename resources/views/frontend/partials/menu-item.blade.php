<li class="{{ request()->url() == $item->url ? 'active' : '' }} {{ $item->children->count() > 0 ? 'has-dropdown' : '' }}">
    <a href="{{ $item->url }}" target="{{ $item->target }}">
        {{ $item->label ?? $item->name }}
        @if($item->children->count() > 0)
            <i class="ri-arrow-down-s-line"></i>
        @endif
    </a>
    @if($item->children->count() > 0)
        <ul class="dropdown-menu">
            @foreach($item->children as $child)
                @include('frontend.partials.menu-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>
