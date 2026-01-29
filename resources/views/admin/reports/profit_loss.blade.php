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
            <div class="flex gap-3">
                <button type="submit" class="bg-[#0f172a] text-white px-8 py-2.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-sync text-xs"></i> <span>Generate Statement</span>
                </button>
                <button type="button" onclick="window.print()" class="bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl shadow-lg shadow-slate-200/20 hover:bg-slate-200 font-bold transition-all">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT: Financial Breakdown -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 bg-slate-900 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-black tracking-tight">Income Statement</h3>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mt-1">Consolidated Financial Overview</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-full border border-emerald-500/20">Fiscal Period</span>
                    </div>
                </div>

                <div class="p-8">
                    <!-- SECTION 1: REVENUE -->
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-arrow-trend-up text-emerald-500"></i>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Operating Revenue</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-600">Gross Sales Income</span>
                                <span class="font-bold text-slate-900">{{ number_format($totalRevenue, 2) }} <span class="text-[10px] text-slate-400 ml-1 italic">AED</span></span>
                            </div>
                            <div class="flex justify-between items-center text-sm text-rose-500">
                                <span class="flex items-center gap-2 uppercase font-bold text-[10px] tracking-widest">Returns & Refunds <span class="bg-rose-50 px-1.5 py-0.5 rounded text-[9px]">Credit</span></span>
                                <span class="font-bold">({{ number_format($totalReturns, 2) }}) <span class="text-[10px] opacity-70 ml-1 italic">AED</span></span>
                            </div>
                            <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-900 leading-none">Net Revenue</span>
                                <span class="text-lg font-black text-slate-900">{{ number_format($totalRevenue - $totalReturns, 2) }} <span class="text-[10px] text-slate-400 ml-1 italic font-bold">AED</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: COGS -->
                    <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-boxes-packing text-amber-600"></i>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Direct Costs</h4>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Cost of Goods Sold (COGS)</span>
                            <span class="text-sm font-bold text-slate-900">{{ number_format($cogs, 2) }} <span class="text-[10px] text-slate-400 ml-1 italic">AED</span></span>
                        </div>
                    </div>

                    <!-- SECTION 3: GROSS PROFIT -->
                    <div class="mb-8 p-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-600">Gross Margin</h4>
                                <p class="text-2xl font-black text-emerald-900 mt-1">{{ number_format($grossProfit, 2) }} <span class="text-sm text-emerald-700/50 uppercase tracking-widest ml-1 italic">AED</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none">Margin %</p>
                                <p class="text-xl font-black text-emerald-900 mt-1">{{ ($totalRevenue - $totalReturns) > 0 ? number_format(($grossProfit / ($totalRevenue - $totalReturns)) * 100, 1) : 0 }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: EXPENSES -->
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-hand-holding-dollar text-rose-500"></i>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Operational Expenses</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm text-slate-600">
                                <span class="flex items-center gap-2 lowercase italic">documented expenses via tracking module</span>
                                <span class="text-rose-600 font-bold">{{ number_format($totalExpenses, 2) }} <span class="text-[10px] text-slate-400 ml-1 italic">AED</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- FINAL: NET PROFIT -->
                    <div class="pt-8 border-t-4 border-double border-slate-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-[0.3em] text-slate-900">Net Operating Income</h4>
                                <p class="text-sm text-slate-400 mt-1 font-bold uppercase tracking-widest">Bottom Line Statement</p>
                            </div>
                            <div class="text-right">
                                <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($netProfit, 2) }}</p>
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">United Arab Emirates Dirham</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Visualization & Context -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Summary Chart Card -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                <h4 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-6">Profit Dynamics</h4>
                <div id="profitChart" class="w-full h-64"></div>
            </div>

            <!-- Context Info -->
            <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fas fa-circle-info"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900">Financial Insights</h4>
                        <p class="text-xs text-amber-800/70 mt-2 leading-relaxed font-medium">
                            The Cost of Goods Sold (COGS) is calculated based on the current product cost price. For accurate tax and profit reporting, ensure all supplier costs are updated during reception.
                        </p>
                    </div>
                </div>
            </div>

            @can('view reports')
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
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

@push('scripts')
<script>
    var options = {
        series: [{
            name: 'Financial Distribution',
            data: [
                {{ $totalRevenue - $totalReturns }}, 
                {{ $cogs }}, 
                {{ $totalExpenses }}, 
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
            categories: ['Revenue', 'COGS', 'Expenses', 'Net Profit'],
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
