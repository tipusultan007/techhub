<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Statement - {{ $startDate->format('d M, Y') }} to {{ $endDate->format('d M, Y') }}</title>
    <style>
        @page {
            margin: 50px 50px 50px 50px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #000;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 5px 0;
        }
        .report-title {
            font-size: 12pt;
            font-weight: normal;
            margin: 0 0 5px 0;
        }
        .report-basis {
            font-size: 9pt;
            color: #444;
            margin: 0 0 5px 0;
        }
        .report-period {
            font-size: 9pt;
            color: #222;
            margin: 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .report-table th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: normal;
            font-size: 9.5pt;
            padding: 6px 8px;
            text-align: left;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .report-table th.align-right {
            text-align: right;
        }
        .report-table td {
            padding: 5px 8px;
            font-size: 9.5pt;
            vertical-align: middle;
        }
        .report-table td.align-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .indent {
            padding-left: 20px !important;
        }
        .group-header td {
            font-weight: bold;
            padding-top: 10px;
            padding-bottom: 4px;
        }
        .group-total td {
            font-weight: bold;
            padding-top: 4px;
            padding-bottom: 6px;
        }
        .summary-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .net-profit-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 3px double #000;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .label-right {
            text-align: right;
            padding-right: 20px !important;
        }
        .spacer-row td {
            padding: 0;
            height: 8px;
            border: none !important;
        }
        .footer-note {
            font-size: 8pt;
            color: #555;
            margin-top: 35px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="company-name">{{ settings('shop_name', 'Techhub') }}</h1>
        <div class="report-title">Profit and Loss</div>
        <div class="report-basis">Basis: Accrual</div>
        <div class="report-period">From {{ $startDate->format('d M Y') }} To {{ $endDate->format('d M Y') }}</div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 50%;">Account</th>
                <th style="width: 25%;">Account Code</th>
                <th style="width: 25%;" class="align-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Operating Income -->
            <tr class="group-header">
                <td colspan="3">Operating Income</td>
            </tr>
            <tr>
                <td class="indent">Sales</td>
                <td></td>
                <td class="align-right">{{ number_format(($totalRevenue - $totalVAT) - $netReturns, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td>Total for Operating Income</td>
                <td></td>
                <td class="align-right">{{ number_format(($totalRevenue - $totalVAT) - $netReturns, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Cost of Goods Sold -->
            <tr class="group-header">
                <td colspan="3">Cost of Goods Sold</td>
            </tr>
            <tr>
                <td class="indent">Cost of Goods Sold</td>
                <td></td>
                <td class="align-right">{{ number_format($cogs, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td>Total for Cost of Goods Sold</td>
                <td></td>
                <td class="align-right">{{ number_format($cogs, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Gross Profit -->
            <tr class="summary-row">
                <td colspan="2" class="label-right">Gross Profit</td>
                <td class="align-right">{{ number_format($grossProfit, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Operating Expense -->
            <tr class="group-header">
                <td colspan="3">Operating Expense</td>
            </tr>
            @if($expenseCategories && $expenseCategories->count() > 0)
                @foreach($expenseCategories as $cat)
                <tr>
                    <td class="indent">{{ $cat['name'] }}</td>
                    <td></td>
                    <td class="align-right">{{ number_format($cat['net'], 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td class="indent" style="color: #666; font-style: italic;">No operating expenses recorded.</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>
            @endif
            <tr class="group-total">
                <td>Total for Operating Expense</td>
                <td></td>
                <td class="align-right">{{ number_format($expensesNet, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Operating Profit -->
            <tr class="summary-row">
                <td colspan="2" class="label-right">Operating Profit</td>
                <td class="align-right">{{ number_format($netProfit, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Non Operating Income -->
            <tr class="group-header">
                <td colspan="3">Non Operating Income</td>
            </tr>
            <tr class="group-total">
                <td>Total for Non Operating Income</td>
                <td></td>
                <td class="align-right">0.00</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Non Operating Expense -->
            <tr class="group-header">
                <td colspan="3">Non Operating Expense</td>
            </tr>
            <tr class="group-total">
                <td>Total for Non Operating Expense</td>
                <td></td>
                <td class="align-right">0.00</td>
            </tr>

            <tr class="spacer-row"><td colspan="3"></td></tr>

            <!-- Net Profit/Loss -->
            <tr class="net-profit-row">
                <td colspan="2" class="label-right">Net Profit/Loss</td>
                <td class="align-right">{{ number_format($netProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-note">
        **Amount is displayed in your base currency AED
    </div>

</body>
</html>
