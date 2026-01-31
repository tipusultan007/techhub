<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ settings('site_name', 'Tech Hub') }} POS</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

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
        :root {
            --brand-navy: #024959;
            --brand-emerald: #2dae9a;
        }
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
    </style>
</head>

<body class="bg-gray-100 h-screen w-screen overflow-hidden flex flex-col font-sans">

    <!-- === HEADER === -->
    <header class="bg-slate-900 text-white h-16 flex items-center justify-between px-6 shadow-md z-20">
        <div class="flex items-center gap-4">
             @if(settings('site_logo_scrolled'))
                <img src="{{ settings('site_logo_scrolled') }}" alt="Logo" class="h-16 object-contain rounded p-1">
                <div class="font-bold text-xl tracking-wide">
                    <span class="text-gray-400 text-sm font-normal">POS Terminal</span>
                </div>
            @else
                <div class="font-bold text-xl tracking-wide">
                    <i class="fas fa-bolt text-yellow-400 mr-2"></i>{{ settings('site_name', 'Tech Hub') }} <span
                        class="text-gray-400 text-sm font-normal">POS Terminal</span>
                </div>
            @endif
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
        <div class="w-full md:w-1/2 flex flex-col border-r border-gray-300 bg-gray-50">

            <!-- Search Bar -->
            <div class="p-4 bg-white shadow-sm z-10">
                <div class="relative">
                    <input type="text" id="search"
                        class="w-full border-2 border-[#2dae9a] rounded-lg p-4 pl-12 text-lg focus:outline-none focus:ring-4 focus:ring-emerald-100 shadow-inner"
                        placeholder="Scan Barcode or Type Product Name / SKU..." autofocus autocomplete="off">
                    <div class="absolute left-4 top-5 text-[#2dae9a] text-xl"><i class="fas fa-barcode"></i></div>
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
                @if(isset($categories) && count($categories) > 0)
                    @foreach ($categories as $cat)
                        <button
                            class="bg-gray-200 text-gray-700 hover:bg-slate-200 px-4 py-2 rounded-full text-sm font-bold transition">{{ $cat->name }}</button>
                    @endforeach
                @endif
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-100" id="product-container">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="product-grid">
                    @foreach ($initialProducts as $p)
                        @php
                            // Secure JSON encoding to prevent HTML attribute breaking
                            $pJson = json_encode($p);
                            $image = $p['image'] ?: 'https://placehold.co/150?text=No+Image';
                            $stockClass = $p['stock'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            $stockText = $p['stock'] > 0 ? $p['stock'] . ' In Stock' : 'Out of Stock';
                        @endphp

                        <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-[#2dae9a] transition relative overflow-hidden group"
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
                                    <div class="text-[#2dae9a] font-bold text-lg">AED {{ number_format($p['price'], 2) }}
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
        <div class="w-full md:w-1/2 bg-white flex flex-col shadow-2xl z-10">

            <!-- Customer & PO Section -->
            <div class="p-4 border-b bg-gray-50">
                <div class="grid grid-cols-2 gap-3">
                    <!-- Customer Select -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Customer</label>
                        <div class="flex gap-1">
                            <div class="flex-1">
                                <select id="customer_id"
                                    class="w-full border border-gray-300 rounded p-2 text-sm focus:border-[#2dae9a] outline-none select2">
                                    <option value="">Walk-in Customer</option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" id="add-customer-btn" class="bg-[#2dae9a] text-white px-3 rounded hover:bg-emerald-700 h-[38px]" title="Add Customer">
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- PO Number at Top -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">PO # (Optional)</label>
                        <div class="relative">
                            <input type="text" id="po_number" placeholder="PO Number..."
                                class="w-full border border-gray-300 rounded p-2 pl-9 text-sm focus:border-[#2dae9a] outline-none font-bold text-gray-700 h-[38px]"
                                title="Purchase Order Number">
                            <div class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Header -->
            <div class="grid grid-cols-12 bg-slate-100 text-gray-600 text-xs font-bold uppercase p-2 border-b">
                <div class="col-span-5 pl-2">Item</div>
                <div class="col-span-2 text-center">Qty</div>
                <div class="col-span-3 text-center">Tax %</div>
                <div class="col-span-2 text-right pr-2">Total</div>
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
                        <span>Total Tax</span>
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
                            class="w-full border border-gray-300 rounded p-2.5 font-bold text-gray-700 bg-white focus:ring-2 focus:ring-[#2dae9a] outline-none text-sm h-[44px]">
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
                        class="font-bold text-[#2dae9a]"></span></p>
            </div>
            <input type="text" id="modalSerialInput"
                class="w-full border-2 border-[#2dae9a] rounded-lg p-3 text-lg text-center focus:outline-none focus:ring-4 focus:ring-emerald-100 mb-2 font-mono"
                placeholder="Scan IMEI / S/N..." autocomplete="off">
            <p id="serialError" class="text-red-500 text-sm text-center font-bold hidden mb-4"><i
                    class="fas fa-exclamation-circle"></i> Invalid Serial</p>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <button onclick="closeSerialModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300">Cancel</button>
                <button onclick="confirmSerial()"
                    class="px-4 py-2 bg-[#2dae9a] text-white rounded-lg font-bold hover:bg-emerald-700">Confirm</button>
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
                <p class="text-sm text-gray-500 mt-1">Add a non-inventory item to the cart</p>
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
                <button onclick="closeServiceModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300">Cancel</button>
                <button onclick="addInstantService()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg active:scale-95 transition">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div id="processingOverlay" class="fixed inset-0 bg-white bg-opacity-80 hidden items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-[#2dae9a] mx-auto mb-4"></div>
            <h2 class="text-xl font-bold text-gray-700">Processing Sale...</h2>
            <p class="text-gray-500">Printing Invoice</p>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div id="customerModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6 transform transition-all scale-100">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-xl font-bold text-gray-900">Add New Customer</h3>
                <button onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="customerForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="john@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="+971 50 XXXXXXX">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="123 Street, Dubai, UAE"></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">TRN Number (Optional)</label>
                        <input type="text" name="trn_number" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="100XXXXXXXXXXXX">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button type="button" onclick="closeCustomerModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#2dae9a] text-white rounded-lg font-bold hover:bg-emerald-700 transition" id="saveCustomerBtn">
                        Save Customer
                    </button>
                </div>
            </form>
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

            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select a customer",
                allowClear: true,
                width: '100%'
            });
        });

        // --- CUSTOMER MODAL LOGIC ---
        $('#add-customer-btn').click(function() {
            $('#customerModal').removeClass('hidden').addClass('flex');
            $('#customerForm')[0].reset();
            setTimeout(() => $('#customerForm input[name="name"]').focus(), 100);
        });

        function closeCustomerModal() {
            $('#customerModal').addClass('hidden').removeClass('flex');
        }

        $('#customerForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let btn = $('#saveCustomerBtn');

            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('pos.customer.store') }}",
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    btn.prop('disabled', false).text('Save Customer');
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        
                        // Add to Select2
                        let phoneDisplay = response.customer.phone ? ' (' + response.customer.phone + ')' : '';
                        let newOption = new Option(response.customer.name + phoneDisplay, response.customer.id, true, true);
                        $('#customer_id').append(newOption).trigger('change');
                        
                        closeCustomerModal();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Save Customer');
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error creating customer';
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    toastr.error(msg);
                }
            });
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
            $.get("{{ route('pos.search') }}", {
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
                        let stockBadge = '';
                        if (p.type !== 'service') {
                            stockBadge = p.stock > 0 ?
                                `<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">${p.stock} In Stock</span>` :
                                `<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold">Out of Stock</span>`;
                        }
                        html += `
                        <div class="product-card bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md hover:border-[#2dae9a] transition relative overflow-hidden group" onclick='addToCart(${pStr})'>
                            <div class="h-32 bg-gray-50 flex items-center justify-center p-2"><img src="${image}" class="h-full object-contain group-hover:scale-105 transition"></div>
                            <div class="p-3"><div class="font-bold text-gray-800 text-sm h-10 overflow-hidden leading-tight mb-1">${p.name}</div><div class="flex justify-between items-end"><div class="text-[#2dae9a] font-bold text-lg">AED ${parseFloat(p.price).toFixed(2)}</div></div><div class="mt-2 flex justify-between items-center"><span class="text-xs text-gray-500 font-mono">${p.sku}</span>${stockBadge}</div></div>
                        </div>`;
                    });
                }
                $('#product-grid').html(html);
            });
        }

        // --- 2. CART LOGIC ---
        function addToCart(product) {
            if (product.type !== 'service' && product.stock <= 0) {
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
            let totalVat = 0;
            let totalPayable = 0;

            if (cart.length === 0) {
                $('#empty-cart-msg').show();
                $('#discount_input').val(0);
                $('#lbl-cart-total').text('0.00');
                $('#lbl-payable').text('0.00');
                $('#lbl-vat').text('0.00');
                $('#lbl-vat').prev().text('Total Tax');
            } else {
                $('#empty-cart-msg').hide();
                cart.forEach((item, index) => {
                    let itemTaxRate = parseFloat(item.tax_rate) / 100 || 0;
                    let itemBasePrice = parseFloat(item.price) || 0; // Ensure it's a number
                    let rowSubtotal = itemBasePrice * (parseFloat(item.qty) || 1);
                    let rowTax = 0;
                    let rowPayable = 0;

                    // Support both inclusive/exclusive tax methods
                    if (item.tax_method === 'exclusive') {
                        rowTax = rowSubtotal * itemTaxRate;
                        rowPayable = rowSubtotal + rowTax;
                    } else { // inclusive (default)
                        rowTax = rowSubtotal - (rowSubtotal / (1 + itemTaxRate));
                        rowPayable = rowSubtotal;
                    }

                    // Accumulate Totals
                    totalVat += rowTax;
                    totalPayable += rowPayable;

                    let metaInfo = item.serial ? `<div class="text-[10px] text-emerald-600 font-bold">SN: ${item.serial}</div>` : `<div class="text-[10px] text-gray-400 font-mono">${item.sku || 'SERVICE'}</div>`;

                    let nameDisplay = item.is_service ? 
                        `<input type="text" value="${item.name}" class="w-full border rounded px-1 py-0.5 text-xs font-medium border-indigo-200 focus:border-indigo-500 outline-none" onchange="updateItemName(${index}, this.value)">` :
                        `<div class="font-bold text-gray-700 text-xs leading-tight">${item.name}</div>`;
                    
                    let priceDisplay = item.is_service ?
                        `<div class="flex items-center justify-end gap-1">
                            <input type="number" step="0.01" value="${item.price}" class="w-16 border rounded px-1 py-0.5 text-right text-xs font-bold text-indigo-600 border-indigo-200 focus:border-indigo-500 outline-none" onchange="updateItemPrice(${index}, this.value)">
                         </div>` :
                        `<div class="font-bold text-gray-700 text-xs">${rowPayable.toFixed(2)}</div>`; // Use calculated rowPayable

                    html += `
                    <tr class="border-b hover:bg-slate-50 transition">
                        <td class="p-2 align-middle" style="width: 42%;">
                            <div class="flex flex-col">
                                ${nameDisplay}
                                ${metaInfo}
                            </div>
                        </td>
                        <td class="p-2 text-center align-middle" style="width: 17%;">
                            ${item.serial ? `<span class="font-bold text-gray-800">1</span>` : `
                            <div class="flex items-center justify-center bg-gray-100 rounded-lg p-0.5 w-20 mx-auto">
                                <button onclick="updateQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-50 text-gray-600 font-bold transition">-</button>
                                <div class="flex-1 text-center font-bold text-gray-800 text-xs mx-1">${item.qty}</div>
                                <button onclick="updateQty(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm hover:bg-gray-50 text-gray-600 font-bold transition">+</button>
                            </div>`}
                        </td>
                        <td class="p-2 text-center align-middle" style="width: 25%;">
                            <select onchange="updateItemTax(${index}, this.value)" class="w-full border rounded text-[10px] font-bold py-1 px-0.5 border-gray-200 outline-none focus:border-indigo-500 transition">
                                <option value="0" ${item.tax_rate == 0 ? 'selected' : ''}>0%</option>
                                <option value="5" ${item.tax_rate == 5 ? 'selected' : ''}>5%</option>
                                <option value="10" ${item.tax_rate == 10 ? 'selected' : ''}>10%</option>
                                <option value="15" ${item.tax_rate == 15 ? 'selected' : ''}>15%</option>
                            </select>
                        </td>
                        <td class="p-2 text-right align-middle" style="width: 16%;">
                            <div class="flex flex-col items-end">
                                ${priceDisplay}
                                <button onclick="removeItem(${index})" class="text-[10px] text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#cart-body').html(html);
            }

            // --- MATHS ---
            let discount = parseFloat($('#discount_input').val()) || 0;
            if (discount > totalPayable) {
                discount = totalPayable;
                $('#discount_input').val(discount);
            }

            let payableAfterDiscount = totalPayable - discount;
            
            // Recalculate VAT based on discount proportion
            // But usually VAT remains same on Net, or reduces? 
            // Standard approach: Display Total Tax as calculated from items, ignoring total discount for simplicity unless discount is applied pre-tax
            // Here we just show the accumulated Tax
            let displayVat = totalVat; 
            
            // If we want proportional VAT reduction:
            if (totalPayable > 0 && discount > 0) {
                 displayVat = totalVat * (payableAfterDiscount / totalPayable);
            }

            $('#lbl-cart-total').text(totalPayable.toFixed(2));
            $('#lbl-payable').text(payableAfterDiscount.toFixed(2));
            $('#lbl-vat').text(displayVat.toFixed(2));
            // Update label to remove hardcoded 5%
            $('#lbl-vat').prev().text('Total Tax');
        }

        function updateQty(index, change) {
            let item = cart[index];
            if (item.type !== 'service' && item.qty + change > item.stock) {
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

        function updateItemName(index, newName) {
            cart[index].name = newName;
            // No need to re-render everything if we just updated memory, 
            // but for safety with calculations if needed:
            // renderCart(); 
        }

        function updateItemPrice(index, newPrice) {
            cart[index].price = parseFloat(newPrice) || 0;
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function updateItemTax(index, newRate) {
            cart[index].tax_rate = parseFloat(newRate) || 0;
            renderCart();
        }

        // --- 3. SERVICE MODAL LOGIC ---
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

            beep.currentTime = 0;
            beep.play();

            cart.push({
                id: null,
                variant_id: null,
                name: name,
                price: price,
                tax_rate: tax_rate,
                tax_method: 'inclusive', // Defaulting to inclusive for POS
                is_service: true,
                qty: 1
            });

            renderCart();
            closeServiceModal();
            toastr.success('Service added to cart');
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
        po_number: $('#po_number').val(),
        
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
            let printUrl = '/backend/orders/' + response.order_id + '/print';
            
            setTimeout(() => {
                window.open(printUrl, '_blank', 'width=400,height=600');
                
                // Reset UI
                cart = [];
                $('#discount_input').val(0);
                $('#customer_id').val('').trigger('change'); 
                $('#payment_method').val('cash');
                $('#po_number').val('');
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
