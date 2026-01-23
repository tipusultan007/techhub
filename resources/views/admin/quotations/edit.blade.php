<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quotation #{{ $quotation->quotation_no }} | ElectroMart UAE</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Toastr for Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .product-card:active { transform: scale(0.98); }
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
        <div class="w-full md:w-2/3 flex flex-col border-r border-gray-300 bg-gray-50">

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
                @foreach ($categories ?? [] as $cat)
                    <button class="bg-gray-200 text-gray-700 hover:bg-slate-200 px-4 py-2 rounded-full text-sm font-bold transition">{{ $cat->name }}</button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-100" id="product-container">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="product-grid">
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
        <div class="w-full md:w-1/3 bg-white flex flex-col shadow-2xl z-10">

            <!-- Customer Select -->
            <div class="p-4 border-b bg-gray-50">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Customer</label>
                <select id="customer_id" class="w-full border border-gray-300 rounded p-2 text-sm focus:border-blue-500 outline-none">
                    <option value="">Walk-in Customer</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" {{ $quotation->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Header -->
            <div class="grid grid-cols-12 bg-slate-100 text-gray-600 text-xs font-bold uppercase p-2 border-b">
                <div class="col-span-6 pl-2">Item</div>
                <div class="col-span-3 text-center">Qty</div>
                <div class="col-span-3 text-right pr-2">Total</div>
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

                    <div class="flex justify-between text-gray-600">
                        <span>VAT (5% Included)</span>
                        <span class="font-mono" id="lbl-vat">0.00</span>
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

    <!-- Processing Overlay -->
    <div id="processingOverlay" class="fixed inset-0 bg-white bg-opacity-80 hidden items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
            <h2 class="text-xl font-bold text-gray-700">Updating Quotation...</h2>
        </div>
    </div>

    <script>
        let cart = [];
        const beep = new Audio('https://www.soundjay.com/button/sounds/beep-07.mp3');

        $(document).ready(function() {
            $('#search').focus();
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
                    sku: item.product ? item.product.sku : '',
                    type: item.product ? item.product.type : 'simple'
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
                            <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-blue-400 transition p-2" onclick='addToCart(${pStr})'>
                                <img src="${image}" class="h-24 w-full object-contain mb-2">
                                <div class="text-xs font-bold h-8 overflow-hidden">${p.name}</div>
                                <div class="text-blue-600 font-bold">AED ${parseFloat(p.price).toFixed(2)}</div>
                            </div>`;
                        });
                    }
                    $('#product-grid').html(html);
                });
            }
        });

        function addToCart(product) {
            beep.play();
            let existing = cart.find(i => i.id === product.id && i.variant_id === product.variant_id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ ...product, qty: 1 });
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
                    total += sub;
                    html += `
                    <tr class="border-b">
                        <td class="p-2 text-xs font-bold">
                            ${item.type === 'service' ? 
                                `<input type="text" value="${item.name}" class="w-full border rounded px-1 py-0.5 text-xs font-bold border-blue-300 focus:border-blue-500 outline-none" onchange="updateItemName(${index}, this.value)">` : 
                                item.name
                            }
                            <div class="text-[10px] text-gray-400 mt-1 font-mono">${item.sku || ''}</div>
                        </td>
                        <td class="p-2 text-center">
                            <div class="flex items-center justify-center border rounded">
                                <button onclick="updateQty(${index}, -1)" class="px-2 border-r">-</button>
                                <span class="px-2">${item.qty}</span>
                                <button onclick="updateQty(${index}, 1)" class="px-2 border-l">+</button>
                            </div>
                        </td>
                        <td class="p-2 text-right">
                            <div class="mb-1">
                                <input type="number" step="0.01" value="${item.price}" 
                                    class="w-20 border rounded p-1 text-right text-xs font-bold text-blue-600 focus:outline-none focus:border-blue-500"
                                    onchange="updatePrice(${index}, this.value)">
                            </div>
                            <div class="font-bold text-gray-700">${sub.toFixed(2)}</div>
                            <button onclick="removeItem(${index})" class="text-red-500 block ml-auto mt-1"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#cart-body').html(html);
            let discount = parseFloat($('#discount_input').val()) || 0;
            let payable = total - discount;
            let tax = payable - (payable / 1.05);

            $('#lbl-cart-total').text(total.toFixed(2));
            $('#lbl-payable').text(payable.toFixed(2));
            $('#lbl-vat').text(tax.toFixed(2));
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
