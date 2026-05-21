@extends('layouts.admin')

@section('header', 'Profit & Loss Statement')

@section('content')
<div class="max-w-full print-full-width">
    
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

    <!-- Print Header (Hidden on Web) -->
    <div class="hidden print-header-logo mb-6 border-b-2 border-slate-900 pb-4">
        <table class="w-full">
            <tr>
                <td>
                    <h2 class="text-xl font-bold uppercase tracking-wider text-slate-900">{{ settings('shop_name', 'Techhub') }}</h2>
                    <p class="text-xs text-slate-500 uppercase tracking-widest mt-1">{{ settings('site_name') }}</p>
                    <div class="text-[10px] text-slate-600 mt-2 font-medium">
                        {!! settings('shop_address') !!}<br>
                        Phone: {{ settings('shop_phone') }} | TRN: {{ settings('shop_trn') }}
                    </div>
                </td>
                <td class="text-right align-top">
                    <h1 class="text-2xl font-black uppercase tracking-tight text-slate-950">Profit & Loss Statement</h1>
                    <div class="text-[10px] text-slate-400 uppercase tracking-widest font-black mt-2">
                        Period: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}<br>
                        Generated: {{ now()->format('d M, Y h:i A') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 print-grid">
        <!-- LEFT: Financial Breakdown -->
        <div class="lg:col-span-2 space-y-6 print-full-width">
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden print-card">
                <div class="px-8 py-6 bg-slate-900 text-white flex justify-between items-center print-card-header">
                    <div>
                        <h3 class="text-lg font-black tracking-tight">Income Statement</h3>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-1">Consolidated Financial Overview</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-full border border-emerald-500/20">Fiscal Period</span>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <!-- SECTION 1: REVENUE -->
                    <div>
                        <div class="flex items-center gap-2 mb-4 border-b border-slate-200 pb-2">
                            <i class="fas fa-arrow-trend-up text-emerald-600"></i>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600">Operating Revenue (VAT Classification)</h4>
                        </div>
                        <div class="space-y-4">
                            <!-- Standard Rated supplies (5% VAT) -->
                            <div class="flex justify-between items-center text-sm font-bold text-slate-800">
                                <span>Standard Rated Sales (5% VAT)</span>
                                <div class="text-right">
                                    <span>{{ number_format($standardRatedNet, 2) }} <span class="text-[10px] text-slate-600 font-black ml-1">AED</span></span>
                                    <div class="text-[10px] text-emerald-700 font-bold">VAT Output: +{{ number_format($standardRatedVat, 2) }} AED</div>
                                </div>
                            </div>
                            
                            <!-- Zero Rated supplies (0% VAT) -->
                            <div class="flex justify-between items-center text-sm font-bold text-slate-800">
                                <span>Zero-Rated / Exempt Sales (0% VAT)</span>
                                <span>{{ number_format($zeroRatedNet, 2) }} <span class="text-[10px] text-slate-600 font-black ml-1">AED</span></span>
                            </div>

                            <!-- Channel Sales (Indented Detail) -->
                            <div class="pl-4 space-y-1.5 border-l-2 border-slate-200 py-1">
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                    <span>• POS Channel Sales (Excl. VAT)</span>
                                    <span>{{ number_format($channelBreakdown['pos']['subtotal'] ?? 0, 2) }} AED</span>
                                </div>
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                    <span>• Online Store Channel Sales (Excl. VAT)</span>
                                    <span>{{ number_format($channelBreakdown['online']['subtotal'] ?? 0, 2) }} AED</span>
                                </div>
                            </div>

                            <!-- Shipping Revenue -->
                            <div class="flex justify-between items-center text-sm font-bold text-slate-800">
                                <span>Shipping Fees Collected (Standard Rated)</span>
                                <span class="text-emerald-700 font-bold">+{{ number_format($totalShipping, 2) }} <span class="text-[10px] text-slate-600 ml-1 font-black">AED</span></span>
                            </div>
                            
                            <!-- Returns and refunds split -->
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center gap-2 uppercase font-bold text-[10px] tracking-widest text-rose-800">Returns & Refunds <span class="bg-rose-100 text-rose-800 px-1.5 py-0.5 rounded text-[9px] font-bold">Credit Note</span></span>
                                <div class="text-right">
                                    <span class="font-extrabold text-rose-800">({{ number_format($netReturns, 2) }}) <span class="text-[10px] opacity-90 ml-1">AED</span></span>
                                    <div class="text-[9px] text-rose-600 font-bold">VAT Reversed: ({{ number_format($vatOnReturns, 2) }}) AED</div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-900 leading-none">Net Sales Revenue (Excl. VAT)</span>
                                <span class="text-lg font-black text-slate-950">{{ number_format(($totalRevenue - $totalVAT) - $netReturns, 2) }} <span class="text-[10px] text-slate-600 ml-1 font-black">AED</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: COGS & STOCK PURCHASES -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 print-bg-white space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-3 border-b border-slate-200 pb-2">
                                <i class="fas fa-boxes-packing text-amber-700"></i>
                                <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600">Direct Cost of Goods Sold (COGS)</h4>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                    <span>COGS - Simple Products</span>
                                    <span>{{ number_format($cogsSimple, 2) }} AED</span>
                                </div>
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                    <span>COGS - Variable Products (Variants)</span>
                                    <span>{{ number_format($cogsVariant, 2) }} AED</span>
                                </div>
                                <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-sm font-bold text-slate-900">
                                    <span>Total Cost of Goods Sold</span>
                                    <span>{{ number_format($cogs, 2) }} <span class="text-[10px] text-slate-600 ml-1 font-bold">AED</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Actual Stock Purchases side-by-side -->
                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <div class="flex items-center gap-2 mb-3 pb-1">
                                <i class="fas fa-file-invoice-dollar text-slate-700"></i>
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">Stock Purchases Audit (Actual Capital Inflow)</h4>
                            </div>
                            <div class="space-y-2 bg-white p-4 rounded-xl border border-slate-200">
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                    <span>Net Purchases (Excl. VAT)</span>
                                    <span>{{ number_format($purchasesNet, 2) }} AED</span>
                                </div>
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700">
                                    <span>Recoverable Input VAT</span>
                                    <span class="text-emerald-700 font-bold">+{{ number_format($purchaseVat, 2) }} AED</span>
                                </div>
                                <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-xs font-bold text-slate-800">
                                    <span>Total Stock Inflow Value (Incl. VAT)</span>
                                    <span>{{ number_format($purchasesTotal, 2) }} AED</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: GROSS PROFIT -->
                    <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-200 print-bg-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-800">Gross Profit Margin</h4>
                                <p class="text-2xl font-black text-emerald-950 mt-1">{{ number_format($grossProfit, 2) }} <span class="text-sm text-emerald-800 uppercase tracking-widest ml-1 italic font-bold">AED</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest leading-none">Gross Margin %</p>
                                <p class="text-xl font-black text-emerald-950 mt-1">
                                    @php
                                        $netRevenue = ($totalRevenue - $totalVAT) - $netReturns;
                                    @endphp
                                    {{ $netRevenue > 0 ? number_format(($grossProfit / $netRevenue) * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: OPERATING EXPENSES -->
                    <div>
                        <div class="flex items-center gap-2 mb-4 border-b border-slate-200 pb-2">
                            <i class="fas fa-hand-holding-dollar text-rose-700"></i>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600">Operating Expenses</h4>
                        </div>
                        
                        @if($expenseCategories->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-slate-200 mb-4 no-print-border">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] font-black text-slate-600 uppercase tracking-wider border-b border-slate-200">
                                        <th class="p-3">Expense Category</th>
                                        <th class="p-3 text-right">Net (Excl. VAT)</th>
                                        <th class="p-3 text-right">VAT Amount</th>
                                        <th class="p-3 text-right font-bold text-slate-800">Gross Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($expenseCategories as $cat)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3 font-semibold text-slate-800">
                                            {{ $cat['name'] }}
                                            <span class="text-[10px] text-slate-500 font-normal ml-1">({{ $cat['count'] }} entries)</span>
                                        </td>
                                        <td class="p-3 text-right text-slate-700 font-mono font-semibold">{{ number_format($cat['net'], 2) }} AED</td>
                                        <td class="p-3 text-right text-slate-600 font-mono font-semibold">{{ number_format($cat['tax'], 2) }} AED</td>
                                        <td class="p-3 text-right text-slate-950 font-bold font-mono">{{ number_format($cat['total'], 2) }} AED</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-xs text-slate-500 italic mb-4">No operating expenses recorded for this period.</div>
                        @endif

                        <div class="pt-4 border-t border-slate-200 flex justify-between items-center">
                            <span class="text-[11px] font-black uppercase tracking-widest text-slate-900 leading-none">Total Operating Expenses (Excl. VAT)</span>
                            <span class="text-md font-bold text-rose-700">{{ number_format($expensesNet, 2) }} <span class="text-[10px] text-slate-600 ml-1 font-bold">AED</span></span>
                        </div>
                        <div class="text-right text-[10px] text-slate-600 mt-1.5 font-bold">
                            Total VAT Paid: {{ number_format($expensesTax, 2) }} AED | Gross Capital Outflow: {{ number_format($totalExpenses, 2) }} AED
                        </div>
                    </div>

                    <!-- FINAL: NET PROFIT -->
                    <div class="pt-8 border-t-4 border-double border-slate-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-[0.3em]" style="color: #0f172a;">Net Operating Income</h4>
                                <p class="text-xs mt-1 font-bold uppercase tracking-widest" style="color: #475569;">Bottom Line Profit / (Loss)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-black tracking-tighter" style="color: {{ $netProfit >= 0 ? '#065f46' : '#b91c1c' }};">{{ number_format($netProfit, 2) }}</p>
                                <p class="text-[11px] font-black uppercase tracking-[0.2em] mt-1" style="color: #475569;">AED (UAE Dirham)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Visualization & Context -->
        <div class="lg:col-span-1 space-y-6 print-full-width">
            
            <!-- Summary Chart Card -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 no-print">
                <h4 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-6">Profit Dynamics</h4>
                <div id="profitChart" class="w-full h-64"></div>
            </div>

            <!-- Payment Method Breakdown -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 print-card print-avoid-break">
                <h4 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-4 print-header-small">Payment Method Breakdown</h4>
                @if($paymentBreakdown->count() > 0)
                <div class="space-y-3">
                    @foreach($paymentBreakdown as $method => $data)
                    <div class="flex justify-between items-center text-sm border-b border-slate-50 pb-2">
                        <span class="text-slate-600 font-bold uppercase text-[10px] tracking-wider">{{ str_replace('_', ' ', $method) }}</span>
                        <span class="font-bold text-slate-900">
                            {{ number_format($data['total'], 2) }} <span class="text-[9px] text-slate-400 font-normal">AED</span>
                            <span class="text-[10px] text-slate-400 font-medium ml-1">({{ $data['count'] }})</span>
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-xs text-slate-400 italic">No sales recorded.</div>
                @endif
            </div>

            <!-- VAT Audit & Reconciliation Summary -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 print-card print-avoid-break">
                <h4 class="text-xs font-extrabold uppercase tracking-[0.2em] mb-4 print-header-small" style="color: #0f172a;">VAT Return Reconciliation</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold" style="color: #334155;">Gross Output VAT (Sales)</span>
                        <span class="font-bold font-mono" style="color: #0f172a;">{{ number_format($totalVAT, 2) }} AED</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold" style="color: #334155;">Less: VAT on Returns</span>
                        <span class="font-bold font-mono" style="color: #b91c1c;">({{ number_format($vatOnReturns, 2) }}) AED</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-bold px-3 py-2 rounded-xl border" style="color: #065f46; background-color: #ecfdf5; border-color: #a7f3d0;">
                        <span>Net Output VAT Due</span>
                        <span class="font-mono">{{ number_format($totalVAT - $vatOnReturns, 2) }} AED</span>
                    </div>
                    <div class="flex justify-between items-center text-xs pt-2 border-t border-slate-200">
                        <span class="font-bold" style="color: #334155;">Recoverable VAT (Purchases)</span>
                        <span class="font-bold font-mono" style="color: #0f172a;">{{ number_format($purchaseVat, 2) }} AED</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold" style="color: #334155;">Recoverable VAT (Expenses)</span>
                        <span class="font-bold font-mono" style="color: #0f172a;">{{ number_format($expensesTax, 2) }} AED</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-bold px-3 py-2 rounded-xl border" style="color: #b91c1c; background-color: #fef2f2; border-color: #fca5a5;">
                        <span>Total Recoverable Input VAT</span>
                        <span class="font-mono">({{ number_format($purchaseVat + $expensesTax, 2) }}) AED</span>
                    </div>
                    <div class="border-t border-slate-200 my-2"></div>
                    <div class="flex justify-between items-center p-3 border rounded-2xl" style="background-color: #f8fafc; border-color: #e2e8f0;">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider block" style="color: #0f172a;">Net Payable / (Refund)</span>
                            <span class="text-[9px] font-bold uppercase tracking-tight" style="color: #475569;">VAT compliance position</span>
                        </div>
                        @php
                            $netVatValue = ($totalVAT - $vatOnReturns) - ($purchaseVat + $expensesTax);
                        @endphp
                        <span class="text-lg font-black font-mono" style="color: {{ $netVatValue >= 0 ? '#065f46' : '#b91c1c' }};">
                            {{ number_format($netVatValue, 2) }} AED
                        </span>
                    </div>
                </div>
            </div>

            <!-- Context Info -->
            <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6 no-print">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fas fa-circle-info"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900 font-bold">Financial Insights</h4>
                        <p class="text-xs text-amber-800/70 mt-2 leading-relaxed font-semibold">
                            Cost of Goods Sold (COGS) calculates values from snapshots stored at item sales reception. Be sure to check that incoming inventory records have accurate costs to maintain statement reliability.
                        </p>
                    </div>
                </div>
            </div>

            @can('view reports')
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 no-print">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-4">Related Intelligence</h4>
                <div class="space-y-3">
                    <a href="{{ route('reports.vat') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors group">
                        <span class="text-xs font-bold text-slate-600">Tax Compliance Report</span>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ route('reports.sales') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors group">
                        <span class="text-xs font-bold text-slate-600">Sales Trend Analytics</span>
                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endcan

        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        /* Force page wrappers to display as standard static block elements */
        html, body, 
        .flex.flex-1.h-full.overflow-hidden.relative,
        main,
        .flex-1.overflow-y-auto {
            height: auto !important;
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
            position: static !important;
            width: 100% !important;
            background: white !important;
        }

        /* Hide elements that shouldn't be printed */
        header, footer, nav, .no-print, aside, #sidebar, .mobile-header, .filter-bar, button, a.btn, .btn, #profitChart {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        
        @page {
            margin: 1.2cm !important;
        }
        
        /* Adjust layout spacing */
        html, body {
            color: #000 !important;
            font-size: 9.5pt !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            line-height: 1.4 !important;
        }

        /* Force dark text and high contrast colors when printing */
        .text-slate-600, .text-slate-550, .text-slate-500, .text-slate-400, .text-gray-500, .text-gray-600 {
            color: #1e293b !important;
        }
        .text-slate-900, .text-slate-800, .text-slate-950, .text-gray-900, .text-gray-800 {
            color: #000000 !important;
        }
        .text-emerald-700, .text-emerald-600, .text-emerald-800 {
            color: #047857 !important;
        }
        .text-rose-700, .text-rose-600, .text-rose-800 {
            color: #b91c1c !important;
        }
        .bg-slate-50, .bg-slate-100, .bg-emerald-50, .bg-rose-50 {
            background-color: transparent !important;
        }
        .border-slate-100, .border-slate-200, .border-emerald-100\/50, .border-rose-100\/50, .border-emerald-300, .border-rose-300 {
            border-color: #cbd5e1 !important;
        }

        .print-full-width {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            float: none !important;
        }

        .print-grid {
            display: block !important;
        }

        /* Show custom print header */
        .print-header-logo {
            display: block !important;
            margin-bottom: 25px !important;
        }

        .print-card {
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            border-radius: 12px !important;
            margin-bottom: 20px !important;
            background-color: white !important;
        }
        
        .print-avoid-break {
            page-break-inside: avoid;
        }

        .print-card-header {
            background-color: #0f172a !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-bg-white {
            background-color: white !important;
            border: 1px solid #e2e8f0 !important;
        }

        .print-header-small {
            font-size: 9.5pt !important;
            border-bottom: 1px solid #000 !important;
            padding-bottom: 4px !important;
        }

        /* Compact typography and details for print */
        .print-card .p-8 {
            padding: 1.25rem !important;
        }
        .print-card .p-6 {
            padding: 1rem !important;
        }
        .print-card .space-y-8 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 1.25rem !important;
        }
        .print-card .space-y-6 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 1rem !important;
        }
        .print-card .space-y-3 > :not([hidden]) ~ :not([hidden]) {
            margin-top: 0.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    var options = {
        series: [{
            name: 'Financial Distribution',
            data: [
                {{ ($totalRevenue - $totalVAT) - $netReturns }}, 
                {{ $cogs }}, 
                {{ $expensesNet }}, 
                {{ $netProfit }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: { show: false },
            fontFamily: 'Outfit, sans-serif'
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                distributed: true,
                columnWidth: '50%',
            }
        },
        dataLabels: { enabled: false },
        colors: ['#024959', '#334155', '#f43f5e', '#2dae9a'],
        xaxis: {
            categories: ['Net Rev', 'COGS', 'Expenses', 'Net Profit'],
            labels: { style: { fontWeight: 700, fontSize: '10px' } }
        },
        yaxis: { show: false },
        tooltip: {
            theme: 'dark',
            y: { formatter: function (val) { return val.toLocaleString() + " AED"; } }
        }
    };

    var chart = new ApexCharts(document.querySelector("#profitChart"), options);
    chart.render();
</script>
@endpush
@endsection
