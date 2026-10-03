<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet</title>
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
        .warning-box {
            margin-top: 20px;
            padding: 10px;
            background-color: #ffe6e6;
            color: #cc0000;
            border: 1px solid #cc0000;
            text-align: center;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        .pl-4 { padding-left: 20px !important; }
        .pl-8 { padding-left: 40px !important; }
        .pl-12 { padding-left: 60px !important; }
        .pt-4 { padding-top: 15px !important; }
        .pb-2 { padding-bottom: 8px !important; }
        .text-sm { font-size: 11px !important; }
        .text-lg { font-size: 16px !important; }
        .text-base { font-size: 14px !important; }
        .font-bold { font-weight: bold !important; }
        .border-b-2 { border-bottom: 2px solid #ddd !important; }
        .border-gray-800 { border-color: #333 !important; }
        .border-gray-200 { border-color: #eee !important; }
        .bg-gray-50 { background-color: #f9f9f9 !important; }
        .bg-gray-100 { background-color: #f2f2f2 !important; }
    </style>
</head>
<body>
    <div class="report-sheet">
        <div class="report-header-center">
            <h1 class="report-company-name">{{ settings('shop_name', 'Techhub') }}</h1>
            <div class="report-title-text">Balance Sheet</div>
            <div class="report-basis-text">Basis: Accrual</div>
            <div class="report-period-text">As of {{ $asOfDate->format('d M Y') }}</div>
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
                <!-- ASSETS -->
                <tr class="spacer-row"><td colspan="3"></td></tr>
                <tr>
                    <td colspan="3" class="font-bold text-lg pt-4 pb-2">Assets</td>
                </tr>
                <tr class="group-header">
                    <td colspan="3" class="pl-4">Current Assets</td>
                </tr>

                <!-- Cash -->
                <tr class="group-header">
                    <td colspan="3" class="pl-8 text-sm">Cash and Cash Equivalents</td>
                </tr>
                @foreach($cashEquivalents as $acc)
                <tr>
                    <td class="pl-12">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td class="pl-8">Total for Cash and Cash Equivalents</td>
                    <td></td>
                    <td class="align-right">{{ number_format($cashEquivalents->sum('computed_balance'), 2) }}</td>
                </tr>

                <!-- Accounts Receivable -->
                <tr class="group-header">
                    <td colspan="3" class="pl-8 text-sm">Accounts Receivable</td>
                </tr>
                @foreach($accountsReceivable as $acc)
                <tr>
                    <td class="pl-12">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td class="pl-8">Total for Accounts Receivable</td>
                    <td></td>
                    <td class="align-right">{{ number_format($accountsReceivable->sum('computed_balance'), 2) }}</td>
                </tr>

                <!-- Other Current Assets -->
                <tr class="group-header">
                    <td colspan="3" class="pl-8 text-sm">Other current assets</td>
                </tr>
                @foreach($otherCurrentAssets as $acc)
                <tr>
                    <td class="pl-12">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td class="pl-8">Total for Other current assets</td>
                    <td></td>
                    <td class="align-right">{{ number_format($otherCurrentAssets->sum('computed_balance'), 2) }}</td>
                </tr>

                <tr class="group-total border-b-2 border-gray-200">
                    <td class="pl-4 font-bold text-sm">Total for Current Assets</td>
                    <td></td>
                    <td class="align-right font-bold text-sm">{{ number_format($totalAssets, 2) }}</td>
                </tr>

                <tr class="group-header">
                    <td colspan="3" class="pl-4">Non Current Assets</td>
                </tr>
                <tr class="group-total">
                    <td class="pl-4">Total for Non Current Assets</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>

                <tr class="group-header">
                    <td colspan="3" class="pl-4">Fixed Assets</td>
                </tr>
                <tr class="group-total">
                    <td class="pl-4">Total for Fixed Assets</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>

                <tr class="group-header">
                    <td colspan="3" class="pl-4">Other Assets</td>
                </tr>
                <tr class="group-total border-b-2 border-gray-200">
                    <td class="pl-4">Total for Other Assets</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>

                <tr class="summary-row bg-gray-50">
                    <td colspan="2" class="font-bold text-sm">Total for Assets</td>
                    <td class="align-right font-bold text-sm">{{ number_format($totalAssets, 2) }}</td>
                </tr>

                <!-- LIABILITIES & EQUITIES -->
                <tr class="spacer-row"><td colspan="3"></td></tr>
                <tr>
                    <td colspan="3" class="font-bold text-lg pt-4 pb-2 border-b-2 border-gray-800">Liabilities & Equities</td>
                </tr>
                
                <!-- Liabilities -->
                <tr>
                    <td colspan="3" class="font-bold pt-4 pb-2">Liabilities</td>
                </tr>
                <tr class="group-header">
                    <td colspan="3" class="pl-4">Current Liabilities</td>
                </tr>

                <!-- Accounts Payable -->
                <tr class="group-header">
                    <td colspan="3" class="pl-8 text-sm">Accounts Payable</td>
                </tr>
                @foreach($accountsPayable as $acc)
                <tr>
                    <td class="pl-12">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td class="pl-8">Total for Accounts Payable</td>
                    <td></td>
                    <td class="align-right">{{ number_format($accountsPayable->sum('computed_balance'), 2) }}</td>
                </tr>

                <!-- Other Current Liabilities -->
                <tr class="group-header">
                    <td colspan="3" class="pl-8 text-sm">Other Current Liabilities</td>
                </tr>
                @foreach($otherCurrentLiabilities as $acc)
                <tr>
                    <td class="pl-12">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="group-total">
                    <td class="pl-8">Total for Other Current Liabilities</td>
                    <td></td>
                    <td class="align-right">{{ number_format($otherCurrentLiabilities->sum('computed_balance'), 2) }}</td>
                </tr>

                <tr class="group-total border-b-2 border-gray-200">
                    <td class="pl-4 font-bold text-sm">Total for Current Liabilities</td>
                    <td></td>
                    <td class="align-right font-bold text-sm">{{ number_format($totalLiabilities, 2) }}</td>
                </tr>

                <tr class="group-header">
                    <td colspan="3" class="pl-4">Non Current Liabilities</td>
                </tr>
                <tr class="group-total">
                    <td class="pl-4">Total for Non Current Liabilities</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>

                <tr class="group-header">
                    <td colspan="3" class="pl-4">Other Liabilities</td>
                </tr>
                <tr class="group-total border-b-2 border-gray-200">
                    <td class="pl-4">Total for Other Liabilities</td>
                    <td></td>
                    <td class="align-right">0.00</td>
                </tr>

                <tr class="summary-row bg-gray-50">
                    <td colspan="2" class="font-bold text-sm">Total for Liabilities</td>
                    <td class="align-right font-bold text-sm">{{ number_format($totalLiabilities, 2) }}</td>
                </tr>

                <!-- Equities -->
                <tr>
                    <td colspan="3" class="font-bold pt-4 pb-2">Equities</td>
                </tr>
                
                <tr>
                    <td class="pl-8">Current Year Earnings</td>
                    <td>-</td>
                    <td class="align-right">{{ number_format($currentYearEarnings, 2) }}</td>
                </tr>
                <tr>
                    <td class="pl-8">Retained Earnings</td>
                    <td>3100</td>
                    <td class="align-right">{{ number_format($retainedEarnings, 2) }}</td>
                </tr>
                @foreach($equityAccounts as $acc)
                <tr>
                    <td class="pl-8">{{ $acc->name }}</td>
                    <td>{{ $acc->code }}</td>
                    <td class="align-right">{{ number_format($acc->computed_balance, 2) }}</td>
                </tr>
                @endforeach
                
                <tr class="group-total border-b-2 border-gray-200 bg-gray-50">
                    <td class="font-bold text-sm">Total for Equities</td>
                    <td></td>
                    <td class="align-right font-bold text-sm">{{ number_format($totalEquity, 2) }}</td>
                </tr>

                <tr class="spacer-row"><td colspan="3"></td></tr>

                <tr class="summary-row bg-gray-100">
                    <td colspan="2" class="font-bold text-base">Total for Liabilities & Equities</td>
                    <td class="align-right font-bold text-base">{{ number_format($totalLiabilities + $totalEquity, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) > 0.02)
        <div class="warning-box">
            Warning: The Balance Sheet is unbalanced. Debits do not equal Credits in journal entries.
        </div>
        @endif

        <div class="pl-footer-note">
            **Amount is displayed in your base currency AED
        </div>
    </div>
</body>
</html>
