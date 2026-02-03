@extends('layouts.admin')

@section('header', 'Suppliers List')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Top Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Suppliers</h2>
        <a href="{{ route('suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
            <i class="fas fa-plus mr-2"></i> Add Supplier
        </a>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Supplier Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Company & TRN</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Info</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $supplier->name }}</div>
                        <div class="text-xs text-gray-500">Since {{ $supplier->created_at->format('M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $supplier->company_name ?? '-' }}</div>
                        @if($supplier->trn_number)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                TRN: {{ $supplier->trn_number }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">No TRN</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><i class="fas fa-envelope w-4 text-gray-400"></i> {{ $supplier->email ?? '-' }}</div>
                        <div class="text-sm text-gray-900"><i class="fas fa-phone w-4 text-gray-400"></i> {{ $supplier->phone ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-gray-500 hover:text-blue-600 mr-3" title="View Profile">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 btn-delete-confirm"
                                title="Delete"
                                data-title="Delete Supplier: {{ $supplier->name }}?"
                                data-type="Supplier"
                                data-summary='{"Purchase Orders": "{{ $supplier->purchaseOrders()->count() }}"}'
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                        No suppliers found. Add your first supplier to start purchasing stock.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection