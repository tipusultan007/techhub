@extends('layouts.admin')

@section('header', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- === STATS CARDS === -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Card 1: Daily Sales -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl shadow-inner">
                <i class="fas fa-cash-register"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Daily Revenue</p>
                <h4 class="text-2xl font-black text-gray-900 leading-none"> {{ number_format($dailySales, 2) }}</h4>
            </div>
        </div>

        <!-- Card 2: Monthly Sales -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-[#2dae9a] text-2xl shadow-inner">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Monthly Goal</p>
                <h4 class="text-2xl font-black text-gray-900 leading-none"> {{ number_format($monthlySales, 2) }}</h4>
            </div>
        </div>

        <!-- Card 3: Total Orders -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-[#2dae9a] text-2xl shadow-inner">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Total Orders</p>
                <h4 class="text-2xl font-black text-gray-900 leading-none">{{ number_format($totalOrders) }}</h4>
            </div>
        </div>

        <!-- Card 4: Customers -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 text-2xl shadow-inner">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Customer CRM</p>
                <h4 class="text-2xl font-black text-gray-900 leading-none">{{ number_format($totalCustomers) }}</h4>
            </div>
        </div>
    </div>

    <!-- === SECONDARY STATS === -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Low Stock Alert -->
        <a href="{{ route('reports.low-stock') }}" class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5 hover:border-red-300 transition-all group">
            <div class="w-14 h-14 {{ $lowStockCount > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }} rounded-2xl flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Low Stock Alerts</p>
                <h4 class="text-2xl font-black text-gray-900 leading-none">{{ number_format($lowStockCount) }} Items</h4>
            </div>
        </a>

        <!-- Pending Quotations -->
        <a href="{{ route('quotations.index', ['status' => 'submitted']) }}" class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5 hover:border-blue-300 transition-all group">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Pending Quotations</p>
                <h4 class="text-2xl font-black text-gray-900 leading-none">{{ number_format($pendingQuotationsCount) }}</h4>
            </div>
        </a>

        <!-- Top Selling SKU -->
        <a href="{{ $topSellingSku != 'N/A' ? route('products.index', ['search' => $topSellingSku]) : '#' }}" class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-5 hover:border-amber-300 transition-all group">
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 text-2xl shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas fa-crown"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Top SKU (Last 7D)</p>
                <h4 class="text-lg font-black text-gray-900 leading-none truncate max-w-[150px]">{{ $topSellingSku }}</h4>
            </div>
        </a>
    </div>

    <!-- === MAIN CHARTS SECTION === -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Revenue Over Time</h3>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Last 30 Days</span>
            </div>
            <div id="revenueChart" style="min-height: 350px;"></div>
        </div>

        <!-- Channel Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Sales Channels</h3>
            </div>
            <div id="channelDonut" style="min-height: 350px;"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Sales Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight text-left">Top Selling Categories</h3>
            </div>
            <div id="categoryChart" style="min-height: 300px;"></div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Recent Transactions</h3>
                <a href="{{ route('orders.index') }}" class="text-xs font-bold text-[#2dae9a] hover:text-emerald-700 uppercase tracking-widest">View History</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Invoice</th>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Customer</th>
                            <th class="px-8 py-4 text-right text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Total</th>
                            <th class="px-8 py-4 text-right text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-4 text-sm font-black text-[#2dae9a]">{{ $order->invoice_no }}</td>
                            <td class="px-8 py-4 text-sm font-bold text-gray-900">{{ $order->customer_name }}</td>
                            <td class="px-8 py-4 text-sm text-right font-black text-gray-900">{{ number_format($order->total, 2) }}</td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-[#2dae9a] hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-sm text-gray-500 font-bold">No recent data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- === LOW STOCK ALERTS === -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-5 border-b border-red-50 bg-red-50/30 flex justify-between items-center">
            <h3 class="text-lg font-bold text-red-700 tracking-tight flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500"></i> Stock Utilization Warning
            </h3>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 uppercase tracking-widest">Restock Request</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-white">
                    <tr>
                        <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Asset / SKU</th>
                        <th class="px-8 py-4 text-right text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Available Reserve</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($lowStockSimple as $product)
                    <tr class="hover:bg-red-50/20 transition-colors">
                        <td class="px-8 py-4 text-sm font-bold text-gray-900">{{ $product->name }}</td>
                        <td class="px-8 py-4 text-sm text-right font-black text-red-600">{{ $product->stock_quantity }} units</td>
                    </tr>
                    @endforeach
                    @foreach($lowStockVariants as $variant)
                    <tr class="hover:bg-red-50/20 transition-colors">
                        <td class="px-8 py-4">
                            <span class="text-sm font-bold text-gray-900 block">{{ $variant->product->name }}</span>
                            <span class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">{{ $variant->variant_name }}</span>
                        </td>
                        <td class="px-8 py-4 text-sm text-right font-black text-red-600">{{ $variant->stock_quantity }} units</td>
                    </tr>
                    @endforeach
                    @if($lowStockSimple->isEmpty() && $lowStockVariants->isEmpty())
                    <tr>
                        <td colspan="2" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-shield-check text-emerald-400 text-3xl"></i>
                                <p class="text-sm font-bold text-gray-400">All inventory levels currently within safe parameters.</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // 1. Revenue Chart
    const revenueLabels = {!! json_encode($chartRevenueLabels) !!};
    const revenueTotals = {!! json_encode($chartRevenueTotals) !!};

    var revenueOptions = {
        series: [{ name: 'Revenue', data: revenueTotals }],
        chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Outfit' },
        colors: ['#2dae9a'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] } },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        xaxis: { categories: revenueLabels, labels: { style: { colors: '#94a3b8', fontWeight: 600 } } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontWeight: 600 } } },
        tooltip: { theme: 'light', y: { formatter: (v) => v + " AED" } }
    };
    new ApexCharts(document.querySelector("#revenueChart"), revenueOptions).render();

    // 2. Channel Donut
    var donutOptions = {
        series: {!! json_encode($chartChannelData) !!},
        labels: {!! json_encode($chartChannelLabels) !!},
        chart: { type: 'donut', height: 350, fontFamily: 'Outfit' },
        colors: ['#6366f1', '#10b981'],
        legend: { position: 'bottom', fontWeight: 600 },
        dataLabels: { enabled: false },
        plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'TOTAL ORDERS', fontWeight: 800 } } } } }
    };
    new ApexCharts(document.querySelector("#channelDonut"), donutOptions).render();

    // 3. Category Bar Chart
    var categoryOptions = {
        series: [{ name: 'Sales', data: {!! json_encode($chartCategoryTotals) !!} }],
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Outfit' },
        plotOptions: { bar: { borderRadius: 8, distributed: true, columnWidth: '50%' } },
        colors: ['#6366f1', '#f43f5e', '#10b981', '#f59e0b', '#06b6d4'],
        xaxis: { categories: {!! json_encode($chartCategoryLabels) !!}, labels: { style: { fontWeight: 700 } } },
        legend: { show: false },
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector("#categoryChart"), categoryOptions).render();
</script>
@endpush
@endsection