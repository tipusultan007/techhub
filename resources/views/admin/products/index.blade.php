@extends('layouts.admin')

@section('header', 'Product Inventory')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Top Action Bar (Print and Add) -->
    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search Name/SKU</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </div>

            <!-- Type -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">All Types</option>
                    <option value="simple" {{ request('type') == 'simple' ? 'selected' : '' }}>Simple</option>
                    <option value="variable" {{ request('type') == 'variable' ? 'selected' : '' }}>Variable</option>
                    <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Service</option>
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Brand</label>
                <select name="brand_id" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow transition text-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('products.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 rounded text-center transition text-sm">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Top Action Bar (Print and Add) -->
    <div class="flex flex-col md:flex-row justify-end items-center mb-6 gap-4">
        <div class="flex gap-2">
            <!-- Print Labels Button -->
            <button type="button" onclick="document.getElementById('barcode-form').submit();" class="bg-slate-800 text-white px-4 py-2 rounded shadow hover:bg-slate-700 font-bold text-sm">
                <i class="fas fa-barcode mr-2"></i> Print Labels
            </button>
            
            <!-- Add Product Link -->
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center text-sm">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Data Table (No longer wrapped in a form) -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <!-- Form for barcode printing is now separate -->
            <form action="{{ route('products.print_barcodes') }}" method="POST" target="_blank" id="barcode-form">
                @csrf
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 w-12"><input type="checkbox" id="select-all"></th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Category / Brand</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Price (AED)</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="product-checkbox">
                                </td>
                                
                                <!-- Product Name & Image -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if ($product->hasMedia('product_image'))
                                                <img class="h-10 w-10 rounded object-cover border" src="{{ $product->getFirstMediaUrl('product_image') }}">
                                            @else
                                                <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center text-gray-400 border"><i class="fas fa-image"></i></div>
                                            @endif
                                        </div>
                                        <div class="ml-4 max-w-xs xl:max-w-md">
                                            <div class="text-sm font-medium text-gray-900 break-words whitespace-normal">{{ $product->name }}</div>
                                            @if ($product->type === 'simple')
                                                <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                                            @else
                                                <div class="text-xs text-gray-500">{{ $product->variants->count() }} Variants</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Type Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product->type === 'simple')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Simple</span>
                                    @elseif ($product->type === 'service')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Service</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Variable</span>
                                    @endif
                                </td>

                                <!-- Category & Brand -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $product->category->name ?? '-' }}</div>
                                    <div class="text-xs">{{ $product->brand->name ?? '-' }}</div>
                                </td>

                                <!-- Price -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                    @if ($product->type === 'simple')
                                        {{ number_format($product->selling_price, 2) }}
                                    @else
                                        <span class="text-xs text-gray-500">From</span> {{ number_format($product->variants->min('selling_price'), 2) }}
                                    @endif
                                </td>

                                <!-- Stock -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $totalStock = $product->type === 'simple' ? $product->stock_quantity : $product->variants->sum('stock_quantity');
                                    @endphp
                                    @if ($product->type === 'service')
                                        <span class="text-gray-400 font-bold text-xs bg-gray-100 px-2 py-1 rounded">SERVICE ITEM</span>
                                    @elseif ($totalStock == 0)
                                        <span class="text-red-600 font-bold text-xs bg-red-100 px-2 py-1 rounded">OUT OF STOCK</span>
                                    @elseif($totalStock < 10)
                                        <span class="text-orange-600 font-bold text-sm">{{ $totalStock }}</span>
                                    @else
                                        <span class="text-green-600 font-bold text-sm">{{ $totalStock }}</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center space-x-4">
                                        <a href="{{ route('products.show', $product) }}" class="text-gray-500 hover:text-blue-600" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit"><i class="fas fa-edit"></i></a>
                                        
                                        <!-- FIX: Changed to a button with onclick -->
                                        <button type="button" onclick="confirmDelete({{ $product->id }})" class="text-red-500 hover:text-red-700" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 bg-gray-50">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form> <!-- Barcode form now wraps only the table -->
        </div>

        <!-- Pagination -->
        @if (method_exists($products, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-b-lg shadow">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- FIX: Hidden Delete Forms (One for each product) -->
    @foreach($products as $product)
        <form id="delete-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>
@endsection

@section('scripts')
    <script>
        // Checkbox "Select All" logic
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.querySelectorAll('.product-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }

        // FIX: SweetAlert2 Confirmation Logic
        function confirmDelete(productId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Find the hidden form for this product and submit it
                    document.getElementById('delete-form-' + productId).submit();
                }
            })
        }
    </script>
@endsection