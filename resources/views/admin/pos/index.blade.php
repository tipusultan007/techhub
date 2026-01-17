<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS | ElectroMart UAE</title>

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
        /* Custom Scrollbar for POS feel */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .product-card:active {
            transform: scale(0.98);
        }

        .blink-red {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
    </style>
</head>

<body class="bg-gray-100 h-screen w-screen overflow-hidden flex flex-col font-sans">

    <!-- === HEADER === -->
    <header class="bg-slate-900 text-white h-16 flex items-center justify-between px-6 shadow-md z-20">
        <div class="flex items-center gap-4">
            <div class="font-bold text-xl tracking-wide">
                <i class="fas fa-bolt text-yellow-400 mr-2"></i>ELECTROMART <span
                    class="text-gray-400 text-sm font-normal">POS Terminal</span>
            </div>
            <div id="connection-status" class="text-xs bg-green-600 px-2 py-0.5 rounded">Online</div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right hidden md:block">
                <div class="text-sm font-bold">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400" id="clock">00:00:00</div>
            </div>
            <a href="{{ route('dashboard') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-bold transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Exit
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
                        placeholder="Scan Barcode or Type Product Name / SKU..." autofocus autocomplete="off">
                    <div class="absolute left-4 top-5 text-blue-500 text-xl"><i class="fas fa-barcode"></i></div>
                    <button id="clear-search" class="absolute right-4 top-4 text-gray-400 hover:text-red-500 hidden">
                        <i class="fas fa-times-circle text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Categories -->
            <div class="px-4 py-2 bg-white border-b flex gap-2 overflow-x-auto whitespace-nowrap no-scrollbar">
                <button class="bg-slate-800 text-white px-4 py-2 rounded-full text-sm font-bold shadow">All
                    Items</button>
                @foreach ($categories ?? [] as $cat)
                    <button
                        class="bg-gray-200 text-gray-700 hover:bg-slate-200 px-4 py-2 rounded-full text-sm font-bold transition">{{ $cat->name }}</button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-100" id="product-container">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="product-grid">
                    @foreach ($initialProducts as $p)
                        @php
                            // Secure JSON encoding to prevent HTML attribute breaking
                            $pJson = json_encode($p);
                            $image = $p['image'] ?: 'https://placehold.co/150?text=No+Image';
                            $stockClass = $p['stock'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            $stockText = $p['stock'] > 0 ? $p['stock'] . ' In Stock' : 'Out of Stock';
                        @endphp

                        <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-blue-400 transition relative overflow-hidden group"
                            onclick="addToCart({{ $pJson }})">

                            <div class="h-32 bg-gray-50 flex items-center justify-center p-2">
                                <img src="{{ $image }}"
                                    class="h-full object-contain group-hover:scale-105 transition" loading="lazy">
                            </div>

                            <div class="p-3">
                                <div class="font-bold text-gray-800 text-sm h-10 overflow-hidden leading-tight mb-1">
                                    {{ $p['name'] }}
                                </div>
                                <div class="flex justify-between items-end">
                                    <div class="text-blue-600 font-bold text-lg">AED {{ number_format($p['price'], 2) }}
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="text-xs text-gray-500 font-mono">{{ $p['sku'] }}</span>
                                    <span class="{{ $stockClass }} text-xs px-2 py-1 rounded font-bold">
                                        {{ $stockText }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: CART & CHECKOUT -->
        <div class="w-full md:w-1/3 bg-white flex flex-col shadow-2xl z-10">

            <!-- Customer Select -->
            <div class="p-4 border-b bg-gray-50 flex gap-2">
                <div class="flex-1">
                    <select id="customer_id"
                        class="w-full border border-gray-300 rounded p-2 text-sm focus:border-blue-500 outline-none">
                        <option value="">Walk-in Customer</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-3 rounded hover:bg-blue-700" title="Add Customer">
                    <i class="fas fa-user-plus"></i>
                </button>
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
                        <!-- Cart Rows JS -->
                        <tr id="empty-cart-msg">
                            <td colspan="3" class="text-center py-20 text-gray-400">
                                <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                                <p>Cart is empty</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals & Checkout Section -->
            <div class="bg-slate-50 p-4 border-t border-slate-200 shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
                <div class="space-y-1 mb-4 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Cart Total</span>
                        <span class="font-bold">AED <span id="lbl-cart-total">0.00</span></span>
                    </div>

                    <!-- Discount Input -->
                    <div class="flex justify-between items-center text-red-600 my-2">
                        <span class="font-bold flex items-center text-xs uppercase"><i class="fas fa-tags mr-1"></i>
                            Discount</span>
                        <div class="flex items-center">
                            <span class="mr-1">-</span>
                            <input type="number" id="discount_input" value="0" min="0"
                                class="w-20 border border-gray-300 rounded p-1 text-right text-sm focus:outline-none focus:border-red-500 text-red-600 font-bold bg-white"
                                oninput="renderCart()">
                        </div>
                    </div>

                    <div class="flex justify-between text-gray-600">
                        <span>VAT (5% Included)</span>
                        <span class="font-mono" id="lbl-vat">0.00</span>
                    </div>

                    <div
                        class="flex justify-between text-2xl font-bold text-slate-900 border-t border-gray-300 pt-2 mt-2">
                        <span>PAYABLE</span>
                        <span>AED <span id="lbl-payable">0.00</span></span>
                    </div>
                </div>

                <!-- Payment Actions -->
                <div class="grid grid-cols-3 gap-3">
                    <!-- Payment Method Select -->
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Method</label>
                        <select id="payment_method"
                            class="w-full border border-gray-300 rounded p-2.5 font-bold text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm h-[44px]">
                            <option value="cash" selected>💵 Cash</option>
                            <option value="card">💳 Card</option>
                            <option value="transfer">🏦 Bank</option>
                        </select>
                    </div>

                    <!-- Pay Button -->
                    <button
                        class="col-span-2 bg-green-600 text-white font-bold rounded hover:bg-green-700 shadow-lg transform active:scale-95 transition flex items-center justify-center gap-2 h-[44px] mt-[18px]"
                        onclick="processSale()">
                        <i class="fas fa-money-bill-wave"></i> COMPLETE SALE
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- === MODALS === -->

    <!-- Serial Number Scanner Modal -->
    <div id="serialModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 transform transition-all scale-100">
            <div class="text-center mb-6">
                <div
                    class="bg-yellow-100 text-yellow-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-barcode"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Scan Serial Number</h3>
                <p class="text-sm text-gray-500 mt-1">Product: <span id="modalProductName"
                        class="font-bold text-blue-600"></span></p>
            </div>
            <input type="text" id="modalSerialInput"
                class="w-full border-2 border-blue-500 rounded-lg p-3 text-lg text-center focus:outline-none focus:ring-4 focus:ring-blue-100 mb-2 font-mono"
                placeholder="Scan IMEI / S/N..." autocomplete="off">
            <p id="serialError" class="text-red-500 text-sm text-center font-bold hidden mb-4"><i
                    class="fas fa-exclamation-circle"></i> Invalid Serial</p>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <button onclick="closeSerialModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300">Cancel</button>
                <button onclick="confirmSerial()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div id="processingOverlay" class="fixed inset-0 bg-white bg-opacity-80 hidden items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
            <h2 class="text-xl font-bold text-gray-700">Processing Sale...</h2>
            <p class="text-gray-500">Printing Invoice</p>
        </div>
    </div>

    <!-- Sound Effects -->
    <audio id="beep-sound" src="https://www.soundjay.com/button/sounds/beep-07.mp3"></audio>
    <audio id="error-sound" src="https://www.soundjay.com/button/sounds/button-10.mp3"></audio>

    <script>
        // --- GLOBAL VARIABLES ---
        let cart = [];
        let pendingProduct = null;
        const beep = document.getElementById('beep-sound');
        const errorBeep = document.getElementById('error-sound');

        $(document).ready(function() {
            $('#search').focus();
            setInterval(() => {
                $('#clock').text(new Date().toLocaleTimeString());
            }, 1000);
        });

        // --- 1. PRODUCT SEARCH ---
        $('#search').on('keyup', function(e) {
            let term = $(this).val();
            if (term.length > 0) $('#clear-search').removeClass('hidden');
            else $('#clear-search').addClass('hidden');

            if (e.key === 'Enter' || term.length > 2) {
                fetchProducts(term, e.key === 'Enter');
            }
        });

        $('#clear-search').click(function() {
            $('#search').val('').focus();
            $(this).addClass('hidden');
            fetchProducts('');
        });

        function fetchProducts(term, isScan = false) {
            $.get('/admin/pos/search', {
                term: term
            }, function(data) {
                let html = '';
                if (data.length === 0) {
                    html =
                        `<div class="col-span-full text-center py-10 text-gray-400"><i class="fas fa-search text-4xl mb-2"></i><p>No products found</p></div>`;
                    if (isScan) {
                        errorBeep.play();
                        toastr.error('Product not found');
                        $('#search').select();
                    }
                } else {
                    if (isScan && data.length === 1) {
                        addToCart(data[0]);
                        $('#search').val('');
                        return;
                    }
                    data.forEach(p => {
                        let pStr = JSON.stringify(p).replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                        let image = p.image || 'https://placehold.co/150?text=No+Image';
                        let stockBadge = p.stock > 0 ?
                            `<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">${p.stock} In Stock</span>` :
                            `<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold">Out of Stock</span>`;
                        html += `
                        <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-blue-400 transition relative overflow-hidden group" onclick='addToCart(${pStr})'>
                            <div class="h-32 bg-gray-50 flex items-center justify-center p-2"><img src="${image}" class="h-full object-contain group-hover:scale-105 transition"></div>
                            <div class="p-3"><div class="font-bold text-gray-800 text-sm h-10 overflow-hidden leading-tight mb-1">${p.name}</div><div class="flex justify-between items-end"><div class="text-blue-600 font-bold text-lg">AED ${parseFloat(p.price).toFixed(2)}</div></div><div class="mt-2 flex justify-between items-center"><span class="text-xs text-gray-500 font-mono">${p.sku}</span>${stockBadge}</div></div>
                        </div>`;
                    });
                }
                $('#product-grid').html(html);
            });
        }

        // --- 2. CART LOGIC ---
        function addToCart(product) {
            if (product.stock <= 0) {
                errorBeep.play();
                toastr.error('Item is Out of Stock!');
                return;
            }
            if (product.has_serial_number == 1) {
                openSerialModal(product);
                return;
            }

            beep.currentTime = 0;
            beep.play();
            let existing = cart.find(i => i.id === product.id && i.variant_id === product.variant_id && !i.serial);
            if (existing) {
                if (existing.qty >= product.stock) {
                    toastr.warning('Max stock reached');
                    return;
                }
                existing.qty++;
            } else {
                cart.push({
                    ...product,
                    qty: 1,
                    serial: null
                });
            }
            renderCart();
        }

        function renderCart() {
            let html = '';
            let cartTotal = 0;

            if (cart.length === 0) {
                $('#empty-cart-msg').show();
                $('#discount_input').val(0);
            } else {
                $('#empty-cart-msg').hide();
                cart.forEach((item, index) => {
                    let itemTotal = item.price * item.qty;
                    cartTotal += itemTotal;
                    let metaInfo = item.serial ?
                        `<div class="text-xs text-blue-600 font-mono mt-0.5"><i class="fas fa-barcode"></i> SN: ${item.serial}</div>` :
                        '';
                    html += `
                    <tr class="border-b hover:bg-slate-50 transition">
                        <td class="p-3"><div class="font-bold text-gray-700 text-sm leading-tight">${item.name}</div>${metaInfo}</td>
                        <td class="p-3 text-center">
                            ${item.serial ? `<span class="font-bold text-gray-800">1</span>` : `<div class="flex items-center justify-center border rounded bg-white"><button onclick="updateQty(${index}, -1)" class="px-2 py-1 text-gray-500 hover:text-red-500">-</button><span class="px-2 text-sm font-bold w-6 text-center">${item.qty}</span><button onclick="updateQty(${index}, 1)" class="px-2 py-1 text-gray-500 hover:text-green-500">+</button></div>`}
                        </td>
                        <td class="p-3 text-right"><div class="font-bold text-gray-700">${itemTotal.toFixed(2)}</div><button onclick="removeItem(${index})" class="text-xs text-red-400 hover:text-red-600 mt-1"><i class="fas fa-trash"></i></button></td>
                    </tr>`;
                });
            }
            $('#cart-body').html(html);

            // --- MATHS ---
            let discount = parseFloat($('#discount_input').val()) || 0;
            if (discount > cartTotal) {
                discount = cartTotal;
                $('#discount_input').val(discount);
            }

            let payable = cartTotal - discount;
            let taxRate = 0.05;
            let subtotal = payable / (1 + taxRate);
            let vat = payable - subtotal;

            $('#lbl-cart-total').text(cartTotal.toFixed(2));
            $('#lbl-payable').text(payable.toFixed(2));
            $('#lbl-vat').text(vat.toFixed(2));
            $('#lbl-subtotal').text(subtotal.toFixed(2));
        }

        function updateQty(index, change) {
            let item = cart[index];
            if (item.qty + change > item.stock) {
                toastr.warning('Insufficient stock');
                return;
            }
            if (item.qty + change <= 0) {
                removeItem(index);
                return;
            }
            item.qty += change;
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            if (confirm('Clear current cart?')) {
                cart = [];
                renderCart();
            }
        }

        // --- 3. SERIAL LOGIC ---
        function openSerialModal(product) {
            pendingProduct = product;
            $('#modalProductName').text(product.name);
            $('#modalSerialInput').val('');
            $('#serialError').addClass('hidden');
            $('#serialModal').removeClass('hidden').addClass('flex');
            setTimeout(() => $('#modalSerialInput').focus(), 100);
        }

        function closeSerialModal() {
            $('#serialModal').addClass('hidden').removeClass('flex');
            pendingProduct = null;
            $('#search').focus();
        }
        $('#modalSerialInput').on('keypress', function(e) {
            if (e.which == 13) confirmSerial();
        });

        function confirmSerial() {
            let serial = $('#modalSerialInput').val().trim();
            if (serial === "") {
                $('#serialError').text("Please scan a serial number").removeClass('hidden');
                errorBeep.play();
                return;
            }
            if (cart.find(i => i.serial === serial)) {
                $('#serialError').text("Serial already in cart").removeClass('hidden');
                errorBeep.play();
                return;
            }

            $.ajax({
                url: '/admin/pos/check-serial',
                data: {
                    serial: serial,
                    product_id: pendingProduct.id,
                    variant_id: pendingProduct.variant_id
                },
                success: function(response) {
                    if (response.valid) {
                        beep.play();
                        cart.push({
                            ...pendingProduct,
                            qty: 1,
                            serial: serial
                        });
                        renderCart();
                        closeSerialModal();
                        toastr.success('Serial Verified');
                    } else {
                        errorBeep.play();
                        $('#serialError').text(response.message).removeClass('hidden');
                        $('#modalSerialInput').select();
                    }
                }
            });
        }

        // --- 4. CHECKOUT ---
        function processSale(method = 'cash') { // Default to cash if clicked via shortcut
    if (cart.length === 0) {
        toastr.error('Cart is empty!');
        return;
    }

    // Get Final Values
    let cartTotal = parseFloat($('#lbl-cart-total').text());
    let discount = parseFloat($('#discount_input').val()) || 0;
    let finalPayable = parseFloat($('#lbl-payable').text());
    
    // If method wasn't passed (clicked Pay button), get from dropdown
    if(!method || typeof method !== 'string') {
        method = $('#payment_method').val();
    }

    // Show Loader
    $('#processingOverlay').removeClass('hidden').addClass('flex');

    let data = {
        items: cart,
        customer_id: $('#customer_id').val(),
        payment_method: method,
        discount: discount,
        
        // --- FIX: Map the calculated total to 'amount_paid' ---
        amount_paid: finalPayable, 
        
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
        url: "{{ route('pos.store') }}",
        method: 'POST',
        data: data,
        success: function(response) {
            // Success Logic
            let printUrl = '/admin/orders/' + response.order_id + '/print';
            
            setTimeout(() => {
                window.open(printUrl, '_blank', 'width=400,height=600');
                
                // Reset UI
                cart = [];
                $('#discount_input').val(0);
                $('#customer_id').val(''); 
                $('#payment_method').val('cash');
                renderCart();
                
                $('#processingOverlay').addClass('hidden').removeClass('flex');
                toastr.success('Sale Completed Successfully');
                $('#search').focus();
            }, 500);
        },
        error: function(xhr) {
            $('#processingOverlay').addClass('hidden').removeClass('flex');
            errorBeep.play();
            // Show specific validation error if available
            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Unknown Error';
            if(xhr.status === 422 && xhr.responseJSON.errors) {
                // Formatting Laravel validation errors
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            }
            alert('Error: ' + msg);
        }
    });
}

        $(document).keydown(function(e) {
            if (e.which === 120) {
                e.preventDefault();
                processSale();
            }
        });
    </script>
</body>

</html>
