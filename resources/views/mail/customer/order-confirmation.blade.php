@extends('mail.customer.layout')

@section('content')
<h1>Thank you for your order!</h1>
<p>Hello {{ $order->customer_name }},</p>
<p>Your order <strong>#{{ $order->invoice_no }}</strong> has been successfully placed. We're getting it ready for you right now.</p>

<div class="order-meta">
    <table>
        <tr>
            <td class="label">Order Number</td>
            <td class="font-bold">#{{ $order->invoice_no }}</td>
        </tr>
        <tr>
            <td class="label">Order Date</td>
            <td>{{ $order->created_at->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Total Amount</td>
            <td class="font-bold">{{ number_format($order->total, 2) }} AED</td>
        </tr>
        <tr>
            <td class="label">Payment Status</td>
            <td><span class="status-badge">{{ strtoupper($order->status) }}</span></td>
        </tr>
    </table>
</div>

<div class="divider"></div>

<h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 20px;">Order Summary</h3>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background-color: #f8fafc;">
            <th style="padding: 12px 15px; text-align: left; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;">Item Details</th>
            <th style="padding: 12px 15px; text-align: center; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; width: 60px;">Qty</th>
            <th style="padding: 12px 15px; text-align: right; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; width: 100px;">Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: top;">
                <div style="font-weight: 500; color: #0f172a; font-size: 13px; line-height: 1.4;">{{ $item->product_name }}</div>
            </td>
            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: center; font-size: 13px; vertical-align: top; color: #334155;">
                {{ $item->quantity }}
            </td>
            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px; vertical-align: top; color: #0f172a; font-weight: 500;">
                {{ number_format($item->subtotal, 2) }} <span style="font-size: 10px; color: #64748b;">AED</span>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="padding: 15px; text-align: right; font-size: 14px; font-weight: 700; color: #64748b;">Grand Total</td>
            <td style="padding: 15px; text-align: right; font-weight: 800; color: #2dae9a; font-size: 18px; background-color: #f8fafc;">
                {{ number_format($order->total, 2) }} <span style="font-size: 12px;">AED</span>
            </td>
        </tr>
    </tfoot>
</table>

<div style="text-align: center;">
    <a href="{{ route('track.order') }}?invoice_no={{ $order->invoice_no }}&email={{ $order->guest_email ?? ($order->customer ? $order->customer->email : '') }}" class="button">Track Your Order</a>
</div>

<p style="margin-top: 30px;">We'll send you another email when your package ships!</p>
@endsection
