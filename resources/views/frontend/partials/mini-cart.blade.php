@if(count($cart) > 0)
    @foreach($cart as $key => $item)
        <div class="cart-item-mini">

            <!-- Image -->
            <div class="mini-img">
                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
            </div>

            <!-- Info -->
            <div class="mini-info">
                <h4>
                    <a href="{{ route('product.show', \App\Models\Product::find($item['product_id'])->slug) }}">
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

    <div class="mini-total" style="border-top:1px solid #f1f5f9; padding-top:15px; margin-top:10px;">
        <span>Subtotal:</span>
        <span>{{ number_format($subtotal, 2) }} {{ settings('currency_symbol', 'AED') }}</span>
    </div>
@else
    <div style="text-align:center; padding: 60px 20px; color: #94a3b8;">
        <div style="background: #f8fafc; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
            <i class="ri-shopping-basket-2-line" style="font-size: 2.5rem; color: #cbd5e1;"></i>
        </div>
        <p style="font-weight: 500; color: var(--text-main);">Your cart is empty</p>
        <button @click="isCartOpen = false" style="margin-top:15px; color:var(--brand-magenta); background:none; border:none; font-weight:600; cursor:pointer; text-decoration: underline;">
            Continue Shopping
        </button>
    </div>
@endif
