@if(count($cart) > 0)
    @foreach($cart as $key => $item)
        @php
            $product = \App\Models\Product::find($item['product_id']);
            $productImage = $product ? $product->getFirstMediaUrl('product_image') : asset('frontend/assets/images/placeholder.jpg');
        @endphp
        <div class="cart-item-mini">

            <!-- Image -->
            <div class="mini-img">
                <img src="{{ $productImage }}" alt="{{ $item['name'] }}">
            </div>

            <!-- Info -->
            <div class="mini-info">
                <h4>
                    <a href="{{ route('product.show', $product ? $product->slug : '#') }}">
                        {{ Str::limit($item['name'], 25) }}
                    </a>
                </h4>
                <div class="mini-meta">
                    {{ $item['quantity'] }} x {{ number_format($item['price'], 2) }}
                </div>
                <span class="mini-price">
                {{ number_format($item['price'] * $item['quantity']) }} {{ settings('currency_symbol', 'AED') }}
            </span>
            </div>

            <!-- Remove Button (AJAX) -->
            <button type="button"
                    class="btn-remove-mini"
                    onclick="removeFromCart('{{ $key }}')"
                    title="Remove Item">
                <i class="ri-delete-bin-line"></i>
            </button>

        </div>
    @endforeach

    <div class="mini-total">
        <span>Subtotal:</span>
        <span>{{ number_format($subtotal, 2) }} {{ settings('currency_symbol', 'AED') }}</span>
    </div>
@else
    <div class="mini-empty-cart">
        <div class="mini-empty-icon">
            <i class="ri-shopping-basket-2-line"></i>
        </div>
        <p class="mini-empty-text">Your cart is empty</p>
        <button @click="isCartOpen = false" class="btn-continue-shopping">
            Continue Shopping
        </button>
    </div>
@endif
