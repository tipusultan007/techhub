@extends('layouts.admin')

@section('header', 'Returns & Credit Notes')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Return History</h2>
        <a href="{{ route('returns.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
            <i class="fas fa-plus mr-2"></i> New Return
        </a>
    </div>

    <!-- Returns Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Credit Note #</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Original Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Refund Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Processed By</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($returns as $return)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-blue-600 font-bold">{{ $return->credit_note_no }}</td>
                    <td class="px-6 py-4 font-mono text-gray-600">
                        <a href="{{ route('orders.show', $return->originalOrder) }}" class="hover:underline">
                            {{ $return->originalOrder->invoice_no }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $return->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-red-600">- AED {{ number_format($return->total_refund, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $return->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('returns.show', $return) }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">No returns have been processed yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t bg-white">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection