<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VAT Return (UAE Form 201) - {{ $startDate->format('d M Y') }} to {{ $endDate->format('d M Y') }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .container { padding: 20px; }
        .header { border-bottom: 2px solid #1e293b; padding-bottom: 10px; margin-bottom: 20px; }
        .header table { width: 100%; }
        .title { font-size: 20px; font-weight: bold; color: #1e293b; text-transform: uppercase; }
        .company-info { text-align: right; }
        .company-name { font-size: 14px; font-weight: bold; display: block; }
        .trn { color: #4b5563; font-size: 12px; }
        .report-meta { margin-bottom: 20px; background: #f8fafc; padding: 10px; border: 1px solid #e2e8f0; }
        .section-header { background-color: #1e293b; color: white; padding: 8px 12px; font-weight: bold; text-transform: uppercase; margin-top: 20px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f1f5f9; color: #475569; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #cbd5e1; }
        td { padding: 8px; border: 1px solid #cbd5e1; vertical-align: top; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .summary-box { background-color: #f8fafc; border: 2px solid #1e293b; padding: 15px; margin-top: 20px; }
        .total-row { background-color: #f1f5f9; font-weight: bold; }
        .box-num { width: 50px; background: #e2e8f0; text-align: center; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .text-green { color: #15803d; }
        .text-red { color: #b91c1c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="title">VAT Return Summary (Form 201)</div>
                        <div style="margin-top: 3px;">UAE Federal Tax Authority Compliance Report</div>
                    </td>
                    <td class="company-info">
                        <span class="company-name">{{ $settings['shop_name'] ?? 'Tech Hub UAE' }}</span>
                        <span class="trn">TRN: {{ $settings['shop_trn'] ?? 'Not Set' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-meta">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="border: none; padding: 0;"><strong>Period:</strong> {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}</td>
                    <td style="border: none; padding: 0;" class="text-right"><strong>Generated:</strong> {{ now()->format('d M, Y h:i A') }}</td>
                </tr>
            </table>
        </div>

        <!-- OUTPUT VAT SECTION -->
        <div class="section-header">VAT on Sales (Output Tax)</div>
        <table>
            <thead>
                <tr>
                    <th class="box-num">Box</th>
                    <th>Description</th>
                    <th class="text-right">Amount (AED)</th>
                    <th class="text-right">VAT (AED)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Box 1 -->
                <tr>
                    <td class="box-num">1a-g</td>
                    <td>Standard Rated Supplies (Sales)</td>
                    <td class="text-right font-mono">{{ number_format($standardRatedNet, 2) }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($standardRatedVat, 2) }}</td>
                </tr>
                <!-- Box 2 -->
                <tr>
                    <td class="box-num">2</td>
                    <td>Zero Rated Supplies (Exports/Services)</td>
                    <td class="text-right font-mono">{{ number_format($zeroRatedNet, 2) }}</td>
                    <td class="text-right font-mono">0.00</td>
                </tr>
                <!-- Box 3 -->
                <tr>
                    <td class="box-num">3</td>
                    <td>Exempt Supplies</td>
                    <td class="text-right font-mono">{{ number_format($exemptNet, 2) }}</td>
                    <td class="text-right font-mono">0.00</td>
                </tr>
                <!-- Box 5 -->
                <tr>
                    <td class="box-num">5</td>
                    <td>Adjustments (Returns / Credit Notes)</td>
                    <td class="text-right font-mono text-red">- {{ number_format($totalRefunds - $vatOnReturns, 2) }}</td>
                    <td class="text-right font-mono text-red">- {{ number_format($vatOnReturns, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">Total Output VAT Due</td>
                    <td class="text-right font-mono">{{ number_format($grossSalesTotal - $totalRefunds, 2) }}</td>
                    <td class="text-right font-mono text-green">{{ number_format($finalOutputVat, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Box 1 Breakdown -->
        <div style="margin-left: 50px; font-size: 10px;">
            <p><strong>Box 1: Breakdown by Emirate</strong></p>
            <table style="width: 80%;">
                <thead>
                    <tr>
                        <th>Emirate</th>
                        <th class="text-right">Net Amount</th>
                        <th class="text-right">VAT (5%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($emirateSales as $emirate => $data)
                    <tr>
                        <td>{{ $emirate }}</td>
                        <td class="text-right font-mono">{{ number_format($data['net'], 2) }}</td>
                        <td class="text-right font-mono">{{ number_format($data['vat'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- INPUT VAT SECTION -->
        <div class="section-header">VAT on Expenses (Input Tax)</div>
        <table>
            <thead>
                <tr>
                    <th class="box-num">Box</th>
                    <th>Description</th>
                    <th class="text-right">Amount (AED)</th>
                    <th class="text-right">Recoverable VAT (AED)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Box 9a -->
                <tr>
                    <td class="box-num">9a</td>
                    <td>Standard Rated Purchases (Stock)</td>
                    <td class="text-right font-mono">{{ number_format($purchasesNet, 2) }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($purchaseVat, 2) }}</td>
                </tr>
                <!-- Box 9b -->
                <tr>
                    <td class="box-num">9b</td>
                    <td>Standard Rated Expenses (General)</td>
                    <td class="text-right font-mono">{{ number_format($expensesNet, 2) }}</td>
                    <td class="text-right font-mono font-bold">{{ number_format($expenseVat, 2) }}</td>
                </tr>
                <!-- Box 10 -->
                <tr>
                    <td class="box-num">10</td>
                    <td>Supplies subject to Reverse Charge (Imports)</td>
                    <td class="text-right font-mono">0.00</td>
                    <td class="text-right font-mono">0.00</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">Total Recoverable Input VAT</td>
                    <td class="text-right font-mono">{{ number_format($purchasesNet + $expensesNet, 2) }}</td>
                    <td class="text-right font-mono text-red">{{ number_format($inputVat, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- NET VAT POSITION -->
        <div class="summary-box">
            <table style="width: 100%; border: none; margin-bottom: 0;">
                <tr style="border: none;">
                    <td style="border: none; padding: 0;">
                        <span style="font-size: 14px; font-weight: bold;">Net VAT Payable / (Refundable)</span>
                    </td>
                    <td style="border: none; padding: 0;" class="text-right">
                        <span style="font-size: 24px; font-weight: bold; color: #1e293b;">AED {{ number_format($netVatPayable, 2) }}</span>
                    </td>
                </tr>
            </table>
            @if($netVatPayable < 0)
                <p class="text-green" style="margin-top: 5px; font-weight: bold; text-align: right;">Refundable from FTA</p>
            @endif
        </div>

        <div class="footer">
            Generated by Techhub ERP. This report is intended for VAT Return filing assistance.<br>
            © {{ date('Y') }} {{ $settings['shop_name'] ?? 'Tech Hub UAE' }}.
        </div>
    </div>
</body>
</html>
