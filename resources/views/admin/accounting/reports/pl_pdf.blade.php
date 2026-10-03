<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            line-height: 1.5;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .report-sheet {
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }
        .report-header-center {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 5px 0;
        }
        .report-title-text {
            font-size: 14px;
            font-weight: normal;
            margin: 0 0 5px 0;
        }
        .report-basis-text {
            font-size: 11px;
            color: #555;
            margin: 0 0 5px 0;
        }
        .report-period-text {
            font-size: 11px;
            color: #222;
            margin: 0;
        }
        .pl-report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .pl-report-table th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: normal;
            font-size: 12px;
            padding: 6px 8px;
            text-align: left;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .pl-report-table th.align-right {
            text-align: right;
        }
        .pl-report-table td {
            padding: 5px 8px;
            font-size: 12px;
            vertical-align: middle;
            color: #000;
        }
        .pl-report-table td.align-right {
            text-align: right;
        }
        .pl-report-table .indent {
            padding-left: 28px !important;
        }
        .pl-report-table .group-header td {
            font-weight: bold;
            padding-top: 10px;
            padding-bottom: 4px;
            font-size: 12px;
        }
        .pl-report-table .group-total td {
            font-weight: bold;
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 12px;
        }
        .pl-report-table .summary-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 12px;
        }
        .pl-report-table .net-profit-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 3px double #000;
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 12px;
        }
        .pl-report-table .label-right {
            text-align: right;
            padding-right: 30px !important;
        }
        .pl-report-table .spacer-row td {
            padding: 0;
            height: 12px;
            border: none !important;
        }
        .pl-footer-note {
            font-size: 10px;
            color: #555;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="report-sheet">
        <div class="report-header-center">
            <h1 class="report-company-name">{{ settings('shop_name', 'Techhub') }}</h1>
            <div class="report-title-text">Profit and Loss</div>
            <div class="report-basis-text">Basis: Accrual</div>
            <div class="report-period-text">From {{ $startDate->format('d M Y') }} To {{ $endDate->format('d M Y') }}</div>
        </div>

        <table class="pl-report-table">
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
                @foreach($operatingIncomeAccounts as $acc)
                <tr>
                    <td class="indent">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td>Total for Operating Income</td>
                    <td></td>
                    <td class="align-right">{{ number_format($totalOperatingIncome, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <!-- Cost of Goods Sold -->
                <tr class="group-header">
                    <td colspan="3">Cost of Goods Sold</td>
                </tr>
                @foreach($cogsAccounts as $acc)
                <tr>
                    <td class="indent">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td>Total for Cost of Goods Sold</td>
                    <td></td>
                    <td class="align-right">{{ number_format($totalCogs, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <tr class="summary-row">
                    <td colspan="2" class="label-right">Gross Profit</td>
                    <td class="align-right">{{ number_format($grossProfit, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <!-- Operating Expense -->
                <tr class="group-header">
                    <td colspan="3">Operating Expense</td>
                </tr>
                @forelse($operatingExpenseAccounts as $acc)
                <tr>
                    <td class="indent">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td class="indent" style="color: #666; font-style: italic;">No operating expenses recorded.</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>
                @endforelse
                <tr class="group-total">
                    <td>Total for Operating Expense</td>
                    <td></td>
                    <td class="align-right">{{ number_format($totalOperatingExpense, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <tr class="summary-row">
                    <td colspan="2" class="label-right">Operating Profit</td>
                    <td class="align-right">{{ number_format($operatingProfit, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <!-- Non Operating Income -->
                <tr class="group-header">
                    <td colspan="3">Non Operating Income</td>
                </tr>
                @foreach($nonOperatingIncomeAccounts as $acc)
                <tr>
                    <td class="indent">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td>Total for Non Operating Income</td>
                    <td></td>
                    <td class="align-right">{{ number_format($totalNonOperatingIncome, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <!-- Non Operating Expense -->
                <tr class="group-header">
                    <td colspan="3">Non Operating Expense</td>
                </tr>
                @foreach($nonOperatingExpenseAccounts as $acc)
                <tr>
                    <td class="indent">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td>Total for Non Operating Expense</td>
                    <td></td>
                    <td class="align-right">{{ number_format($totalNonOperatingExpense, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <!-- Net Profit/Loss -->
                <tr class="net-profit-row">
                    <td colspan="2" class="label-right">Net Profit/Loss</td>
                    <td class="align-right">{{ number_format($netProfit, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="pl-footer-note">
            **Amount is displayed in your base currency AED
        </div>
    </div>
</body>
</html>
