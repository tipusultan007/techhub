@extends('layouts.admin')

@section('header', 'Expense Report')

@section('content')
<div class="max-w-full">
    
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 no-print">
        <form action="{{ route('reports.expenses') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border rounded p-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border rounded p-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Category</label>
                <select name="category_id" class="border rounded p-2 text-sm w-full bg-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 font-bold">
                <i class="fas fa-filter mr-2"></i> Filter Report
            </button>
            <div class="flex gap-2">
                <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700 font-bold">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <a href="{{ route('reports.expenses.pdf', request()->all()) }}" class="bg-red-600 text-white px-6 py-2 rounded shadow hover:bg-red-700 font-bold">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Print Header (Only visible when printing) -->
    <div class="hidden print-only mb-8">
        <div class="flex justify-between items-start border-b-2 border-gray-100 pb-6">
            <div>
                @if(settings('site_logo'))
                    <img src="{{ settings('site_logo') }}" alt="Logo" class="h-12 mb-2">
                @else
                    <h1 class="text-2xl font-black text-slate-800 uppercase">{{ settings('shop_name', 'ELECTROMART') }}</h1>
                @endif
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ settings('site_name') }}</p>
                <div class="mt-2 text-[10px] text-gray-600 space-y-0.5 uppercase font-bold">
                    <p>{{ settings('shop_address') }}</p>
                    <p>Phone: {{ settings('shop_phone') }} | TRN: {{ settings('shop_trn') }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Expense Report</h2>
                <p class="text-[10px] font-bold text-gray-500 mt-1">
                    Period: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}
                </p>
                <p class="text-[10px] font-bold text-gray-500 mt-0.5">
                    Generated: {{ now()->format('d M, Y h:i A') }}
                </p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; font-size: 10pt; color: black !important; }
            .max-w-full { width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .grid { display: block !important; }
            .md\:grid-cols-4 { display: flex !important; flex-wrap: wrap !important; gap: 10px !important; }
            .md\:grid-cols-4 > div { flex: 1 !important; border: 1px solid #eee !important; padding: 10px !important; box-shadow: none !important; margin-bottom: 10px; }
            .lg\:grid-cols-3 { display: flex !important; gap: 20px !important; }
            .lg\:col-span-2 { flex: 2 !important; }
            .lg\:grid-cols-3 > div:last-child { flex: 1 !important; }
            
            table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; }
            th { border-bottom: 2px solid #333 !important; color: black !important; background: #f0f0f0 !important; -webkit-print-color-adjust: exact; }
            td { border-bottom: 1px solid #eee !important; }
            .bg-slate-800 { background-color: #333 !important; color: white !important; -webkit-print-color-adjust: exact; }
            
            @page { margin: 1cm; size: auto; }
            .shadow-sm, .shadow { box-shadow: none !important; }
            .rounded-2xl, .rounded-lg { border-radius: 0 !important; }
            .border { border: 1px solid #eee !important; }
        }
    </style>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Expenses</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1">AED {{ number_format($totalExpenses, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fas fa-calculator text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Avg Expense</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1">AED {{ number_format($averageExpense, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-list-ol text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Records</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1">{{ $expenseCount }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-star text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Top Category</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1 truncate max-w-[150px]">{{ $categorySummary->isNotEmpty() ? $categorySummary->first()['name'] : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Charts and Category Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Trend Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 no-print">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-[0.2em]">Expense Trend</h3>
            </div>
            <div id="expenseChart" class="w-full h-80"></div>
        </div>

        <!-- Category Summary Table -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4">By Category</h3>
            <div class="space-y-4">
                @foreach($categorySummary as $summary)
                <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $summary['name'] }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $summary['count'] }} entries</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-900">{{ number_format($summary['total'], 2) }}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">AED</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Detailed Transaction Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Reference/Note</th>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Added By</th>
                        <th class="px-6 py-4 text-center text-[10px] font-medium uppercase tracking-widest">File</th>
                        <th class="px-6 py-4 text-right text-[10px] font-medium uppercase tracking-widest w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($expenses as $expense)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $expense->date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $expense->category->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $expense->note ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $expense->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($expense->hasMedia('attachment'))
                                <a href="{{ $expense->getFirstMediaUrl('attachment') }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-paperclip"></i>
                                </a>
                            @else
                                <span class="text-gray-200">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-slate-900 uppercase tracking-tighter">{{ number_format($expense->amount, 2) }} <span class="text-[9px] text-slate-400 ml-0.5">AED</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var options = {
        series: [{
            name: 'Expenses',
            data: {!! json_encode($chartData->values()) !!}
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            fontFamily: 'Outfit, sans-serif'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3, colors: ['#fb7185'] }, // Rose Color
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100],
                colorStops: [
                    { offset: 0, color: "#fb7185", opacity: 0.4 },
                    { offset: 100, color: "#fb7185", opacity: 0 }
                ]
            }
        },
        xaxis: {
            categories: {!! json_encode($chartData->keys()) !!},
            labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
        },
        yaxis: {
            labels: { 
                style: { colors: '#94a3b8', fontWeight: 600 },
                formatter: function (value) { return value.toLocaleString() + " AED"; }
            }
        },
        tooltip: {
            theme: 'dark',
            y: { formatter: function (val) { return val.toLocaleString() + " AED"; } }
        },
        grid: { borderColor: '#f1f5f9' }
    };

    var chart = new ApexCharts(document.querySelector("#expenseChart"), options);
    chart.render();
</script>
@endpush
