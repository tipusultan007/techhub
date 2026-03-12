@extends('layouts.frontend')

@section('title', 'My Orders | Tech Hub')

@push('styles')
    <style>
        /* --- MY ORDERS PAGE SPECIFIC CSS --- */

        .page-header {
            margin: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .account-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }

        /* Filters Bar */
        .orders-filters {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: var(--shadow);
        }

        .filter-tabs {
            display: flex;
            gap: 5px;
        }

        .filter-tab {
           padding: 5px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
        }

        .filter-tab:hover {
            background: #f1f5f9;
            color: var(--text-main);
        }

        .filter-tab.active {
            background: var(--brand-deep-blue);
            color: white;
            font-weight: 600;
        }

        .filter-search {
            position: relative;
            width: 250px;
        }

        .filter-tabs {
            display: flex;
            gap: 5px;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
        }
        .filter-tabs::-webkit-scrollbar { display: none; }




        .filter-search input {
            width: 100%;
            padding: 8px 15px 8px 35px;
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: 0.85rem;
            outline: none;
        }

        .filter-search i {
            position: absolute;
            left: 12px;
            top: 9px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Order Cards */
        .order-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: 0.2s;
            box-shadow: var(--shadow);
        }

        .order-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-color: var(--brand-emerald);
        }

        /* Card Header */
        .o-header {
            background: #f8fafc;
            padding: 15px 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .o-id {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1rem;
        }

        .o-date {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-processing {
            background: #fff7ed;
            color: #f59e0b;
        }

        .status-delivered {
            background: #ecfdf5;
            color: #10b981;
        }

        .status-cancelled {
            background: #fef2f2;
            color: #ef4444;
        }

        .status-pending {
            background: #fefce8;
            color: #ca8a04;
        }

        /* Card Body */
        .o-body {
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .o-items {
            display: flex;
            gap: 15px;
        }

        .o-thumb {
            width: 70px;
            height: 70px;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .o-thumb img {
            max-height: 100%;
            mix-blend-mode: multiply;
        }

        .o-more-count {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 8px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            top: 0;
            left: 0;
        }

        .o-summary {
            text-align: right;
            min-width: 150px;
        }

        .o-total-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .o-total-val {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--brand-deep-blue);
            margin-top: 5px;
        }

        /* Card Footer / Actions */
        .o-footer {
            padding: 15px 25px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-outline {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            background: white;
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-outline:hover {
            border-color: var(--brand-deep-blue);
            color: var(--brand-deep-blue);
        }

        .btn-solid {
            padding: 8px 16px;
            border: 1px solid var(--brand-deep-blue);
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            background: var(--brand-deep-blue);
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-solid:hover {
            background: var(--brand-emerald);
            border-color: var(--brand-emerald);
        }

        @media (max-width: 900px) {
            .account-layout {
                grid-template-columns: 1fr;
            }

            .orders-filters {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-search {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .o-body {
                flex-direction: column;
                align-items: flex-start;
            }

            .o-summary {
                text-align: left;
                margin-top: 10px;
            }

            .o-footer {
                justify-content: stretch;
            }

            .o-footer a {
                flex: 1;
                text-align: center;
            }
        }
        /* RESPONSIVE ORDER CARD */
        @media (max-width: 768px) {
            /* Layout */
            .account-layout { grid-template-columns: 1fr; }

            /* Filters */
            .orders-filters { flex-direction: column; align-items: stretch; padding: 15px; }
            .filter-search { width: 100%; }

            /* Order Card Body Stacking */
            .o-body {
                flex-direction: column; align-items: flex-start; gap: 15px;
            }

            .o-items {
                width: 100%; overflow-x: auto; padding-bottom: 5px;
            }

            .o-summary {
                width: 100%; text-align: left;
                border-top: 1px solid #f1f5f9; padding-top: 15px;
                display: flex; justify-content: space-between; align-items: center;
            }
            .o-total-val { margin-top: 0; font-size: 1.1rem; }

            /* Order Card Footer Buttons */
            .o-footer {
                flex-direction: column;
            }
            .o-footer a, .o-footer button {
                width: 100%; text-align: center;
            }
        }

    </style>
@endpush

@section('content')
    <div class="container">

        <div class="page-header">
            <h1 class="page-title">My Orders</h1>
        </div>

        <div class="account-layout">

            <!-- SIDEBAR PARTIAL -->
            @include('frontend.customer.partials.sidebar')

            <div class="orders-content">

                <!-- Filters -->
                <div class="orders-filters">
                    <div class="filter-tabs">
                        <a href="{{ route('customer.orders') }}"
                           class="filter-tab {{ !request('status') ? 'active' : '' }}">All Orders</a>
                        <a href="{{ route('customer.orders', ['status' => 'pending']) }}"
                           class="filter-tab {{ request('status') == 'pending' ? 'active' : '' }}">Active</a>
                        <a href="{{ route('customer.orders', ['status' => 'completed']) }}"
                           class="filter-tab {{ request('status') == 'completed' ? 'active' : '' }}">Delivered</a>
                        <a href="{{ route('customer.orders', ['status' => 'cancelled']) }}"
                           class="filter-tab {{ request('status') == 'cancelled' ? 'active' : '' }}">Cancelled</a>
                    </div>
                    <div class="filter-search">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search by Order ID or Product">
                    </div>
                </div>

                <!-- Orders List -->
                <div class="order-list">

                    @forelse($orders as $order)
                        <div class="order-card">
                            <div class="o-header">
                                <div>
                                    <div class="o-id">Order #{{ $order->invoice_no }}</div>
                                    <div class="o-date">Placed on {{ $order->created_at->format('M d, Y') }}</div>
                                </div>

                                @php
                                    $statusClass = match($order->status) {
                                        'completed', 'delivered' => 'status-delivered',
                                        'cancelled', 'returned' => 'status-cancelled',
                                        'processing' => 'status-processing',
                                        default => 'status-pending'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                            </div>

                            <div class="o-body">
                                <div class="o-items">
                                    <!-- Loop through first 3 items -->
                                    @foreach($order->items->take(3) as $item)
                                        @php
                                            $product = \App\Models\Product::find($item->product_id);
                                            $img = $product ? $product->getFirstMediaUrl('product_image') : asset('frontend/assets/images/placeholder.jpg');
                                        @endphp
                                        <div class="o-thumb" title="{{ $item->product_name }}">
                                            <img src="{{ $img }}" alt="{{ $item->product_name }}">
                                        </div>
                                    @endforeach

                                    <!-- Show Count if more than 3 items -->
                                    @if($order->items->count() > 3)
                                        <div class="o-thumb">
                                            <div class="o-more-count">+{{ $order->items->count() - 3 }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="o-summary">
                                    <div class="o-total-label">Total Amount</div>
                                    <div class="o-total-val">{{ number_format($order->total, 2) }} AED</div>
                                </div>
                            </div>

                            <div class="o-footer">
                                <a href="{{ route('customer.orders.show', $order) }}" class="btn-outline">View
                                    Details</a>
                                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                    <a href="{{ route('track.order', ['invoice_no' => $order->invoice_no, 'email' => $order->guest_email ?? ($order->customer ? $order->customer->email : '')]) }}" class="btn-outline">Track Order</a>
                                @endif

                                @if($order->status === 'pending' && $order->payment_method === 'rakbank')
                                    <a href="{{ route('rakbank.pay', $order) }}" class="btn-solid" style="background: var(--brand-emerald); border-color: var(--brand-emerald);">
                                        <i class="ri-secure-payment-line mr-1"></i> Pay Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            style="text-align: center; padding: 60px; background: white; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <i class="ri-shopping-bag-3-line"
                               style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                            <h3 style="font-weight: 700; color: #64748b;">No orders found</h3>
                            <a href="{{ route('shop.index') }}" class="btn-solid"
                               style="margin-top: 15px; display: inline-block;">Start Shopping</a>
                        </div>
                    @endforelse

                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection
