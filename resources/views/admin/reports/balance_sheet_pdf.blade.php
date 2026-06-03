<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet - As of {{ $asOfDate->format('d M, Y') }}</title>
    <style>
        @page { margin: 50px 50px 50px 50px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9.5pt; color: #000; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-name { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 5px 0; }
        .report-title { font-size: 12pt; font-weight: normal; margin: 0 0 5px 0; }
        .report-basis { font-size: 9pt; color: #444; margin: 0 0 5px 0; }
        .report-period { font-size: 9pt; color: #222; margin: 0; }
        
        .report-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .report-table th { background-color: #f2f2f2; color: #000; font-weight: normal; font-size: 9.5pt; padding: 6px 8px; text-align: left; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; }
        .report-table th.align-right { text-align: right; }
        .report-table td { padding: 5px 8px; font-size: 9.5pt; vertical-align: middle; }
        .report-table td.align-right { text-align: right; }
        
        .font-bold { font-weight: bold; }
        .indent { padding-left: 20px !important; }
        .indent-2 { padding-left: 40px !important; }
        .indent-3 { padding-left: 60px !important; }
        
        .group-header td { font-weight: bold; padding-top: 10px; padding-bottom: 4px; }
        .group-total td { padding-top: 4px; padding-bottom: 6px; }
        
        .summary-row td { font-weight: bold; border-top: 1px solid #000; border-bottom: 1px solid #000; padding-top: 6px; padding-bottom: 6px; }
        .net-profit-row td { font-weight: bold; border-top: 1px solid #000; border-bottom: 3px double #000; padding-top: 6px; padding-bottom: 6px; }
        
        .spacer-row td { padding: 0; height: 8px; border: none !important; }
        .footer-note { font-size: 8pt; color: #555; margin-top: 35px; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="company-name">{{ settings('shop_name', 'Techhub') }}</h1>
        <div class="report-title">Balance Sheet</div>
        <div class="report-basis">Basis: Accrual</div>
        <div class="report-period">As of {{ $asOfDate->format('d M Y') }}</div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 75%;">Account</th>
                <th style="width: 25%;" class="align-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr class="spacer-row"><td colspan="2"></td></tr>

            <!-- Assets -->
            <tr class="group-header">
                <td colspan="2">Assets</td>
            </tr>
            <tr class="group-total">
                <td class="indent font-bold">Current Assets</td>
                <td></td>
            </tr>
            
            <!-- Cash and Cash Equivalents -->
            <tr>
                <td class="indent-2">Cash and Cash Equivalents (Calculated)</td>
                <td class="align-right">{{ number_format($calculatedCash, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td class="indent font-bold">Total for Cash and Cash Equivalents</td>
                <td class="align-right font-bold">{{ number_format($calculatedCash, 2) }}</td>
            </tr>

            <!-- Accounts Receivable -->
            <tr>
                <td class="indent-2">Accounts Receivable</td>
                <td class="align-right">{{ number_format($accountsReceivable, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td class="indent font-bold">Total for Accounts Receivable</td>
                <td class="align-right font-bold">{{ number_format($accountsReceivable, 2) }}</td>
            </tr>
            
            <!-- Inventory -->
            <tr>
                <td class="indent-2">Inventory Asset</td>
                <td class="align-right">{{ number_format($inventoryAsset, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td class="indent font-bold">Total for Inventory Asset</td>
                <td class="align-right font-bold">{{ number_format($inventoryAsset, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="2"></td></tr>

            <tr class="group-total">
                <td class="font-bold">Total for Current Assets</td>
                <td class="align-right font-bold">{{ number_format($totalAssets, 2) }}</td>
            </tr>
            
            <tr class="summary-row">
                <td class="font-bold">Total for Assets</td>
                <td class="align-right font-bold">{{ number_format($totalAssets, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="2"></td></tr>

            <!-- Liabilities and Equities -->
            <tr class="group-header">
                <td colspan="2">Liabilities & Equities</td>
            </tr>
            
            <!-- Liabilities -->
            <tr class="group-total">
                <td class="indent font-bold">Liabilities</td>
                <td></td>
            </tr>
            <tr class="group-total">
                <td class="indent-2 font-bold">Current Liabilities</td>
                <td></td>
            </tr>
            
            <tr>
                <td class="indent-3">VAT Payable</td>
                <td class="align-right">{{ number_format($vatPayable, 2) }}</td>
            </tr>
            
            <tr class="group-total">
                <td class="indent-2 font-bold">Total for Current Liabilities</td>
                <td class="align-right font-bold">{{ number_format($totalLiabilities, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td class="indent font-bold">Total for Liabilities</td>
                <td class="align-right font-bold">{{ number_format($totalLiabilities, 2) }}</td>
            </tr>

            <tr class="spacer-row"><td colspan="2"></td></tr>
            
            <!-- Equities -->
            <tr class="group-total">
                <td class="indent font-bold">Equities</td>
                <td></td>
            </tr>
            <tr>
                <td class="indent-2">Current Year Earnings</td>
                <td class="align-right">{{ number_format($currentYearEarnings, 2) }}</td>
            </tr>
            <tr>
                <td class="indent-2">Retained Earnings</td>
                <td class="align-right">{{ number_format($retainedEarnings, 2) }}</td>
            </tr>
            <tr>
                <td class="indent-2">Historical Adjustments & Capital</td>
                <td class="align-right">{{ number_format($historicalAdjustments, 2) }}</td>
            </tr>
            <tr class="group-total">
                <td class="indent font-bold">Total for Equities</td>
                <td class="align-right font-bold">{{ number_format($totalEquities, 2) }}</td>
            </tr>
            
            <tr class="spacer-row"><td colspan="2"></td></tr>

            <!-- Total Liabilities & Equities -->
            <tr class="net-profit-row">
                <td class="font-bold">Total for Liabilities & Equities</td>
                <td class="align-right font-bold">{{ number_format($totalLiabilities + $totalEquities, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-note">
        **Amount is displayed in your base currency AED
    </div>

</body>
</html>
