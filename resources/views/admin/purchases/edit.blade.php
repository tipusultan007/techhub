@extends('layouts.admin')

@section('header', 'Edit Purchase Order (Stock In)')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="po-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="md:col-span-1">
                <label class="block text-sm font-bold text-gray-700">Supplier</label>
                <select name="supplier_id" class="w-full border rounded p-2 mt-1 bg-gray-50" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Date</label>
                <input type="date" name="date" class="w-full border rounded p-2 mt-1" value="{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Reference / Invoice #</label>
                <input type="text" name="reference_no" value="{{ $purchase->reference_no }}" class="w-full border rounded p-2 mt-1 bg-gray-100 font-mono font-bold" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Status</label>
                <input type="text" value="{{ ucfirst($purchase->status) }}" class="w-full border rounded p-2 mt-1 bg-gray-100 font-bold" readonly>
                <input type="hidden" name="status" value="{{ $purchase->status }}">
                <p class="text-[10px] text-gray-400 mt-1 italic">* Status change is restricted in edit mode to preserve stock integrity.</p>
            </div>
        </div>

        <!-- Product Search Box -->
        <div class="bg-blue-50 p-4 rounded border mb-6">
            <label class="block text-sm font-bold text-blue-800 mb-1">Search Product to Add</label>
            <div class="relative">
                <input type="text" id="product_search" class="w-full border rounded p-3 pl-10 focus:ring-2 focus:ring-blue-500" placeholder="Type product name or SKU..." autocomplete="off">
                <div class="absolute left-3 top-3.5 text-gray-400"><i class="fas fa-search"></i></div>
                <div id="search_results" class="absolute z-10 bg-white shadow-lg border w-full mt-1 rounded hidden max-h-60 overflow-y-auto"></div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto mb-6 border rounded">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left font-bold text-gray-600">Product / SKU</th>
                        <th class="p-3 w-32 font-bold text-gray-600">Unit Cost</th>
                        <th class="p-3 w-24 font-bold text-gray-600">Qty</th>
                        <th class="p-3 w-24 font-bold text-gray-600">Tax %</th>
                        <th class="p-3 w-24 font-bold text-gray-600 text-right">Tax</th>
                        <th class="p-3 w-32 font-bold text-gray-600 text-right">Row Total</th>
                        <th class="p-3 w-10"></th>
                    </tr>
                </thead>
                <tbody id="po_items_body" class="bg-white divide-y divide-gray-200">
                    @foreach($purchase->items as $index => $item)
                    <tr id="row_{{ $index }}">
                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                        <input type="hidden" name="items[{{ $index }}][variant_id]" value="{{ $item->product_variant_id }}">
                        
                        <td class="p-2 align-middle">
                            <div class="font-medium text-gray-800">
                                {{ $item->product->name }}
                                @if($item->variant)
                                    - {{ $item->variant->variant_name }}
                                @endif
                                <br>
                                <span class="text-xs text-gray-400">SKU: {{ $item->variant->sku ?? $item->product->sku }}</span>
                            </div>
                        </td>
                        <td class="p-2 align-middle">
                            <input type="number" step="0.01" name="items[{{ $index }}][cost]" value="{{ $item->unit_cost }}" class="w-full border rounded p-1 cost-input text-right" oninput="calculateTotal()" required>
                        </td>
                        <td class="p-2 align-middle">
                            <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->quantity }}" min="1" class="w-full border rounded p-1 qty-input text-center" oninput="calculateTotal()" required>
                        </td>
                        <td class="p-2 align-middle">
                            <select name="items[{{ $index }}][tax_rate]" class="w-full border rounded p-1 item-tax-rate" onchange="calculateTotal()">
                                <option value="0" {{ $item->tax_rate == 0 ? 'selected' : '' }}>0%</option>
                                <option value="5" {{ $item->tax_rate == 5 ? 'selected' : '' }}>5%</option>
                                <option value="10" {{ $item->tax_rate == 10 ? 'selected' : '' }}>10%</option>
                            </select>
                        </td>
                        <td class="p-2 align-middle text-right font-mono text-xs text-gray-500 row-tax-display">{{ number_format($item->tax_amount, 2, '.', '') }}</td>
                        <td class="p-2 align-middle text-right font-mono bg-gray-50 subtotal-display">
                            {{ number_format($item->subtotal, 2, '.', '') }}
                        </td>
                        <td class="p-2 text-center align-middle">
                            <button type="button" class="text-red-500 hover:text-red-700 bg-red-50 p-1 rounded" onclick="removeRow({{ $index }})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="text-right p-2 font-bold text-gray-600">Subtotal (Net):</td>
                        <td class="p-2 text-right font-bold text-gray-800" id="display_subtotal">0.00</td>
                        <td></td>
                    </tr>
                    <tbody id="tax_summary_body" class="bg-gray-50">
                        <!-- Dynamic Tax Lines -->
                    </tbody>
                    <tr class="bg-blue-50 border-t border-blue-200">
                        <td colspan="5" class="text-right p-3 font-bold text-lg text-blue-900">GRAND TOTAL:</td>
                        <td class="p-3 text-right font-bold text-lg text-blue-900">AED <span id="display_grand_total">0.00</span></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div>
                <label class="block text-sm font-bold text-gray-700">Attachment</label>
                <input type="file" name="attachment" class="w-full border rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                @if($purchase->hasMedia('attachments'))
                    <div class="mt-1 text-xs text-blue-600 font-bold">
                        <i class="fas fa-paperclip mr-1"></i> Has existing attachment
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700">Notes (Optional)</label>
                <textarea name="notes" class="w-full border rounded p-2 mt-1" rows="2" placeholder="Any additional information...">{{ $purchase->notes }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('purchases.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded font-bold hover:bg-gray-200 shadow">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded font-bold hover:bg-blue-700 shadow flex items-center transition transform active:scale-95">
                <i class="fas fa-save mr-2"></i> Update Purchase Order
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let rowIdx = {{ $purchase->items->count() }};

        // 1. Live Search Logic
        $('#product_search').on('keyup', function() {
            let term = $(this).val();
            if(term.length > 1) {
                $.ajax({
                    url: "{{ route('purchases.search') }}",
                    data: { term: term },
                    success: function(data) {
                        let html = '';
                        if(data.length === 0) {
                            html = '<div class="p-2 text-gray-500">No products found</div>';
                        } else {
                            data.forEach(item => {
                                let itemStr = JSON.stringify(item).replace(/"/g, "&quot;");
                                html += `<div class="p-2 hover:bg-blue-100 cursor-pointer border-b text-sm" onclick="addItem(${itemStr})">
                                            <div class="font-bold">${item.label}</div>
                                            <div class="text-xs text-gray-500">Curr Cost: ${item.cost}</div>
                                         </div>`;
                            });
                        }
                        $('#search_results').html(html).removeClass('hidden');
                    }
                });
            } else {
                $('#search_results').addClass('hidden');
            }
        });

        // Hide dropdown on click outside
        $(document).click(function(e) {
            if (!$(e.target).closest('#product_search, #search_results').length) {
                $('#search_results').addClass('hidden');
            }
        });

        // 2. Add Item to Table
        window.addItem = function(item) {
            $('#search_results').addClass('hidden');
            $('#product_search').val('');

            let html = `
            <tr id="row_${rowIdx}">
                <input type="hidden" name="items[${rowIdx}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${rowIdx}][variant_id]" value="${item.variant_id || ''}">
                
                <td class="p-2 align-middle">
                    <div class="font-medium text-gray-800">${item.label}</div>
                </td>
                <td class="p-2 align-middle">
                    <input type="number" step="0.01" name="items[${rowIdx}][cost]" value="${item.cost}" class="w-full border rounded p-1 cost-input text-right" oninput="calculateTotal()" required>
                </td>
                <td class="p-2 align-middle">
                    <input type="number" name="items[${rowIdx}][qty]" value="1" min="1" class="w-full border rounded p-1 qty-input text-center" oninput="calculateTotal()" required>
                </td>
                <td class="p-2 align-middle">
                    <select name="items[${rowIdx}][tax_rate]" class="w-full border rounded p-1 item-tax-rate" onchange="calculateTotal()">
                        <option value="0" ${item.tax_rate == 0 ? 'selected' : ''}>0%</option>
                        <option value="5" ${item.tax_rate == 5 ? 'selected' : ''}>5%</option>
                        <option value="10" ${item.tax_rate == 10 ? 'selected' : ''}>10%</option>
                    </select>
                </td>
                <td class="p-2 align-middle text-right font-mono text-xs text-gray-500 row-tax-display">0.00</td>
                <td class="p-2 align-middle text-right font-mono bg-gray-50 subtotal-display">
                    ${item.cost}
                </td>
                <td class="p-2 text-center align-middle">
                    <button type="button" class="text-red-500 hover:text-red-700 bg-red-50 p-1 rounded" onclick="removeRow(${rowIdx})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
            
            $('#po_items_body').append(html);
            rowIdx++;
            calculateTotal();
        };

        // 3. Calculate Totals
        window.calculateTotal = function() {
            let netTotal = 0;
            let totalVat = 0;
            let taxes = {};
            
            $('#po_items_body tr').each(function() {
                let cost = parseFloat($(this).find('.cost-input').val()) || 0;
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let taxRate = parseFloat($(this).find('.item-tax-rate').val()) || 0;
                
                let rowTotal = cost * qty;
                let rowVat = rowTotal * (taxRate / 100);

                $(this).find('.row-tax-display').text(rowVat.toFixed(2));
                $(this).find('.subtotal-display').text(rowTotal.toFixed(2));
                
                netTotal += rowTotal;
                totalVat += rowVat;

                if (taxRate > 0) {
                    if (!taxes[taxRate]) taxes[taxRate] = 0;
                    taxes[taxRate] += rowVat;
                }
            });

            // Update Summary
            $('#display_subtotal').text(netTotal.toFixed(2));
            
            let taxHtml = '';
            for (let rate in taxes) {
                let label = rate == 5 ? 'VAT (5%)' : `Tax (${rate}%)`;
                taxHtml += `
                    <tr>
                        <td colspan="5" class="text-right p-2 font-bold text-gray-600 italic">${label}:</td>
                        <td class="p-2 text-right font-bold text-red-600 italic">AED ${taxes[rate].toFixed(2)}</td>
                        <td></td>
                    </tr>`;
            }
            $('#tax_summary_body').html(taxHtml);

            let grandTotal = netTotal + totalVat;
            $('#display_grand_total').text(grandTotal.toFixed(2));
        };

        // 4. Remove Row
        window.removeRow = function(id) {
            $('#row_' + id).remove();
            calculateTotal();
        };

        // Initial Calculation
        calculateTotal();
    });
</script>
@endsection
