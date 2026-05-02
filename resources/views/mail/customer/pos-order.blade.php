@extends('mail.customer.layout')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <h1 style="margin-bottom: 5px;">e-Receipt</h1>
    <p style="color: #64748b; font-size: 14px;">Thank you for your purchase at our store.</p>
</div>

<div class="order-meta" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 25px;">
    <table style="width: 100%;">
        <tr>
            <td class="label" style="padding-bottom: 10px;">Invoice #</td>
            <td class="font-bold" style="padding-bottom: 10px;">{{ $order->invoice_no }}</td>
        </tr>
        <tr>
            <td class="label" style="padding-bottom: 10px;">Date & Time</td>
            <td style="padding-bottom: 10px;">{{ $order->created_at->format('d M, Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="label" style="padding-bottom: 10px;">Store Location</td>
            <td style="padding-bottom: 10px;">{{ settings('site_name') }}<br><span style="font-size: 11px; color: #64748b;">{{ settings('contact_address') }}</span></td>
        </tr>
        <tr>
            <td class="label" style="padding-bottom: 10px;">Served By</td>
            <td style="padding-bottom: 10px;">{{ $order->user->name ?? 'Sales Associate' }}</td>
        </tr>
    </table>
</div>

<div class="divider"></div>

<h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px;">Items Purchased</h3>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr style="border-bottom: 2px solid #e2e8f0;">
            <th style="padding: 10px 0; text-align: left; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase;">Description</th>
            <th style="padding: 10px 0; text-align: center; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; width: 60px;">Qty</th>
            <th style="padding: 10px 0; text-align: right; font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; width: 100px;">Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 15px 0;">
                <div style="font-weight: 600; color: #0f172a; font-size: 14px;">{{ $item->product_name }}</div>
                @if($item->serial_numbers)
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">S/N: {{ $item->serial_numbers }}</div>
                @endif
                @if($item->warranty_end_date)
                    <div style="font-size: 11px; color: #059669; font-weight: 600;">Warranty until: {{ \Carbon\Carbon::parse($item->warranty_end_date)->format('d M, Y') }}</div>
                @endif
            </td>
            <td style="padding: 15px 0; text-align: center; font-size: 14px; color: #334155;">
                {{ $item->quantity }}
            </td>
            <td style="padding: 15px 0; text-align: right; font-size: 14px; color: #0f172a; font-weight: 600;">
                {{ number_format($item->subtotal + $item->tax_amount, 2) }} <span style="font-size: 10px; color: #64748b;">AED</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="background-color: #f8fafc; border-radius: 12px; padding: 20px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px 0; color: #64748b; font-size: 14px;">Subtotal (excl. VAT)</td>
            <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #0f172a; font-size: 14px;">{{ number_format($order->subtotal, 2) }} AED</td>
        </tr>
        <tr>
            <td style="padding: 5px 0; color: #64748b; font-size: 14px;">VAT (5%)</td>
            <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #0f172a; font-size: 14px;">{{ number_format($order->vat_amount, 2) }} AED</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td style="padding: 5px 0; color: #dc2626; font-size: 14px;">Discount</td>
            <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #dc2626; font-size: 14px;">-{{ number_format($order->discount, 2) }} AED</td>
        </tr>
        @endif
        <tr style="border-top: 1px solid #e2e8f0;">
            <td style="padding: 15px 0; font-size: 18px; font-weight: 800; color: #0f172a;">Total Amount</td>
            <td style="padding: 15px 0; text-align: right; font-weight: 800; color: #2dae9a; font-size: 22px;">
                {{ number_format($order->total, 2) }} <span style="font-size: 12px;">AED</span>
            </td>
        </tr>
    </table>
</div>

<div style="margin-top: 20px; padding: 15px; border: 1px solid #e2e8f0; border-radius: 12px;">
    <table style="width: 100%;">
        <tr>
            <td style="font-size: 13px; color: #64748b;">Payment Method: <span style="font-weight: 700; color: #0f172a;">{{ strtoupper($order->payment_method) }}</span></td>
            <td style="text-align: right; font-size: 13px; color: #64748b;">Paid Amount: <span style="font-weight: 700; color: #0f172a;">{{ number_format($order->paid_amount, 2) }} AED</span></td>
        </tr>
        @if($order->due_amount > 0)
        <tr>
            <td colspan="2" style="text-align: right; font-size: 13px; color: #dc2626; padding-top: 5px;">Balance Due: <span style="font-weight: 800;">{{ number_format($order->due_amount, 2) }} AED</span></td>
        </tr>
        @endif
    </table>
</div>

<div style="text-align: center; margin-top: 30px;">
    <p style="font-size: 12px; color: #94a3b8; margin-bottom: 5px;">This is a computer-generated digital receipt.</p>
    <p style="font-size: 12px; color: #94a3b8;">For any queries, please visit our store or contact us.</p>
</div>
@endsection
