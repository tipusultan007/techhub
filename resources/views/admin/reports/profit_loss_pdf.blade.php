<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Statement - {{ $startDate->format('d M, Y') }} to {{ $endDate->format('d M, Y') }}</title>
    <style>
        @page {
            margin: 45px 45px 55px 45px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            color: #0f172a;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .shop-name {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0;
        }
        .site-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 2px 0 0 0;
        }
        .shop-info {
            font-size: 7.5pt;
            color: #0f172a;
            margin-top: 6px;
            line-height: 1.25;
        }
        .report-title {
            text-align: right;
        }
        .report-title h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-meta {
            font-size: 7.5pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
            line-height: 1.25;
        }
        
        .statement-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .statement-table td {
            padding: 5px 6px;
            vertical-align: middle;
        }
        .section-row {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 9pt;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        .subsection-title {
            font-weight: bold;
            font-size: 8pt;
            color: #0f172a;
            text-transform: uppercase;
            padding-top: 8px !important;
            padding-bottom: 4px !important;
        }
        .indent-1 {
            padding-left: 15px !important;
            color: #0f172a;
        }
        .indent-2 {
            padding-left: 30px !important;
            color: #0f172a;
            font-size: 7.5pt;
        }
        .total-row {
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #0f172a;
        }
        .net-profit-row {
            font-weight: bold;
            font-size: 10.5pt;
            color: #0f172a;
            background-color: #f8fafc;
            border-top: 1.5px solid #0f172a;
            border-bottom: 4px double #0f172a;
        }
        
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .grid-table th {
            background: #f1f5f9;
            padding: 5px 6px;
            text-align: left;
            font-size: 7.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
        }
        .grid-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 7.5pt;
        }
        
        .text-right { text-align: right; }
        .text-rose { color: #b91c1c; }
        .text-emerald { color: #047857; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: Courier, monospace; color: #000000; }
        
        .text-slate-400 { color: #1e293b; }
        .text-slate-500 { color: #0f172a; }
        .text-slate-600 { color: #000000; }
        
        .page-break {
            page-break-before: always;
        }
        
        .footer {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 7pt;
            color: #1e293b;
            text-align: center;
        }
        .page-number:before {
            content: "Page " counter(page);
        }
        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    @if(settings('site_logo'))
                        <img src="{{ public_path(parse_url(settings('site_logo'), PHP_URL_PATH)) }}" style="max-height: 45px;">
                    @else
                        <h2 class="shop-name">{{ settings('shop_name', 'Techhub') }}</h2>
                    @endif
                    <p class="site-name">{{ settings('site_name') }}</p>
                    <div class="shop-info">
                        {!! settings('shop_address') !!}<br>
                        Phone: {{ settings('shop_phone') }} | TRN: {{ settings('shop_trn') }}
                    </div>
                </td>
                <td class="report-title">
                    <h1>Income Statement</h1>
                    <p class="report-meta">
                        Period: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}<br>
                        Generated: {{ now()->format('d M, Y h:i A') }}<br>
                        Currency: AED
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- MAIN INCOME STATEMENT -->
    <table class="statement-table">
        <tbody>
            <!-- REVENUE -->
            <tr class="section-row">
                <td colspan="2">1. Operating Revenue</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent-1">Standard Rated Product Sales (5% VAT)</td>
                <td class="text-right text-emerald font-mono" style="font-size:7.5pt; padding-right:15px;">VAT Output: +{{ number_format($standardRatedVat, 2) }}</td>
                <td class="text-right font-mono">{{ number_format($standardRatedNet, 2) }}</td>
            </tr>
            <tr>
                <td class="indent-1">Zero-Rated & Exempt Product Sales (0% VAT)</td>
                <td></td>
                <td class="text-right font-mono">{{ number_format($zeroRatedNet, 2) }}</td>
            </tr>
            <tr>
                <td class="indent-2">• POS Channel Sales (Excl. VAT)</td>
                <td class="text-right font-mono" style="padding-right:20px; font-size:7.5pt;">{{ number_format($channelBreakdown['pos']['subtotal'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent-2">• Online Store Channel Sales (Excl. VAT)</td>
                <td class="text-right font-mono" style="padding-right:20px; font-size:7.5pt;">{{ number_format($channelBreakdown['online']['subtotal'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent-1">Shipping Fees Collected (Standard Rated)</td>
                <td></td>
                <td class="text-right font-mono">{{ number_format($totalShipping, 2) }}</td>
            </tr>
            <tr class="text-rose">
                <td class="indent-1">Less: Sales Returns & Refunds (Credit Notes)</td>
                <td class="text-right text-rose font-mono" style="font-size:7.5pt; padding-right:15px;">VAT Reversed: ({{ number_format($vatOnReturns, 2) }})</td>
                <td class="text-right font-mono">({{ number_format($netReturns, 2) }})</td>
            </tr>
            <tr class="total-row">
                <td>Net Revenue (Excl. VAT)</td>
                <td></td>
                <td class="text-right font-mono">{{ number_format(($totalRevenue - $totalVAT) - $netReturns, 2) }}</td>
            </tr>

            <!-- COGS -->
            <tr class="section-row">
                <td colspan="2">2. Direct Costs & Cost of Goods Sold (COGS)</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent-1">Cost of Goods Sold (COGS) - Simple Products</td>
                <td class="text-right font-mono" style="padding-right:20px; font-size:7.5pt;">{{ number_format($cogsSimple, 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent-1">Cost of Goods Sold (COGS) - Variable Products (Variants)</td>
                <td class="text-right font-mono" style="padding-right:20px; font-size:7.5pt;">{{ number_format($cogsVariant, 2) }}</td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td>Total Cost of Goods Sold</td>
                <td></td>
                <td class="text-right font-mono">({{ number_format($cogs, 2) }})</td>
            </tr>

            <!-- STOCK PURCHASES AUDIT -->
            <tr>
                <td colspan="3" class="subsection-title indent-1">Stock Purchases Audit (Actual Capital Inflow)</td>
            </tr>
            <tr>
                <td class="indent-2">Net Stock Purchases (Excl. VAT)</td>
                <td></td>
                <td class="text-right font-mono">{{ number_format($purchasesNet, 2) }}</td>
            </tr>
            <tr>
                <td class="indent-2">Recoverable Input VAT on Purchases</td>
                <td></td>
                <td class="text-right text-emerald font-mono">+{{ number_format($purchaseVat, 2) }}</td>
            </tr>
            <tr style="border-bottom: 1px dashed #cbd5e1;">
                <td class="indent-2 font-bold">Total Stock Purchase Value (Incl. VAT)</td>
                <td></td>
                <td class="text-right font-mono font-bold">{{ number_format($purchasesTotal, 2) }}</td>
            </tr>

            <!-- GROSS PROFIT -->
            <tr class="total-row" style="background-color: #f8fafc;">
                <td>Gross Profit Margin</td>
                <td class="text-right font-bold text-slate-500" style="font-size:7.5pt;">
                    Margin: 
                    @php
                        $netRevenue = ($totalRevenue - $totalVAT) - $netReturns;
                    @endphp
                    {{ $netRevenue > 0 ? number_format(($grossProfit / $netRevenue) * 100, 1) : 0 }}%
                </td>
                <td class="text-right font-mono font-bold text-emerald">{{ number_format($grossProfit, 2) }}</td>
            </tr>

            <!-- OPERATING EXPENSES -->
            <tr class="section-row">
                <td colspan="2">3. Operating Expenses (VAT-Exclusive)</td>
                <td></td>
            </tr>
            @if($expenseCategories->count() > 0)
                @foreach($expenseCategories as $cat)
                <tr>
                    <td class="indent-1">{{ $cat['name'] }}</td>
                    <td class="text-right font-mono" style="padding-right:20px; font-size:7.5pt; color:#0f172a;">
                        Entries: {{ $cat['count'] }} | VAT Paid: {{ number_format($cat['tax'], 2) }}
                    </td>
                    <td class="text-right font-mono">{{ number_format($cat['net'], 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td class="indent-1 italic text-slate-400" colspan="3">No operating expenses recorded.</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Operating Expenses (Excl. VAT)</td>
                <td></td>
                <td class="text-right font-mono text-rose">({{ number_format($expensesNet, 2) }})</td>
            </tr>
            <tr style="font-size:7.5pt; color:#0f172a;">
                <td colspan="3" class="text-right" style="padding-top: 4px;">
                    Total VAT Paid on Expenses: {{ number_format($expensesTax, 2) }} AED &bull; Gross Capital Outflow: {{ number_format($totalExpenses, 2) }} AED
                </td>
            </tr>

            <!-- NET INCOME -->
            <tr class="net-profit-row">
                <td>Net Operating Income / Bottom Line (Excl. VAT)</td>
                <td></td>
                <td class="text-right font-mono {{ $netProfit >= 0 ? 'text-emerald' : 'text-rose' }}">
                    {{ number_format($netProfit, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- HEADER FOR SECOND PAGE -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h2 class="shop-name">{{ settings('shop_name', 'Techhub') }}</h2>
                    <p class="site-name">{{ settings('site_name') }}</p>
                </td>
                <td class="report-title">
                    <h1>Supporting Audit Schedules</h1>
                    <p class="report-meta">
                        Period: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}<br>
                        Currency: AED
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="avoid-break">
        <!-- SALES CHANNEL DISTRIBUTION -->
        <h3 style="font-size: 9.5pt; color: #0f172a; margin-top: 5px; margin-bottom: 5px; text-transform: uppercase; border-bottom: 1.5px solid #0f172a; padding-bottom: 2px;">Sales Channel Distribution</h3>
        <table class="grid-table">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th class="text-right">Transaction Count</th>
                    <th class="text-right">Discounts Allowed</th>
                    <th class="text-right">VAT Collected (Output)</th>
                    <th class="text-right">Gross Total (Incl. VAT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['pos' => 'POS Terminal', 'online' => 'Online Store'] as $channelKey => $channelName)
                    @php
                        $chData = $channelBreakdown[$channelKey] ?? ['count' => 0, 'discount' => 0, 'vat' => 0, 'total' => 0];
                    @endphp
                    <tr>
                        <td class="font-bold">{{ $channelName }}</td>
                        <td class="text-right">{{ $chData['count'] }}</td>
                        <td class="text-right font-mono text-rose">AED {{ number_format($chData['discount'], 2) }}</td>
                        <td class="text-right font-mono">AED {{ number_format($chData['vat'], 2) }}</td>
                        <td class="text-right font-bold font-mono">AED {{ number_format($chData['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="avoid-break">
        <!-- PAYMENT METHOD BREAKDOWN -->
        <h3 style="font-size: 9.5pt; color: #0f172a; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; border-bottom: 1.5px solid #0f172a; padding-bottom: 2px;">Sales by Payment Method</h3>
        <table class="grid-table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th class="text-right">Transaction Count</th>
                    <th class="text-right">Total Amount Collected (Incl. VAT)</th>
                </tr>
            </thead>
            <tbody>
                @if($paymentBreakdown->count() > 0)
                    @foreach($paymentBreakdown as $method => $data)
                    <tr>
                        <td class="font-bold" style="text-transform: uppercase;">{{ str_replace('_', ' ', $method) }}</td>
                        <td class="text-right">{{ $data['count'] }}</td>
                        <td class="text-right font-bold font-mono">AED {{ number_format($data['total'], 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center italic text-slate-400">No transactions recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="avoid-break">
        <!-- DETAILED OPERATING EXPENSES BREAKDOWN -->
        <h3 style="font-size: 9.5pt; color: #0f172a; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; border-bottom: 1.5px solid #0f172a; padding-bottom: 2px;">Operating Expenses Audit Detail</h3>
        <table class="grid-table">
            <thead>
                <tr>
                    <th>Expense Category</th>
                    <th class="text-right">Entries Count</th>
                    <th class="text-right">Net Amount (Excl. VAT)</th>
                    <th class="text-right">VAT Paid (Input Tax)</th>
                    <th class="text-right">Gross Total (Incl. VAT)</th>
                </tr>
            </thead>
            <tbody>
                @if($expenseCategories->count() > 0)
                    @foreach($expenseCategories as $cat)
                    <tr>
                        <td class="font-bold">{{ $cat['name'] }}</td>
                        <td class="text-right">{{ $cat['count'] }}</td>
                        <td class="text-right font-mono">AED {{ number_format($cat['net'], 2) }}</td>
                        <td class="text-right font-mono">AED {{ number_format($cat['tax'], 2) }}</td>
                        <td class="text-right font-bold font-mono">AED {{ number_format($cat['total'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td>TOTAL EXPENSES</td>
                        <td class="text-right">{{ $expenseCategories->sum('count') }}</td>
                        <td class="text-right font-mono">AED {{ number_format($expensesNet, 2) }}</td>
                        <td class="text-right font-mono text-rose">AED {{ number_format($expensesTax, 2) }}</td>
                        <td class="text-right font-mono">AED {{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="5" class="text-center italic text-slate-400">No expenses recorded.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="avoid-break">
        <!-- VAT RETURN RECONCILIATION -->
        <h3 style="font-size: 9.5pt; color: #0f172a; margin-top: 15px; margin-bottom: 5px; text-transform: uppercase; border-bottom: 1.5px solid #0f172a; padding-bottom: 2px;">VAT Return Reconciliation Schedule</h3>
        <table class="grid-table">
            <thead>
                <tr>
                    <th>Reconciliation Component</th>
                    <th>Details / Tax Treatment</th>
                    <th class="text-right">Amount (AED)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-bold">Gross Output VAT (Sales)</td>
                    <td>Standard Rated Supplies (5% Output VAT collected on sales)</td>
                    <td class="text-right font-mono">AED {{ number_format($totalVAT, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Less: VAT on Returns</td>
                    <td>Returned VAT on credit notes / customer refund orders</td>
                    <td class="text-right font-mono text-rose">AED ({{ number_format($vatOnReturns, 2) }})</td>
                </tr>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td>Net Output VAT Due (A)</td>
                    <td>Final Output Tax liability for the period</td>
                    <td class="text-right font-mono">AED {{ number_format($totalVAT - $vatOnReturns, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Recoverable VAT (Stock Purchases)</td>
                    <td>Input VAT paid on Standard-Rated inventory inflows (recoverable)</td>
                    <td class="text-right font-mono">AED {{ number_format($purchaseVat, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Recoverable VAT (Operating Expenses)</td>
                    <td>Input VAT paid on documented operational expenditures (recoverable)</td>
                    <td class="text-right font-mono">AED {{ number_format($expensesTax, 2) }}</td>
                </tr>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td>Total Recoverable Input VAT (B)</td>
                    <td>Total input tax eligible for recovery (Purchases + Expenses)</td>
                    <td class="text-right font-mono text-rose">AED ({{ number_format($purchaseVat + $expensesTax, 2) }})</td>
                </tr>
                @php
                    $netVatCompliance = ($totalVAT - $vatOnReturns) - ($purchaseVat + $expensesTax);
                @endphp
                <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 1.5px solid #0f172a; border-bottom: 2.5px double #0f172a;">
                    <td style="color: #0f172a; padding: 6px 6px;">Net VAT Compliance Position (A - B)</td>
                    <td style="color: #1e293b; font-size: 7.5pt; padding: 6px 6px;">Net liability to be paid / (claimed) to UAE Federal Tax Authority (FTA)</td>
                    <td class="text-right font-mono" style="color: {{ $netVatCompliance >= 0 ? '#047857' : '#b91c1c' }}; font-size: 9pt; padding: 6px 6px;">
                        AED {{ number_format($netVatCompliance, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Generated by {{ settings('site_name') }} ERP &bull; Confidential Financial Statement &bull; <span class="page-number"></span>
    </div>

</body>
</html>
