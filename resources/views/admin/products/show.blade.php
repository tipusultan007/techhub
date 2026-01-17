@extends('layouts.admin')

@section('header', 'Product Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
        </a>
        <div class="flex space-x-3">
            <a href="{{ route('products.edit', $product) }}" class="bg-indigo-600 text-white px-5 py-2 rounded shadow hover:bg-indigo-700 font-bold transition">
                <i class="fas fa-edit mr-2"></i> Edit Product
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure? This will delete the product and all variant history.');">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded shadow hover:bg-red-700 font-bold transition">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        <div class="md:flex">
            <!-- Left Column: Image & Quick Stats -->
            <div class="md:w-1/3 bg-gray-50 p-6 border-r border-gray-200">
                <div class="flex justify-center mb-6">
                    @if($product->hasMedia('product_image'))
                        <img src="{{ $product->getFirstMediaUrl('product_image') }}" class="max-h-80 w-auto object-contain rounded shadow-sm border bg-white p-2">
                    @else
                        <div class="h-64 w-64 bg-gray-200 rounded flex flex-col items-center justify-center text-gray-400 border border-dashed border-gray-400">
                            <i class="fas fa-image fa-4x mb-2"></i>
                            <span>No Image</span>
                        </div>
                    @endif
                </div>

                <!-- Quick Tax Info -->
                <div class="bg-white p-4 rounded border shadow-sm mb-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Tax Information</h4>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Method:</span>
                        <span class="font-bold capitalize">{{ $product->tax_method }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>VAT Rate:</span>
                        <span class="font-bold text-blue-600">{{ $product->tax_rate }}%</span>
                    </div>
                </div>

                <!-- Total Stock Summary -->
                <div class="bg-white p-4 rounded border shadow-sm">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Total Inventory</h4>
                    @php
                        $totalStock = ($product->type === 'simple') ? $product->stock_quantity : $product->variants->sum('stock_quantity');
                    @endphp
                    <div class="text-center">
                        <span class="text-4xl font-bold {{ $totalStock < 10 ? 'text-orange-500' : 'text-green-600' }}">
                            {{ $totalStock }}
                        </span>
                        <span class="block text-xs text-gray-400 mt-1">Units Available</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details -->
            <div class="md:w-2/3 p-8">
                <div class="flex justify-between items-start border-b pb-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                        <div class="mt-2 space-x-2">
                            <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 border">
                                Brand: {{ $product->brand->name ?? 'N/A' }}
                            </span>
                            <span class="inline-block bg-blue-50 rounded-full px-3 py-1 text-sm font-semibold text-blue-700 border border-blue-100">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-xs text-gray-500 uppercase font-bold">Type</span>
                        <span class="inline-block px-3 py-1 rounded bg-purple-100 text-purple-800 text-xs font-bold uppercase mt-1">
                            {{ $product->type }}
                        </span>
                    </div>
                </div>

                <!-- Simple Product Specifics -->
                @if($product->type === 'simple')
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-blue-50 p-4 rounded border border-blue-100 mb-8">
                    <div>
                        <span class="block text-xs text-gray-500 uppercase font-bold">Selling Price</span>
                        <span class="text-xl font-bold text-gray-900">AED {{ number_format($product->selling_price, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase font-bold">Cost Price</span>
                        <span class="text-lg font-medium text-gray-600">AED {{ number_format($product->cost_price, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase font-bold">SKU</span>
                        <span class="text-sm font-mono text-gray-800">{{ $product->sku }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500 uppercase font-bold">Barcode</span>
                        <span class="text-sm font-mono text-gray-800">{{ $product->barcode ?? '-' }}</span>
                    </div>
                </div>
                @endif

                <!-- Variable Product Table -->
                @if($product->type === 'variable')
                <div class="mb-8">
                    <h3 class="font-bold text-gray-800 text-lg mb-3">Product Variants</h3>
                    <div class="overflow-hidden border rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Variant</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Price</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->variants as $variant)
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $variant->variant_name }}</td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-500">{{ $variant->sku }}</td>
                                    <td class="px-4 py-2 text-sm font-bold">AED {{ number_format($variant->selling_price, 2) }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($variant->stock_quantity == 0)
                                            <span class="text-red-500 font-bold text-xs bg-red-100 px-2 py-1 rounded">Out of Stock</span>
                                        @else
                                            <span class="text-green-700 font-bold">{{ $variant->stock_quantity }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Rich Text Content (Description) -->
                <div class="mb-8">
                    <h3 class="font-bold text-gray-800 text-lg border-b pb-2 mb-4">Description</h3>
                    <div class="summernote-output text-gray-600 leading-relaxed text-sm">
                        {!! $product->description !!}
                    </div>
                </div>

                <!-- Rich Text Content (Specs) -->
                @if($product->specifications)
                <div>
                    <h3 class="font-bold text-gray-800 text-lg border-b pb-2 mb-4">Technical Specifications</h3>
                    <div class="summernote-output text-gray-600 leading-relaxed text-sm">
                        {!! $product->specifications !!}
                    </div>
                </div>
                @endif
                
                <div class="mt-8 pt-4 border-t text-xs text-gray-400 flex justify-between">
                    <span>Created: {{ $product->created_at->format('F d, Y H:i') }}</span>
                    <span>Last Updated: {{ $product->updated_at->format('F d, Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for Summernote HTML Output to look good in Tailwind */
    .summernote-output ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
    .summernote-output ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
    .summernote-output table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    .summernote-output th, .summernote-output td { border: 1px solid #e5e7eb; padding: 0.5rem; text-align: left; }
    .summernote-output th { background-color: #f9fafb; font-weight: bold; }
    .summernote-output h1, .summernote-output h2, .summernote-output h3 { font-weight: bold; margin-top: 1rem; margin-bottom: 0.5rem; color: #1f2937; }
</style>
@endsection