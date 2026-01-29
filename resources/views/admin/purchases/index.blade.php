@extends('layouts.admin')

@section('header', 'Purchase Orders (Stock In)')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Top Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Purchase History</h2>
        <a href="{{ route('purchases.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
            <i class="fas fa-plus mr-2"></i> New Stock In
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Total Cost</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-600">
                        {{ $order->reference_no }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $order->supplier->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                        AED {{ number_format($order->total_cost, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($order->status === 'completed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @elseif($order->status === 'partial_received')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Partial Received</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('purchases.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('purchases.edit', $order->id) }}" class="text-blue-600 hover:text-blue-800 mr-3" title="Edit Order">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('purchases.print', $order->id) }}" target="_blank" class="text-gray-500 hover:text-gray-900" title="Print Invoice">
                            <i class="fas fa-print"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No purchase orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection