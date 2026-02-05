<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #{{ $quotation->quotation_no }}</title>
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
        $GLOBALS['quotation_no'] = $quotation->quotation_no;
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
                                        // Handle WebP explicitly if needed, but standard data URI usually works
                                        $mime = 'image/' . ($type == 'svg' ? 'svg+xml' : $type);
                                        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                                    }
                                }
                            } catch (\Exception $e) {}
                        @endphp
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px; display: block;">
                        @else
                           <!-- Debug: Logo file not found or load failed -->
                           <div style="height: 60px; margin-bottom: 10px;"></div>
                        @endif
                    @endif
                    
                    <div style="font-size: 11px; color: #64748B; line-height: 1.4;">
                        {!! settings('shop_address', '') !!}<br>
                        Phone: {{ settings('shop_phone', '+971 00 000 0000') }}<br>
                        Email: {{ settings('contact_email', 'sales@techhubrak.ae') }}<br>
                        Web: www.techhubrak.ae<br>
                        TRN: {{ settings('shop_trn', '100XXXXXXXXXXXX') }}
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div class="doc-title">Quotation</div>
                    
                    <div class="doc-details">
                        <div class="detail-row">
                            <span class="info-label">Quotation #:</span>
                            <span class="info-value">{{ $quotation->quotation_no }}</span>
                        </div>
                        @if($quotation->po_number)
                        <div class="detail-row">
                            <span class="info-label">PO#:</span>
                            <span class="info-value">{{ $quotation->po_number }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ ($quotation->date ?? $quotation->created_at)->format('d M Y') }}</span>
                        </div>
                        @if($quotation->expiry_date)
                        <div class="detail-row">
                            <span class="info-label">Valid Until:</span>
                            <span class="info-value" style="color: #E11D48;">{{ $quotation->expiry_date->format('d M Y') }}</span>
                        </div>
                        @endif
                        <div class="detail-row" style="margin-top: 8px;">
                            <span class="info-label">Status:</span>
                            <span style="font-weight: 700; text-transform: uppercase; font-size: 10px; padding: 2px 6px; background: #F1F5F9; border-radius: 4px; color: #475569;">
                                {{ $quotation->status }}
                            </span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Customer Section -->
        <table class="section-table">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="section-title">Customer Details</div>
                    <div class="customer-name">{{ $quotation->customer_name }}</div>
                    @if($quotation->customer)
                        <div style="font-size: 11px; color: #475569; line-height: 1.5;">
                            {{ $quotation->customer->phone }}<br>
                            {{ $quotation->customer->email }}<br>
                            @if($quotation->customer->trn_number)
                                <div style="margin-top: 4px; font-weight: 600; color: #2DAE9A;">TRN: {{ $quotation->customer->trn_number }}</div>
                            @endif
                        </div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="section-title" style="margin-left: auto;">Generated By</div>
                    <div style="font-size: 12px; font-weight: 600; color: #0F172A;">
                        {{ $quotation->user->name ?? 'Admin' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th style="width: 5%; text-align: center;">Qty</th>
                    <th style="width: 15%; text-align: right;">Rate</th>
                    <th style="width: 8%; text-align: center;">Tax %</th>
                    <th style="width: 10%; text-align: right;">Tax</th>
                    <th style="width: 12%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td style="text-align: center; color: #64748B;">{{ $loop->iteration }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->description)
                            <div class="item-desc">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-center" style="font-weight: 700; font-size: 12px;">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right" style="color: #475569;">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center" style="color: #475569;">{{ number_format($item->tax_rate, 2) }}</td>
                    <td class="text-right" style="color: #475569;">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right" style="font-weight: 700; color: #0F172A;">{{ number_format($item->subtotal, 2) }}</td>
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
                        <td class="total-value">AED {{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount > 0)
                    <tr class="total-row">
                        <td class="total-label" style="color: #E11D48;">Discount</td>
                        <td class="total-value" style="color: #E11D48;">- AED {{ number_format($quotation->discount, 2) }}</td>
                    </tr>
                    @endif
                    
                    @php
                        $groupedTaxes = $quotation->items->groupBy('tax_rate');
                    @endphp
                    @foreach($groupedTaxes as $rate => $items)
                        @php
                            $taxAmount = $items->sum('tax_amount');
                            if($taxAmount <= 0) continue;
                            $label = $rate == 0 ? 'Zero Rate (0%)' : ($rate == 5 ? 'VAT (5%)' : "VAT ($rate%)");
                        @endphp
                        <tr class="total-row">
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

        <!-- Footer / Terms -->
        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <div class="notes-section">
                            <div class="notes-title">Notes / Terms</div>
                            @if(settings('quotation_notes'))
                                {!! nl2br(e(settings('quotation_notes'))) !!}
                            @else
                                <ul style="padding-left: 15px; margin: 0;">
                                  
                                </ul>
                            @endif
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
                $quotationNo = isset($GLOBALS["quotation_no"]) ? $GLOBALS["quotation_no"] : "";
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
