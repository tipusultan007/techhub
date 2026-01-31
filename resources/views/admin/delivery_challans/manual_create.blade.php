@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-truck text-[#d97706]"></i> Create Delivery Challan
        </h1>
        <a href="{{ route('delivery-challans.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[calc(100vh-180px)]">
        <!-- Left Side: Product Selection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-4 border-b space-y-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="product-search" placeholder="Search products by Name, SKU or Barcode..." 
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#d97706]/20 focus:border-[#d97706] transition-all">
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                    <button onclick="filterByCategory('')" class="cat-btn active px-4 py-1.5 rounded-full text-xs font-bold border transition-all whitespace-nowrap bg-[#d97706] text-white border-[#d97706]">
                        All Products
                    </button>
                    @foreach($categories as $category)
                        <button onclick="filterByCategory({{ $category->id }})" class="cat-btn px-4 py-1.5 rounded-full text-xs font-bold border border-gray-200 text-gray-600 hover:border-[#d97706] hover:text-[#d97706] transition-all whitespace-nowrap">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="product-grid" class="p-4 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 overflow-y-auto custom-scrollbar">
                @foreach($initialProducts as $product)
                    <div onclick="addToCart({{ json_encode(['id' => $product->id, 'name' => $product->name, 'price' => $product->price, 'image' => $product->image, 'type' => 'simple', 'sku' => $product->sku]) }})" 
                        class="group cursor-pointer bg-white border border-gray-100 rounded-xl p-3 hover:border-[#d97706] hover:shadow-md transition-all">
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-50 mb-3 relative">
                            <img src="{{ $product->image ? asset($product->image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <i class="fas fa-plus text-white text-2xl"></i>
                            </div>
                        </div>
                        <h3 class="text-xs font-bold text-gray-700 line-clamp-2 mb-1 group-hover:text-[#d97706]">{{ $product->name }}</h3>
                        <div class="text-[10px] text-gray-400 font-mono">{{ $product->sku }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Cart & Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-4 border-b bg-gray-50/50">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Customer</label>
                        <select id="customer_id" class="select2 w-full">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Challan Date</label>
                        <input type="date" id="challan_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-[#d97706] text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">PO# (Optional)</label>
                        <input type="text" id="po_number" placeholder="Enter PO Number" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-[#d97706] text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Note (Optional)</label>
                        <input type="text" id="note" placeholder="Driver name, vehicle, etc." class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-[#d97706] text-sm">
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col">
                <div class="flex-1 overflow-y-auto custom-scrollbar p-0">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase text-center w-32">Quantity</th>
                                <th class="px-4 py-3 text-[10px] font-black text-slate-500 uppercase text-right w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table-body" class="divide-y divide-slate-100">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                    <div id="empty-cart" class="p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <i class="fas fa-shopping-basket text-2xl"></i>
                        </div>
                        <p class="text-slate-400 text-sm font-medium">No products added for delivery yet</p>
                    </div>
                </div>

                <div class="p-4 border-t bg-gray-50/50">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Items</span>
                        <span id="total-qty" class="text-xl font-black text-[#d97706]">0</span>
                    </div>
                    <button onclick="saveChallan()" id="save-btn" class="w-full bg-[#d97706] hover:bg-[#b45309] text-white py-4 rounded-xl font-black text-lg shadow-lg shadow-[#d97706]/20 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle"></i> SAVE DELIVERY CHALLAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 0.5rem;
        height: 38px;
        border-color: #e5e7eb;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let cart = [];
    let currentCategoryId = '';

    $(document).ready(function() {
        $('.select2').select2();

        // Product search with debounce
        let searchTimeout;
        $('#product-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchProducts($(this).val(), currentCategoryId);
            }, 300);
        });
    });

    function filterByCategory(catId) {
        currentCategoryId = catId;
        $('.cat-btn').removeClass('active bg-[#d97706] text-white border-[#d97706]').addClass('border-gray-200 text-gray-600');
        event.target.classList.add('active', 'bg-[#d97706]', 'text-white', 'border-[#d97706]');
        fetchProducts($('#product-search').val(), catId);
    }

    function fetchProducts(term, catId) {
        $.get("{{ route('delivery-challans.search-products') }}", { term: term, category_id: catId }, function(data) {
            let html = '';
            data.forEach(product => {
                html += `
                    <div onclick='addToCart(${JSON.stringify(product)})' 
                        class="group cursor-pointer bg-white border border-gray-100 rounded-xl p-3 hover:border-[#d97706] hover:shadow-md transition-all">
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-50 mb-3 relative">
                            <img src="${product.image ? '/'+product.image : '/images/no-image.png'}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <i class="fas fa-plus text-white text-2xl"></i>
                            </div>
                        </div>
                        <h3 class="text-xs font-bold text-gray-700 line-clamp-2 mb-1 group-hover:text-[#d97706]">${product.name}</h3>
                        <div class="text-[10px] text-gray-400 font-mono">${product.sku}</div>
                    </div>
                `;
            });
            $('#product-grid').html(html || '<div class="col-span-full p-12 text-center text-gray-400 italic">No products found</div>');
        });
    }

    function addToCart(product) {
        let exists = cart.find(item => item.product_id == product.id && item.type == product.type);
        if (exists) {
            exists.qty++;
        } else {
            cart.push({
                product_id: product.id,
                product_id_parent: product.product_id || null,
                name: product.name,
                price: product.price,
                sku: product.sku,
                type: product.type,
                qty: 1
            });
        }
        renderCart();
    }

    function updateQty(index, delta) {
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function renderCart() {
        let html = '';
        let totalQty = 0;

        cart.forEach((item, index) => {
            totalQty += item.qty;
            html += `
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-4">
                        <div class="font-bold text-slate-700 text-xs">${item.name}</div>
                        <div class="text-[10px] font-mono text-slate-400 mt-1 uppercase tracking-tighter">${item.sku}</div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center bg-slate-50 rounded-lg p-1 w-fit mx-auto border border-slate-100 shadow-inner">
                            <button onclick="updateQty(${index}, -1)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-[#d97706] transition-all">
                                <i class="fas fa-minus text-[10px]"></i>
                            </button>
                            <input type="number" value="${item.qty}" onchange="cart[${index}].qty = parseInt(this.value); renderCart();"
                                class="w-10 text-center bg-transparent font-black text-slate-700 text-sm focus:outline-none pointer-events-none">
                            <button onclick="updateQty(${index}, 1)" class="w-7 h-7 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-[#d97706] transition-all">
                                <i class="fas fa-plus text-[10px]"></i>
                            </button>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <button onclick="removeItem(${index})" class="text-slate-300 hover:text-red-500 transition-colors">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#cart-table-body').html(html);
        $('#total-qty').text(totalQty);
        $('#empty-cart').toggle(cart.length === 0);
        $('#save-btn').prop('disabled', cart.length === 0).toggleClass('opacity-50 cursor-not-allowed', cart.length === 0);
    }

    function saveChallan() {
        let customerId = $('#customer_id').val();
        let date = $('#challan_date').val();
        let poNumber = $('#po_number').val();
        let note = $('#note').val();

        if (!customerId) {
            Swal.fire('Error', 'Please select a customer', 'error');
            return;
        }

        if (cart.length === 0) {
            Swal.fire('Error', 'Please add at least one product', 'error');
            return;
        }

        $('#save-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> SAVING...');

        $.ajax({
            url: "{{ route('delivery-challans.manual.store') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                customer_id: customerId,
                date: date,
                po_number: poNumber,
                note: note,
                items: cart
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                $('#save-btn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> SAVE DELIVERY CHALLAN');
                let message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong';
                Swal.fire('Error', message, 'error');
            }
        });
    }
</script>
@endpush
@endsection
