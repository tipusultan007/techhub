@extends('layouts.frontend')

@section('title', 'Shopping Cart | Tech Hub')

@push('styles')
    <style>
        /* Modern Quantity Selector */
        .qty-selector {
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            background: white;
            width: fit-content;
        }
        .qty-btn {
            width: 36px;
            height: 36px;
            background: #f8fafc;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .qty-btn:hover { background: #e2e8f0; color: #0f172a; }
        .qty-btn:active { background: #cbd5e1; }

        .qty-val {
            width: 44px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.95rem;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            color: #0f172a;
        }

        /* Page Styles */
        .page-title { margin: 30px 0 20px; font-size: 1.8rem; font-weight: 800; color: #0f172a; }
        .cart-layout { display: grid; grid-template-columns: 1fr 380px; gap: 30px; align-items: start; margin-bottom: 60px; }

        .cart-items-container { background: white; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .cart-header-row { padding: 15px 25px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 0.9rem; color: #64748b; }

        .cart-item { display: flex; padding: 25px; border-bottom: 1px solid #e2e8f0; gap: 20px; transition: 0.2s; }
        .cart-item:last-child { border-bottom: none; }

        .item-img { width: 100px; height: 100px; background: #f8fafc; border-radius: 8px; display: flex; align-items: center; justify-content: center; padding: 5px; border: 1px solid #e2e8f0; }
        .item-img img { max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }

        .item-details { flex: 1; }
        .item-brand { font-size: 11px; text-transform: uppercase; color: #be185d; font-weight: 700; margin-bottom: 5px; }
        .item-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 5px; color: #0f172a; line-height: 1.3; }
        .item-title a:hover { color: #0038A8; text-decoration: underline; }
        .item-meta { font-size: 0.85rem; color: #64748b; margin-bottom: 10px; }
        .stock-status { font-size: 0.75rem; display: flex; align-items: center; gap: 5px; margin-top: 8px; color: #16a34a; font-weight: 600; }

        .item-controls { display: flex; align-items: center; justify-content: space-between; margin-top: 15px; }

        .item-price-col { text-align: right; min-width: 120px; display: flex; flex-direction: column; justify-content: space-between; }
        .item-price { font-size: 1.2rem; font-weight: 800; color: #0038A8; }

        .remove-btn { color: #ef4444; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px; justify-content: flex-end; transition: 0.2s; background: none; border: none; font-weight: 500; }
        .remove-btn:hover { text-decoration: underline; opacity: 0.8; }

        /* Summary Box */
        .summary-box { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; position: sticky; top: 110px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .summary-title { font-size: 1.2rem; font-weight: 800; margin-bottom: 20px; color: #0f172a; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.95rem; color: #64748b; }
        .summary-row.total { border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 20px; margin-bottom: 0; color: #0038A8; font-weight: 800; font-size: 1.4rem; align-items: center; }

        .checkout-btn { background: linear-gradient(135deg, #0038A8 0%, #9d4edd 100%); color: white; width: 100%; border: none; padding: 15px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; margin-top: 25px; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 4px 10px rgba(192, 77, 238, 0.3); }
        .checkout-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(192, 77, 238, 0.4); }

        /* Empty Cart */
        .empty-cart { text-align: center; padding: 80px 20px; background: white; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 60px; }
        .empty-cart i { font-size: 4rem; color: #e2e8f0; margin-bottom: 20px; display: block; }

        @media (max-width: 900px) { .cart-layout { grid-template-columns: 1fr; } .summary-box { position: static; margin-top: 20px; } }
        @media (max-width: 576px) { .cart-item { flex-direction: column; } .item-controls { flex-wrap: wrap; gap: 15px; width: 100%; } .item-price-col { text-align: left; margin-top: 15px; flex-direction: row; align-items: center; width: 100%; } }
    </style>
@endpush

@section('content')

    <!-- Alpine.js Application -->
    <div class="container" x-data="cartPage()" x-cloak>

        <h1 class="page-title">Shopping Cart (<span x-text="Object.keys(cart).length"></span> Items)</h1>

        <template x-if="Object.keys(cart).length > 0">
            <div class="cart-layout">

                <!-- LEFT COLUMN: ITEMS -->
                <div class="cart-items-container">
                    <div class="cart-header-row">Product Details</div>

                    <!-- Loop through Cart Items using Blade to set initial structure -->
                    @foreach($cart as $key => $item)

                     @php
                        $product = \App\Models\Product::find($item['product_id']);
                        $productImage = $product ? $product->getFirstMediaUrl('product_image') : asset('frontend/assets/images/placeholder.jpg');
                    @endphp
                    
                        <div class="cart-item" id="item-{{ $key }}">
                            <div class="item-img">
                                <img src="{{ $productImage }}" alt="{{ $item['name'] }}">
                            </div>

                            <div class="item-details">
                                <div class="item-brand">Tech Hub</div>
                                <h3 class="item-title">
                                    <a href="{{ route('product.show', \App\Models\Product::find($item['product_id'], ['*'])->slug) }}">
                                        {{ $item['name'] }}
                                    </a>
                                </h3>

                                @if(isset($item['variant_name']))
                                    <div class="item-meta">{{ $item['variant_name'] }}</div>
                                @endif

                                <div class="stock-status">
                                    <i class="ri-check-line"></i> In Stock - Delivery by Tomorrow
                                </div>

                                <div class="item-controls">
                                    <!-- Quantity Selector -->
                                    <div class="qty-selector">
                                        <!-- Decrease -->
                                        <button type="button" class="qty-btn" @click="updateQty('{{ $key }}', -1)">-</button>

                                        <!-- Number Display -->
                                        <span class="qty-val" x-text="cart['{{ $key }}'].quantity">
                                    {{ $item['quantity'] }}
                                </span>

                                        <!-- Increase -->
                                        <button type="button" class="qty-btn" @click="updateQty('{{ $key }}', 1)">+</button>
                                    </div>
                                </div>
                            </div>

                            <div class="item-price-col">
                                <!-- Reactive Row Price Calculation -->
                                <div class="item-price">
                            <span x-text="formatNumber(cart['{{ $key }}'].price * cart['{{ $key }}'].quantity)">
                                {{ number_format($item['price'] * $item['quantity']) }}
                            </span>
                                    <span style="font-size: 0.6em;">AED</span>
                                    <span style="font-size: 0.5em; color: #64748b; margin-left: 2px;">+ 5% VAT</span>
                                </div>

                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $key }}">
                                    <button type="submit" class="remove-btn">
                                        <i class="ri-delete-bin-line"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- RIGHT COLUMN: SUMMARY -->
                <div class="summary-box">
                    <div class="summary-title">Order Summary</div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span x-text="subtotal + ' AED'">{{ number_format($subtotal, 2) }} AED</span>
                    </div>

                    <template x-if="discount > 0">
                        <div class="summary-row text-green-600 font-bold">
                            <span>Discount <span x-text="'(' + (coupon ? coupon.code : '') + ')'"></span></span>
                            <span x-text="'-' + discount + ' AED'"></span>
                        </div>
                    </template>

                    <div class="summary-row">
                        <span>Shipping</span>
                        <span class="text-green-600 font-bold">Free</span>
                    </div>
                    <div class="summary-row">
                        <span>VAT (5%)</span>
                        <span x-text="vat + ' AED'">{{ number_format($vat, 2) }} AED</span>
                    </div>

                    <div class="summary-row total">
                        <span>Total <span style="font-size:10px; font-weight:400; color:#64748b; display:block;">Inclusive of VAT</span></span>
                        <span x-text="total + ' AED'">{{ number_format($total, 2) }} AED</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="checkout-btn">
                        Secure Checkout <i class="ri-lock-line"></i>
                    </a>

                    <!-- Coupon Input -->
                    <div class="coupon-section mt-6 pt-6 border-t border-gray-100">
                        @if(Session::has('coupon'))
                            <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg border border-green-100">
                                <div class="text-sm">
                                    <span class="font-bold text-green-700">{{ Session::get('coupon')['code'] }}</span> applied
                                </div>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-wider">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="code" placeholder="Enter Coupon Code" 
                                    class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition-all">Apply</button>
                            </form>
                        @endif
                        @if(session('error'))
                            <p class="text-red-500 text-xs mt-2 font-medium"><i class="ri-error-warning-line"></i> {{ session('error') }}</p>
                        @endif
                    </div>

                    <!-- Payment Icons -->
                    <div class="payment-methods">
                        <p style="font-size: 0.8rem; color: #64748b; margin-top: 10px;">We Accept:</p>
                        <div class="pm-icons" style="display:flex; justify-content:center; gap:10px; font-size:1.5rem; color:#94a3b8; margin-top:5px;">
                            <i class="ri-visa-line"></i>
                            <i class="ri-mastercard-line"></i>
                            <i class="ri-paypal-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Empty State (Shows if cart is empty) -->
        <template x-if="Object.keys(cart).length === 0">
            <div class="empty-cart">
                <i class="ri-shopping-cart-line"></i>
                <h2 style="font-size:1.5rem; font-weight:700; margin-bottom:10px;">Your cart is empty</h2>
                <p style="color:#64748b; margin-bottom:25px;">Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ url('/') }}" class="checkout-btn" style="width: auto; display: inline-flex; background: #0038A8;">
                    Continue Shopping
                </a>
            </div>
        </template>

        <!-- Cross Sell -->
        @if($crossSellProducts->count() > 0)
            <div class="cross-sell" x-show="Object.keys(cart).length > 0">
                <h2 class="section-title"><i class="ri-flashlight-fill" style="color:#C04DEE"></i> You might also like</h2>
                <div class="grid-5">
                    @foreach($crossSellProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Alpine Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartPage', () => ({
                // Initialize with PHP data
                cart: @json($cart),
                subtotal: '{{ number_format($subtotal, 2) }}',
                discount: '{{ number_format($discount ?? 0, 2) }}',
                coupon: @json($coupon ?? null),
                vat: '{{ number_format($vat, 2) }}',
                total: '{{ number_format($total, 2) }}',

                updateQty(key, change) {
                    // Parse current quantity
                    let item = this.cart[key];
                    let currentQty = parseInt(item.quantity);
                    let newQty = currentQty + change;

                    // Prevent going below 1
                    if (newQty < 1) return;

                    // 1. Optimistic Update (Immediate UI Change)
                    this.cart[key].quantity = newQty;

                    // 2. Send Request to Server
                    fetch("{{ route('cart.update') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ key: key, quantity: newQty })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Update Totals from Server Response
                                this.subtotal = data.subtotal;
                                this.discount = data.discount;
                                this.vat = data.vat;
                                this.total = data.total;
                                // Handle automatic coupon removal if conditions not met
                                if (data.couponRemoved) {
                                    this.coupon = null;
                                    location.reload(); // Refresh to show error/status if needed, or handle in JS
                                }
                            } else {
                                // Revert on error
                                this.cart[key].quantity = currentQty;
                                alert('Could not update quantity.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.cart[key].quantity = currentQty; // Revert
                        });
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-US').format(num);
                }
            }));
        });
    </script>

    <!-- Ensure Alpine & Icons are loaded -->
    <style>[x-cloak] { display: none !important; }</style>

@endsection
