@extends('layouts.frontend')

@section('title', 'Track Order | Tech Hub')

@push('styles')
    <style>
        /* --- TRACK ORDER PAGE SPECIFIC --- */

        /* Search Card */
        .track-search-card {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            margin: 30px 0;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 20px;
            transition: all 0.4s ease;
        }

        @media(min-width: 768px) {
            .track-search-card { flex-direction: row; text-align: left; }
        }

        .search-title {
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--text-main);
            min-width: 200px;
        }

        .track-form {
            display: flex;
            gap: 12px;
            flex: 1;
            width: 100%;
            flex-wrap: wrap;
        }
        .input-track {
            flex: 1;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            outline: none;
            transition: 0.3s;
            min-width: 220px;
            font-size: 0.95rem;
        }
        .input-track:focus {
            border-color: var(--brand-magenta);
            box-shadow: 0 0 0 4px rgba(192, 77, 238, 0.15);
        }

        .btn-track {
            background: var(--brand-deep-blue);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: var(--radius);
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            white-space: nowrap;
        }
        .btn-track:hover {
            background: var(--brand-magenta);
            transform: translateY(-2px);
        }

        /* Main Tracking Layout */
        .tracking-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }

        /* Status Card (Left) */
        .status-card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .order-id-lg {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--brand-deep-blue);
            margin-bottom: 5px;
        }
        .est-delivery {
            font-size: 1.1rem;
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Horizontal Progress Bar */
        .progress-track {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 50px;
        }
        .progress-track::before {
            content: '';
            position: absolute;
            top: 19px;
            left: 0;
            right: 0;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
            border-radius: 2px;
        }
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .step-icon {
            width: 44px;
            height: 44px;
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 1.3rem;
            transition: 0.4s;
        }

        /* Active State */
        .step.active .step-icon {
            border-color: var(--brand-magenta);
            background: var(--brand-magenta);
            color: white;
            box-shadow: 0 0 0 6px rgba(192, 77, 238, 0.25);
            transform: scale(1.1);
        }
        .step.active .step-label {
            color: var(--brand-deep-blue);
            font-weight: 700;
        }
        .step-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Vertical Timeline */
        .timeline {
            position: relative;
            padding-left: 20px;
            border-left: 2px solid #e2e8f0;
            margin-left: 15px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -27px;
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #cbd5e1;
            border: 4px solid white;
            box-shadow: 0 0 0 3px #cbd5e1;
        }
        .timeline-item.latest .timeline-dot {
            background: var(--brand-magenta);
            box-shadow: 0 0 0 6px rgba(192, 77, 238, 0.25);
            transform: scale(1.2);
        }

        .t-date {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .t-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        .t-loc {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* Sidebar */
        .info-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }
        .card-head {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-main);
        }

        .info-row {
            margin-bottom: 15px;
        }
        .info-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 0.8px;
        }
        .info-val {
            font-size: 0.98rem;
            color: var(--text-main);
            line-height: 1.5;
        }

        /* Mini Items */
        .mini-item {
            display: flex;
            gap: 14px;
            align-items: center;
            margin-bottom: 14px;
            border-bottom: 1px solid #f8fafc;
            padding-bottom: 12px;
        }
        .mini-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .mini-thumb {
            width: 56px;
            height: 56px;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            flex-shrink: 0;
            background: #fafafa;
        }
        .mini-thumb img {
            max-height: 100%;
            mix-blend-mode: multiply;
        }
        .mini-text {
            font-size: 0.94rem;
            line-height: 1.4;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Help Box */
        .help-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: var(--radius);
            padding: 24px;
            text-align: center;
        }
        .btn-wa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #25d366;
            color: white;
            padding: 12px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            margin-top: 12px;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-wa:hover {
            background: #1eac56;
            transform: translateY(-2px);
        }

        /* Beautiful Empty State with BIG Search Icon */
        .empty-state {
            text-align: center;
            padding: 120px 20px 80px;
            margin: 40px auto;
            max-width: 700px;
            animation: fadeIn 1s ease-out;
        }

        .empty-icon-wrapper {
            width: 160px;
            height: 160px;
            margin: 0 auto 32px;
            background: linear-gradient(135deg, #f0e5ff 0%, #e0d4ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(192, 77, 238, 0.25);
            animation: pulseGlow 4s ease-in-out infinite;
        }

        .empty-icon {
            font-size: 78px;
            color: var(--brand-magenta);
            filter: drop-shadow(0 4px 10px rgba(192, 77, 238, 0.4));
        }

        .empty-state h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 18px;
            background: linear-gradient(90deg, var(--brand-deep-blue), var(--brand-magenta));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .empty-state p {
            font-size: 1.2rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 28px;
            max-width: 560px;
            margin-left: auto;
            margin-right: auto;
        }

        .empty-hint {
            background: #f8f9ff;
            border: 2px dashed var(--brand-magenta);
            border-radius: var(--radius);
            padding: 20px;
            display: inline-block;
            font-size: 1rem;
            color: var(--brand-deep-blue);
            font-weight: 500;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        @media (max-width: 900px) {
            .tracking-grid { grid-template-columns: 1fr; }
            .empty-state h2 { font-size: 1.9rem; }
        }
    </style>
@endpush

@section('content')
    <div class="container">

        <!-- SEARCH FORM (Always Visible) -->
        <div class="track-search-card">
            <div class="search-title">
                 Track Your Order
            </div>
            <form action="{{ route('track.order') }}" method="POST" class="track-form">
                @csrf
                <input type="text" name="invoice_no" class="input-track" placeholder="Invoice No (e.g. INV-2025-0001)" value="{{ request('invoice_no') }}" required>
                <input type="email" name="email" class="input-track" placeholder="Billing Email Address" value="{{ request('email') ?? (auth()->check() ? auth()->user()->email : '') }}" required>
                <button type="submit" class="btn-track">
                    <i class="ri-search-line"></i> Track Order
                </button>
            </form>
        </div>

        <!-- Error Message -->
        @if(session('error'))
            <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #dc2626; padding: 18px; border-radius: 12px; margin: 20px 0; text-align: center; font-weight: 500;">
                <i class="ri-error-warning-line" style="font-size:1.3rem; margin-right:8px;"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- MAIN CONTENT: Show Results OR Beautiful Empty State -->
        @if(isset($order))
            <!-- ORDER FOUND → Full Tracking View -->
            <div class="tracking-grid">
                <!-- Left: Status + Timeline -->
                <div class="status-col">
                    <div class="status-card">
                        <div class="status-header">
                            <div>
                                <div class="order-id-lg">#{{ $order->invoice_no }}</div>
                                <div style="font-size:0.95rem; color:var(--text-muted);">
                                    Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div class="est-delivery">
                                    @if($order->status === 'completed')
                                        Delivered Successfully
                                    @else
                                        In Progress
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @php
                            $steps = ['pending', 'processing', 'shipped', 'completed'];
                            $currentStepIndex = array_search($order->status, $steps);
                            if($currentStepIndex === false) $currentStepIndex = 0;
                        @endphp

                        <div class="progress-track">
                            <div class="step {{ $currentStepIndex >= 0 ? 'active' : '' }}">
                                <div class="step-icon"><i class="ri-shopping-bag-3-fill"></i></div>
                                <div class="step-label">Order Placed</div>
                            </div>
                            <!-- Step 2 -->
                            <div class="step {{ $currentStepIndex >= 1 ? 'active' : '' }}">
                                <div class="step-icon"><i class="ri-settings-4-fill"></i></div>
                                <div class="step-label">Processing</div>
                            </div>
                            <!-- Step 3 -->
                            <div class="step {{ $currentStepIndex >= 2 ? 'active' : '' }}">
                                <div class="step-icon"><i class="ri-truck-fill"></i></div>
                                <div class="step-label">Shipped</div>
                            </div>
                            <!-- Step 4 -->
                            <div class="step {{ $currentStepIndex >= 3 ? 'active' : '' }}">
                                <div class="step-icon"><i class="ri-home-smile-fill"></i></div>
                                <div class="step-label">Delivered</div>
                            </div>
                        </div>

                        <h3 style="margin: 30px 0 20px; font-size:1.2rem; font-weight:700;">Order Activity</h3>
                        <div class="timeline">
                            <div class="timeline-item {{ $currentStepIndex == 0 ? 'latest' : '' }}">
                                <div class="timeline-dot"></div>
                                <div class="t-date">{{ $order->created_at->format('M d, h:i A') }}</div>
                                <div class="t-title">Order Placed Successfully</div>
                                <div class="t-loc">Online Store</div>
                            </div>

                            @if($currentStepIndex >= 1)
                                <div class="timeline-item {{ $currentStepIndex == 1 ? 'latest' : '' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="t-date">{{ $order->updated_at->format('M d, h:i A') }}</div>
                                    <div class="t-title">Order Processing Started</div>
                                    <div class="t-loc">Warehouse</div>
                                </div>
                            @endif

                            @if($currentStepIndex >= 2)
                                <div class="timeline-item {{ $currentStepIndex == 2 ? 'latest' : '' }}">
                                    <div class="timeline-dot"></div>
                                    <div class="t-date">{{ $order->updated_at->format('M d, h:i A') }}</div>
                                    <div class="t-title">Package Shipped</div>
                                    <div class="t-loc">Dubai Hub → {{ $order->shipping_city }}</div>
                                </div>
                            @endif

                            @if($currentStepIndex >= 3)
                                <div class="timeline-item latest">
                                    <div class="timeline-dot"></div>
                                    <div class="t-date">{{ $order->updated_at->format('M d, h:i A') }}</div>
                                    <div class="t-title">Package Delivered</div>
                                    <div class="t-loc">{{ $order->shipping_city }}, UAE</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Sidebar Info -->
                <div class="sidebar-col">
                    <!-- Shipment Details -->
                    <div class="info-card">
                        <div class="card-head">Shipment Details</div>
                        <div class="info-row">
                            <div class="info-label">Payment Method</div>
                            <div class="info-val">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Total Amount</div>
                            <div class="info-val" style="font-weight: 700; font-size:1.1rem; color:var(--brand-deep-blue);">
                                {{ number_format($order->total, 2) }} AED
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Delivery Address</div>
                            <div class="info-val">
                                <strong>{{ $order->customer_name }}</strong><br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, UAE<br>
                                {{ $order->guest_phone }}
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="info-card">
                        <div class="card-head">Items ({{ $order->items->count() }})</div>
                        @foreach($order->items as $item)
                            @php
                                $product = \App\Models\Product::where([['id', $item->product_id]])->first();
                                $img = ($product) ? $product->getFirstMediaUrl('product_image', 'thumb') : asset('images/placeholder.jpg');
                            @endphp
                            <div class="mini-item">
                                <div class="mini-thumb">
                                    <img src="{{ $img }}" alt="{{ $item->product_name }}">
                                </div>
                                <div class="mini-text">
                                    <span style="font-size: 12px;">{{ $item->product_name }}</span> <br>
                                    <small style="color:var(--text-muted); font-size: 11px;">Qty: {{ $item->quantity }} × {{ number_format($item->price, 2) }} AED</small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Help -->
                    <div class="help-box">
                        <div style="font-weight:700; font-size:1.1rem; margin-bottom:8px;">Need Help?</div>
                        <p style="font-size:0.92rem; color:#475569; margin-bottom:16px;">
                            Our support team is available 24/7 via WhatsApp
                        </p>
                        <a href="https://wa.me/97140000000" class="btn-wa" target="_blank">
                            WhatsApp Chat Now
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- NO ORDER FOUND → Beautiful Empty State -->
            @if(!session('error'))
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="ri-search-eye-line"></i>
                    </div>
                    <h2>Ready to Track Your Order?</h2>
                    <p>Enter your invoice number and the email used during checkout to see real-time updates on your shipment.</p>
                    <div class="empty-hint">
                        Example: <strong>INV-2025-0123</strong> and hello@example.com
                    </div>
                </div>
            @endif
        @endif

    </div>
@endsection
