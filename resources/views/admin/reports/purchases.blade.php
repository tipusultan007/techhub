@extends('layouts.admin')

@section('header', 'Purchase Reports')

@section('content')
<div class="max-w-full">
    
    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm mb-6 no-print border border-slate-100">
        <form action="{{ route('reports.purchases') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Start Date</label>
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-100 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">End Date</label>
                <div class="relative">
                    <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-100 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-500 text-white px-8 py-2.5 rounded-xl shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i> <span>Generate Report</span>
                </button>
                <button type="button" onclick="window.print()" class="bg-slate-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-slate-800/20 hover:bg-slate-900 font-bold transition-all">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="fas fa-truck-loading text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Purchases</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">AED {{ number_format($totalPurchases, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tax Paid (VAT)</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">AED {{ number_format($totalTax, 2) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-check-double text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Received Orders</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">{{ $receivedCount }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-hourglass-half text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Orders</p>
                <p class="text-xl font-black text-slate-900 leading-none mt-1">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Purchase Transaction Registry</h3>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $purchases->count() }} Records Found</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">#</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Ref No</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Supplier</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest w-32">Tax</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest w-32">Total Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($purchases as $index => $purchase)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-[10px] font-bold text-slate-400 font-mono">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('purchases.show', $purchase) }}" class="text-sm font-black text-emerald-600 hover:underline font-mono">{{ $purchase->reference_no }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $purchase->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-900 leading-none">{{ $purchase->supplier->name }}</div>
                            <div class="text-[10px] text-slate-400 mt-1 uppercase font-bold">{{ $purchase->supplier->company_name }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'received' => 'bg-emerald-100 text-emerald-800',
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'ordered' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800'
                                ];
                                $color = $statusColors[$purchase->status] ?? 'bg-slate-100 text-slate-800';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full {{ $color }} text-[10px] font-black uppercase tracking-tighter">{{ $purchase->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-slate-500">{{ number_format($purchase->tax_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-black text-slate-900 uppercase tracking-tighter">
                            {{ number_format($purchase->total_cost, 2) }} <span class="text-[9px] text-slate-400 ml-0.5">AED</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
