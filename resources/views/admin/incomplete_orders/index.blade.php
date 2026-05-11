@extends('layouts.admin')

@section('header', 'Abandoned / Incomplete Orders')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-700">Payment Attempts</h3>
            <p class="text-xs text-gray-500">Trace users who didn't finish their payment</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="px-6 py-4 border-b bg-white">
        <form action="{{ route('incomplete-orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Invoice / Reference</label>
                <input type="text" name="invoice_no" value="{{ request('invoice_no') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="INV-XXXXX">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Abandoned)</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Ordered)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="md:col-span-4 flex justify-end gap-2">
                <a href="{{ route('incomplete-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300 transition">
                    Reset
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm font-bold hover:bg-blue-700 transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Invoice Ref</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer / IP</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                    {{ $order->invoice_no }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-bold text-gray-900">
                        {{ $order->customer_data['first_name'] ?? '' }} {{ $order->customer_data['last_name'] ?? '' }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $order->customer_data['email'] ?? 'N/A' }}</div>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="text-[10px] bg-gray-100 px-1 rounded text-gray-600 border" title="Customer IP">C: {{ $order->customer_ip }}</span>
                        <span class="text-[10px] bg-blue-50 px-1 rounded text-blue-600 border border-blue-100" title="Visitor IP">V: {{ $order->visitor_ip }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                    {{ number_format($order->total, 2) }} AED
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($order->status == 'completed')
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700">
                            Ordered
                        </span>
                        <div class="mt-1 text-[10px] text-gray-400">ID: #{{ $order->order_id }}</div>
                    @else
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-700">
                            Abandoned
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $order->created_at->format('d M Y, h:i A') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('incomplete-orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                        <i class="fas fa-eye"></i> Details
                    </a>
                    
                    @if(auth()->user()->hasRole('Super Admin'))
                    <form action="{{ route('incomplete-orders.destroy', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this trace?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    No abandoned payment attempts found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">
        {{ $orders->links() }}
    </div>
</div>
@endsection
