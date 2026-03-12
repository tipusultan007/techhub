@extends('layouts.frontend')

@section('title', 'Order #' . $order->invoice_no . ' | Tech Hub')

@push('styles')
    <style>
        .page-header { margin: 30px 0; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); }
        .account-layout { display: grid; grid-template-columns: 280px 1fr; gap: 30px; margin-bottom: 60px; }

        .details-card { background: white; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); margin-bottom: 25px; }
        .d-header { background: #f8fafc; padding: 20px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .d-id { font-weight: 700; color: var(--text-main); font-size: 1.1rem; }
        .d-meta { font-size: 0.9rem; color: var(--text-muted); }

        .status-badge { padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-processing { background: #fff7ed; color: #f59e0b; }
        .status-delivered { background: #ecfdf5; color: #10b981; }
        .status-cancelled { background: #fef2f2; color: #ef4444; }
        .status-pending { background: #fefce8; color: #ca8a04; }

        .d-body { padding: 25px; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { text-align: left; padding: 10px; font-size: 0.85rem; color: var(--text-muted); border-bottom: 1px solid #f1f5f9; font-weight: 600; text-transform: uppercase; }
        .items-table td { padding: 15px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .items-table tr:last-child td { border-bottom: none; }

        .item-flex { display: flex; gap: 15px; align-items: center; }
        .item-thumb { width: 60px; height: 60px; border: 1px solid #f1f5f9; border-radius: 6px; padding: 4px; display: flex; align-items: center; justify-content: center; background: #fff; }
        .item-thumb img { max-height: 100%; mix-blend-mode: multiply; }
        .item-info h4 { font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px; }
        .item-info div { font-size: 0.85rem; color: var(--text-muted); }

        .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 10px; }
        .summary-box { background: #f8fafc; padding: 20px; border-radius: 8px; }
        .s-title { font-weight: 700; color: var(--text-main); margin-bottom: 15px; font-size: 0.95rem; text-transform: uppercase; }
        .s-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #64748b; }
        .s-row.total { border-top: 1px solid #e2e8f0; padding-top: 15px; margin-top: 15px; font-weight: 800; font-size: 1.1rem; color: var(--brand-deep-blue); }

        .btn-back { display: inline-flex; align-items: center; gap: 5px; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: 0.2s; }
        .btn-back:hover { color: var(--brand-deep-blue); }

        @media print {
            .top-bar, header, .nav-bar, footer, .account-sidebar, .btn-back, .btn-solid, .page-header button {
                display: none !important;
            }
            .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
            .account-layout { display: block !important; margin: 0 !important; gap: 0 !important; }
            .order-details-content { width: 100% !important; }
            .details-card { box-shadow: none !important; border: 1px solid #ccc !important; margin-bottom: 20px !important; }
            .d-header { background: #fff !important; border-bottom: 2px solid #000 !important; padding: 15px !important; }
            .d-body { padding: 15px !important; }
            .summary-box { border: 1px solid #ccc !important; background: #fff !important; padding: 15px !important; }
            body { background: white !important; font-size: 12pt !important; }
            .page-title { margin-bottom: 20px !important; text-align: center; }
            .items-table th { background: #eee !important; color: #000 !important; border-bottom: 1px solid #000 !important; }
            .items-table td { border-bottom: 1px solid #eee !important; }
        }

        @media (max-width: 900px) { .account-layout { grid-template-columns: 1fr; } .summary-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="page-header">
            <div>
                <a href="{{ route('customer.orders') }}" class="btn-back"><i class="ri-arrow-left-line"></i> Back to Orders</a>
                <h1 class="page-title" style="margin-top: 10px;">Order Details</h1>
            </div>
            @if($order->status !== 'cancelled')
                <div style="display: flex; gap: 10px;">
                    @if($order->status === 'pending' && $order->payment_method === 'rakbank')
                        <a href="{{ route('rakbank.pay', $order) }}" class="btn-solid" style="background: var(--brand-emerald); border-color: var(--brand-emerald);">
                            <i class="ri-secure-payment-line mr-1"></i> Pay Now
                        </a>
                    @endif
                    <a href="{{ route('customer.orders.download', $order) }}" class="btn-solid">
                        <i class="ri-download-line"></i> Download Invoice
                    </a>
                </div>
            @endif
        </div>

        <div class="account-layout">
            @include('frontend.customer.partials.sidebar')

            <div class="order-details-content">
                <div class="details-card">
                    <div class="d-header">
                        <div>
                            <div class="d-id">Order #{{ $order->invoice_no }}</div>
                            <div class="d-meta">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</div>
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

                    <div class="d-body">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $product = \App\Models\Product::where([['id', $item->product_id]])->first();
                                        $img = $product ? $product->getFirstMediaUrl('product_image', 'thumb') : asset('images/placeholder.jpg');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="item-flex">
                                                <div class="item-thumb">
                                                    <img src="{{ $img }}" alt="{{ $item->product_name }}">
                                                </div>
                                                <div class="item-info">
                                                    <h4>{{ $item->product_name }}</h4>
                                                    @if($item->variant_name)
                                                        <div>{{ $item->variant_name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td style="text-align: right; font-weight: 700;">{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="summary-grid">
                    <div class="summary-box">
                        <div class="s-title">Shipping Address</div>
                        <p style="line-height: 1.6; font-size: 0.95rem; color: #475569;">
                            <strong>{{ $order->customer_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_area ? $order->shipping_area . ',' : '' }} {{ $order->shipping_city }}<br>
                            {{ $order->guest_phone ?? ($order->customer ? $order->customer->phone : '') }}<br>
                            {{ $order->guest_email ?? ($order->customer ? $order->customer->email : '') }}
                        </p>
                    </div>

                    <div class="summary-box">
                        <div class="s-title">Order Summary</div>
                        <div class="s-row">
                            <span>Subtotal</span>
                            <span>{{ number_format($order->subtotal, 2) }} AED</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="s-row" style="color: #16a34a;">
                                <span>Discount</span>
                                <span>-{{ number_format($order->discount, 2) }} AED</span>
                            </div>
                        @endif
                        <div class="s-row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="s-row">
                            <span>VAT (5%)</span>
                            <span>{{ number_format($order->vat_amount, 2) }} AED</span>
                        </div>
                        <div class="s-row total">
                            <span>Total</span>
                            <span>{{ number_format($order->total, 2) }} AED</span>
                        </div>
                        <div style="margin-top: 15px; font-size: 0.85rem; color: #64748b;">
                            Payment Method: <span style="font-weight: 600; color: var(--text-main);">{{ strtoupper($order->payment_method) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
