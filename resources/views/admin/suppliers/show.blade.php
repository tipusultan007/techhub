@extends('layouts.admin')

@section('header', 'Supplier Profile')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Breadcrumb / Back -->
    <div class="mb-4">
        <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Suppliers
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left Column: Supplier Details -->
        <div class="col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center mb-6 border-b pb-6">
                    <div class="h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-3xl font-bold mx-auto mb-3">
                        {{ substr($supplier->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $supplier->name }}</h2>
                    <p class="text-gray-500">{{ $supplier->company_name }}</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">TRN Number</label>
                        <p class="font-mono text-gray-800 bg-gray-50 p-2 rounded border mt-1">
                            {{ $supplier->trn_number ?? 'Not Available' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">Email</label>
                        <p class="text-gray-800">{{ $supplier->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">Phone</label>
                        <p class="text-gray-800">{{ $supplier->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">Address</label>
                        <p class="text-gray-600 text-sm">{{ $supplier->address ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="block w-full text-center bg-gray-800 text-white py-2 rounded hover:bg-gray-700">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Right Column: Purchase History -->
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700">Recent Purchase Orders</h3>
                    <a href="{{ route('purchases.create', ['supplier_id' => $supplier->id]) }}" class="text-sm text-blue-600 hover:underline">+ New Purchase</a>
                </div>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">PO Ref</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($supplier->purchaseOrders as $order)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $order->reference_no }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">AED {{ number_format($order->total_cost, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('purchases.show', $order) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No purchase orders found for this supplier.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection