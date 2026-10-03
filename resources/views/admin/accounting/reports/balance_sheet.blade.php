@extends('layouts.admin')

@section('header', 'Balance Sheet')

@section('content')
<div class="max-w-full">
    
    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 no-print border border-slate-100">
        <form action="{{ route('accounting.balance_sheet') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">As Of Date</label>
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="date" name="end_date" value="{{ $asOfDate->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-100 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button type="submit" class="bg-[#0f172a] text-white px-5 py-2.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-sync text-xs"></i> <span>Generate</span>
                </button>
                <a href="{{ route('accounting.balance_sheet.pdf', request()->all()) }}" class="bg-rose-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-rose-600/20 hover:bg-rose-700 font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> <span>PDF</span>
                </a>
                <button type="button" onclick="window.print()" class="bg-slate-100 text-slate-600 px-4 py-2.5 rounded-xl shadow-lg shadow-slate-200/20 hover:bg-slate-200 font-bold transition-all">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Report Sheet (Web & Print Card) -->
    <div class="report-sheet max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-sm rounded-3xl border border-slate-100 mt-6 print:m-0 print:p-0 print:border-none print:shadow-none">
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
        <div class="mt-8 p-4 bg-red-100 text-red-800 rounded font-bold text-center no-print">
            Warning: The Balance Sheet is unbalanced. Debits do not equal Credits in journal entries.
        </div>
        @endif

        <div class="pl-footer-note">
            **Amount is displayed in your base currency AED
        </div>
    </div>
</div>

@push('styles')
<style>
    .report-sheet {
        font-family: 'Helvetica', 'Arial', sans-serif;
        color: #000;
        line-height: 1.5;
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
        padding: 8px 12px;
        text-align: left;
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
    .pl-report-table th.align-right {
        text-align: right;
    }
    .pl-report-table td {
        padding: 6px 12px;
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
    .pl-4 { padding-left: 1rem !important; }
    .pl-8 { padding-left: 2rem !important; }
    .pl-12 { padding-left: 3rem !important; }
    .pl-report-table .group-header td {
        font-weight: bold;
        padding-top: 14px;
        padding-bottom: 6px;
        font-size: 12px;
    }
    .pl-report-table .group-total td {
        font-weight: bold;
        padding-top: 6px;
        padding-bottom: 8px;
        font-size: 12px;
    }
    .pl-report-table .summary-row td {
        font-weight: bold;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        padding-top: 8px;
        padding-bottom: 8px;
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
        margin-top: 40px;
    }

    @media print {
        header, footer, nav, .no-print, aside, #sidebar, .mobile-header, .filter-bar, button, a.btn, .btn, .navbar, .main-header, .sidebar-wrapper, .sidebar-mini, .sidebar-open, .sidebar-closed, .control-sidebar {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        html, body, main, .content-wrapper, .container, .max-w-full, .max-w-4xl {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            box-shadow: none !important;
            border: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .report-sheet {
            padding: 20px !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            max-width: 100% !important;
            margin: 0 !important;
        }

        .pl-report-table th, .pl-report-table td { font-size: 9.5pt !important; padding: 5px 8px !important; }
        .pl-report-table .group-header td { font-size: 9.5pt !important; padding-top: 10px !important; padding-bottom: 4px !important; }
        .pl-report-table .group-total td, .pl-report-table .summary-row td { font-size: 9.5pt !important; padding-top: 6px !important; padding-bottom: 6px !important; }
        .pl-footer-note { font-size: 8pt !important; margin-top: 30px !important; }
    }
</style>
@endpush
@endsection
