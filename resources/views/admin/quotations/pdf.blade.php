<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ $quotation->quotation_no }}</title>
    
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
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

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 800;
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
            font-style: italic;
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
                <td style="width: 50%;">
                    <div class="shop-name">{{ settings('shop_name', 'ELECTROMART') }}</div>
                    <div class="shop-site">{{ settings('site_name', 'Premium Tech Solutions') }} UAE</div>
                    <div style="margin-top: 15px; color: #475569; line-height: 1.4;">
                        <span style="display: block; font-weight: 700;">{{ settings('shop_address', 'Dubai, UAE') }}</span>
                        <span>Phone: {{ settings('shop_phone', settings('contact_phone', '+971 00 000 0000')) }}</span><br>
                        <span>Email: {{ settings('contact_email', 'sales@electromart.ae') }}</span><br>
                        <span style="font-weight: 800; text-transform: uppercase; margin-top: 5px; display: inline-block;">TRN: {{ settings('shop_trn', '100XXXXXXXXXXXX') }}</span>
                    </div>
                </td>
                <td class="header-right" style="width: 50%; vertical-align: top;">
                    <div class="doc-type">Quotation</div>
                    <div style="margin-top: 10px;">
                        <span class="info-label">Quotation Number</span><br>
                        <span class="info-value">{{ $quotation->quotation_no }}</span>
                    </div>
                    <div style="margin-top: 15px;">
                        <table style="width: 100%;">
                            <tr>
                                <td align="right">
                                    <span class="info-label">Date</span><br>
                                    <span class="info-value">{{ $quotation->created_at->format('d M, Y') }}</span>
                                </td>
                            </tr>
                            @if($quotation->expiry_date)
                            <tr>
                                <td align="right" style="padding-top: 10px;">
                                    <span class="info-label">Valid Until</span><br>
                                    <span class="info-value" style="color: #e11d48;">{{ $quotation->expiry_date->format('d M, Y') }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Customer Info -->
        <table class="customer-section">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="customer-box">
                        <div class="info-label" style="margin-bottom: 5px;">Customer Details</div>
                        <div style="font-size: 14px; font-weight: 800; color: #0f172a;">{{ $quotation->customer_name }}</div>
                        @if($quotation->customer)
                            <div style="margin-top: 5px; font-weight: 700; color: #475569;">
                                <span>Ph: {{ $quotation->customer->phone }}</span><br>
                                @if($quotation->customer->email)
                                    <span>Em: {{ $quotation->customer->email }}</span><br>
                                @endif
                                @if($quotation->customer->trn_number)
                                    <span style="text-transform: uppercase; font-weight: 800; color: #1e293b; margin-top: 5px; display: inline-block;">TRN: {{ $quotation->customer->trn_number }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </td>
                <td style="width: 50%; vertical-align: middle; text-align: right;">
                    <span class="badge badge-{{ $quotation->status }}">{{ $quotation->status }}</span>
                    <div style="margin-top: 8px;">
                        <span class="info-label">Generated by:</span>
                        <span style="font-weight: 800; color: #1e293b;">{{ $quotation->user->name ?? 'Admin' }}</span>
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
                    <th style="width: 15%; text-align: right;">Unit Price</th>
                    <th style="width: 15%; text-align: right;">Total (Incl. VAT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td><span style="font-weight: 700; color: #0f172a; font-size: 12px;">{{ $item->product_name }}</span></td>
                    <td align="center" style="font-weight: 700;">{{ $item->quantity }}</td>
                    <td align="right" style="color: #475569;">AED {{ number_format($item->unit_price, 2) }}</td>
                    <td align="right" style="font-weight: 700; color: #0f172a;">AED {{ number_format($item->subtotal, 2) }}</td>
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
                        <td class="total-value">AED {{ number_format($quotation->subtotal + $quotation->discount + $quotation->vat_amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="total-label">Excl. VAT</td>
                        <td class="total-value">AED {{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount > 0)
                    <tr>
                        <td class="total-label" style="color: #e11d48;">Discount</td>
                        <td class="total-value" style="color: #e11d48;">- AED {{ number_format($quotation->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="total-label">VAT (5%)</td>
                        <td class="total-value">AED {{ number_format($quotation->vat_amount, 2) }}</td>
                    </tr>
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
                        <div class="terms-title">Terms & Conditions</div>
                        <ul class="terms-list">
                            <li>1. Prices are valid for 15 days from the date of quotation.</li>
                            <li>2. Items are subject to availability at the time of order confirmation.</li>
                            <li>3. Standard warranty applies to all electronic items unless otherwise specified.</li>
                            <li>4. This is a computer-generated document and does not require a physical signature.</li>
                        </ul>
                    </td>
                    <td style="width: 40%; vertical-align: bottom; text-align: right;">
                        <div style="font-size: 8px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1px;">
                            Official Quotation • {{ settings('site_name', 'ElectroMart') }}
                        </div>
                        <div style="font-size: 8px; color: #e2e8f0; margin-top: 5px;">
                            Generated by {{ settings('site_name', 'ElectroMart') }} ERP
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
