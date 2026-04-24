<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->invoice_no }}</title>
    
    <style>
        :root {
            --brand-navy: #024959;
            --brand-emerald: #2dae9a;
        }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #1e293b;
            background-color: #f3f4f6;
        }

        .no-print {
            text-align: center;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            margin: 0 5px;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-print { background: #1e293b; color: #fff; border: none; }
        .btn-pdf { background: #2563eb; color: #fff; border: none; }
        .btn-close { background: #fff; color: #334155; border: 1px solid #d1d5db; }

        .invoice-box {
            max-width: 210mm;
            min-height: 297mm;
            margin: 10px auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
        }

        .shop-name {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .shop-site {
            font-size: 10px;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header-right {
            text-align: right;
        }

        .doc-type {
            font-size: 32px;
            font-weight: 800;
            color: #e2e8f0;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-label {
            color: #94a3b8;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
        }

        .customer-section {
            width: 100%;
            margin-bottom: 40px;
        }

        .customer-box {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
            width: 45%;
        }

        .status-box {
            text-align: right;
            width: 45%;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef9c3; color: #854d0e; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-table {
            width: 250px;
            float: right;
            padding: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .total-row {
            margin-bottom: 8px;
        }

        .total-label {
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9px;
        }

        .total-value {
            text-align: right;
            font-weight: 700;
            color: #334155;
        }

        .grand-total {
            border-top: 1px solid #cbd5e1;
            margin-top: 10px;
            padding-top: 10px;
        }

        .grand-total-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .grand-total-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-emerald);
            text-align: right;
        }

        .footer-section {
            margin-top: 100px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        .terms-title {
            font-size: 9px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 8px;
            color: #64748b;
            font-weight: 700;
            font-style: normal;
            line-height: 1.6;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .invoice-box { 
                box-shadow: none !important; 
                margin: 0 !important; 
                width: 100% !important;
                padding: 0 !important;
                min-height: auto !important;
                overflow: hidden !important;
                border: none !important;
            }
            @page { size: A4; margin: 0.5cm; }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Print Invoice</button>
        <a href="{{ route('orders.download-pdf', $order->id) }}" class="btn btn-pdf">Download PDF</a>
        <button onclick="window.close()" class="btn btn-close">Close</button>
    </div>

    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if(settings('site_logo'))
                        <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" style="max-height: 60px; margin-bottom: 8px;">
                    @endif
                    <div style="color: #64748b; line-height: 1.4; font-size: 11px;">
                        {!! settings('shop_address', '') !!}<br>
                        Phone: {{ settings('shop_phone', '+971 00 000 0000') }}<br>
                        Email: {{ settings('contact_email', 'sales@techhubrak.ae') }}<br>
                        Web: www.techhubrak.ae<br>
                        TRN: {{ settings('shop_trn', '100XXXXXXXXXXXX') }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <h1 style="font-size: 28px; font-weight: 800; color: #2DAE9A; margin: 0; text-transform: uppercase;">
                        {{ $order->vat_amount > 0 ? 'Invoice' : 'Invoice' }}
                    </h1>
                    <div style="margin-top: 10px; font-size: 11px; line-height: 1.6;">
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">Invoice #:</span> 
                            <span class="info-value" style="font-family: monospace;">{{ $order->invoice_no }}</span>
                        </div>
                        @if($order->po_number)
                            <div style="margin-bottom: 2px;">
                                <span class="info-label">PO #:</span> 
                                <span class="info-value text-uppercase" style="font-family: monospace;">{{ $order->po_number }}</span>
                            </div>
                        @endif
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">Date:</span> 
                            <span class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <span class="info-label">Status:</span> 
                            <span class="badge badge-{{ $order->status }}">{{ $order->status }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Info Sections -->
        <table class="customer-section">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="info-label" style="margin-bottom: 8px; color: #94a3b8;">Bill To:</div>
                    <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">{{ $order->customer_name ?? 'Walk-in Customer' }}</div>
                    @if($order->customer)
                        <div style="color: #475569; line-height: 1.5; font-size: 11px;">
                            <span>{{ $order->customer->phone }}</span><br>
                            @if($order->customer->email)
                                <span>{{ $order->customer->email }}</span><br>
                            @endif
                            @if($order->customer->trn_number)
                                <span style="font-weight: 800; color: #2563eb; margin-top: 6px; display: inline-block; font-family: monospace;">TRN: {{ $order->customer->trn_number }}</span>
                            @endif
                        </div>
                    @else
                         <div style="color: #94a3b8; font-style: italic; font-size: 11px;">Guest Customer</div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="info-label" style="margin-bottom: 8px; color: #94a3b8;">Payment Details:</div>
                    <div style="color: #475569; line-height: 1.6; font-size: 11px;">
                        <div>Method: <span style="font-weight: 800; color: #1e293b; text-transform: uppercase;">{{ $order->payment_method }}</span></div>
                        <div style="margin-top: 4px;">Cashier: <span style="font-weight: 700; color: #1e293b;">{{ $order->user->name ?? 'Admin' }}</span></div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Item Description</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: right;">Price</th>
                    <th style="width: 10%; text-align: center;">Tax %</th>
                    <th style="width: 10%; text-align: right;">Tax</th>
                    <th style="width: 15%; text-align: right;">Total (Incl. VAT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <span style="font-weight: normal; color: #0f172a; font-size: 12px;">{{ $item->product_name }}</span>
                        @if($item->variant)
                            <div style="font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 2px;">{{ $item->variant->variant_name }}</div>
                        @endif
                        @if($item->serial_numbers)
                            <div style="margin-top: 5px; font-size: 9px; font-family: monospace; font-weight: 700; color: #2563eb;">
                                SN: {{ $item->serial_numbers }}
                                @if($item->warranty_end_date)
                                    <div style="color: #64748b; text-transform: uppercase; font-size: 8px; margin-top: 2px;">
                                        Warranty: {{ \Carbon\Carbon::parse($item->warranty_end_date)->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td align="center" style="font-weight: 700;">{{ $item->quantity + 0 }}</td>
                    <td align="right" style="color: #475569;"> {{ number_format($item->unit_price, 2) }}</td>
                    <td align="center" style="color: #475569;">{{ number_format($item->tax_rate, 2) }}</td>
                    <td align="right" style="color: #475569;">{{ number_format($item->tax_amount, 2) }}</td>
                    <td align="right" style="font-weight: 700; color: #0f172a;"> {{ number_format($item->subtotal + $item->tax_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="clearfix">
            <div class="totals-table">
                <table style="width: 100%;">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td class="total-value"><img src="{{ asset('images/aed.png') }}" alt="UAE Dirham" width="12" height="12"> {{ number_format($order->total + $order->discount, 2) }}</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <td class="total-label" style="color: #e11d48;">Discount</td>
                        <td class="total-value" style="color: #e11d48;">- <img src="{{ asset('images/aed.png') }}" alt="UAE Dirham" width="12" height="12"> {{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="total-label">Net Amount</td>
                        <td class="total-value"><img src="{{ asset('images/aed.png') }}" alt="UAE Dirham" width="12" height="12"> {{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @php
                        $groupedTaxes = $order->items->groupBy('tax_rate');
                    @endphp
                    @foreach($groupedTaxes as $rate => $items)
                        @php
                            $taxAmount = $items->sum('tax_amount');
                            if($taxAmount <= 0) continue;
                            $label = $rate == 0 ? 'Zero Rate (0%)' : ($rate == 5 ? 'VAT (5%)' : "Tax ($rate%)");
                        @endphp
                        <tr>
                            <td class="total-label">{{ $label }}</td>
                            <td class="total-value"><img src="{{ asset('images/aed.png') }}" alt="UAE Dirham" width="12" height="12"> {{ number_format($taxAmount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="grand-total">
                        <td class="grand-total-label">Total</td>
                        <td class="grand-total-value"><img src="{{ asset('images/aed.png') }}" alt="UAE Dirham" width="12" height="12"> {{ number_format($order->total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="total-label" style="padding-top: 10px;">Paid Amount</td>
                        <td class="total-value" style="padding-top: 10px;">AED {{ number_format($order->paid_amount, 2) }}</td>
                    </tr>
                    @if($order->due_amount > 0)
                    <tr>
                        <td class="total-label" style="color: #e11d48; font-weight: 800;">Balance Due</td>
                        <td class="total-value" style="color: #e11d48; font-weight: 800;">AED {{ number_format($order->due_amount, 2) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <div class="terms-title">Notes</div>
                        <ul class="terms-list">
                            @if(settings('invoice_notes'))
                                {!! nl2br(e(settings('invoice_notes'))) !!}
                            @endif
                        </ul>
                    </td>
                    
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
