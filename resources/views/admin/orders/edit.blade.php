@extends('layouts.admin')

@section('header', 'Edit Order #' . $order->invoice_no)

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('orders.update', $order->id) }}" method="POST" id="order-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Header Info -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="md:col-span-1">
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">Customer</label>
                <select name="customer_id" class="w-full border rounded p-2 mt-1 bg-gray-50 select2" required>
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} {{ $customer->phone ? '(' . $customer->phone . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">Date</label>
                <input type="text" class="w-full border rounded p-2 mt-1 bg-gray-100 font-mono" value="{{ $order->created_at->format('d M Y, h:i A') }}" readonly>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">Invoice #</label>
                <input type="text" value="{{ $order->invoice_no }}" class="w-full border rounded p-2 mt-1 bg-gray-100 font-mono font-bold" readonly>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">Payment Method</label>
                <select name="payment_method" class="w-full border rounded p-2 mt-1 bg-white font-bold" required>
                    <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                    <option value="card" {{ $order->payment_method == 'card' ? 'selected' : '' }}>💳 Card</option>
                    <option value="transfer" {{ $order->payment_method == 'transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                    <option value="advance" {{ $order->payment_method == 'advance' ? 'selected' : '' }}>💰 Advance</option>
                    <option value="rakbank" {{ $order->payment_method == 'rakbank' ? 'selected' : '' }}>🌐 Rakbank (Online)</option>
                    <option value="custom" {{ $order->payment_method == 'custom' ? 'selected' : '' }}>⚙️ Custom</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">PO # (Optional)</label>
                <input type="text" name="po_number" value="{{ $order->po_number }}" class="w-full border rounded p-2 mt-1 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="PO Number">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">Sales Person</label>
                <select name="user_id" class="w-full border rounded p-2 mt-1 bg-white select2">
                    <option value="">Select Sales Person</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $order->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-1">Attachment</label>
                <input type="file" name="attachment[]" multiple class="w-full border rounded p-1.5 mt-1 bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                @if($order->hasMedia('attachments'))
                    <div class="mt-2 space-y-2">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-tighter">Existing Attachments:</p>
                        @foreach($order->getMedia('attachments') as $media)
                            <div class="flex items-center justify-between p-2 bg-slate-50 border rounded-lg">
                                <span class="text-xs text-slate-600 truncate max-w-[150px]">{{ $media->file_name }}</span>
                                <a href="{{ $media->getUrl() }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold">View</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Search Box -->
        <div class="bg-emerald-50 p-4 rounded border border-emerald-100 mb-6">
            <label class="block text-sm font-bold text-emerald-800 mb-1">Add Products</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <input type="text" id="product_search" class="w-full border border-emerald-200 rounded p-3 pl-10 focus:ring-2 focus:ring-emerald-500" placeholder="Scan Barcode or Type product name / SKU..." autocomplete="off">
                    <div class="absolute left-3 top-3.5 text-emerald-400"><i class="fas fa-search text-lg"></i></div>
                    <div id="search_results" class="absolute z-50 bg-white shadow-xl border w-full mt-1 rounded-md hidden max-h-80 overflow-y-auto"></div>
                </div>
                <button type="button" onclick="openServiceModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 rounded-lg font-bold shadow-sm transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Add Service
                </button>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto mb-6 border rounded shadow-sm">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-3 text-left font-bold uppercase text-[10px] tracking-wider">Item Description</th>
                        <th class="p-3 w-32 font-bold uppercase text-[10px] tracking-wider text-right">Price (AED)</th>
                        <th class="p-3 w-32 font-bold uppercase text-[10px] tracking-wider text-center">Quantity</th>
                        <th class="p-3 w-32 font-bold uppercase text-[10px] tracking-wider text-center">Tax %</th>
                        <th class="p-3 w-32 font-bold uppercase text-[10px] tracking-wider text-right">Row Total</th>
                        <th class="p-3 w-10"></th>
                    </tr>
                </thead>
                <tbody id="order_items_body" class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $index => $item)
                    <tr id="row_{{ $index }}" class="hover:bg-gray-50 transition">
                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                        <input type="hidden" name="items[{{ $index }}][variant_id]" value="{{ $item->product_variant_id }}">
                        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}">
                        
                        <td class="p-3 align-middle">
                            <div class="font-bold text-gray-800 text-sm">
                                {{ $item->product_name }}
                            </div>
                            <div class="text-[10px] text-gray-500 font-mono mt-1">
                                @if($item->product_id)
                                    SKU: {{ $item->variant->sku ?? ($item->product->sku ?? 'N/A') }}
                                    @if($item->product && $item->product->has_serial_number)
                                        <div class="mt-2 p-1 bg-yellow-50 border border-yellow-100 rounded">
                                            <label class="block text-[9px] font-bold text-yellow-800 uppercase tracking-tighter">Serial Number (SN)</label>
                                            <input type="text" name="items[{{ $index }}][serial_numbers]" value="{{ $item->serial_numbers }}" class="w-full border-yellow-200 rounded px-1.5 py-0.5 text-[11px] font-mono focus:ring-1 focus:ring-yellow-500 bg-white" placeholder="Enter S/N...">
                                        </div>
                                    @elseif($item->serial_numbers)
                                        <span class="ml-2 font-bold text-emerald-600">SN: {{ $item->serial_numbers }}</span>
                                    @endif
                                @else
                                    SERVICE
                                @endif
                            </div>
                        </td>
                        <td class="p-3 align-middle text-right">
                            <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item->unit_price }}" class="w-full border rounded p-1 price-input text-right font-bold text-gray-700 focus:ring-1 focus:ring-emerald-500" oninput="calculateTotal()" required>
                        </td>
                        <td class="p-3 align-middle">
                            <div class="flex items-center justify-center bg-gray-50 rounded p-1 border">
                                <button type="button" onclick="changeQty({{ $index }}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-100 text-gray-700 font-bold">-</button>
                                <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->quantity + 0 }}" min="0.01" step="any" class="w-16 border-none bg-transparent p-1 qty-input text-center font-bold text-gray-800 focus:ring-0" oninput="calculateTotal()" required>
                                <button type="button" onclick="changeQty({{ $index }}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-100 text-gray-700 font-bold">+</button>
                            </div>
                        </td>
                        <td class="p-3 align-middle text-center">
                            <select name="items[{{ $index }}][tax_rate]" class="w-full border rounded p-1 item-tax-rate text-center font-bold" onchange="calculateTotal()">
                                <option value="0" {{ $item->tax_rate == 0 ? 'selected' : '' }}>0%</option>
                                <option value="5" {{ $item->tax_rate == 5 ? 'selected' : '' }}>5%</option>
                                <option value="10" {{ $item->tax_rate == 10 ? 'selected' : '' }}>10%</option>
                            </select>
                        </td>
                        <td class="p-3 align-middle text-right font-bold text-slate-800 row-total-display">
                            {{ number_format($item->subtotal, 2, '.', '') }}
                        </td>
                        <td class="p-3 text-center align-middle">
                            <button type="button" class="text-red-400 hover:text-red-600 p-1" onclick="removeRow({{ $index }})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-emerald-50">
                    <tr>
                        <td colspan="4" class="text-right p-3 font-bold text-emerald-900 border-r border-emerald-100 uppercase tracking-tighter text-xs">Total Without VAT/Tax:</td>
                        <td class="p-3 text-right font-bold text-emerald-900 text-lg" id="display_subtotal">0.00</td>
                        <td></td>
                    </tr>
                    <tr class="bg-white">
                        <td colspan="4" class="text-right p-3 font-bold text-gray-600 border-r border-gray-100 uppercase tracking-tighter text-xs">Total Tax:</td>
                        <td class="p-3 text-right font-bold text-gray-700 text-lg" id="display_tax">0.00</td>
                        <td></td>
                    </tr>
                    <tr class="bg-white">
                        <td colspan="4" class="text-right p-3 font-bold text-red-700 border-r border-gray-100 uppercase tracking-tighter text-xs">Overall Discount (AED):</td>
                        <td class="p-3 text-right">
                            <input type="number" step="0.01" name="discount" value="{{ $order->discount }}" class="w-full border rounded p-2 text-right font-bold text-red-600 focus:ring-1 focus:ring-red-500" id="discount_input" oninput="calculateTotal()">
                        </td>
                        <td></td>
                    </tr>
                    <tr class="bg-white">
                        <td colspan="4" class="text-right p-3 font-bold text-blue-700 border-r border-gray-100 uppercase tracking-tighter text-xs">Shipping Charge (AED):</td>
                        <td class="p-3 text-right">
                            <input type="number" step="0.01" name="shipping_charge" value="{{ $order->shipping_charge }}" class="w-full border rounded p-2 text-right font-bold text-blue-600 focus:ring-1 focus:ring-blue-500" id="shipping_input" oninput="calculateTotal()">
                        </td>
                        <td></td>
                    </tr>
                    <tr class="bg-emerald-600 text-white">
                        <td colspan="4" class="text-right p-4 font-black uppercase text-xl">Payable Total (AED):</td>
                        <td class="p-4 text-right font-black text-2xl" id="display_grand_total">0.00</td>
                        <td></td>
                    </tr>
                    <!-- Advanced Payment: Paid & Due -->
                    <tr class="bg-emerald-50">
                        <td colspan="4" class="text-right p-3 font-bold text-emerald-800 uppercase tracking-tighter text-xs">Paid Amount (AED):</td>
                        <td class="p-3 text-right">
                            <input type="number" step="0.01" name="paid_amount" value="{{ $order->paid_amount }}" class="w-full border rounded p-2 text-right font-bold text-emerald-700 focus:ring-1 focus:ring-emerald-500" id="paid_amount_input" oninput="calculateDue()">
                        </td>
                        <td></td>
                    </tr>
                    <tr class="bg-red-50" id="due_row">
                        <td colspan="4" class="text-right p-3 font-bold text-red-800 uppercase tracking-tighter text-xs">Balance Due (AED):</td>
                        <td class="p-3 text-right font-bold text-red-700 text-lg" id="display_due">0.00</td>
                        <input type="hidden" name="due_amount" id="due_amount_hidden" value="{{ $order->due_amount }}">
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500 italic">
                * Note: Changing quantities will automatically adjust product stock.
            </div>
            <div class="flex gap-4">
                <a href="{{ route('orders.show', $order) }}" class="bg-gray-100 text-gray-700 px-8 py-3 rounded-lg font-bold hover:bg-gray-200 transition shadow-sm border border-gray-200">Cancel</a>
                <button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-emerald-700 shadow-md flex items-center transition transform active:scale-95 border border-emerald-700">
                    <i class="fas fa-check-circle mr-2"></i> Update Sales Order
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Custom Service Modal -->
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 transform transition-all scale-100">
        <div class="text-center mb-6">
            <div class="bg-indigo-100 text-indigo-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Add Instant Service</h3>
            <p class="text-sm text-gray-500 mt-1">Add a non-inventory item to the order</p>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Service Name</label>
                <input type="text" id="service_name" class="w-full border-2 border-indigo-100 rounded-lg p-3 focus:outline-none focus:border-indigo-500" placeholder="e.g. Installation Fee" autocomplete="off">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Price (AED)</label>
                    <input type="number" id="service_price" class="w-full border-2 border-indigo-100 rounded-lg p-3 focus:outline-none focus:border-indigo-500" placeholder="0.00" step="0.01">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tax Rate (%)</label>
                    <select id="service_tax" class="w-full border-2 border-indigo-100 rounded-lg p-3 focus:outline-none focus:border-indigo-500">
                        <option value="0">0% (Tax Free)</option>
                        <option value="5" selected>5% (Standard)</option>
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mt-8">
            <button type="button" onclick="closeServiceModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300">Cancel</button>
            <button type="button" onclick="addInstantService()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg active:scale-95 transition">Add to Order</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding-top: 4px;
    }
</style>
@endpush

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ width: '100%' });

        let rowIdx = {{ $order->items->count() }};

        // 1. Live Search Logic
        $('#product_search').on('keyup', function(e) {
            let term = $(this).val();
            if(term.length > 1) {
                $.ajax({
                    url: "{{ route('pos.search') }}",
                    data: { term: term },
                    success: function(data) {
                        let html = '';
                        if(data.length === 0) {
                            html = '<div class="p-4 text-gray-500 text-center"><i class="fas fa-info-circle mr-2"></i>No products found</div>';
                        } else {
                            data.forEach(item => {
                                let itemStr = JSON.stringify(item).replace(/"/g, "&quot;");
                                html += `<div class="p-3 hover:bg-emerald-50 cursor-pointer border-b last:border-0 transition" onclick="addItem(${itemStr})">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <div class="font-bold text-gray-800">${item.name}</div>
                                                    <div class="text-xs text-gray-500 font-mono">SKU: ${item.sku} | Stock: <span class="${item.stock > 10 ? 'text-emerald-600' : 'text-orange-500'} font-bold">${item.stock}</span></div>
                                                </div>
                                                <div class="font-bold text-emerald-600">AED ${parseFloat(item.price).toFixed(2)}</div>
                                            </div>
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

        // Add Item via Enter Key
        $('#product_search').on('keypress', function(e) {
            if(e.which == 13) {
                e.preventDefault();
                // If there's only one result, add it
                let results = $('#search_results').children();
                if(results.length === 1) {
                    results.first().click();
                }
            }
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#product_search, #search_results').length) {
                $('#search_results').addClass('hidden');
            }
        });

        // 2. Add Item to Table
        window.addItem = function(item) {
            $('#search_results').addClass('hidden');
            $('#product_search').val('').focus();

            let html = `
            <tr id="row_${rowIdx}" class="hover:bg-gray-50 transition border-b">
                <input type="hidden" name="items[${rowIdx}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${rowIdx}][variant_id]" value="${item.variant_id || ''}">
                
                <td class="p-3 align-middle">
                    <div class="font-bold text-gray-800 text-sm">${item.name}</div>
                    <div class="text-[10px] text-gray-500 font-mono">SKU: ${item.sku}</div>
                    ${item.has_serial_number ? `
                        <div class="mt-2 p-1 bg-yellow-50 border border-yellow-100 rounded">
                            <label class="block text-[9px] font-bold text-yellow-800 uppercase tracking-tighter">Serial Number (SN)</label>
                            <input type="text" name="items[${rowIdx}][serial_numbers]" class="w-full border-yellow-200 rounded px-1.5 py-0.5 text-[11px] font-mono focus:ring-1 focus:ring-yellow-500 bg-white" placeholder="Enter S/N...">
                        </div>
                    ` : ''}
                </td>
                <td class="p-3 align-middle text-right">
                    <input type="number" step="0.01" name="items[${rowIdx}][price]" value="${item.price}" class="w-full border rounded p-1 price-input text-right font-bold text-gray-700 focus:ring-1 focus:ring-emerald-500" oninput="calculateTotal()" required>
                </td>
                <td class="p-3 align-middle">
                    <div class="flex items-center justify-center bg-gray-50 rounded p-1 border">
                        <button type="button" onclick="changeQty(${rowIdx}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-100 text-gray-700 font-bold">-</button>
                        <input type="number" name="items[${rowIdx}][qty]" value="1" min="0.01" step="any" class="w-16 border-none bg-transparent p-1 qty-input text-center font-bold text-gray-800 focus:ring-0" oninput="calculateTotal()" required>
                        <button type="button" onclick="changeQty(${rowIdx}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-100 text-gray-700 font-bold">+</button>
                    </div>
                </td>
                <td class="p-3 align-middle text-center">
                    <select name="items[${rowIdx}][tax_rate]" class="w-full border rounded p-1 item-tax-rate text-center font-bold" onchange="calculateTotal()">
                        <option value="0" ${item.tax_rate == 0 ? 'selected' : ''}>0%</option>
                        <option value="5" ${item.tax_rate == 5 ? 'selected' : ''}>5%</option>
                        <option value="10" ${item.tax_rate == 10 ? 'selected' : ''}>10%</option>
                    </select>
                </td>
                <td class="p-3 align-middle text-right font-bold text-slate-800 row-total-display">
                    ${parseFloat(item.price).toFixed(2)}
                </td>
                <td class="p-3 text-center align-middle">
                    <button type="button" class="text-red-400 hover:text-red-600 p-1" onclick="removeRow(${rowIdx})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
            
            $('#order_items_body').append(html);
            rowIdx++;
            calculateTotal();
        };

        // 3. Service Logic
        window.openServiceModal = function() {
            $('#service_name').val('');
            $('#service_price').val('');
            $('#service_tax').val('5');
            $('#serviceModal').removeClass('hidden').addClass('flex');
            setTimeout(() => $('#service_name').focus(), 100);
        };

        window.closeServiceModal = function() {
            $('#serviceModal').addClass('hidden').removeClass('flex');
        };

        window.addInstantService = function() {
            let name = $('#service_name').val().trim();
            let price = parseFloat($('#service_price').val()) || 0;
            let tax_rate = parseFloat($('#service_tax').val()) || 0;

            if (!name) {
                alert('Please enter service name');
                return;
            }

            let html = `
            <tr id="row_${rowIdx}" class="hover:bg-gray-50 transition border-b">
                <input type="hidden" name="items[${rowIdx}][product_id]" value="">
                <input type="hidden" name="items[${rowIdx}][variant_id]" value="">
                
                <td class="p-3 align-middle">
                    <div class="font-bold text-gray-800 text-sm">${name}</div>
                    <div class="text-[10px] text-gray-500 font-mono">SERVICE</div>
                    <input type="hidden" name="items[${rowIdx}][product_name]" value="${name}">
                </td>
                <td class="p-3 align-middle text-right">
                    <input type="number" step="0.01" name="items[${rowIdx}][price]" value="${price}" class="w-full border rounded p-1 price-input text-right font-bold text-gray-700 focus:ring-1 focus:ring-emerald-500" oninput="calculateTotal()" required>
                </td>
                <td class="p-3 align-middle">
                    <div class="flex items-center justify-center bg-gray-50 rounded p-1 border">
                        <button type="button" onclick="changeQty(${rowIdx}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-100 text-gray-700 font-bold">-</button>
                        <input type="number" name="items[${rowIdx}][qty]" value="1" min="0.01" step="any" class="w-16 border-none bg-transparent p-1 qty-input text-center font-bold text-gray-800 focus:ring-0" oninput="calculateTotal()" required>
                        <button type="button" onclick="changeQty(${rowIdx}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-100 text-gray-700 font-bold">+</button>
                    </div>
                </td>
                <td class="p-3 align-middle text-center">
                    <select name="items[${rowIdx}][tax_rate]" class="w-full border rounded p-1 item-tax-rate text-center font-bold" onchange="calculateTotal()">
                        <option value="0" ${tax_rate == 0 ? 'selected' : ''}>0%</option>
                        <option value="5" ${tax_rate == 5 ? 'selected' : ''}>5%</option>
                        <option value="10" ${tax_rate == 10 ? 'selected' : ''}>10%</option>
                        <option value="15" ${tax_rate == 15 ? 'selected' : ''}>15%</option>
                    </select>
                </td>
                <td class="p-3 align-middle text-right font-bold text-slate-800 row-total-display">
                    ${price.toFixed(2)}
                </td>
                <td class="p-3 text-center align-middle">
                    <button type="button" class="text-red-400 hover:text-red-600 p-1" onclick="removeRow(${rowIdx})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;

            $('#order_items_body').append(html);
            rowIdx++;
            calculateTotal();
            closeServiceModal();
        };

        // 4. Helpers
        window.changeQty = function(id, delta) {
            let input = $('#row_' + id).find('.qty-input');
            let current = parseFloat(input.val()) || 0;
            let next = Math.max(0.01, current + delta);
            input.val(next);
            calculateTotal();
        }

        window.calculateTotal = function() {
            let totalNet = 0;
            let totalTax = 0;
            
            $('#order_items_body tr').each(function() {
                let price = parseFloat($(this).find('.price-input').val()) || 0;
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let taxRate = parseFloat($(this).find('.item-tax-rate').val()) || 0;
                
                let rowNet = price * qty;
                let rowTax = rowNet * (taxRate / 100);
                let rowTotal = rowNet + rowTax;

                $(this).find('.row-total-display').text(rowTotal.toFixed(2));
                
                totalNet += rowNet;
                totalTax += rowTax;
            });

            $('#display_subtotal').text(totalNet.toFixed(2));
            
            let totalInclusive = totalNet + totalTax;
            let discount = parseFloat($('#discount_input').val()) || 0;
            let shipping = parseFloat($('#shipping_input').val()) || 0;
            let grandTotal = Math.max(0, totalInclusive - discount + shipping);

            // Proportional tax reduction if discount is applied to total
            if (totalInclusive > 0) {
                totalTax = totalTax * (grandTotal / totalInclusive);
            }
            
            $('#display_tax').text(totalTax.toFixed(2));
            $('#display_grand_total').text(grandTotal.toFixed(2));
            
            calculateDue();
        };

        window.calculateDue = function() {
            let payable = parseFloat($('#display_grand_total').text()) || 0;
            let paidInput = $('#paid_amount_input');
            
            // If this is the first load and paid amount is 0 (or matches original), 
            // we might want to keep it as is, but usually we want to show current due.
            let paid = parseFloat(paidInput.val()) || 0;
            let due = payable - paid;
            
            $('#display_due').text(due.toFixed(2));
            $('#due_amount_hidden').val(due.toFixed(2));
            
            if (due <= 0) {
                $('#due_row').removeClass('bg-red-50 text-red-800').addClass('bg-green-50 text-green-800');
                $('#display_due').removeClass('text-red-700').addClass('text-green-700');
            } else {
                $('#due_row').removeClass('bg-green-50 text-green-800').addClass('bg-red-50 text-red-800');
                $('#display_due').removeClass('text-green-700').addClass('text-red-700');
            }
        };

        window.removeRow = function(id) {
            $('#row_' + id).fadeOut(200, function() {
                $(this).remove();
                calculateTotal();
            });
        };

        // Initial Calculation
        calculateTotal();
    });
</script>
@endsection
