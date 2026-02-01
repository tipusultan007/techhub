<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchase->reference_no }}</title>
    <style>
        /* DOMPDF & Browser Compatible CSS */
        @page { margin: 20px; }
        body { 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px; 
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container { width: 100%; margin: 0 auto; }
        
        /* Table Layout for Columns */
        .w-full { width: 100%; }
        .header-table { margin-bottom: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        .header-table td { vertical-align: top; }
        
        .title { font-size: 26px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; color: #1e293b; }
        .po-number { font-size: 14px; font-weight: bold; color: #4f46e5; }
        
        .info-table { margin-bottom: 30px; }
        .info-table td { width: 50%; vertical-align: top; padding: 10px; }
        .info-box { background: #f8fafc; border: 1px solid #f1f5f9; padding: 15px; border-radius: 10px; }
        .info-title { font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px; border-bottom: 1px solid #eee; padding-bottom: 3px; letter-spacing: 1px; }
        
        /* Product Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #1e293b; color: #fff; padding: 10px; text-align: left; font-size: 9px; text-transform: uppercase; font-weight: bold; }
        .items-table td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .product-name { font-weight: 400; font-size: 12px; color: #0f172a; }
        .variant-name { font-size: 9px; color: #64748b; text-transform: uppercase; margin-top: 2px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Footer Area */
        .footer-container { margin-top: 20px; }
        .footer-left { width: 60%; float: left; }
        .footer-right { width: 35%; float: right; }
        .clear { clear: both; }
        
        .totals-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px; }
        .total-row { border-bottom: 1px solid #fff; padding: 4px 0; }
        .grand-total { border-top: 1px solid #cbd5e1; margin-top: 10px; padding-top: 10px; }
        .grand-total td { font-size: 13px; font-weight: bold; }
        
        .policy-box { background: #fffbeb; border: 1px solid #fef3c7; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 9px; color: #92400e; line-height: 1.5; font-style: normal; }
        .signature-line { border-bottom: 1px solid #334155; margin-top: 60px; width: 220px; }
        
        .no-print-area { 
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-print {
            background: #1e293b;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media print {
            .no-print-area { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print-area">
        <a href="javascript:window.print()" class="btn-print">Print Document</a>
    </div>

    <div class="container">
        <!-- Header -->
        <table class="w-full header-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if(settings('site_logo'))
                        <img src="{{ asset(settings('site_logo')) }}" alt="{{ settings('site_name') }}" style="max-height: 60px; margin-bottom: 8px;">
                    @endif
                    <div style="color: #64748b; line-height: 1.4; font-size: 11px;">
                        <strong>{{ settings('shop_address', 'Dubai, UAE') }}</strong><br>
                        <strong>Phone:</strong> {{ settings('shop_phone', '+971 00 000 0000') }}<br>
                        <strong>Email:</strong> {{ settings('contact_email', 'sales@techhubrak.ae') }}<br>
                        <strong>Website:</strong> https://techhubrak.ae<br>
                        <div style="font-weight: bold; color: #1e293b; margin-top: 8px; text-transform: uppercase; font-size: 12px;">TRN: {{ settings('shop_trn', '100XXXXXXXXXXXX') }}</div>
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <h1 style="font-size: 28px; font-weight: bold; color: #2DAE9A; margin: 0; text-transform: uppercase;">
                        Purchase Order
                    </h1>
                    <div style="margin-top: 10px; font-size: 11px; line-height: 1.6;">
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">REF #:</span> 
                            <span class="info-value" style="font-weight: bold; color: #4f46e5;">{{ $purchase->reference_no }}</span>
                        </div>
                        <div style="margin-bottom: 2px;">
                            <span class="info-label">Date:</span> 
                            <span class="info-value">{{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <span class="info-label">Status:</span> 
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 9px; font-weight: bold; text-transform: uppercase; background: #fef9c3; color: #854d0e;">{{ $purchase->status }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Address Grid -->
        <table class="w-full info-table">
            <tr>
                <td style="width: 50%; padding-right: 20px;">
                    <div class="info-title">VENDOR / SUPPLIER</div>
                    <div class="info-box">
                        <div style="font-size: 16px; font-weight: bold; color: #0f172a; margin-bottom: 2px;">{{ $purchase->supplier->name }}</div>
                        <div style="font-weight: bold; color: #475569; font-style: italic; margin-bottom: 8px; font-size: 11px;">{{ $purchase->supplier->company_name }}</div>
                        <div style="color: #64748b; font-size: 11px; line-height: 1.4;">
                            {{ $purchase->supplier->address }}<br>
                            Ph: {{ $purchase->supplier->phone }}
                        </div>
                        @if($purchase->supplier->trn_number)
                            <div style="margin-top: 10px; font-size: 10px; font-weight: bold; color: #1e293b; border-top: 1px solid #e2e8f0; pt-2;">TRN: {{ $purchase->supplier->trn_number }}</div>
                        @endif
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="info-title">SHIP TO / WAREHOUSE</div>
                    <div class="info-box">
                        <div style="font-size: 14px; font-weight: bold; color: #0f172a; margin-bottom: 4px;">{{ settings('shop_name', 'TECH HUB') }}</div>
                        <div style="color: #64748b; font-size: 11px; line-height: 1.5;">
                            <strong>{{ settings('shop_address', 'Dubai, United Arab Emirates') }}</strong><br>
                            <strong>Phone:</strong> {{ settings('contact_phone', settings('shop_phone')) }}<br>
                            <strong>Email:</strong> {{ settings('contact_email', 'info@techhubrak.ae') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30px;" class="text-center">#</th>
                    <th>Product Description</th>
                    <th style="width: 80px;" class="text-right">Unit Cost</th>
                    <th style="width: 40px;" class="text-center">Qty</th>
                    <th style="width: 40px;" class="text-center">Tax %</th>
                    <th style="width: 60px;" class="text-right">Tax</th>
                    <th style="width: 80px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                <tr>
                    <td class="text-center" style="color: #94a3b8; font-weight: bold;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="product-name">{{ $item->product->name ?? 'N/A' }}</div>
                        @if($item->variant)
                            <div class="variant-name">{{ $item->variant->variant_name }}</div>
                        @endif
                    </td>
                    <td class="text-right" style="color: #475569;">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-center" style="font-weight: bold; color: #1e293b;">{{ $item->quantity }}</td>
                    <td class="text-center" style="color: #475569;">{{ number_format($item->tax_rate, 2) }}</td>
                    <td class="text-right" style="color: #475569;">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">{{ number_format($item->subtotal + $item->tax_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Area -->
        <div class="footer-container">
            <div class="footer-left">
                <div class="policy-box">
                    <strong>Purchasing Policy & Delivery Instructions:</strong><br>
                    {!! nl2br(e(settings('purchase_policy', ""))) !!}
                </div>
                
                <div style="margin-top: 40px;">
                    <div class="signature-line"></div>
                    <div style="font-weight: bold; margin-top: 8px; text-transform: uppercase; font-size: 10px;">Authorized Signature</div>
                    <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">PROCUREMENT DEPARTMENT • TECH HUB GROUP</div>
                </div>
            </div>
            
            <div class="footer-right">
                <div class="totals-box">
                    <table class="w-full">
                        <tr class="total-row">
                            <td style="color: #64748b; font-weight: bold; padding: 5px 0;">Subtotal (Net)</td>
                            <td class="text-right" style="font-weight: bold;">{{ number_format($purchase->total_cost - $purchase->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td style="color: #64748b; font-weight: bold; padding: 5px 0;">Tax (VAT)</td>
                            <td class="text-right" style="color: #ef4444; font-weight: bold;">{{ number_format($purchase->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td style="font-weight: bold; color: #2DAE9A; text-transform: uppercase;">Grand Total</td>
                            <td class="text-right" style="color: #2DAE9A;">
                                <span style="font-size: 9px; color: #94a3b8; margin-right: 3px;">AED</span>
                                <span style="font-size: 18px; font-weight: bold;">{{ number_format($purchase->total_cost, 2) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>

</body>
</html>