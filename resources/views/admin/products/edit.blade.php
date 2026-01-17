@extends('layouts.admin')

@section('header', 'Edit Product')

@push('styles')
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-md p-6">
        
        <!-- Top Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Editing: {{ $product->name }}</h2>
                <p class="text-sm text-gray-500">SKU: {{ $product->type === 'simple' ? $product->sku : 'Multiple SKUs' }}</p>
            </div>
            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                <i class="fas fa-eye mr-2"></i> View Product
            </a>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')

            <!-- === SECTION 1: GENERAL INFORMATION === -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-blue-600 pl-2">General Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <!-- Brand -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Brand <span class="text-red-500">*</span></label>
                        <select name="brand_id" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white" required>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Image Handling -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Current Image</label>
                        <div class="flex items-center mt-1">
                            @if($product->hasMedia('product_image'))
                                <img src="{{ $product->getFirstMediaUrl('product_image') }}" class="h-16 w-16 object-cover rounded border mr-4">
                            @else
                                <span class="text-gray-400 text-sm italic mr-4">No image uploaded</span>
                            @endif
                            
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 mb-1">Upload New (Optional)</label>
                                <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <!-- Product Type (Read Only) -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Product Type</label>
                        <input type="text" value="{{ ucfirst($product->type) }} Product" disabled 
                            class="w-full mt-1 bg-gray-100 border border-gray-300 rounded-md p-2 text-gray-600 font-semibold cursor-not-allowed">
                        <input type="hidden" name="type" value="{{ $product->type }}">
                        <p class="text-xs text-gray-400 mt-1">Type cannot be changed after creation.</p>
                    </div>
                </div>
            </div>

            <!-- === SECTION 2: ELECTRONICS SETTINGS === -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-yellow-500 pl-2">Electronics Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-yellow-50 p-4 rounded-lg border border-yellow-100">

                    <!-- Serial Number Toggle -->
                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="has_serial_number" value="1" id="has_serial_number"
                            class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                            {{ $product->has_serial_number ? 'checked' : '' }}>
                        <label for="has_serial_number" class="ml-2 block text-sm font-bold text-gray-700">
                            Requires Serial Number Scan?
                        </label>
                    </div>

                    <!-- Warranty Duration -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Warranty Duration</label>
                        <input type="number" name="warranty_duration" value="{{ $product->warranty_duration }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2">
                    </div>

                    <!-- Warranty Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Duration Type</label>
                        <select name="warranty_type" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white">
                            <option value="">No Warranty</option>
                            <option value="days" {{ $product->warranty_type == 'days' ? 'selected' : '' }}>Days</option>
                            <option value="months" {{ $product->warranty_type == 'months' ? 'selected' : '' }}>Months</option>
                            <option value="years" {{ $product->warranty_type == 'years' ? 'selected' : '' }}>Years</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- === SECTION 3: TAX & VAT SETTINGS === -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-green-600 pl-2">Tax & VAT Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50 p-4 rounded-lg border border-green-100">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Tax Method <span class="text-red-500">*</span></label>
                        <select name="tax_method" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white">
                            <option value="inclusive" {{ $product->tax_method == 'inclusive' ? 'selected' : '' }}>Inclusive (Price includes VAT)</option>
                            <option value="exclusive" {{ $product->tax_method == 'exclusive' ? 'selected' : '' }}>Exclusive (Add VAT on top)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">VAT Rate (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate) }}" step="0.01"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2">
                    </div>
                </div>
            </div>

            <!-- === SECTION 4: DETAILED DESCRIPTION === -->
            <div class="grid grid-cols-1 gap-6 mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-0 border-l-4 border-purple-600 pl-2">Product Details</h3>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="summernote_description" class="w-full">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Specifications</label>
                    <textarea name="specifications" id="summernote_specs" class="w-full">{{ old('specifications', $product->specifications) }}</textarea>
                </div>
            </div>

            <!-- === SECTION 5: SIMPLE PRODUCT INVENTORY === -->
            @if($product->type === 'simple')
            <div id="simple_section" class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-l-4 border-blue-600 pl-2">
                    <i class="fas fa-box text-blue-600 mr-2"></i> Simple Product Inventory
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">Selling Price (AED)</label>
                        <input type="number" step="0.01" name="price" value="{{ $product->selling_price }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">Cost Price (AED)</label>
                        <input type="number" step="0.01" name="cost" value="{{ $product->cost_price }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">SKU</label>
                        <input type="text" name="sku" value="{{ $product->sku }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">Current Stock</label>
                        <input type="number" name="stock" value="{{ $product->stock_quantity }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs uppercase font-bold text-gray-500">Barcode</label>
                        <input type="text" name="barcode" value="{{ $product->barcode }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500">
                    </div>
                </div>
            </div>
            @endif

            <!-- === SECTION 6: VARIABLE PRODUCT INVENTORY === -->
            @if($product->type === 'variable')
            
            <!-- Logic to determine which attributes are used by this product -->
            @php
                // Get all unique Attribute IDs linked to this product's variants
                // E.g. [1, 2] (Color, Size)
                $usedAttributeIds = $product->variants->pluck('attributeValues')->flatten()->pluck('attribute_id')->unique();
                
                // Filter the main attributes collection to only get the ones used here
                $usedAttributes = $attributes->whereIn('id', $usedAttributeIds);
            @endphp

            <div id="variable_section" class="mb-8">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center border-l-4 border-purple-600 pl-2">
                        <i class="fas fa-layer-group text-purple-600 mr-2"></i> Product Variants
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        This product is configured with: 
                        @foreach($usedAttributes as $attr)
                            <span class="font-bold text-gray-800 bg-gray-200 px-2 py-0.5 rounded text-xs">{{ $attr->name }}</span>
                        @endforeach
                    </p>
                    
                    <!-- Hidden Selects for JS to use when adding NEW rows -->
                    @foreach ($usedAttributes as $attr)
                        <select id="values_for_{{ $attr->id }}" class="hidden">
                            @foreach ($attr->values as $val)
                                <option value="{{ $val->id }}">{{ $val->value }}</option>
                            @endforeach
                        </select>
                    @endforeach
                </div>

                <div class="overflow-x-auto border rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <!-- Dynamic Headers based on used attributes -->
                                @foreach($usedAttributes as $attr)
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-800 uppercase bg-yellow-100 border-b-2 border-yellow-200">{{ $attr->name }}</th>
                                @endforeach

                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barcode</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="variants_body" class="bg-white divide-y divide-gray-200">
                            @foreach($product->variants as $index => $variant)
                            <tr class="hover:bg-gray-50 transition">
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                
                                <!-- Attribute Selects (Pre-selected) -->
                                @foreach($usedAttributes as $attr)
                                    @php
                                        // Find which value this variant has for this attribute
                                        $selectedValueId = $variant->attributeValues->where('attribute_id', $attr->id)->first()->id ?? null;
                                    @endphp
                                    <td class="p-2 bg-yellow-50">
                                        <select name="variants[{{ $index }}][specs][{{ $attr->id }}]" class="w-full border border-gray-300 rounded p-1.5 text-sm bg-white">
                                            @foreach ($attr->values as $val)
                                                <option value="{{ $val->id }}" {{ $selectedValueId == $val->id ? 'selected' : '' }}>
                                                    {{ $val->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach

                                <td class="p-2"><input type="text" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}" class="w-full border rounded p-1.5 text-sm" required></td>
                                <td class="p-2"><input type="number" step="0.01" name="variants[{{ $index }}][cost]" value="{{ $variant->cost_price }}" class="w-full border rounded p-1.5 text-sm" required></td>
                                <td class="p-2"><input type="number" step="0.01" name="variants[{{ $index }}][price]" value="{{ $variant->selling_price }}" class="w-full border rounded p-1.5 text-sm" required></td>
                                <td class="p-2"><input type="number" name="variants[{{ $index }}][stock]" value="{{ $variant->stock_quantity }}" class="w-full border rounded p-1.5 text-sm"></td>
                                <td class="p-2"><input type="text" name="variants[{{ $index }}][barcode]" value="{{ $variant->barcode }}" class="w-full border rounded p-1.5 text-sm"></td>
                                <td class="p-2 text-center">
                                    <button type="button" class="text-red-500 hover:text-red-700 bg-red-100 p-2 rounded remove-row transition" data-id="{{ $index }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4 border-t bg-gray-50">
                        <button type="button" id="add_manual_row_btn" class="text-sm bg-slate-800 text-white px-3 py-2 rounded hover:bg-slate-700">
                            <i class="fas fa-plus mr-1"></i> Add Another Variation
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex justify-end pt-6 border-t gap-3">
                <a href="{{ route('products.index') }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium">Cancel</a>
                <button type="submit"
                    class="px-8 py-2 bg-blue-600 text-white rounded-md font-bold hover:bg-blue-700 shadow-lg transform hover:-translate-y-0.5 transition-all">
                    Update Product
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {

            // --- 1. Rich Text Editors Init ---
            let summernoteOptions = {
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            };

            $('#summernote_description').summernote({ ...summernoteOptions, placeholder: 'Write a detailed description...' });
            $('#summernote_specs').summernote({ ...summernoteOptions, placeholder: 'Insert technical specifications...' });

            // --- 2. Variable Product JS (Only runs if variable) ---
            @if($product->type === 'variable')
                
                // Start index after existing variants to avoid ID collision
                let rowIdx = {{ $product->variants->count() + 10 }}; 

                // We need the Used Attributes IDs from PHP to JS to generate correct columns
                let usedAttributeIds = @json($usedAttributeIds);

                $('#add_manual_row_btn').click(function() {
                    let cols = '';

                    // Loop through the used attributes to create select columns
                    usedAttributeIds.forEach(attrId => {
                        let optionsHtml = $(`#values_for_${attrId}`).html();
                        
                        cols += `
                        <td class="p-2 bg-yellow-50">
                            <select name="variants[${rowIdx}][specs][${attrId}]" class="w-full border border-gray-300 rounded p-1.5 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                                ${optionsHtml}
                            </select>
                        </td>`;
                    });

                    // Standard Inputs
                    cols += `
                        <td class="p-2"><input type="text" name="variants[${rowIdx}][sku]" class="w-full border rounded p-1.5 text-sm" placeholder="SKU" required></td>
                        <td class="p-2"><input type="number" step="0.01" name="variants[${rowIdx}][cost]" class="w-full border rounded p-1.5 text-sm" placeholder="0.00" required></td>
                        <td class="p-2"><input type="number" step="0.01" name="variants[${rowIdx}][price]" class="w-full border rounded p-1.5 text-sm" placeholder="0.00" required></td>
                        <td class="p-2"><input type="number" name="variants[${rowIdx}][stock]" class="w-full border rounded p-1.5 text-sm" value="0"></td>
                        <td class="p-2"><input type="text" name="variants[${rowIdx}][barcode]" class="w-full border rounded p-1.5 text-sm" placeholder="Scan"></td>
                        <td class="p-2 text-center">
                            <input type="hidden" name="variants[${rowIdx}][name]" value="Auto-Generated"> 
                            <button type="button" class="text-red-500 hover:text-red-700 bg-red-100 p-2 rounded remove-row transition"><i class="fas fa-trash"></i></button>
                        </td>
                    `;

                    $('#variants_body').append(`<tr class="border-b hover:bg-gray-50 transition">${cols}</tr>`);
                    rowIdx++;
                });

                $(document).on('click', '.remove-row', function() {
                    // In edit mode, removing the row ensures it's not sent in the request.
                    // The Controller logic typically syncs variants, so missing IDs are deleted.
                    $(this).closest('tr').remove();
                });
            @endif

        });
    </script>
@endsection