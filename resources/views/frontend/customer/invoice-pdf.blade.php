<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->invoice_no }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 14px; margin: 0; padding: 0; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; }
        
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 5px; vertical-align: top; }
        
        .header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; font-size: 28px; color: #0f172a; }
        .company-info p { margin: 2px 0; color: #64748b; font-size: 12px; }
        
        .invoice-details { margin-bottom: 40px; }
        .invoice-details td { width: 50%; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 12px; color: #94a3b8; margin-bottom: 10px; }
        
        .items-table { margin-bottom: 30px; }
        .items-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; padding: 12px 10px; text-align: left; font-size: 12px; text-transform: uppercase; color: #64748b; }
        .items-table td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; }
        
        .summary-table { width: 40%; float: right; }
        .summary-table td { padding: 8px 10px; }
        .total-row { font-weight: bold; font-size: 18px; color: #0f172a; border-top: 2px solid #e2e8f0; }
        
        .footer { margin-top: 100px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .clear { clear: both; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-success { background: #ecfdf5; color: #10b981; }
        .badge-pending { background: #fefce8; color: #ca8a04; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td class="company-info">
                        @if(settings('site_logo'))
                            <img src="{{ public_path(parse_url(settings('site_logo'), PHP_URL_PATH)) }}" alt="{{ settings('site_name') }}" style="max-height: 50px; margin-bottom: 5px;">
                        @else
                            <h1>{{ settings('shop_name', 'TECH HUB') }}</h1>
                        @endif
                        <p>{!! settings('shop_address', '') !!}</p>
                        <span>Phone: {{ settings('shop_phone', settings('contact_phone', '+971 00 000 0000')) }}</span><br>
                        <span>Email: {{ settings('contact_email', 'sales@techhubrak.ae') }}</span><br>
                        <p>TRN: {{ settings('shop_trn', '100200300400500') }}</p>
                    </td>
                    <td style="text-align: right;">
                        <h2 style="margin: 0; color: #0f172a;">INVOICE</h2>
                        <p style="margin: 5px 0;">#{{ $order->invoice_no }}</p>
                        <p style="margin: 2px 0; font-size: 12px; color: #64748b;">Date: {{ $order->created_at->format('d M Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <td>
                        <div class="section-title">Bill To:</div>
                        <p style="margin: 0; font-weight: bold; font-size: 16px;">{{ $order->customer_name }}</p>
                        <p style="margin: 5px 0;">{{ $order->shipping_address }}</p>
                        <p style="margin: 2px 0;">{{ $order->shipping_city }}</p>
                        <p style="margin: 2px 0;">{{ $order->guest_phone ?? ($order->customer ? $order->customer->phone : '') }}</p>
                    </td>
                    <td style="text-align: right;">
                        <div class="section-title">Payment Information:</div>
                        <p style="margin: 0;">Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                        <p style="margin: 5px 0;">Status: <span class="badge {{ $order->status == 'completed' ? 'badge-success' : 'badge-pending' }}">{{ strtoupper($order->status) }}</span></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="items-table">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: center; font-size: 11px;">Qty</th>
                        <th style="text-align: right; font-size: 11px;">Unit Price</th>
                        <th style="text-align: right; font-size: 11px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: bold; font-size: 11px;">{{ $item->product_name }}</div>
                            @if($item->serial_numbers)
                                <div style="font-size: 10px; color: #64748b;">S/N: {{ $item->serial_numbers }}</div>
                            @endif
                        </td>
                        <td style="text-align: center; font-size: 11px;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-size: 11px;">{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right; font-size: 11px;">{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary-table">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td style="text-align: right;">{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td style="color: #ef4444;">Discount</td>
                    <td style="text-align: right; color: #ef4444;">-{{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>VAT (5%)</td>
                    <td style="text-align: right;">{{ number_format($order->vat_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total (AED)</td>
                    <td style="text-align: right;">{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <div class="clear"></div>

        <div class="footer">
            <p>{{ settings('invoice_notes', 'Thank you for choosing Tech Hub. For any inquiries, contact us at sales@techhub.ae') }}</p>
            <p>This is a system generated invoice and does not require a signature.</p>
        </div>
    </div>
</body>
</html>
