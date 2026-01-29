@extends('layouts.admin')

@section('header', 'Add New Product')

@push('styles')
    <!-- Summernote CSS for Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf

            <!-- === SECTION 1: GENERAL INFORMATION === -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-blue-600 pl-2">General Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. iPhone 15 Pro Max" required>
                    </div>

                    <!-- Brand -->
                    <div id="brand_field_container">
                        <label class="block text-sm font-bold text-gray-700">Brand <span class="text-red-500">*</span></label>
                        <select name="brand_id" id="brand_input" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white" required>
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Image -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Product Image</label>
                        <input type="file" name="image"
                            class="w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- Product Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Product Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type_selector"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-gray-50 font-semibold border-blue-200">
                            <option value="simple">Simple Product (Single Item)</option>
                            <option value="variable">Variable Product (Sizes/Colors)</option>
                            <option value="service">Service (Installation, Support, etc.)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- === SECTION 2: ELECTRONICS SETTINGS === -->
            <div id="electronics_section" class="mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-yellow-500 pl-2">Electronics Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-yellow-50 p-4 rounded-lg border border-yellow-100">

                    <!-- Serial Number Toggle -->
                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="has_serial_number" value="1" id="has_serial_number"
                            class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <label for="has_serial_number" class="ml-2 block text-sm font-bold text-gray-700">
                            Requires Serial Number Scan?
                            <p class="text-xs text-gray-500 font-normal">Check this for Phones, Laptops, etc.</p>
                        </label>
                    </div>

                    <!-- Warranty Duration -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Warranty Duration</label>
                        <input type="number" name="warranty_duration"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2" placeholder="e.g. 12">
                    </div>

                    <!-- Warranty Type -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Duration Type</label>
                        <select name="warranty_type" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white">
                            <option value="">No Warranty</option>
                            <option value="days">Days</option>
                            <option value="months" selected>Months</option>
                            <option value="years">Years</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- === SECTION 3: TAX & VAT SETTINGS (UAE) === -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-green-600 pl-2">Tax & VAT Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50 p-4 rounded-lg border border-green-100">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Tax Method <span class="text-red-500">*</span></label>
                        <select name="tax_method" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-white">
                            <option value="inclusive" selected>Inclusive (Price includes VAT)</option>
                            <option value="exclusive">Exclusive (Add VAT on top of Price)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">For UAE Retail (B2C), usually 'Inclusive'.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">VAT Rate (%)</label>
                        <input type="number" name="tax_rate" value="5" step="0.01"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2">
                        <p class="text-xs text-gray-500 mt-1">Standard UAE VAT is 5%.</p>
                    </div>
                </div>
            </div>

            <!-- === SECTION 4: DETAILED DESCRIPTION === -->
            <div class="grid grid-cols-1 gap-6 mb-8 border-b pb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-0 border-l-4 border-purple-600 pl-2">Product Details</h3>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="summernote_description" class="w-full"></textarea>
                </div>

                <!-- Specifications -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Specifications (Technical Details)</label>
                    <p class="text-xs text-gray-500 mb-2">Tip: Use the table icon in the toolbar to create a specs grid.</p>
                    <textarea name="specifications" id="summernote_specs" class="w-full"></textarea>
                </div>
            </div>

            <!-- === SECTION 5: SIMPLE PRODUCT INVENTORY === -->
            <div id="simple_section" class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center border-l-4 border-blue-600 pl-2">
                    <i class="fas fa-box text-blue-600 mr-2"></i> Simple Product Inventory
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">Selling Price (AED) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="price"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 simple-input focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">Sale Price (AED)</label>
                        <input type="number" step="0.01" name="sale_price"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500" placeholder="Optional">
                    </div>
                    <div id="cost_field_container">
                        <label class="block text-xs uppercase font-bold text-gray-500">Cost Price (AED) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="cost" id="cost_input"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 simple-input focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">SKU <span class="text-red-500">*</span></label>
                        <input type="text" name="sku"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 simple-input focus:border-blue-500" required>
                    </div>
                    <div id="stock_field_container">
                        <label class="block text-xs uppercase font-bold text-gray-500">Current Stock</label>
                        <input type="number" name="stock" id="stock_input"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500" value="0">
                    </div>
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-500">Low Stock Alert</label>
                        <input type="number" name="alert_quantity" id="alert_quantity_input"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500" value="5">
                    </div>
                    <div class="md:col-span-4" id="barcode_field_container">
                        <label class="block text-xs uppercase font-bold text-gray-500">Barcode (Scanner Input)</label>
                        <input type="text" name="barcode" id="barcode_input"
                            class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:border-blue-500"
                            placeholder="Click here and scan barcode...">
                    </div>
                </div>
            </div>

            <!-- === SECTION 6: VARIABLE PRODUCT INVENTORY (PROFESSIONAL ATTRIBUTES) === -->
            <div id="variable_section" class="hidden mb-8">
                <div class="bg-gray-50 p-4 rounded border mb-4">
                    <h4 class="font-bold text-gray-700 mb-2 border-b pb-2">1. Select Attributes for this Product</h4>
                    
                    <div class="grid grid-cols-3 gap-4">
                        @foreach ($attributes as $attr)
                            <div class="bg-white p-3 border rounded shadow-sm hover:shadow-md transition">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="attr-checkbox form-checkbox h-5 w-5 text-blue-600 rounded"
                                        value="{{ $attr->id }}" data-name="{{ $attr->name }}">
                                    <span class="ml-2 font-bold text-gray-700">{{ $attr->name }}</span>
                                </label>
                                <div class="mt-2 text-sm text-gray-500">
                                    <!-- Hidden Select to hold values for JS to read -->
                                    <select id="values_for_{{ $attr->id }}" class="hidden">
                                        @foreach ($attr->values as $val)
                                            <option value="{{ $val->id }}">{{ $val->value }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $attr->values->count() }} options</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="generate_inputs_btn"
                        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold shadow hover:bg-blue-700">
                        <i class="fas fa-cogs mr-1"></i> 2. Configure Variant Rows
                    </button>
                </div>

                <!-- Variants Table -->
                <div class="overflow-x-auto border rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr id="variant_header_row">
                                <!-- Dynamic Headers will be appended here by JS -->
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="variants_body" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                    <div class="p-4 border-t bg-gray-50">
                        <button type="button" id="add_manual_row_btn" class="text-sm bg-slate-800 text-white px-3 py-2 rounded hidden hover:bg-slate-700">
                            <i class="fas fa-plus mr-1"></i> Add Another Variation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end pt-6 border-t gap-3">
                <a href="{{ route('products.index') }}"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium">Cancel</a>
                <button type="submit"
                    class="px-8 py-2 bg-green-600 text-white rounded-md font-bold hover:bg-green-700 shadow-lg transform hover:-translate-y-0.5 transition-all">
                    Save Product
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

            // --- 1. Product Type Toggle Logic ---
            $('#type_selector').change(function() {
                let type = $(this).val();
                if (type === 'simple') {
                    $('#simple_section').removeClass('hidden');
                    $('#variable_section').addClass('hidden');
                    $('#electronics_section').removeClass('hidden');
                    $('#stock_field_container').removeClass('hidden');
                    $('#barcode_field_container').removeClass('hidden');
                    $('#brand_field_container').removeClass('hidden');
                    $('#cost_field_container').removeClass('hidden');

                    // Enable required for simple fields
                    $('.simple-input').prop('required', true);
                    $('#brand_input').prop('required', true);
                    $('#cost_input').prop('required', true);
                    $('#barcode_input').prop('required', false); // Barcode is always optional
                } else if (type === 'service') {
                    $('#simple_section').removeClass('hidden');
                    $('#variable_section').addClass('hidden');
                    $('#electronics_section').addClass('hidden'); 
                    $('#stock_field_container').addClass('hidden');
                    $('#barcode_field_container').addClass('hidden');
                    $('#brand_field_container').addClass('hidden');
                    $('#cost_field_container').addClass('hidden');
                    
                    $('#stock_input').val(0); // Reset stock for service
                    
                    // Enable required for simple fields except hidden ones
                    $('.simple-input').prop('required', true);
                    $('#stock_input').prop('required', false);
                    $('#brand_input').prop('required', false);
                    $('#cost_input').prop('required', false);
                    $('#barcode_input').prop('required', false);
                } else {
                    $('#simple_section').addClass('hidden');
                    $('#variable_section').removeClass('hidden');
                    $('#electronics_section').removeClass('hidden');
                    $('#brand_field_container').removeClass('hidden');
                    $('#cost_field_container').removeClass('hidden');

                    // Disable required for simple fields so form can submit
                    $('.simple-input').prop('required', false);
                    $('#brand_input').prop('required', true);
                    $('#cost_input').prop('required', false); // Cost is handled per variant
                }
            });

            // Trigger change on load to ensure UI matches current selection (handles old input)
            $('#type_selector').trigger('change');

            // --- 2. Rich Text Editors Init ---
            let summernoteOptions = {
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']], // Crucial for Specs
                    ['view', ['fullscreen', 'codeview']]
                ]
            };

            $('#summernote_description').summernote({ ...summernoteOptions, placeholder: 'Write a detailed description...' });
            $('#summernote_specs').summernote({ ...summernoteOptions, placeholder: 'Insert technical specifications...' });

            // --- 3. Professional Attribute Logic ---
            let selectedAttributes = [];
            let rowIdx = 0;

            // Generate Columns based on checked checkboxes
            $('#generate_inputs_btn').click(function() {
                selectedAttributes = [];
                
                // Find checked attributes
                $('.attr-checkbox:checked').each(function() {
                    selectedAttributes.push({
                        id: $(this).val(),
                        name: $(this).data('name')
                    });
                });

                if(selectedAttributes.length === 0) {
                    alert("Please select at least one attribute (e.g., Color) to configure variants.");
                    return;
                }

                // Rebuild Table Headers
                let headers = '';
                selectedAttributes.forEach(attr => {
                    headers += `<th class="px-4 py-3 text-left text-xs font-bold text-gray-800 uppercase bg-yellow-100 border-b-2 border-yellow-200">${attr.name}</th>`;
                });
                
                // Add standard headers
                headers += `
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cost (AED)</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Price (AED)</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sale (AED)</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Alert</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Barcode</th>
                    <th></th>`;
                
                $('#variant_header_row').html(headers);
                $('#variants_body').empty(); // Clear existing rows
                $('#add_manual_row_btn').removeClass('hidden');
                
                // Add first row automatically
                addVariantRow();
            });

            // Add Row Button Click
            $('#add_manual_row_btn').click(function() { addVariantRow(); });

            function addVariantRow() {
                let cols = '';
                
                // Dynamic Dropdowns for Attributes
                selectedAttributes.forEach(attr => {
                    // Get options from the hidden select generated by PHP
                    let optionsHtml = $(`#values_for_${attr.id}`).html();
                    
                    cols += `
                    <td class="p-2 bg-yellow-50">
                        <select name="variants[${rowIdx}][specs][${attr.id}]" class="w-full border border-gray-300 rounded p-1.5 text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                            ${optionsHtml}
                        </select>
                    </td>`;
                });

                // Standard Inputs
                cols += `
                    <td class="p-2"><input type="text" name="variants[${rowIdx}][sku]" class="w-full border rounded p-1.5 text-sm" placeholder="SKU" required></td>
                    <td class="p-2"><input type="number" step="0.01" name="variants[${rowIdx}][cost]" class="w-full border rounded p-1.5 text-sm" placeholder="0.00" required></td>
                    <td class="p-2"><input type="number" step="0.01" name="variants[${rowIdx}][price]" class="w-full border rounded p-1.5 text-sm" placeholder="0.00" required></td>
                    <td class="p-2"><input type="text" step="0.01" name="variants[${rowIdx}][sale_price]" class="w-full border rounded p-1.5 text-sm" placeholder="Optional"></td>
                    <td class="p-2"><input type="number" name="variants[${rowIdx}][stock]" class="w-full border rounded p-1.5 text-sm" value="0"></td>
                    <td class="p-2"><input type="number" name="variants[${rowIdx}][alert_quantity]" class="w-full border rounded p-1.5 text-sm" value="5"></td>
                    <td class="p-2"><input type="text" name="variants[${rowIdx}][barcode]" class="w-full border rounded p-1.5 text-sm" placeholder="Scan"></td>
                    <td class="p-2 text-center">
                        <input type="hidden" name="variants[${rowIdx}][name]" value="Auto-Generated"> 
                        <button type="button" class="text-red-500 hover:text-red-700 bg-red-100 p-2 rounded remove-row transition"><i class="fas fa-trash"></i></button>
                    </td>
                `;

                $('#variants_body').append(`<tr class="border-b hover:bg-gray-50 transition">${cols}</tr>`);
                rowIdx++;
            }
            
            // Remove Row Logic
            $(document).on('click', '.remove-row', function() { 
                $(this).closest('tr').remove(); 
            });

        });
    </script>
@endsection