@extends('layouts.admin')

@section('header', 'Customers')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <form action="{{ route('customers.index') }}" method="GET" class="w-1/3 relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, TRN..." 
                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </form>
        
        <a href="{{ route('customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Add Customer
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">TRN (B2B)</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $customer->name }}</div>
                        <div class="text-xs text-gray-500">Since {{ $customer->created_at->format('M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><i class="fas fa-phone w-4 text-gray-400"></i> {{ $customer->phone ?? '-' }}</div>
                        <div class="text-sm text-gray-900"><i class="fas fa-envelope w-4 text-gray-400"></i> {{ $customer->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->trn_number ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                        {{ $customer->orders()->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-900 mr-3"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('customers.edit', $customer) }}" class="text-gray-600 hover:text-gray-900 mr-3"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete customer?');">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-6 text-gray-500">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">{{ $customers->links() }}</div>
    </div>
</div>
@endsection