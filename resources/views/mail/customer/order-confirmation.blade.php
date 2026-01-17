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

<h3>Order Summary</h3>
<table style="width: 100%; margin-bottom: 20px;">
    @foreach($order->items as $item)
    <tr>
        <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
            <div style="font-weight: 700; color: #0f172a;">{{ $item->product_name }}</div>
            <div style="font-size: 12px; color: #64748b;">Qty: {{ $item->quantity }}</div>
        </td>
        <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; vertical-align: middle;">
            {{ number_format($item->subtotal, 2) }} AED
        </td>
    </tr>
    @endforeach
    <tr>
        <td style="padding: 10px 0; font-weight: 700; color: #0f172a;">Total</td>
        <td style="padding: 10px 0; text-align: right; font-weight: 800; color: #2563eb; font-size: 18px;">
            {{ number_format($order->total, 2) }} AED
        </td>
    </tr>
</table>

<div style="text-align: center;">
    <a href="{{ route('track.order') }}?invoice_no={{ $order->invoice_no }}&email={{ $order->guest_email ?? ($order->customer ? $order->customer->email : '') }}" class="button">Track Your Order</a>
</div>

<p style="margin-top: 30px;">We'll send you another email when your package ships!</p>
@endsection
