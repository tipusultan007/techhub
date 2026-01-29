<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ $quotation->quotation_no }}</title>
    
    <style>
        * {
            font-family: "DejaVu Sans", sans-serif !important;
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #1e293b;
            background-color: #fff;
        }

        .invoice-box {
            width: 100%;
            background: #fff;
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
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .shop-site {
            font-size: 10px;
            font-weight: bold;
            color: #2563eb;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header-right {
            text-align: right;
        }

        .doc-type {
            font-size: 32px;
            font-weight: bold;
            color: #e2e8f0;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .info-label {
            color: #94a3b8;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 12px;
            font-weight: bold;
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

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending { background: #fef9c3; color: #854d0e; }
        .badge-converted { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

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
            font-weight: bold;
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
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9px;
        }

        .total-value {
            text-align: right;
            font-weight: bold;
            color: #334155;
        }

        .grand-total {
            border-top: 1px solid #cbd5e1;
            margin-top: 10px;
            padding-top: 10px;
        }

        .grand-total-label {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .grand-total-value {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
        }

        .footer-section {
            margin-top: 100px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        .terms-title {
            font-size: 9px;
            font-weight: bold;
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
            font-weight: bold;
            font-style: normal;
            line-height: 1.6;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if(settings('site_logo'))
                        @php
                            $logoPath = settings('site_logo');
                            try {
                                $path = parse_url($logoPath, PHP_URL_PATH);
                                $fullPath = public_path($path);
                                if (file_exists($fullPath)) {
                                    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                                    $data = file_get_contents($fullPath);
                                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                } else {
                                    $logoBase64 = $logoPath;
                                }
                            } catch (\Exception $e) {
                                $logoBase64 = $logoPath;
                            }
                        @endphp
                        <img src="{{ $logoBase64 }}" alt="{{ settings('site_name') }}" style="max-height: 45px; margin-bottom: 8px;">
                    @endif
                    <div style="color: #64748b; line-height: 1.4; font-size: 11px;">
                        {{ settings('shop_address', 'Dubai, UAE') }}<br>
                        Phone: {{ settings('shop_phone', '+971 00 000 0000') }}<br>
                        Email: {{ settings('contact_email', 'sales@techhubrak.ae') }}<br>
                        <div style="font-weight: bold; color: #1e293b; margin-top: 8px; text-transform: uppercase; font-size: 12px;">TRN: {{ settings('shop_trn', '100XXXXXXXXXXXX') }}</div>
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <h1 style="font-size: 28px; font-weight: bold; color: #1e293b; margin: 0; text-transform: uppercase;">
                        Quotation
                    </h1>
                    <div style="margin-top: 10px; font-size: 11px; line-height: 1.6;">
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">Quotation #:</span> 
                            <span class="info-value">{{ $quotation->quotation_no }}</span>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">Date:</span> 
                            <span class="info-value">{{ ($quotation->date ?? $quotation->created_at)->format('d M Y') }}</span>
                        </div>
                        @if($quotation->expiry_date)
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">Valid Until:</span> 
                            <span class="info-value" style="color: #e11d48;">{{ $quotation->expiry_date->format('d M Y') }}</span>
                        </div>
                        @endif
                        <div style="margin-top: 8px;">
                            <span class="info-label">Status:</span> 
                            <span class="badge badge-{{ $quotation->status }}">{{ $quotation->status }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Info Sections -->
        <table class="customer-section">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="info-label" style="margin-bottom: 8px; color: #94a3b8;">Customer Details:</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; margin-bottom: 4px;">{{ $quotation->customer_name }}</div>
                    @if($quotation->customer)
                        <div style="color: #475569; line-height: 1.5; font-size: 11px;">
                            <span>{{ $quotation->customer->phone }}</span><br>
                            @if($quotation->customer->email)
                                <span>{{ $quotation->customer->email }}</span><br>
                            @endif
                            @if($quotation->customer->trn_number)
                                <span style="font-weight: bold; color: #2563eb; margin-top: 6px; display: inline-block;">TRN: {{ $quotation->customer->trn_number }}</span>
                            @endif
                        </div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="info-label" style="margin-bottom: 8px; color: #94a3b8;">Generated By:</div>
                    <div style="color: #475569; line-height: 1.6; font-size: 11px;">
                         <span style="font-weight: bold; color: #1e293b;">{{ $quotation->user->name ?? 'Admin' }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Item Description</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: right;">Rate</th>
                    <th style="width: 10%; text-align: center;">Tax %</th>
                    <th style="width: 10%; text-align: right;">Tax</th>
                    <th style="width: 10%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td><span style="font-weight: 400; color: #0f172a; font-size: 12px;">{{ $item->product_name }}</span></td>
                    <td align="center" style="font-weight: 400;">{{ number_format($item->quantity, 3) }}</td>
                    <td align="right" style="color: #475569;">{{ number_format($item->unit_price, 2) }}</td>
                    <td align="center" style="color: #475569;">{{ number_format($item->tax_rate, 2) }}</td>
                    <td align="right" style="color: #475569;">{{ number_format($item->tax_amount, 2) }}</td>
                    <td align="right" style="font-weight: 400; color: #0f172a;">{{ number_format($item->subtotal, 2) }}</td>
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
                        <td class="total-value">AED {{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount > 0)
                    <tr>
                        <td class="total-label" style="color: #e11d48;">Discount</td>
                        <td class="total-value" style="color: #e11d48;">- AED {{ number_format($quotation->discount, 2) }}</td>
                    </tr>
                    @endif
                    @php
                        $groupedTaxes = $quotation->items->groupBy('tax_rate');
                    @endphp
                    @foreach($groupedTaxes as $rate => $items)
                        @php
                            $taxAmount = $items->sum('tax_amount');
                            if($taxAmount <= 0) continue;
                            $label = $rate == 0 ? 'Zero Rate (0%)' : ($rate == 5 ? 'VAT (5%)' : "Tax ($rate%)");
                        @endphp
                        <tr>
                            <td class="total-label">{{ $label }}</td>
                            <td class="total-value">AED {{ number_format($taxAmount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="grand-total">
                        <td class="grand-total-label">Total Amount</td>
                        <td class="grand-total-value">AED {{ number_format($quotation->total, 2) }}</td>
                    </tr>
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
                            @if(settings('quotation_notes'))
                                {!! nl2br(e(settings('quotation_notes'))) !!}
                            @else
                                <li>1. Prices are valid for 15 days from the date of quotation.</li>
                                <li>2. Items are subject to availability at the time of order confirmation.</li>
                                <li>3. Standard warranty applies to all electronic items unless otherwise specified.</li>
                                <li>4. This is a computer-generated document and does not require a physical signature.</li>
                            @endif
                        </ul>
                    </td>
                    <td style="width: 40%; vertical-align: bottom; text-align: right;">
                        <div style="font-size: 8px; font-weight: bold; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1px;">
                            Official Quotation • {{ settings('site_name', 'Tech Hub') }}
                        </div>
                        <div style="font-size: 8px; color: #e2e8f0; margin-top: 5px;">
                            Generated by {{ settings('site_name', 'Tech Hub') }} ERP
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
