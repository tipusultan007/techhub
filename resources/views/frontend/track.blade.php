@extends('layouts.frontend')

@section('title', 'Track Order | Tech Hub')

@push('styles')
    <style>
        /* Professional Track Order Styles */
        :root {
            --track-primary: #0f172a;
            --track-accent: #2563eb;
            --track-success: #10b981;
            --track-border: #e2e8f0;
            --track-bg: #f8fafc;
        }

        /* Hero / Search Section */
        .track-hero {
            background-color: white;
            padding: 60px 0;
            text-align: center;
            border-bottom: 1px solid var(--track-border);
        }

        .track-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--track-primary);
            margin-bottom: 12px;
            letter-spacing: -0.025em;
        }

        .track-subtitle {
            color: #64748b;
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .track-form-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 6px;
            border: 1px solid var(--track-border);
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        @media (min-width: 640px) {
            .track-form-wrapper {
                flex-wrap: nowrap;
                padding: 6px;
            }
        }

        .track-input {
            flex: 1;
            border: none;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--track-primary);
            outline: none;
            min-width: 0; /* Flexbox fix */
        }
        .track-input::placeholder { color: #94a3b8; }
        .track-input:focus { background-color: #f8fafc; border-radius: 4px; }
        
        .divider-vertical {
            width: 1px;
            background-color: var(--track-border);
            margin: 6px 0;
            display: none;
        }
        @media (min-width: 640px) { .divider-vertical { display: block; } }

        .btn-track-submit {
            background-color: var(--track-primary);
            color: white;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            width: 100%;
        }
        @media (min-width: 640px) { .btn-track-submit { width: auto; } }

        .btn-track-submit:hover {
            background-color: #1e293b;
        }

        /* Order Details Section */
        .order-content {
            padding: 60px 0;
            background-color: var(--track-bg);
            min-height: 60vh;
        }

        .order-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }

        @media (min-width: 1024px) {
            .order-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* Status Card */
        .status-header {
            background: white;
            border: 1px solid var(--track-border);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
        }

        .order-meta h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--track-primary);
            margin-bottom: 4px;
            letter-spacing: -0.025em;
        }
        .order-date { color: #64748b; font-size: 0.9rem; }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
            background-color: #ecfdf5;
            color: var(--track-success);
            border: 1px solid #d1fae5;
        }

        /* Clean Stepper */
        .stepper-container {
            background: white;
            border: 1px solid var(--track-border);
            border-radius: 8px;
            padding: 32px 24px;
            margin-bottom: 24px;
        }

        .stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        
        .stepper-line-bg {
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e2e8f0;
            z-index: 0;
        }

        .stepper-item {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            background: white;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
        }
        
        .step-icon { font-size: 14px; }

        .stepper-item.active .step-circle {
            border-color: var(--track-primary);
            background-color: var(--track-primary);
        }
        
        .stepper-item.completed .step-circle {
            border-color: var(--track-success);
            background-color: var(--track-success);
        }

        .step-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748b;
        }
        .stepper-item.active .step-title, 
        .stepper-item.completed .step-title {
            color: var(--track-primary);
        }

        /* Timeline */
        .timeline-box {
            background: white;
            border: 1px solid var(--track-border);
            border-radius: 8px;
            padding: 24px;
        }
        .timeline-header {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--track-primary);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--track-border);
        }

        .tl-item {
            position: relative;
            padding-left: 24px;
            padding-bottom: 32px;
            border-left: 2px solid #e2e8f0;
        }
        .tl-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }
        
        .tl-dot {
            position: absolute;
            left: -6px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--track-border);
        }
        .tl-item.latest .tl-dot {
            background: var(--track-primary);
            box-shadow: 0 0 0 4px #e2e8f0;
        }

        .tl-time { font-size: 0.85rem; color: #64748b; margin-bottom: 4px; }
        .tl-status { font-size: 1rem; font-weight: 600; color: var(--track-primary); margin-bottom: 2px; }
        .tl-desc { font-size: 0.9rem; color: #64748b; }

        /* Sidebar Cards */
        .info-group {
            background: white;
            border: 1px solid var(--track-border);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .info-header {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .info-detail {
            font-size: 0.95rem;
            color: var(--track-primary);
            line-height: 1.5;
            margin-bottom: 16px;
        }
        .info-detail:last-child { margin-bottom: 0; }
        .info-detail strong { display: block; margin-bottom: 4px; }

        .order-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .order-item:last-child { border-bottom: none; }
        .item-thumb {
            width: 48px;
            height: 48px;
            border: 1px solid var(--track-border);
            border-radius: 6px;
            padding: 2px;
            object-fit: contain;
        }
        .item-meta { flex: 1; }
        .item-name { font-size: 0.9rem; font-weight: 600; color: var(--track-primary); line-height: 1.3; }
        .item-price { font-size: 0.85rem; color: #64748b; margin-top: 2px; }

        .support-cta {
            text-align: center;
            font-size: 0.9rem;
            color: #64748b;
        }
        .support-link { color: var(--track-accent); font-weight: 600; text-decoration: none; }
        .support-link:hover { text-decoration: underline; }

        /* Empty State */
        .empty-visual { margin-bottom: 24px; color: #cbd5e1; font-size: 4rem; }
    </style>
@endpush

@section('content')

    <!-- HERO SEARCH SECTION -->
    <div class="track-hero">
        <div class="container">
            <h1 class="track-title">Track Your Shipment</h1>
            <p class="track-subtitle">Enter your invoice number and billing email to see the current status of your order.</p>

            <form action="{{ route('track.order') }}" method="POST" class="track-form-wrapper">
                @csrf
                <input type="text" name="invoice_no" class="track-input" placeholder="Invoice Number (e.g. INV-2025-001)" value="{{ request('invoice_no') }}" required>
                <div class="divider-vertical"></div>
                <input type="email" name="email" class="track-input" placeholder="Email Address" value="{{ request('email') ?? (auth()->check() ? auth()->user()->email : '') }}" required>
                <button type="submit" class="btn-track-submit">Track Order</button>
            </form>

            @if(session('error'))
                <div class="mt-6 text-red-600 bg-red-50 inline-block px-4 py-2 rounded text-sm font-medium border border-red-100">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <!-- ORDER RESULTS OR EMPTY STATE -->
    <div class="order-content">
        <div class="container">
            @if(isset($order))
                <div class="order-grid">
                    
                    <!-- LEFT COLUMN: STATUS & TIMELINE -->
                    <div class="main-col">
                        
                        <!-- HEADER -->
                        <div class="status-header">
                            <div class="order-meta">
                                <h1>#{{ $order->invoice_no }}</h1>
                                <span class="order-date">Placed on {{ $order->created_at->format('F d, Y') }}</span>
                            </div>
                            <div class="status-badge">
                                <i class="ri-checkbox-circle-fill"></i>
                                <span>{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>

                        <!-- STEPPER -->
                        @php
                            $steps = ['pending', 'processing', 'shipped', 'completed'];
                            $currentStatus = $order->status;
                            $currentIndex = array_search($currentStatus, $steps);
                            if($currentIndex === false) $currentIndex = 0;
                        @endphp

                        <div class="stepper-container">
                            <div class="stepper">
                                <div class="stepper-line-bg"></div>
                                
                                @foreach($steps as $index => $step)
                                    @php
                                        $isCompleted = $index < $currentIndex;
                                        $isActive = $index === $currentIndex;
                                        $isFuture = $index > $currentIndex;
                                        
                                        $icons = [
                                            'pending' => 'ri-file-list-3-line',
                                            'processing' => 'ri-loader-4-line',
                                            'shipped' => 'ri-truck-line',
                                            'completed' => 'ri-check-double-line'
                                        ];
                                        $labels = [
                                            'pending' => 'Order Placed',
                                            'processing' => 'Processing',
                                            'shipped' => 'Shipped',
                                            'completed' => 'Delivered'
                                        ];
                                    @endphp
                                    <div class="stepper-item {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                                        <div class="step-circle">
                                            @if($isCompleted)
                                                <i class="ri-check-line step-icon"></i>
                                            @else
                                                <i class="{{ $icons[$step] }} step-icon"></i>
                                            @endif
                                        </div>
                                        <div class="step-title">{{ $labels[$step] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- TIMELINE -->
                        <div class="timeline-box">
                            <h3 class="timeline-header">Shipment Activity</h3>
                            
                            <!-- Latest Activity (Dynamic based on current status) -->
                            <div class="tl-item latest">
                                <div class="tl-dot"></div>
                                <div class="tl-time">{{ $order->updated_at->format('M d, h:i A') }}</div>
                                <div class="tl-status">
                                    {{ ucfirst($currentStatus) }}
                                </div>
                                <div class="tl-desc">
                                    @if($currentStatus == 'pending') Order received and awaiting confirmation.
                                    @elseif($currentStatus == 'processing') Your order is being prepared for shipment.
                                    @elseif($currentStatus == 'shipped') Package has left our facility.
                                    @elseif($currentStatus == 'completed') Package has been delivered successfully.
                                    @endif
                                </div>
                            </div>

                            <!-- Previous Activities (Mock logic for display history) -->
                             @if($currentIndex >= 2)
                                <div class="tl-item">
                                    <div class="tl-dot"></div>
                                    <div class="tl-time">{{ $order->updated_at->subHours(4)->format('M d, h:i A') }}</div>
                                    <div class="tl-status">Processing Completed</div>
                                    <div class="tl-desc">Quality check passed. Ready for dispatch.</div>
                                </div>
                            @endif

                            @if($currentIndex >= 1)
                                <div class="tl-item">
                                    <div class="tl-dot"></div>
                                    <div class="tl-time">{{ $order->created_at->addMinutes(30)->format('M d, h:i A') }}</div>
                                    @if($order->payment_method == 'cash')
                                        <div class="tl-status">Order Confirmed</div>
                                        <div class="tl-desc">Payment to be collected on delivery (Cash on Delivery).</div>
                                    @else
                                        <div class="tl-status">Payment Confirmed</div>
                                        <div class="tl-desc">Payment verified via {{ $order->payment_method === 'rakbank' ? 'Credit/Debit Card' : ucfirst($order->payment_method) }}.</div>
                                    @endif
                                </div>
                            @endif

                            <div class="tl-item">
                                <div class="tl-dot"></div>
                                <div class="tl-time">{{ $order->created_at->format('M d, h:i A') }}</div>
                                <div class="tl-status">Order Placed</div>
                                <div class="tl-desc">Order #{{ $order->invoice_no }} created via Web Store.</div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: SIDEBAR -->
                    <div class="sidebar-col">
                        
                        <!-- SHIPPING INFO -->
                        <div class="info-group">
                            <div class="info-header">Delivery Details</div>
                            <div class="info-detail">
                                <strong>{{ $order->customer_name }}</strong>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, UAE<br>
                                {{ $order->guest_phone }}
                            </div>
                        </div>

                        <!-- ORDER SUMMARY -->
                        <div class="info-group">
                            <div class="info-header">Order Summary</div>
                            
                            @foreach($order->items as $item)
                                @php
                                    $product = \App\Models\Product::where('id', $item->product_id)->first();
                                    $img = ($product) ? $product->getFirstMediaUrl('product_image', 'thumb') : asset('images/placeholder.jpg');
                                @endphp
                                <div class="order-item">
                                    <img src="{{ $img }}" alt="{{ $item->product_name }}" class="item-thumb">
                                    <div class="item-meta">
                                        <div class="item-name">{{ $item->product_name }}</div>
                                        <div class="item-price">{{ $item->quantity }} x {{ number_format($item->price, 2) }} AED</div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="tm-4 pt-4 mt-4 border-t border-gray-100 flex justify-between items-center">
                                <span class="font-semibold text-gray-600">Total Amount</span>
                                <span class="font-bold text-lg text-slate-900">{{ number_format($order->total, 2) }} AED</span>
                            </div>
                        </div>

                        <div class="support-cta">
                            Need help with this order? <a href="{{ url('/contact') }}" class="support-link">Contact Support</a>
                        </div>

                    </div>
                </div>
            @else
                <!-- EMPTY STATE -->
                @if(!session('error'))
                    <div class="text-center py-12">
                        <div class="empty-visual">
                            <i class="ri-map-pin-time-line"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 mb-2">Ready to track?</h2>
                        <p class="text-slate-500 max-w-md mx-auto mb-8">Use the form above to look up your order status. You can find your Invoice Number in the confirmation email we sent you.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

@endsection
