@extends('layouts.admin')

@section('header', 'Customer Profile')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex text-gray-500 text-sm mb-4" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="{{ route('customers.index') }}" class="hover:text-blue-600">Customers</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
            </li>
            <li class="flex items-center text-gray-900 font-bold">
                {{ $customer->name }}
            </li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center font-bold text-3xl text-blue-600 shadow-inner">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $customer->name }}</h1>
                <p class="text-gray-500 flex items-center gap-2 mt-1">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 uppercase tracking-wider">Customer</span>
                    <span class="text-xs font-medium">Joined {{ $customer->created_at->format('d M, Y') }}</span>
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('customers.edit', $customer) }}" class="flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-edit"></i> Edit Details
            </a>
            <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Archive this customer?');">
                @csrf @method('DELETE')
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-red-50 text-red-600 border border-red-100 rounded-xl font-bold hover:bg-red-100 transition-all shadow-sm">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Contact Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Contact Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Email Address</p>
                            <p class="text-sm font-bold text-gray-900">{{ $customer->email ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Phone Number</p>
                            <p class="text-sm font-bold text-gray-900">{{ $customer->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Billing Address</p>
                            <p class="text-sm font-bold text-gray-900 leading-relaxed">{{ $customer->address ?? 'No address set' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- B2B Details -->
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-lg border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-white/5">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-building text-blue-400"></i> B2B Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Tax Registration (TRN)</p>
                            <p class="text-xl font-extrabold text-white mt-1">{{ $customer->trn_number ?? 'REGULAR' }}</p>
                        </div>
                        @if($customer->trn_number)
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                            <i class="fas fa-check-circle text-blue-400 text-xl"></i>
                        </div>
                        @else
                        <span class="px-3 py-1 bg-slate-700 text-slate-400 rounded-lg text-[0.65rem] font-bold uppercase tracking-wider">Retail Customer</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Stats & Orders -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl shadow-inner">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Orders</p>
                        <h4 class="text-2xl font-black text-gray-900">{{ $customer->orders()->count() }}</h4>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl shadow-inner">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Lifetime Value</p>
                        <h4 class="text-2xl font-black text-gray-900">{{ number_format($customer->orders()->sum('total'), 2) }} <span class="text-sm font-bold text-gray-400 uppercase">AED</span></h4>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-900 tracking-tight">Recent Sales History</h3>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Showing Last 10</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Order Ref</th>
                                <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Date</th>
                                <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-8 py-4 text-right text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Total Value</th>
                                <th class="px-8 py-4 text-right text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($customer->orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-black text-blue-600 tracking-tight">#{{ $order->invoice_no }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">{{ $order->created_at->format('d M, Y') }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                            'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        ];
                                        $class = $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[0.65rem] font-extrabold uppercase tracking-widest border {{ $class }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right font-black text-gray-900 text-sm">
                                    {{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('orders.show', $order) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                            <i class="fas fa-ghost text-2xl text-gray-200"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-400">This customer hasn't placed any orders yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
