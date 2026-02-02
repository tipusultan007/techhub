<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quotation #{{ $quotation->quotation_no }} | Tech Hub UAE</title>

    <!-- Tailwind CSS & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Toastr for Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .product-card:active { transform: scale(0.98); }

        /* Select2 Custom Styling to match Tailwind */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            padding-top: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
</head>

<body class="bg-gray-100 h-screen w-screen overflow-hidden flex flex-col font-sans">

    <!-- === HEADER === -->
    <header class="bg-slate-900 text-white h-16 flex items-center justify-between px-6 shadow-md z-20">
        <div class="flex items-center gap-4">
            <div class="font-bold text-xl tracking-wide">
                <i class="fas fa-edit text-blue-400 mr-2"></i>EDIT QUOTATION <span
                    class="text-gray-400 text-sm font-normal">#{{ $quotation->quotation_no }}</span>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right hidden md:block">
                <div class="text-sm font-bold">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400" id="clock">00:00:00</div>
            </div>
            <a href="{{ route('quotations.show', $quotation->id) }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-bold transition">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
        </div>
    </header>

    <!-- === MAIN LAYOUT === -->
    <div class="flex-1 flex overflow-hidden">

        <!-- LEFT COLUMN: PRODUCTS & SEARCH -->
        <div class="w-full md:w-1/2 flex flex-col border-r border-gray-300 bg-gray-50">

            <!-- Search Bar -->
            <div class="p-4 bg-white shadow-sm z-10">
                <div class="relative">
                    <input type="text" id="search"
                        class="w-full border-2 border-blue-500 rounded-lg p-4 pl-12 text-lg focus:outline-none focus:ring-4 focus:ring-blue-100 shadow-inner"
                        placeholder="Search Product Name / SKU / Barcode..." autofocus autocomplete="off">
                    <div class="absolute left-4 top-5 text-blue-500 text-xl"><i class="fas fa-search"></i></div>
                    <button id="clear-search" class="absolute right-4 top-4 text-gray-400 hover:text-red-500 hidden">
                        <i class="fas fa-times-circle text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Categories -->
            <div class="px-4 py-2 bg-white border-b flex gap-2 overflow-x-auto whitespace-nowrap no-scrollbar">
                <button class="bg-slate-800 text-white px-4 py-2 rounded-full text-sm font-bold shadow">All Items</button>
                <button onclick="openServiceModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full text-sm font-bold shadow transition">
                    <i class="fas fa-plus-circle mr-1"></i> Add Service
                </button>
                @foreach ($categories ?? [] as $cat)
                    <button class="bg-gray-200 text-gray-700 hover:bg-slate-200 px-4 py-2 rounded-full text-sm font-bold transition">{{ $cat->name }}</button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-100" id="product-container">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="product-grid">
                    @foreach ($initialProducts as $p)
                        @php
                            $pJson = json_encode($p);
                            $image = $p['image'] ?: 'https://placehold.co/150?text=No+Image';
                        @endphp
                        <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-blue-400 transition relative overflow-hidden group"
                            onclick="addToCart({{ $pJson }})">
                            <div class="h-32 bg-gray-50 flex items-center justify-center p-2">
                                <img src="{{ $image }}" class="h-full object-contain group-hover:scale-105 transition" loading="lazy">
                            </div>
                            <div class="p-3">
                                <div class="font-bold text-gray-800 text-sm h-10 overflow-hidden leading-tight mb-1">
                                    {{ $p['name'] }}
                                </div>
                                <div class="text-blue-600 font-bold text-lg">AED {{ number_format($p['price'], 2) }}</div>
                                <div class="mt-2 text-xs text-gray-500 font-mono">{{ $p['sku'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: CART & SUMMARY -->
        <div class="w-full md:w-1/2 bg-white flex flex-col shadow-2xl z-10">

            <!-- Customer Select -->
            <div class="p-4 border-b bg-gray-50 flex gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Customer</label>
                    <select id="customer_id" class="w-full border border-gray-300 rounded p-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">Walk-in Customer</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ $quotation->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PO# (Optional)</label>
                    <input type="text" id="po_number" value="{{ $quotation->po_number }}" placeholder="Enter PO#" class="w-full border border-gray-300 rounded p-2 text-sm focus:border-blue-500 outline-none">
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quote Date</label>
                    <input type="date" id="quotation_date" value="{{ $quotation->date ? $quotation->date->format('Y-m-d') : $quotation->created_at->format('Y-m-d') }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:border-blue-500 outline-none">
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Expiry Date</label>
                    <input type="date" id="expiry_date" value="{{ $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '' }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:border-blue-500 outline-none">
                </div>
            </div>

            <!-- Cart Header -->
            <div class="grid grid-cols-12 bg-slate-100 text-gray-600 text-xs font-bold uppercase p-2 border-b">
                <div class="col-span-4 pl-2">Item</div>
                <div class="col-span-2 text-center">Qty</div>
                <div class="col-span-2 text-center">Tax %</div>
                <div class="col-span-2 text-right">Tax</div>
                <div class="col-span-2 text-right pr-2">Total</div>
            </div>

            <!-- Cart Items (Scrollable) -->
            <div class="flex-1 overflow-y-auto" id="cart-scroll">
                <table class="w-full text-sm">
                    <tbody id="cart-body">
                        <!-- Items will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="bg-slate-50 p-4 border-t border-slate-200">
                <div class="space-y-1 mb-4 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-bold">AED <span id="lbl-cart-total">0.00</span></span>
                    </div>

                    <div class="flex justify-between items-center text-red-600 my-2">
                        <span class="font-bold flex items-center text-xs uppercase"><i class="fas fa-tags mr-1"></i> Discount</span>
                        <div class="flex items-center">
                            <span class="mr-1">-</span>
                            <input type="number" id="discount_input" value="{{ $quotation->discount }}" min="0" class="w-20 border border-gray-300 rounded p-1 text-right text-sm focus:outline-none focus:border-red-500 text-red-600 font-bold bg-white" oninput="renderCart()">
                        </div>
                    </div>

                    <div id="tax-details-container" class="space-y-1">
                        <!-- Grouped taxes will be injected here -->
                    </div>

                    <div class="flex justify-between text-2xl font-bold text-slate-900 border-t border-gray-300 pt-2 mt-2">
                        <span>TOTAL</span>
                        <span>AED <span id="lbl-payable">0.00</span></span>
                    </div>
                </div>

                <button class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 shadow-lg transform active:scale-95 transition flex items-center justify-center gap-2" onclick="updateQuotation()">
                    <i class="fas fa-save"></i> UPDATE QUOTATION
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Service Modal -->
    <div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 transform transition-all scale-100">
            <div class="text-center mb-6">
                <div class="bg-indigo-100 text-indigo-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Add Instant Service</h3>
                <p class="text-sm text-gray-500 mt-1">Add a non-inventory item to the quotation</p>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Service Name</label>
                    <input type="text" id="service_name" class="w-full border-2 border-indigo-100 rounded-lg p-3 focus:outline-none focus:border-indigo-500" placeholder="e.g. Consulting Fee" autocomplete="off">
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
                            <option value="5" selected>5% (VAT)</option>
                            <option value="10">10%</option>
                            <option value="15">15%</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-8">
                <button onclick="closeServiceModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300">Cancel</button>
                <button onclick="addInstantService()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg active:scale-95 transition">Add to Quotation</button>
            </div>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div id="processingOverlay" class="fixed inset-0 bg-white bg-opacity-80 hidden items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
            <h2 class="text-xl font-bold text-gray-700">Updating Quotation...</h2>
        </div>
    </div>

    <script>
        let cart = [];
       

        $(document).ready(function() {
            $('#search').focus();
            $('#customer_id').select2({
                placeholder: "Select a customer",
                allowClear: true
            });
            setInterval(() => {
                $('#clock').text(new Date().toLocaleTimeString());
            }, 1000);

            // Load existing items into cart
            const existingItems = @json($quotation->items);
            existingItems.forEach(item => {
                cart.push({
                    id: item.product_id,
                    variant_id: item.product_variant_id,
                    name: item.product_name,
                    price: parseFloat(item.unit_price),
                    qty: parseInt(item.quantity),
                    tax_rate: parseFloat(item.tax_rate || 0),
                    tax_amount: parseFloat(item.tax_amount || 0),
                    sku: item.product ? item.product.sku : (item.is_service ? 'SERVICE' : ''),
                    type: item.product ? item.product.type : (item.is_service ? 'service' : 'simple'),
                    is_service: !!item.is_service
                });
            });
            renderCart();
        });

        $('#search').on('keyup', function(e) {
            let term = $(this).val();
            if (term.length > 0) $('#clear-search').removeClass('hidden');
            else $('#clear-search').addClass('hidden');

            if (e.key === 'Enter' || term.length > 2) {
                $.get('{{ route("quotations.search") }}', { term: term }, function(data) {
                    let html = '';
                    if (data.length === 0) {
                        html = `<div class="col-span-full text-center py-10 text-gray-400"><p>No products found</p></div>`;
                    } else {
                        data.forEach(p => {
                            let pStr = JSON.stringify(p).replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                            let image = p.image || 'https://placehold.co/150?text=No+Image';
                            html += `
                            <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-blue-400 transition p-2 relative" onclick='addToCart(${pStr})'>
                                <img src="${image}" class="h-24 w-full object-contain mb-2">
                                <div class="text-xs font-bold h-8 overflow-hidden">${p.name}</div>
                                <div class="text-blue-600 font-bold text-sm">AED ${parseFloat(p.price).toFixed(2)}</div>
                            </div>`;
                        });
                    }
                    $('#product-grid').html(html);
                });
            }
        });

        function addToCart(product) {
            let existing = cart.find(i => i.id === product.id && i.variant_id === product.variant_id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ ...product, qty: 1, tax_rate: 0, tax_amount: 0 });
            }
            renderCart();
        }

        function renderCart() {
            let html = '';
            let total = 0;
            if (cart.length === 0) {
                html = `
                <tr id="empty-cart-msg">
                    <td colspan="3" class="text-center py-20 text-gray-400">
                        <i class="fas fa-file-alt text-4xl mb-2"></i>
                        <p>No items added yet</p>
                    </td>
                </tr>`;
            } else {
                cart.forEach((item, index) => {
                    let sub = item.price * item.qty;
                    let taxRate = parseFloat(item.tax_rate) || 0;
                    let taxAmount = sub * (taxRate / 100);
                    item.tax_amount = taxAmount;
                    total += sub;

                    html += `
                    <tr class="border-b">
                        <td class="p-2 text-xs font-bold w-[35%]">
                            ${item.type === 'service' ? 
                                `<input type="text" value="${item.name}" class="w-full border rounded px-1 py-0.5 text-xs font-bold border-blue-300 focus:border-blue-500 outline-none" onchange="updateItemName(${index}, this.value)">` : 
                                item.name
                            }
                            <div class="text-[10px] text-gray-400 mt-1 font-mono">${item.sku || ''}</div>
                        </td>
                        <td class="p-2 text-center w-[15%]">
                            <div class="flex items-center justify-center bg-gray-100 rounded-lg p-1 w-24 mx-auto">
                                <button onclick="updateQty(${index}, -1)" class="w-8 h-8 flex items-center justify-center bg-white rounded-md shadow-sm hover:bg-gray-50 text-gray-600 font-bold transition">-</button>
                                <div class="flex-1 text-center font-bold text-gray-800 text-sm mx-1">${item.qty}</div>
                                <button onclick="updateQty(${index}, 1)" class="w-8 h-8 flex items-center justify-center bg-white rounded-md shadow-sm hover:bg-gray-50 text-gray-600 font-bold transition">+</button>
                            </div>
                        </td>
                        <td class="p-2 text-center w-[15%]">
                            <select class="w-full border rounded p-1 text-xs" onchange="updateTax(${index}, this.value)">
                                <option value="0" ${item.tax_rate == 0 ? 'selected' : ''}>0%</option>
                                <option value="5" ${item.tax_rate == 5 ? 'selected' : ''}>5%</option>
                                <option value="10" ${item.tax_rate == 10 ? 'selected' : ''}>10%</option>
                            </select>
                        </td>
                        <td class="p-2 text-right w-[15%]">
                            <div class="text-xs font-mono text-gray-500">${taxAmount.toFixed(2)}</div>
                        </td>
                        <td class="p-2 text-right w-[20%]">
                            <div class="mb-1">
                                <input type="number" step="0.01" value="${item.price}" 
                                    class="w-full border rounded p-1 text-right text-xs font-bold text-blue-600 focus:outline-none focus:border-blue-500"
                                    onchange="updatePrice(${index}, this.value)">
                            </div>
                            <div class="font-bold text-gray-700 text-xs">${sub.toFixed(2)}</div>
                            <button onclick="removeItem(${index})" class="text-red-500 block ml-auto mt-1"><i class="fas fa-trash text-xs"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#cart-body').html(html);

            let discount = parseFloat($('#discount_input').val()) || 0;

            // Group taxes
            let groupedTaxes = {};
            let totalTax = 0;
            cart.forEach(item => {
                let rate = parseFloat(item.tax_rate) || 0;
                let amt = parseFloat(item.tax_amount) || 0;
                let label = rate == 0 ? 'Zero Rate (0%)' : (rate == 5 ? 'VAT (5%)' : `Tax (${rate}%)`);
                
                if (!groupedTaxes[label]) groupedTaxes[label] = 0;
                groupedTaxes[label] += amt;
                totalTax += amt;
            });

            let taxHtml = '';
            for (let label in groupedTaxes) {
                taxHtml += `
                <div class="flex justify-between text-gray-600 text-sm">
                    <span>${label}</span>
                    <span class="font-mono">${groupedTaxes[label].toFixed(2)}</span>
                </div>`;
            }
            $('#tax-details-container').html(taxHtml);

            let payable = (total + totalTax) - discount;

            $('#lbl-cart-total').text(total.toFixed(2));
            $('#lbl-payable').text(payable.toFixed(2));
        }

        function updateTax(index, newRate) {
            cart[index].tax_rate = parseFloat(newRate);
            renderCart();
        }

        function updatePrice(index, newPrice) {
            newPrice = parseFloat(newPrice);
            if (isNaN(newPrice) || newPrice < 0) {
                toastr.error('Invalid price');
                renderCart();
                return;
            }
            cart[index].price = newPrice;
            renderCart();
        }

        function updateItemName(index, newName) {
            cart[index].name = newName;
        }

        function updateQty(index, change) {
            cart[index].qty += change;
            if (cart[index].qty <= 0) removeItem(index);
            else renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function openServiceModal() {
            $('#service_name').val('');
            $('#service_price').val('');
            $('#service_tax').val('5');
            $('#serviceModal').removeClass('hidden').addClass('flex');
            setTimeout(() => $('#service_name').focus(), 100);
        }

        function closeServiceModal() {
            $('#serviceModal').addClass('hidden').removeClass('flex');
        }

        function addInstantService() {
            let name = $('#service_name').val().trim();
            let price = parseFloat($('#service_price').val()) || 0;
            let tax_rate = parseFloat($('#service_tax').val()) || 0;

            if (!name) {
                toastr.error('Please enter service name');
                return;
            }

        

            cart.push({
                id: null,
                variant_id: null,
                name: name,
                price: price,
                tax_rate: tax_rate,
                is_service: true,
                sku: 'SERVICE',
                qty: 1
            });

            renderCart();
            closeServiceModal();
            toastr.success('Service added to quotation');
        }

        function updateQuotation() {
            if (cart.length === 0) {
                toastr.error('Cart is empty!');
                return;
            }
            $('#processingOverlay').removeClass('hidden').addClass('flex');
            $.ajax({
                url: "{{ route('quotations.update', $quotation->id) }}",
                method: 'PUT',
                data: {
                    items: cart,
                    customer_id: $('#customer_id').val(),
                    po_number: $('#po_number').val(),
                    date: $('#quotation_date').val(),
                    expiry_date: $('#expiry_date').val(),
                    discount: $('#discount_input').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    window.location.href = "{{ route('quotations.show', $quotation->id) }}";
                },
                error: function(err) {
                    $('#processingOverlay').addClass('hidden');
                    alert('Error: ' + (err.responseJSON ? err.responseJSON.message : 'Failed to update'));
                }
            });
        }
    </script>
</body>
</html>
