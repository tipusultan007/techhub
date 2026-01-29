@extends('layouts.admin')

@section('header', 'Sales Report')

@section('content')
<div class="max-w-full">
    
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 no-print">
        <form action="{{ route('reports.sales') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border rounded p-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border rounded p-2 text-sm w-full">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 font-bold">
                <i class="fas fa-filter mr-2"></i> Filter Report
            </button>
            <div class="flex gap-2">
                <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700 font-bold">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <a href="{{ route('reports.sales.pdf', request()->all()) }}" class="bg-red-600 text-white px-6 py-2 rounded shadow hover:bg-red-700 font-bold">
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
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Sales Report</h2>
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
            body { background: white !important; font-size: 10pt; }
            .max-w-7xl { max-width: 100% !important; margin: 0 !important; width: 100% !important; padding: 0 !important; }
            .grid { display: flex !important; flex-wrap: wrap !important; gap: 10px !important; }
            .grid > div { flex: 1 !important; border: 1px solid #e2e8f0 !important; box-shadow: none !important; }
            table { font-size: 9pt !important; }
            th { background-color: #f8fafc !important; color: #475569 !important; border-bottom: 2px solid #e2e8f0 !important; }
            @page { margin: 1.5cm; }
        }
    </style>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fas fa-cash-register text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gross Sales</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">AED {{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Net Sales</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">AED {{ number_format($netSales, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i class="fas fa-file-invoice-dollar text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">VAT (5%)</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">AED {{ number_format($totalVAT, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Orders</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">{{ $orders->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-6 no-print">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Sales Performance Trend</h3>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-full">Daily Volume</span>
        </div>
        <div id="salesChart" class="w-full h-80"></div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Detailed Transaction Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">#</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Invoice</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Cashier</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest w-24">Method</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest w-32">VAT</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest w-32">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($orders as $index => $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-[10px] font-bold text-slate-400 font-mono">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('orders.show', $order) }}" target="_blank" class="text-sm font-black text-blue-600 hover:underline font-mono">{{ $order->invoice_no }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700 font-medium">{{ $order->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full bg-slate-100 text-[10px] font-black text-slate-600 uppercase tracking-tighter">{{ $order->payment_method }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-slate-500">{{ number_format($order->vat_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-black text-slate-900 uppercase tracking-tighter">{{ number_format($order->total, 2) }} <span class="text-[9px] text-slate-400 ml-0.5">AED</span></td>
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
            name: 'Daily Sales',
            data: {!! json_encode($chartData->values()) !!}
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            fontFamily: 'Outfit, sans-serif'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3, colors: ['#2dae9a'] },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100],
                colorStops: [
                    { offset: 0, color: "#2dae9a", opacity: 0.4 },
                    { offset: 100, color: "#2dae9a", opacity: 0 }
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

    var chart = new ApexCharts(document.querySelector("#salesChart"), options);
    chart.render();
</script>
@endpush