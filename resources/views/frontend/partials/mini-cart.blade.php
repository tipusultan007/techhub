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
                    <span style="font-size: 0.7em; color: #64748b; margin-left: 2px;">{{ ($item['tax_method'] ?? 'inclusive') === 'exclusive' ? '(+ VAT)' : '(Incl. VAT)' }}</span>
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
        <span>Subtotal (Excl. VAT):</span>
        <span>{{ number_format($subtotal, 2) }} {{ settings('currency_symbol', 'AED') }}</span>
    </div>
    <div class="mini-total" style="border-top: none; padding-top: 0; margin-top: -10px; font-size: 0.9em; color: #64748b;">
        <span>VAT (5%):</span>
        <span>{{ number_format($vat, 2) }} {{ settings('currency_symbol', 'AED') }}</span>
    </div>
    <div class="mini-total" style="border-top: none; padding-top: 0; margin-top: -5px; font-size: 0.9em; color: #16a34a; font-weight: 600;">
        <span>Shipping:</span>
        <span>{{ $shipping > 0 ? number_format($shipping, 2) . ' ' . settings('currency_symbol', 'AED') : 'FREE' }}</span>
    </div>
    <div class="mini-total" style="border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 5px; font-weight: 800; color: #0038A8;">
        <span>Total:</span>
        <span>{{ number_format($total, 2) }} {{ settings('currency_symbol', 'AED') }}</span>
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
