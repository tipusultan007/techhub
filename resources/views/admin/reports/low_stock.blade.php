@extends('layouts.admin')

@section('header', 'Low Stock Alerts')

@section('content')
    <div class="space-y-6">

        {{-- Summary Card --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-red-50 p-6 rounded-2xl border border-red-100 flex items-center justify-between">
                <div>
                    <h4 class="text-red-800 font-bold text-lg mb-1">Total Alerts</h4>
                    <p class="text-3xl font-black text-red-600">{{ $totalAlerts }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Alert Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Products Requiring Attention</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                            <th class="px-6 py-4">Product Name</th>
                            <th class="px-6 py-4">SKU</th>
                            <th class="px-6 py-4 text-center">Current Stock</th>
                            <th class="px-6 py-4 text-center">Alert Limit</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($lowStockSimple as $product)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ $product->name }}
                                    <span class="block text-xs font-normal text-gray-400 mt-0.5">Simple Product</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $product->sku }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold {{ $product->stock_quantity == 0 ? 'text-red-600' : 'text-orange-600' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500">{{ $product->alert_quantity }}</td>
                                <td class="px-6 py-4">
                                    @if($product->stock_quantity == 0)
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-bold uppercase">Out of Stock</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-orange-100 text-orange-700 text-xs font-bold uppercase">Low Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs underline">
                                        Restock
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        @forelse($lowStockVariants as $variant)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ $variant->product->name }}
                                    <span class="block text-xs font-normal text-gray-500 mt-0.5">
                                        Variant: <span class="font-bold text-gray-700">{{ $variant->variant_name }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs">{{ $variant->sku }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold {{ $variant->stock_quantity == 0 ? 'text-red-600' : 'text-orange-600' }}">
                                        {{ $variant->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500">{{ $variant->alert_quantity }}</td>
                                <td class="px-6 py-4">
                                    @if($variant->stock_quantity == 0)
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-bold uppercase">Out of Stock</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-orange-100 text-orange-700 text-xs font-bold uppercase">Low Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('products.edit', $variant->product_id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs underline">
                                        Restock
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        @if($lowStockSimple->isEmpty() && $lowStockVariants->isEmpty())
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-check-circle text-4xl text-green-400 mb-3"></i>
                                        <p class="font-bold text-gray-500">All Stock Levels Healthy</p>
                                        <p class="text-xs mt-1">No products currently below their alert threshold.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
