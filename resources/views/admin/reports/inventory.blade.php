@extends('layouts.admin')

@section('header', 'Stock Valuation Report')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Summary -->
    <div class="bg-white p-6 rounded-lg shadow mb-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-700">Total Asset Value (Cost Price)</h3>
            <p class="text-sm text-gray-500">Based on current stock and unit cost.</p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-bold text-green-600">AED {{ number_format($grandTotalValue, 2) }}</p>
            <p class="text-sm font-bold text-gray-600">{{ $totalItems }} Total Units</p>
        </div>
    </div>

    <!-- Simple Products Table -->
    <div class="mb-8">
        <h3 class="font-bold text-gray-800 mb-2 text-lg">Simple Products Inventory</h3>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($simpleProducts as $p)
                    <tr>
                        <td class="px-6 py-3 font-bold text-gray-800">{{ $p->name }}</td>
                        <td class="px-6 py-3 font-mono text-gray-500">{{ $p->sku }}</td>
                        <td class="px-6 py-3 text-right {{ $p->stock_quantity <= 5 ? 'text-red-600 font-bold' : '' }}">{{ $p->stock_quantity }}</td>
                        <td class="px-6 py-3 text-right">{{ number_format($p->cost_price, 2) }}</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-800">{{ number_format($p->stock_quantity * $p->cost_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Variable Products Table -->
    <div>
        <h3 class="font-bold text-gray-800 mb-2 text-lg">Variable Products Inventory</h3>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Product Name</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Variant</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Unit Cost</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($variants as $v)
                    <tr>
                        <td class="px-6 py-3 font-bold text-gray-800">{{ $v->product->name }}</td>
                        <td class="px-6 py-3 text-blue-600">{{ $v->variant_name }}</td>
                        <td class="px-6 py-3 font-mono text-gray-500">{{ $v->sku }}</td>
                        <td class="px-6 py-3 text-right {{ $v->stock_quantity <= 5 ? 'text-red-600 font-bold' : '' }}">{{ $v->stock_quantity }}</td>
                        <td class="px-6 py-3 text-right">{{ number_format($v->cost_price, 2) }}</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-800">{{ number_format($v->stock_quantity * $v->cost_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection