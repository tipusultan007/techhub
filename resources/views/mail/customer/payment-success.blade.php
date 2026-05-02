@extends('mail.customer.layout')

@section('content')
<h1>Payment Successful!</h1>
<p>Hello {{ $order->customer_name }},</p>
<p>We are pleased to inform you that your payment for order <strong>#{{ $order->invoice_no }}</strong> was successful. Your order is now being processed.</p>

<div class="order-meta">
    <table>
        <tr>
            <td class="label">Order Number</td>
            <td class="font-bold">#{{ $order->invoice_no }}</td>
        </tr>
        <tr>
            <td class="label">Transaction ID</td>
            <td class="font-bold">{{ $order->transaction_id }}</td>
        </tr>
        <tr>
            <td class="label">Total Paid</td>
            <td class="font-bold">{{ number_format($order->total, 2) }} AED</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td>{{ ucfirst($order->payment_method) }}</td>
        </tr>
    </table>
</div>

<div class="divider"></div>

<div style="text-align: center;">
    <a href="{{ route('track.order') }}?invoice_no={{ $order->invoice_no }}&email={{ $order->guest_email ?? ($order->customer ? $order->customer->email : '') }}" class="button">View Order Details</a>
</div>

<p style="margin-top: 30px;">Thank you for shopping with {{ settings('site_name') }}!</p>
@endsection
