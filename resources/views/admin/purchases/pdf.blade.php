<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchase->reference_no }}</title>
    <style>
        /* Professional Premium Theme */
        :root {
            --brand-primary: #2DAE9A; /* Teal */
            --brand-dark: #0F172A;    /* Navy/Slate */
            --gray-light: #F8FAFC;
            --gray-border: #E2E8F0;
            --text-main: #333333;
            --text-secondary: #64748B;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .invoice-box {
            width: 100%;
            margin: 0 auto;
            background: #fff;
        }

        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .clearfix::after { content: ""; clear: both; display: table; }

        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 25px;
        }

        .shop-name {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 5px;
        }

        .doc-title {
            font-size: 24px;
            font-weight: 700;
            color: #2DAE9A;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }

        .doc-details {
            margin-top: 15px;
            font-size: 11px;
            color: #64748B;
        }

        .detail-row {
            margin-bottom: 4px;
        }

        .info-label {
            font-weight: 600;
            text-transform: uppercase;
            color: #64748B;
            font-size: 10px;
            letter-spacing: 0.5px;
            margin-right: 5px;
        }

        .info-value {
            font-weight: 600;
            color: #0F172A;
            font-size: 11px;
        }

        /* Customer & Meta */
        .section-table {
            width: 100%;
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 4px;
            width: 80%;
        }

        .customer-name {
            font-size: 15px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 4px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #F8FAFC;
            padding: 12px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
        }

        .items-table td {
            padding: 14px 10px;
            border-bottom: 1px solid #F8FAFC;
            vertical-align: top;
        }

        .item-name {
            font-size: 12px;
            font-weight: 400;
            color: #0F172A;
            margin-bottom: 2px;
        }

        .item-desc {
            font-size: 10px;
            color: #64748B;
            line-height: 1.3;
        }

        /* Totals */
        .totals-container {
            width: 100%;
        }
        
        .totals-table {
            width: 280px;
            float: right;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 15px;
            background-color: #F8FAFC;
        }

        .total-row td {
            padding: 4px 0;
        }

        .total-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748B;
        }

        .total-value {
            font-size: 11px;
            font-weight: 700;
            color: #0F172A;
            text-align: right;
        }

        .grand-total {
            border-top: 1px solid #CBD5E1;
            margin-top: 10px;
            padding-top: 10px;
        }

        .grand-total-label {
            font-size: 12px;
            font-weight: 700;
            color: #0F172A;
            text-transform: uppercase;
        }

        .grand-total-value {
            font-size: 18px;
            font-weight: 700;
            color: #2DAE9A;
            text-align: right;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
            border-top: 1px solid #E2E8F0;
            padding-top: 20px;
        }

        .notes-section {
            font-size: 10px;
            color: #64748B;
            line-height: 1.5;
        }

        .notes-title {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-size: 9px;
            color: #64748B;
        }
    </style>
</head>

<body>
    @php
        $GLOBALS['purchase_no'] = $purchase->reference_no;
    @endphp
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if(settings('site_logo'))
                        @php
                            $logoSettings = settings('site_logo');
                            $logoBase64 = '';
                            try {
                                if ($logoSettings) {
                                    $relativePath = parse_url($logoSettings, PHP_URL_PATH);
                                    $absolutePath = public_path($relativePath);
                                    if (file_exists($absolutePath)) {
                                        $type = pathinfo($absolutePath, PATHINFO_EXTENSION);
                                        $data = file_get_contents($absolutePath);
                                        $mime = 'image/' . ($type == 'svg' ? 'svg+xml' : $type);
                                        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                                    }
                                }
                            } catch (\Exception $e) {}
                        @endphp
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px; display: block;">
                        @endif
                    @endif
                    
                    <div style="font-size: 11px; color: #64748B; line-height: 1.4;">
                        {{ settings('shop_address', 'Dubai, UAE') }}<br>
                        <strong>Phone:</strong> {{ settings('shop_phone', '+971 00 000 0000') }}<br>
                        <strong>Email:</strong> {{ settings('contact_email', 'sales@techhubrak.ae') }}<br>
                        <strong>TRN:</strong> {{ settings('shop_trn', '100XXXXXXXXXXXX') }}
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div class="doc-title">Purchase Order</div>
                    
                    <div class="doc-details">
                        <div class="detail-row">
                            <span class="info-label">PO #:</span>
                            <span class="info-value">{{ $purchase->reference_no }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ date('d M, Y', strtotime($purchase->date)) }}</span>
                        </div>
                        <div class="detail-row" style="margin-top: 8px;">
                            <span class="info-label">Status:</span>
                            <span style="font-weight: 700; text-transform: uppercase; font-size: 10px; padding: 2px 6px; background: #F1F5F9; border-radius: 4px; color: #475569;">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Supplier Section -->
        <table class="section-table">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="section-title">Supplier Details</div>
                    <div class="customer-name">{{ $purchase->supplier->name ?? 'N/A' }}</div>
                    <div style="font-size: 11px; color: #475569; line-height: 1.5;">
                        {{ $purchase->supplier->phone ?? '' }}<br>
                        {{ $purchase->supplier->email ?? '' }}<br>
                        {{ $purchase->supplier->address ?? '' }}
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="section-title" style="margin-left: auto;">Terms</div>
                    <div style="font-size: 11px; color: #64748B; font-style: italic;">
                        {{ $purchase->note ?? 'No specific notes.' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 45%;">Product Description</th>
                    <th style="width: 15%; text-align: right;">Unit Cost</th>
                    <th style="width: 10%; text-align: center;">Qty</th>
                    <th style="width: 10%; text-align: right;">Tax</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                <tr>
                    <td style="text-align: center; color: #64748B;">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product->name ?? 'Item' }}</div>
                        @if($item->variant)
                        <div class="item-desc">{{ $item->variant->name }}</div>
                        @endif
                    </td>
                    <td class="text-right" style="color: #475569;">{{ number_format($item->net_unit_cost, 2) }}</td>
                    <td class="text-center" style="font-weight: 700; font-size: 12px;">{{ $item->quantity }}</td>
                    <td class="text-right" style="color: #475569;">{{ number_format($item->tax, 2) }}</td>
                    <td class="text-right" style="font-weight: 700; color: #0F172A;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

         <!-- Totals -->
        <div class="clearfix">
            <div class="totals-table">
                <table style="width: 100%;">
                    <tr class="total-row">
                        <td class="total-label">Subtotal</td>
                        <td class="total-value">{{ number_format($purchase->total_cost, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="total-label">Order Tax</td>
                        <td class="total-value">{{ number_format($purchase->order_tax, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="total-label">Shipping</td>
                        <td class="total-value">{{ number_format($purchase->shipping_cost, 2) }}</td>
                    </tr>
                    @if($purchase->discount > 0)
                    <tr class="total-row">
                        <td class="total-label" style="color: #E11D48;">Discount</td>
                        <td class="total-value" style="color: #E11D48;">-{{ number_format($purchase->discount, 2) }}</td>
                    </tr>
                    @endif
                    
                    <tr class="grand-total">
                        <td class="grand-total-label">Grand Total</td>
                        <td class="grand-total-value">{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <div class="notes-section">
                            <div class="notes-title">Authorized Signature</div>
                            <div style="border-bottom: 1px solid #334155; width: 200px; margin-top: 40px;"></div>
                        </div>
                    </td>
                    
                </tr>
            </table>
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Helvetica", "normal");
                $size = 9;
                $color = array(0.39, 0.45, 0.55); // #64748B
                $y = $pdf->get_height() - 35;

                // Border Line
                $pdf->line(40, $y - 10, $pdf->get_width() - 40, $y - 10, array(0.8, 0.8, 0.8), 0.75);

                // Left: Quotation Number
                $quotationNo = isset($GLOBALS["purchase_no"]) ? $GLOBALS["purchase_no"] : "";
                $leftText = "Ref: " . $quotationNo;
                $pdf->text(40, $y, $leftText, $font, $size, $color);

                // Right: Page Number
                $rightText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $textWidth = $fontMetrics->getTextWidth($rightText, $font, $size);
                $x = $pdf->get_width() - $textWidth - 40;
                $pdf->text($x, $y, $rightText, $font, $size, $color);
            ');
        }
    </script>
</body>
</html>
