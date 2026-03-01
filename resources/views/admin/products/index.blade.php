@extends('layouts.admin')

@section('header', 'Product Inventory')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Top Action Bar (Print and Add) -->
    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-full">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search Name/SKU</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name, SKU, or barcode..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

            <!-- Status -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Per Page</label>
                <select name="per_page" class="w-full border border-gray-300 rounded-lg p-2 text-sm" onchange="this.form.submit()">
                    @foreach([15, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>{{ $size }} per page</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2 lg:col-span-1">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded shadow transition text-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Top Action Bar (Print and Add) -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex items-center gap-2">
            <div class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg border border-gray-200 flex items-center gap-2 shadow-sm">
                <i class="fas fa-layer-group text-slate-400"></i>
                <span class="text-xs font-bold uppercase tracking-wider">Total Products:</span>
                <span class="text-sm font-black text-blue-600">{{ $products->total() }}</span>
            </div>
            
            @if(request()->anyFilled(['search', 'type', 'category_id', 'brand_id', 'status']))
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">(Filtered Results)</span>
            @endif
        </div>

        <div class="flex flex-wrap gap-2 justify-end w-full md:w-auto">
            <!-- Reset Button Moved Here -->
            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded shadow flex items-center text-sm">
                <i class="fas fa-undo mr-1"></i> Reset
            </a>

            <!-- Bulk Actions Dropdown -->
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="bg-slate-700 text-white px-4 py-2 rounded shadow hover:bg-slate-600 font-bold text-sm flex items-center whitespace-nowrap">
                    <i class="fas fa-tasks mr-2"></i> Bulk Actions <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>
                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <button type="button" onclick="bulkUpdateStatus('published')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-check-circle mr-2 text-green-500"></i> Mark as Published
                        </button>
                        <button type="button" onclick="bulkUpdateStatus('draft')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-slate-500"></i> Mark as Draft
                        </button>
                        <div class="border-t border-gray-100"></div>
                        <button type="button" onclick="bulkDelete()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center font-semibold">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Selected
                        </button>
                    </div>
                </div>
            </div>

            <!-- Barcode Size Selection -->
            <div class="flex items-center gap-2">
                <select name="barcode_size" form="barcode-form" class="bg-white border border-gray-300 rounded shadow-sm px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="1x0.375">1" x 0.375"</option>
                    <option value="48.5x25.4" selected>48.5mm x 25.4mm</option>
                    <option value="2x1">2" x 1"</option>
                </select>
                <!-- Print Labels Button -->
                <button type="button" onclick="document.getElementById('barcode-form').submit();" class="bg-slate-800 text-white px-4 py-2 rounded shadow hover:bg-slate-700 font-bold text-sm whitespace-nowrap">
                    <i class="fas fa-barcode mr-2"></i> Print Labels
                </button>
            </div>
            
            <!-- Import Button -->
            <a href="{{ route('products.import.form') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow flex items-center text-sm">
                <i class="fas fa-file-excel mr-2"></i> Import Excel
            </a>

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
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
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

                                <!-- Status Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product->status === 'draft')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">Draft</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
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

        function getSelectedIds() {
            let ids = [];
            document.querySelectorAll('.product-checkbox:checked').forEach(checkbox => {
                ids.push(checkbox.value);
            });
            return ids;
        }

        function bulkDelete() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire('No selection', 'Please select at least one product.', 'info');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${ids.length} products!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete selected!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post("{{ route('products.bulk_delete') }}", {
                        ids: ids
                    }).then(response => {
                        if (response.data.success) {
                            let skippedMsg = '';
                            if (response.data.skipped && response.data.skipped.length > 0) {
                                skippedMsg = '<div class="mt-4 text-left"><p class="font-bold text-red-600">The following products were skipped because they are linked to orders or purchases:</p><ul class="list-disc list-inside text-sm text-gray-700 max-h-40 overflow-y-auto mt-2">' + 
                                    response.data.skipped.map(name => `<li>${name}</li>`).join('') + 
                                    '</ul></div>';
                            }

                            Swal.fire({
                                title: 'Bulk Delete Result',
                                html: `<p>${response.data.message}</p>${skippedMsg}`,
                                icon: response.data.skipped.length > 0 ? 'warning' : 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    }).catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Something went wrong while deleting products.', 'error');
                    });
                }
            })
        }

        function bulkUpdateStatus(status) {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire('No selection', 'Please select at least one product.', 'info');
                return;
            }

            Swal.fire({
                title: 'Confirm status update?',
                text: `You are about to update the status of ${ids.length} products to "${status}".`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, update status'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post("{{ route('products.bulk_update_status') }}", {
                        ids: ids,
                        status: status
                    }).then(response => {
                        if (response.data.success) {
                            Swal.fire('Updated!', response.data.message, 'success').then(() => {
                                window.location.reload();
                            });
                        }
                    }).catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Something went wrong while updating product status.', 'error');
                    });
                }
            })
        }
    </script>
@endsection