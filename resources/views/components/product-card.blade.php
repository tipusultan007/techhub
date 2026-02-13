    @props(['product'])

@php
    // 1. Image
    $image = $product->getFirstMediaUrl('product_image') ?: asset('images/placeholder.jpg');

    // 2. Settings
    $currency = settings('currency_symbol', 'AED');
    $isNew = $product->created_at->diffInDays(now()) <= 7;

    // 3. Logic Initialization
    $isVariable = $product->type === 'variable';
    $priceDisplay = '';
    $oldPriceDisplay = null;
    $isOnSale = false;
    $discountPercent = 0;
    $stock = 0;

    if ($isVariable) {
        // --- VARIABLE PRODUCT LOGIC ---
        // Load variants to prevent N+1 issue if not eager loaded
        $variants = $product->variants;
        $stock = $variants->sum('stock_quantity');

        // Get Min and Max of the ACTIVE prices (what customers pay)
        $minPrice = $variants->map->active_price->min();
        $maxPrice = $variants->map->active_price->max();

        // Determine display string
        if ($minPrice == $maxPrice) {
            $priceDisplay = number_format($minPrice);
        } else {
            $priceDisplay = number_format($minPrice) . ' - ' . number_format($maxPrice);
        }

        // Check if the item is effectively on sale (if Min active price < Min regular price)
        $minRegular = $variants->min('selling_price');
        if ($minPrice < $minRegular) {
            $isOnSale = true;
            $discountPercent = round((($minRegular - $minPrice) / $minRegular) * 100);
        }

    } else {
        // --- SIMPLE PRODUCT LOGIC ---
        $stock = $product->stock_quantity;
        
        // Direct Column Usage
        $regularPrice = $product->selling_price; // 100
        $salePrice    = $product->sale_price;    // 80

        // Determine if on sale
        if ($salePrice && $salePrice < $regularPrice) {
            $activePrice = $salePrice;
            $isOnSale = true;
            $oldPriceDisplay = number_format($regularPrice);
            $discountPercent = round((($regularPrice - $activePrice) / $regularPrice) * 100);
        } else {
            $activePrice = $regularPrice;
            $isOnSale = false;
        }

        $priceDisplay = number_format($activePrice);
    }

    $isOutOfStock = $stock <= 0;
@endphp

<div class="product-card">

    {{-- BADGES --}}
    @if($isOutOfStock)
        <div class="badge-sale badge-sold-out">Sold Out</div>
    @elseif($isOnSale)
        <div class="badge-sale">-{{ $discountPercent }}%</div>
    @elseif($isNew)
        <div class="badge-new">NEW</div>
    @endif

    {{-- IMAGE --}}
    <a href="{{ route('product.show', $product->slug) }}" class="p-img">
        <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy">
    </a>

    <div class="p-details">
        {{-- Category --}}
        <div class="p-cat">
            {{ $product->category->name ?? 'General' }}
        </div>

        {{-- Title --}}
        <a href="{{ route('product.show', $product->slug) }}" class="p-title text-clamp-3">
            {{ $product->name }}
        </a>

        {{-- PRICE --}}
        <div class="p-price">
            {{-- Main Price --}}
            <span class="currency-symbol">{{ $currency }}</span> {{ $priceDisplay }}
            <span style="font-size: 10px; color: #64748b; margin-left: 2px;">+ 5% VAT</span>

            {{-- Crossed Out Price (Only for Simple Products usually, ranges get messy) --}}
            {{-- @if($oldPriceDisplay)
                <span class="p-old">{{ $currency }} {{ $oldPriceDisplay }}</span>
            @endif --}}
        </div>

        {{-- ACTIONS --}}
        @if($isOutOfStock)
            <button class="add-cart-hover btn-disabled" disabled>
                Out of Stock
            </button>
        @elseif($isVariable)
            <a href="{{ route('product.show', $product->slug) }}" class="add-cart-hover btn-block-link">
                Select Options
            </a>
        @else
            <button type="button"
                    class="add-cart-hover"
                    onclick="addToCart({{ $product->id }}, 1, null)">
                Add to Cart
            </button>
        @endif
    </div>
</div>
