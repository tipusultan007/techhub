@extends('mail.customer.layout')

@section('content')
<h1>Great news! Your order status has changed.</h1>
<p>Hello {{ $order->customer_name }},</p>
<p>The status of your order <strong>#{{ $order->invoice_no }}</strong> has been updated to <strong>{{ strtoupper($order->status) }}</strong>.</p>

<div class="order-meta">
    <div style="text-align: center; margin-bottom: 20px;">
        <span class="status-badge" style="padding: 10px 30px; font-size: 16px; background: #eff6ff; color: #2563eb;">
            {{ ucfirst($order->status) }}
        </span>
    </div>
    
    @if($comment)
    <div style="background: #ffffff; border-left: 4px solid #3b82f6; padding: 15px; margin-top: 10px; font-style: italic; font-size: 14px; color: #475569;">
        "{{ $comment }}"
    </div>
    @endif
</div>

<p>You can check the latest details of your order and track its progress using the link below.</p>

<div style="text-align: center;">
    <a href="{{ route('track.order') }}?invoice_no={{ $order->invoice_no }}&email={{ $order->guest_email ?? ($order->customer ? $order->customer->email : '') }}" class="button">View Order Details</a>
</div>

<div class="divider"></div>

<p>If you have any questions regarding this update, please don't hesitate to contact our support team.</p>

<p>Warm Regards,<br><strong>The ElectroMart Team</strong></p>
@endsection
