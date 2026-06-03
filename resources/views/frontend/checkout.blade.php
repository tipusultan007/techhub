@extends('layouts.frontend')

@section('title', 'Checkout | Tech Hub')

@push('styles')
    <style>
        /* Checkout Specific Styles */
        .checkout-title { margin: 30px 0; font-size: 1.8rem; font-weight: 800; display: flex; align-items: center; gap: 15px; color: var(--text-main); }
        .checkout-title i { font-size: 1.2rem; color: var(--text-muted); }

        .checkout-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; margin-bottom: 60px; }

        /* Forms */
        .checkout-section { background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 30px; margin-bottom: 25px; box-shadow: var(--shadow); }
        .section-head { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: var(--text-main); display: flex; justify-content: space-between; align-items: center; }

        .edit-link { font-size: 0.85rem; color: var(--brand-emerald); cursor: pointer; text-decoration: underline; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .full-width { grid-column: span 2; }

        .form-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
        .form-input { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.95rem; outline: none; transition: 0.2s; background: #fff; }
        .form-input:focus { border-color: var(--brand-emerald); box-shadow: 0 0 0 3px rgba(45, 174, 154, 0.1); }

        /* Payment Radio Cards */
        .payment-options { display: flex; flex-direction: column; gap: 15px; }
        .pay-radio { display: none; }
        .pay-card { border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; cursor: pointer; display: flex; align-items: center; gap: 15px; transition: 0.2s; }
        .pay-radio:checked + .pay-card { border-color: var(--brand-deep-blue); background: #f0f9ff; box-shadow: 0 0 0 1px var(--brand-deep-blue); }

        .radio-circle { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .pay-radio:checked + .pay-card .radio-circle { border-color: var(--brand-deep-blue); }
        .radio-dot { width: 10px; height: 10px; background: var(--brand-deep-blue); border-radius: 50%; display: none; }
        .pay-radio:checked + .pay-card .radio-dot { display: block; }

        /* Summary */
        .summary-sticky { position: sticky; top: 110px; }
        .order-summary { background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 25px; box-shadow: var(--shadow); }
        .item-row { display: flex; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
        .item-thumb { width: 60px; height: 60px; background: #f8fafc; border-radius: 6px; display: flex; align-items: center; justify-content: center; padding: 5px; border: 1px solid #f1f5f9; }
        .item-thumb img { max-height: 100%; mix-blend-mode: multiply; }
        .item-name { font-size: 0.9rem; font-weight: 600; line-height: 1.3; color: var(--text-main); }

        .place-order-btn { background: var(--brand-gradient); color: white; width: 100%; border: none; padding: 18px; border-radius: var(--radius); font-weight: 700; font-size: 1.1rem; cursor: pointer; margin-top: 25px; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(192, 77, 238, 0.3); }
        .place-order-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(192, 77, 238, 0.4); }

        /* Guest Alert */
        .guest-alert { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 15px; border-radius: var(--radius); margin-bottom: 20px; font-size: 0.9rem; display: flex; gap: 10px; align-items: center; }

        @media (max-width: 900px) { .checkout-layout { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <div class="container">

        <div class="checkout-title">
            Checkout <i class="ri-arrow-right-s-line"></i> <span style="font-size:1rem; color:var(--text-muted); font-weight:400;">Secure Payment</span>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" class="checkout-layout">
            @csrf

            <!-- Anti-Bot Honeypot Field -->
            <div style="display:none;" aria-hidden="true">
                <label for="_website_url">Leave this field empty</label>
                <input type="text" name="_website_url" id="_website_url" tabindex="-1" autocomplete="off">
            </div>

            <!-- LEFT COLUMN: INPUTS -->
            <div class="checkout-forms">

                <!-- 1. Contact Info -->
                <div class="checkout-section">
                    <div class="section-head">
                        1. Contact Information
                        @guest('customer')
                            <a href="{{ route('customer.login') }}" class="edit-link">Already have an account? Login</a>
                        @endguest
                    </div>

                    @auth('customer')
                        <div class="guest-alert">
                            <i class="ri-user-smile-line text-lg"></i>
                            <div>
                                Logged in as <strong>{{ auth('customer')->user()->name }}</strong> ({{ auth('customer')->user()->email }})
                            </div>
                        </div>
                    @endauth

                    <div class="form-group">
                        <label class="form-label">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" class="form-input"
                               value="{{ auth('customer')->check() ? auth('customer')->user()->email : old('email') }}"
                               placeholder="e.g. name@example.com" required>
                        <p style="font-size: 11px; color: #64748b; margin-top: 5px;">
                            We'll send the receipt and order updates here.
                        </p>
                    </div>
                </div>

                <!-- 2. Shipping Info -->
                <div class="checkout-section">
                    <div class="section-head">2. Shipping Address</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" class="form-input"
                                   value="{{ old('first_name', auth('customer')->user()->name ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Mobile Number (UAE) <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" class="form-input" placeholder="+971 50 123 4567" value="{{ old('phone', auth('customer')->user()->phone ?? '') }}" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="address" class="form-input" placeholder="e.g. Street, Building, Flat no." value="{{ old('address', auth('customer')->user()->address ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Emirate <span class="text-red-500">*</span></label>
                            <select name="city" class="form-input" required>
                                <option value="">Select Emirate</option>
                                <option value="Dubai" selected>Dubai</option>
                                <option value="Abu Dhabi">Abu Dhabi</option>
                                <option value="Sharjah">Sharjah</option>
                                <option value="Ajman">Ajman</option>
                                <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                                <option value="Fujairah">Fujairah</option>
                                <option value="Umm Al Quwain">Umm Al Quwain</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Area (Optional)</label>
                            <input type="text" name="area" class="form-input" placeholder="e.g. Dubai Marina" value="{{ old('area') }}">
                        </div>
                    </div>
                </div>

                <!-- 3. Payment Method -->
                <div class="checkout-section">
                    <div class="section-head">3. Payment Method</div>
                    <div class="payment-options">

                        <!-- Card (RAKBANK) -->
                        <div>
                            <input type="radio" name="payment_method" id="pay-card" value="rakbank" class="pay-radio" checked>
                            <label for="pay-card" class="pay-card">
                                <div class="radio-circle"><div class="radio-dot"></div></div>
                                <div style="flex:1;">
                                    <span style="font-weight:700; color:var(--text-main); display:block;">Credit / Debit Card (RAKBANK)</span>
                                    <span style="font-size:0.85rem; color:#64748b;">Secure online payment via RAKBANK</span>
                                </div>
                                <div style="display:flex; gap:10px; font-size:1.5rem; color:#64748b;">
                                    <i class="ri-visa-line"></i>
                                    <i class="ri-mastercard-line"></i>
                                </div>
                            </label>
                        </div>

                        <p style="font-size: 0.8rem; color: #64748b; margin-top: 10px; text-align: center;">
                            <i class="ri-information-line"></i> We accept credit and debit cards only.
                        </p>

                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: SUMMARY -->
            <div class="summary-wrapper">
                <div class="summary-sticky">
                    <div class="order-summary">
                        <h3 style="margin-bottom:20px; font-size:1.1rem; font-weight:800; color:var(--text-main);">Order Summary</h3>

                        @foreach($cart as $item)
                            @php
                                $product = \App\Models\Product::find($item['product_id']);
                                $productImage = $product ? $product->getFirstMediaUrl('product_image') : asset('frontend/assets/images/placeholder.jpg');
                            @endphp
                            <div class="item-row">
                                <div class="item-thumb">
                                    <img src="{{ $productImage }}" alt="{{ $item['name'] }}">
                                </div>
                                <div style="flex:1;">
                                    <div class="item-name">{{ $item['name'] }}</div>
                                    <div style="font-size:0.8rem; color:#64748b;">Qty: {{ $item['quantity'] }}</div>
                                </div>
                                <div style="font-weight:700; font-size:0.95rem; display: flex; flex-direction: column; align-items: flex-end;">
                                    <span>{{ number_format($item['price'] * $item['quantity']) }}</span>
                                    <span style="font-size: 0.7em; color: #64748b; font-weight: 400;">{{ ($item['tax_method'] ?? 'inclusive') === 'exclusive' ? '(+ VAT)' : '(Incl. VAT)' }}</span>
                                </div>
                            </div>
                        @endforeach

                        <div style="margin:20px 0; border-bottom:1px solid #f1f5f9;"></div>

                        <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.95rem; color:#64748b;">
                            <span>Subtotal (Excl. VAT)</span>
                            <span>{{ number_format($subtotal, 2) }} AED</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.95rem; color:#64748b;">
                            <span>VAT (5%)</span>
                            <span>{{ number_format($vat, 2) }} AED</span>
                        </div>

                        @if(isset($discount) && $discount > 0)
                        <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.95rem; color:#16a34a; font-weight:700;">
                            <span>Discount ({{ $coupon['code'] }})</span>
                            <span>-{{ number_format($discount, 2) }} AED</span>
                        </div>
                        @endif

                        <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.95rem; color:#64748b;">
                            <span>Shipping</span>
                            <span class="{{ $shipping > 0 ? '' : 'text-green-600 font-bold' }}">
                                {{ $shipping > 0 ? number_format($shipping, 2) . ' AED' : 'Free' }}
                            </span>
                        </div>

                        <div style="font-size: 0.8rem; color: #16a34a; margin-bottom: 15px; text-align: right;">
                            <i class="ri-truck-line"></i> Delivered on the next business day
                        </div>

                        <div style="border-top:1px solid #e2e8f0; padding-top:20px; margin-top:20px; font-size:1.3rem; font-weight:800; color:var(--brand-deep-blue); display:flex; justify-content:space-between; align-items:center;">
                            <span>Total</span>
                            <span>{{ number_format($total, 2) }} AED</span>
                        </div>

                        <button type="submit" class="place-order-btn">
                            Place Order <i class="ri-arrow-right-line"></i>
                        </button>

                        <div style="text-align:center; font-size:0.8rem; color:#64748b; margin-top:15px; display:flex; justify-content:center; align-items:center; gap:5px;">
                            <i class="ri-lock-2-fill"></i> Guaranteed Safe & Secure Checkout
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
