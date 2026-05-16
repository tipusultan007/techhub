<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VAT Return Report - {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 30px;
        }
        .header {
            border-bottom: 2px solid #1e293b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
        }
        .company-info {
            text-align: right;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            display: block;
        }
        .trn {
            color: #4b5563;
            font-size: 13px;
        }
        .report-meta {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border: 1px solid #e2e8f0;
        }
        td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .summary-box {
            background-color: #1e293b;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-top: 30px;
        }
        .summary-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .summary-amount {
            font-size: 28px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .emirate-row td {
            background-color: #fff;
        }
        .total-row td {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .text-green {
            color: #15803d;
        }
        .text-red {
            color: #b91c1c;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="title">VAT Return Summary</div>
                        <div style="margin-top: 5px;">UAE Federal Tax Authority Compliance</div>
                    </td>
                    <td class="company-info">
                        <span class="company-name">{{ $settings['shop_name'] ?? 'Tech Hub UAE' }}</span>
                        <span class="trn">TRN: {{ $settings['shop_trn'] ?? 'Not Set' }}</span><br>
                        <span style="font-size: 11px; color: #64748b;">{{ $settings['shop_address'] ?? '' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-meta">
            <table>
                <tr>
                    <td style="border: none; padding: 0;">
                        <strong>Reporting Period:</strong> {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}
                    </td>
                    <td style="border: none; padding: 0;" class="text-right">
                        <strong>Generated At:</strong> {{ now()->format('d M, Y h:i A') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- 1. OUTPUT VAT -->
        <div class="section-title">1. VAT on Sales (Output Tax)</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gross VAT from Standard Rated Supplies</td>
                    <td class="text-right font-mono">{{ number_format($grossOutputVat, 2) }}</td>
                </tr>
                <tr>
                    <td>Less: VAT reversed on Returns (Credit Notes)</td>
                    <td class="text-right font-mono text-red">- {{ number_format($vatOnReturns, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Net Output VAT Due</td>
                    <td class="text-right font-mono text-green">{{ number_format($finalOutputVat, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- 2. BREAKDOWN BY EMIRATE -->
        <div class="section-title">Box 1: Standard Rated Supplies by Emirate</div>
        <table>
            <thead>
                <tr>
                    <th>Emirate</th>
                    <th class="text-right">Net Amount (AED)</th>
                    <th class="text-right">VAT Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emirateSales as $emirate => $data)
                <tr class="emirate-row">
                    <td>{{ $emirate }}</td>
                    <td class="text-right font-mono">{{ number_format($data['net'], 2) }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($data['vat'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right font-mono">{{ number_format(collect($emirateSales)->sum('net'), 2) }}</td>
                    <td class="text-right font-mono">{{ number_format(collect($emirateSales)->sum('vat'), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- 3. INPUT VAT -->
        <div class="section-title">2. VAT on Expenses (Input Tax)</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>VAT on Purchases / Received Inventory</td>
                    <td class="text-right font-mono">{{ number_format($inputVat, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="text-red">Total Recoverable Input VAT</td>
                    <td class="text-right font-mono text-red">{{ number_format($inputVat, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- FINAL SUMMARY -->
        <div class="summary-box">
            <div class="summary-title">Net VAT Payable to FTA</div>
            <div class="summary-amount">AED {{ number_format($netVatPayable, 2) }}</div>
            @if($netVatPayable < 0)
                <div style="margin-top: 5px; font-size: 14px;">(Refundable)</div>
            @endif
        </div>

        <div class="footer">
            This is a computer-generated report and does not require a signature.<br>
            © {{ date('Y') }} {{ $settings['shop_name'] ?? 'Tech Hub' }}. All rights reserved.
        </div>
    </div>
</body>
</html>
