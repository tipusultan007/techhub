<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $challan->challan_number }}</title>
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

        /* Footer */
        .footer {
            margin-top: 80px;
        }

        .signature-box {
            width: 45%;
            float: left;
            text-align: center;
        }
        
        .signature-box-right {
            width: 45%;
            float: right;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #334155;
            margin-bottom: 8px;
        }
        
        .signature-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    @php
        $GLOBALS['challan_number'] = $challan->challan_number;
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
                    <div class="doc-title">Delivery Challan</div>
                    
                    <div class="doc-details">
                        <div class="detail-row">
                            <span class="info-label">DC #:</span>
                            <span class="info-value">{{ $challan->challan_number }}</span>
                        </div>
                        @if($challan->po_number)
                        <div class="detail-row">
                            <span class="info-label">PO #:</span>
                            <span class="info-value">{{ $challan->po_number }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($challan->date)->format('d M, Y') }}</span>
                        </div>
                        @if($challan->quotation)
                        <div class="detail-row">
                            <span class="info-label">Ref Quote:</span>
                            <span class="info-value">{{ $challan->quotation->quotation_no }}</span>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Sent To Section -->
        <table class="section-table">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="section-title">Delivered To</div>
                    <div class="customer-name">{{ $challan->customer->name ?? $challan->quotation->customer_name }}</div>
                    @if($challan->customer)
                        <div style="font-size: 11px; color: #475569; line-height: 1.5;">
                            {{ $challan->customer->phone }}<br>
                            {{ $challan->customer->email }}<br>
                            {{ $challan->customer->address }}
                        </div>
                    @else
                         <div style="font-size: 11px; color: #475569;">
                             {{ $challan->quotation->customer->phone ?? '' }}
                         </div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div class="section-title" style="margin-left: auto;">Note</div>
                    <div style="font-size: 11px; color: #64748B; font-style: italic;">
                         {{ $challan->note ?? 'No specific notes.' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 65%;">Product Description</th>
                    <th style="width: 30%; text-align: center;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($challan->items as $index => $item)
                <tr>
                    <td style="text-align: center; color: #64748B;">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                    </td>
                    <td class="text-center" style="font-weight: 700; font-size: 12px; color: #0F172A;">{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer clearfix">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Receiver's Signature</div>
            </div>
            <div class="signature-box-right">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </div>
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

                // Left: Challan Number
                $challanNo = isset($GLOBALS["challan_number"]) ? $GLOBALS["challan_number"] : "";
                $leftText = "DC: " . $challanNo;
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
