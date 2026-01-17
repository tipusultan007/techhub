@extends('layouts.admin')

@section('header', 'Sales Report')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
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
            <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700 font-bold">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-xs font-bold text-gray-500 uppercase">Gross Sales</p>
            <p class="text-xl font-bold text-blue-600">AED {{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-xs font-bold text-gray-500 uppercase">Net Sales (Excl. Tax)</p>
            <p class="text-xl font-bold text-green-600">AED {{ number_format($netSales, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <p class="text-xs font-bold text-gray-500 uppercase">VAT Payable (5%)</p>
            <p class="text-xl font-bold text-red-600">AED {{ number_format($totalVAT, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Orders</p>
            <p class="text-xl font-bold text-orange-600">{{ $orders->count() }}</p>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Cashier</th>
                    <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Method</th>
                    <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">VAT</th>
                    <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 whitespace-nowrap text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-3 font-mono text-blue-600 font-bold">
                        <a href="{{ route('orders.show', $order) }}" target="_blank">{{ $order->invoice_no }}</a>
                    </td>
                    <td class="px-6 py-3">{{ $order->user->name ?? 'System' }}</td>
                    <td class="px-6 py-3 uppercase text-xs font-bold">{{ $order->payment_method }}</td>
                    <td class="px-6 py-3 text-right">{{ number_format($order->vat_amount, 2) }}</td>
                    <td class="px-6 py-3 text-right font-bold">{{ number_format($order->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection