@extends('layouts.admin')

@section('header', 'Sales by Sales Person (POS)')

@section('content')
<div class="max-w-full">
    
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 no-print">
        <form action="{{ route('reports.sales-by-person') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border rounded p-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border rounded p-2 text-sm w-full">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Sales Person</label>
                <select name="user_id" class="border rounded p-2 text-sm w-full bg-white">
                    <option value="">All Staff</option>
                    @foreach($salesPeople as $person)
                        <option value="{{ $person->id }}" {{ request('user_id') == $person->id ? 'selected' : '' }}>{{ $person->name }}</option>
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
                <a href="{{ route('reports.sales-by-person.pdf', request()->all()) }}" class="bg-red-600 text-white px-6 py-2 rounded shadow hover:bg-red-700 font-bold">
                    <i class="fas fa-file-pdf mr-2"></i> PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Sales (POS)</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1">AED {{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Orders</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1">{{ $totalOrders }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-user-tie text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Top Salesperson</p>
                <p class="text-xl font-bold text-slate-900 leading-none mt-1 truncate max-w-[150px]">{{ $salesByPerson->isNotEmpty() ? $salesByPerson->first()['name'] : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Salesperson Performance Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100 mb-6">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Staff Performance Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Sales Person</th>
                        <th class="px-6 py-4 text-center text-[10px] font-medium uppercase tracking-widest">Order Count</th>
                        <th class="px-6 py-4 text-center text-[10px] font-medium uppercase tracking-widest">Avg Order Value</th>
                        <th class="px-6 py-4 text-right text-[10px] font-medium uppercase tracking-widest">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($salesByPerson as $data)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $data['name'] }}</td>
                        <td class="px-6 py-4 text-center text-sm text-slate-600">{{ $data['count'] }}</td>
                        <td class="px-6 py-4 text-center text-sm text-slate-600">AED {{ number_format($data['avg'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-slate-900 uppercase tracking-tighter">
                            {{ number_format($data['total'], 2) }} <span class="text-[9px] text-slate-400 ml-0.5">AED</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Transaction Log (POS Only)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Invoice</th>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Customer</th>
                        <th class="px-6 py-4 text-left text-[10px] font-medium uppercase tracking-widest">Staff</th>
                        <th class="px-6 py-4 text-right text-[10px] font-medium uppercase tracking-widest w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ $order->invoice_no }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $order->user->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-slate-900 uppercase tracking-tighter">
                            {{ number_format($order->total, 2) }} <span class="text-[9px] text-slate-400 ml-0.5">AED</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        body { background: white !important; font-size: 10pt; color: black !important; }
        .max-w-full { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .grid { display: block !important; }
        .md\:grid-cols-3 { display: flex !important; flex-wrap: wrap !important; gap: 10px !important; }
        .md\:grid-cols-3 > div { flex: 1 !important; border: 1px solid #eee !important; padding: 10px !important; box-shadow: none !important; margin-bottom: 10px; }
        
        table { width: 100% !important; border-collapse: collapse !important; margin-top: 20px !important; }
        th { border-bottom: 2px solid #333 !important; color: black !important; background: #f0f0f0 !important; -webkit-print-color-adjust: exact; }
        td { border-bottom: 1px solid #eee !important; }
        .bg-slate-800 { background-color: #333 !important; color: white !important; -webkit-print-color-adjust: exact; }
        
        @page { margin: 1cm; size: auto; }
        .shadow-sm, .shadow { box-shadow: none !important; }
        .rounded-2xl, .rounded-lg { border-radius: 0 !important; }
    }
</style>
@endpush
