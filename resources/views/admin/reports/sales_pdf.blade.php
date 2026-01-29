<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $startDate->format('d M, Y') }} to {{ $endDate->format('d M, Y') }}</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 10pt;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-table {
            width: 100%;
        }
        .shop-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0;
        }
        .site-name {
            font-size: 10pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin: 2px 0 0 0;
        }
        .shop-info {
            font-size: 8pt;
            color: #475569;
            margin-top: 10px;
            line-height: 1.4;
        }
        .report-title {
            text-align: right;
            vertical-align: top;
        }
        .report-title h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            color: #1e293b;
            text-transform: uppercase;
        }
        .report-meta {
            font-size: 8pt;
            font-weight: bold;
            color: #94a3b8;
            margin-top: 5px;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 10px;
            margin-left: -10px;
            margin-right: -10px;
        }
        .summary-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            width: 25%;
        }
        .card-label {
            font-size: 7pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .sales-table th {
            background: #f1f5f9;
            padding: 10px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }
        .sales-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9pt;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #2563eb; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    @if(settings('site_logo'))
                        <img src="{{ public_path(parse_url(settings('site_logo'), PHP_URL_PATH)) }}" style="max-height: 40px;">
                    @else
                        <h2 class="shop-name">{{ settings('shop_name', 'ELECTROMART') }}</h2>
                    @endif
                    <p class="site-name">{{ settings('site_name') }}</p>
                    <div class="shop-info">
                        {{ settings('shop_address') }}<br>
                        Phone: {{ settings('shop_phone') }} | TRN: {{ settings('shop_trn') }}
                    </div>
                </td>
                <td class="report-title">
                    <h1>Sales Report</h1>
                    <p class="report-meta">
                        Period: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}<br>
                        Generated: {{ now()->format('d M, Y h:i A') }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary-grid">
        <tr>
            <td class="summary-card" style="border-left-color: #3b82f6;">
                <div class="card-label">Gross Sales</div>
                <div class="card-value">AED {{ number_format($totalSales, 2) }}</div>
            </td>
            <td class="summary-card" style="border-left-color: #10b981;">
                <div class="card-label">Net Sales</div>
                <div class="card-value">AED {{ number_format($netSales, 2) }}</div>
            </td>
            <td class="summary-card" style="border-left-color: #ef4444;">
                <div class="card-label">VAT (5%)</div>
                <div class="card-value">AED {{ number_format($totalVAT, 2) }}</div>
            </td>
            <td class="summary-card" style="border-left-color: #f59e0b;">
                <div class="card-label">Total Orders</div>
                <div class="card-value">{{ $orders->count() }}</div>
            </td>
        </tr>
    </table>

    <table class="sales-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Cashier</th>
                <th>Method</th>
                <th class="text-right">VAT</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td class="font-bold text-blue">{{ $order->invoice_no }}</td>
                <td>{{ $order->user->name ?? 'System' }}</td>
                <td style="text-transform: uppercase; font-size: 8pt;">{{ $order->payment_method }}</td>
                <td class="text-right">{{ number_format($order->vat_amount, 2) }}</td>
                <td class="text-right font-bold">AED {{ number_format($order->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated by {{ settings('site_name') }} ERP System &bull; Confidential Document
    </div>

</body>
</html>
