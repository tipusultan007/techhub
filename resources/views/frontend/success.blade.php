@extends('layouts.frontend')

@section('title', 'Order Confirmed | Tech Hub')

@push('styles')
    <style>
        /* --- SUCCESS PAGE SPECIFIC CSS --- */

        .success-wrapper { max-width: 800px; margin: 50px auto; text-align: center; }

        /* Success Card */
        .success-card {
            background: white; border-radius: var(--radius); border: 1px solid var(--border); padding: 50px; box-shadow: var(--shadow);
        }

        .icon-circle {
            width: 100px; height: 100px;
            background: #ecfdf5; /* Light Green */
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 25px;
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .success-icon { font-size: 3.5rem; color: #10b981; }

        .success-title { font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 10px; }
        .success-msg { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 40px; }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Order Info Grid */
        .order-info-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: left;
            background: #f8fafc; border-radius: var(--radius); padding: 25px; border: 1px solid var(--border); margin-bottom: 40px;
        }

        .info-label { font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .info-value { font-size: 1rem; font-weight: 700; color: var(--text-main); }

        /* Tracking Bar */
        .tracking-bar {
            display: flex; justify-content: space-between; position: relative; margin: 0 20px 50px;
        }
        .tracking-bar::before {
            content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 2px; background: #e2e8f0; z-index: 1;
        }
        .track-step { position: relative; z-index: 2; text-align: center; }
        .step-dot {
            width: 30px; height: 30px; background: white; border: 2px solid #e2e8f0;
            border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;
        }
        .track-step.active .step-dot { background: var(--brand-magenta); border-color: var(--brand-magenta); color: white; }
        .track-step.active .step-text { color: var(--text-main); font-weight: 700; }
        .step-text { font-size: 0.85rem; color: var(--text-muted); }

        /* Items List */
        .ordered-items { text-align: left; border-top: 1px solid var(--border); padding-top: 30px; }
        .item-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
        .item-left { display: flex; align-items: center; gap: 15px; }
        .item-thumb { width: 50px; height: 50px; background: #f8fafc; border-radius: 6px; padding: 5px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); }
        .item-thumb img { max-height: 100%; mix-blend-mode: multiply; }
        .item-name { font-weight: 600; font-size: 0.95rem; color: var(--text-main); }
        .item-price { font-weight: 800; color: var(--brand-deep-blue); }

        /* Actions */
        .action-buttons { display: flex; gap: 20px; justify-content: center; margin-top: 40px; }
        .btn-primary, .btn-outline {
            padding: 15px 30px; border-radius: var(--radius); font-weight: 700; cursor: pointer; transition: 0.2s; font-size: 1rem; display: inline-block;
        }
        .btn-primary { background: var(--brand-gradient); color: white; border: none; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); }
        .btn-outline { background: white; color: var(--text-main); border: 1px solid var(--border); }
        .btn-outline:hover { border-color: var(--brand-magenta); color: var(--brand-magenta); }

        @media (max-width: 768px) {
            .order-info-grid { grid-template-columns: 1fr 1fr; gap: 20px; }
            .tracking-bar { display: none; }
            .success-card { padding: 30px 20px; }
            .action-buttons { flex-direction: column; }
        }
    </style>
@endpush

@section('content')
    <div class="container">

        <div class="success-wrapper">

            <div class="success-card">

                <div class="icon-circle">
                    <i class="ri-check-line success-icon"></i>
                </div>

                <h1 class="success-title">Thank You For Your Order!</h1>
                <p class="success-msg">
                    Your order has been confirmed and is being processed.
                    We've sent a confirmation email to
                    <b>{{ $order->guest_email }}</b>.
                </p>
                <!-- Order Details -->
                <div class="order-info-grid">
                    <div>
                        <span class="info-label">Order Number</span>
                        <span class="info-value">#{{ $order->invoice_no }}</span>
                    </div>
                    <div>
                        <span class="info-label">Date</span>
                        <span class="info-value">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="info-label">Total</span>
                        <span class="info-value">{{ number_format($order->total, 2) }} AED</span>
                    </div>
                    <div>
                        <span class="info-label">Payment</span>
                        <span class="info-value">{{ ucfirst($order->payment_method) }}</span>
                    </div>
                </div>

                <!-- Visual Progress -->
                <div class="tracking-bar">
                    <div class="track-step active">
                        <div class="step-dot"><i class="ri-check-line"></i></div>
                        <div class="step-text">Placed</div>
                    </div>
                    <div class="track-step active">
                        <div class="step-dot"><i class="ri-loader-4-line"></i></div>
                        <div class="step-text">Processing</div>
                    </div>
                    <div class="track-step">
                        <div class="step-dot"><i class="ri-truck-line"></i></div>
                        <div class="step-text">Shipped</div>
                    </div>
                    <div class="track-step">
                        <div class="step-dot"><i class="ri-home-smile-2-line"></i></div>
                        <div class="step-text">Delivered</div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="ordered-items">
                    <h3 style="margin-bottom:20px; font-size:1.1rem; font-weight:700;">Items in your shipment</h3>

                    @foreach($order->items as $item)
                        <div class="item-row">
                            <div class="item-left">
                                <div class="item-thumb">
                                    @php
                                        // Attempt to find product image, fallback if deleted product
                                        $product = \App\Models\Product::find($item->product_id);
                                        $image = $product ? $product->getFirstMediaUrl('product_images', 'thumb') : asset('images/placeholder.jpg');
                                    @endphp
                                    <img src="{{ $image ?: asset('images/placeholder.jpg') }}" alt="{{ $item->product_name }}">
                                </div>
                                <div>
                                    <div class="item-name">{{ $item->product_name }}</div>
                                    <div style="font-size:0.8rem; color:#64748b;">Qty: {{ $item->quantity }}</div>
                                </div>
                            </div>
                            <span class="item-price">{{ number_format($item->subtotal, 2) }} AED</span>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:30px; background:#eff6ff; padding:15px; border-radius:8px; font-size:0.9rem; color:#1e40af; border:1px solid #dbeafe;">
                    <i class="ri-truck-line" style="vertical-align:middle; margin-right:5px;"></i>
                    Estimated Delivery: <b>{{ now()->addDays(2)->format('l, M d') }}</b>
                </div>

                <div class="action-buttons">
                    <a href="#" class="btn-primary">Track My Order</a>
                    <a href="{{ url('/') }}" class="btn-outline">Continue Shopping</a>
                </div>

            </div>
        </div>

    </div>
@endsection
