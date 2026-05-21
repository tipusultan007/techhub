@extends('layouts.admin')

@section('header', 'Profit & Loss Statement')

@section('content')
<div class="max-w-full">
    
    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 no-print border border-slate-100">
        <form action="{{ route('reports.profit_loss') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Reporting Period Start</label>
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-100 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Reporting Period End</label>
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-100 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button type="submit" class="bg-[#0f172a] text-white px-5 py-2.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-sync text-xs"></i> <span>Generate</span>
                </button>
                <a href="{{ route('reports.profit_loss.pdf', request()->all()) }}" class="bg-rose-600 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-rose-600/20 hover:bg-rose-700 font-bold transition-all flex items-center gap-2">
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
    .pl-report-table .net-profit-row td {
        font-weight: bold;
        border-top: 1px solid #000;
        border-bottom: 3px double #000;
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
        /* Hide navbar, sidebar, filter bar, buttons, header, etc. */
        header, footer, nav, .no-print, aside, #sidebar, .mobile-header, .filter-bar, button, a.btn, .btn, .navbar, .main-header, .sidebar-wrapper, .sidebar-mini, .sidebar-open, .sidebar-closed, .control-sidebar {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        /* Reset layout for print */
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

        /* Adjust printing font sizes and line heights */
        .pl-report-table th {
            font-size: 9.5pt !important;
            padding: 6px 8px !important;
        }
        .pl-report-table td {
            font-size: 9.5pt !important;
            padding: 5px 8px !important;
        }
        .pl-report-table .group-header td {
            font-size: 9.5pt !important;
            padding-top: 10px !important;
            padding-bottom: 4px !important;
        }
        .pl-report-table .group-total td {
            font-size: 9.5pt !important;
            padding-top: 4px !important;
            padding-bottom: 6px !important;
        }
        .pl-report-table .summary-row td {
            font-size: 9.5pt !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
        }
        .pl-report-table .net-profit-row td {
            font-size: 9.5pt !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
        }
        .pl-footer-note {
            font-size: 8pt !important;
            margin-top: 30px !important;
        }
    }
</style>
@endpush
@endsection
