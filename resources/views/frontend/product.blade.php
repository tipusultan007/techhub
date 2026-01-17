@extends('layouts.frontend')

@section('title', $product->name . ' | Tech Hub')

@push('styles')
    <style>
        /* --- PRODUCT PAGE STYLES (Tech Hub Theme) --- */

        /* Breadcrumbs */
        .breadcrumbs { margin: 20px 0; font-size: 13px; color: var(--text-muted); }
        .breadcrumbs span { margin: 0 8px; color: #cbd5e1; }
        .breadcrumbs a:hover { color: var(--brand-magenta); }

        /* Main Layout */
        .product-wrapper {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 0;
            overflow: hidden;
            border: 1px solid var(--border);
            margin-bottom: 40px;
        }

        /* Gallery */
        .gallery-section { padding: 40px; border-right: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; }
        .main-image { width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; }
        .main-image img { max-height: 100%; max-width: 100%; object-fit: contain; mix-blend-mode: multiply; }

        .thumbnails { display: flex; gap: 15px; overflow-x: auto; max-width: 100%; padding-bottom: 5px; }
        .thumb { width: 70px; height: 70px; min-width: 70px; border: 1px solid var(--border); border-radius: var(--radius); padding: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; background: #fff; }
        .thumb:hover, .thumb.active { border-color: var(--brand-magenta); box-shadow: 0 0 0 2px rgba(192, 77, 238, 0.2); }
        .thumb img { max-height: 100%; max-width: 100%; }

        /* Info Section */
        .info-section { padding: 40px; }
        .p-brand { color: var(--brand-magenta); font-weight: 700; font-size: 13px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 1px; }
        .p-title { font-size: 26px; font-weight: 800; line-height: 1.3; margin-bottom: 10px; color: var(--text-main); }

        .p-rating { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-size: 13px; }
        .stars { color: var(--accent-gold); }

        .p-price-box { margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid var(--border); }
        .current-price { font-size: 32px; font-weight: 800; color: var(--brand-deep-blue); display: flex; align-items: baseline; gap: 10px; }
        .old-price { font-size: 16px; color: var(--text-muted); text-decoration: line-through; font-weight: 400; }

        /* Options (Variants) */
        .option-group { margin-bottom: 25px; }
        .option-title { font-size: 14px; font-weight: 600; margin-bottom: 10px; display: block; color: var(--text-main); }
        .options-grid { display: flex; flex-wrap: wrap; gap: 10px; }

        .opt-radio { display: none; }
        .opt-label { padding: 10px 18px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 13px; font-weight: 500; cursor: pointer; transition: 0.2s; background: white; color: var(--text-main); }

        /* Branding Colors for Selection */
        .opt-radio:checked + .opt-label {
            border-color: var(--brand-magenta);
            background: linear-gradient(135deg, #fdf4ff 0%, #ffffff 100%);
            color: var(--brand-deep-blue);
            font-weight: 700;
            box-shadow: 0 0 0 1px var(--brand-magenta);
        }

        /* Actions */
        .action-box { display: flex; gap: 15px; margin-top: 30px; }
        .btn-main { flex: 1; background: var(--brand-gradient); color: white; padding: 16px; border-radius: var(--radius); font-weight: 700; font-size: 16px; border: none; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px; transition: 0.3s; box-shadow: 0 4px 15px rgba(192, 77, 238, 0.3); }
        .btn-main:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(192, 77, 238, 0.4); }

        .btn-wish { width: 54px; height: 54px; border: 1px solid var(--border); border-radius: var(--radius); background: white; font-size: 24px; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .btn-wish:hover { color: var(--accent-red); border-color: var(--accent-red); background: #fef2f2; }

        /* Trust Box */
        .trust-box { margin-top: 30px; background: #f8fafc; border-radius: var(--radius); padding: 15px; border: 1px solid var(--border); }
        .trust-item { display: flex; gap: 12px; margin-bottom: 12px; align-items: start; }
        .trust-item:last-child { margin-bottom: 0; }
        .t-icon { color: var(--brand-magenta); font-size: 18px; margin-top: 2px; }
        .t-text h4 { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
        .t-text p { font-size: 12px; color: var(--text-muted); }

        /* Tabs */
        .tabs-container { margin-top: 40px; background: white; border-radius: var(--radius); padding: 30px; border: 1px solid var(--border); box-shadow: var(--shadow); }
        .tabs-head { display: flex; gap: 30px; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .tab-link { padding-bottom: 15px; cursor: pointer; font-weight: 500; color: var(--text-muted); border-bottom: 2px solid transparent; transition: 0.2s; }
        .tab-link:hover { color: var(--brand-deep-blue); }
        .tab-link.active { color: var(--brand-magenta); border-bottom-color: var(--brand-magenta); font-weight: 700; }
        .tab-content { font-size: 14px; line-height: 1.7; color: #475569; }
        .tab-content.active { display: block !important; }

        /* Related Products */
        .related-title { font-size: 20px; font-weight: 700; margin: 40px 0 20px; color: var(--text-main); }

        @media (max-width: 900px) {
            .product-wrapper { grid-template-columns: 1fr; }
            .gallery-section { border-right: none; border-bottom: 1px solid var(--border); }
        }

        /* Active Wishlist State */
        .btn-wish.active {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #ef4444;
        }
        /* Animation for the heart icon */
        .btn-wish.active i {
            animation: heartPop 0.3s ease-out;
        }
        @keyframes heartPop {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
    </style>

    <style>
        /* Modern Quantity Selector Styles */
        .qty-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            width: fit-content;
            margin-right: 15px;
        }
        .qty-btn {
            width: 40px;
            height: 44px;
            background: #f8fafc;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #64748b;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-btn:hover { background: #e2e8f0; color: #0f172a; }
        .qty-input {
            width: 50px;
            height: 44px;
            border: none;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
            color: #0f172a;
            outline: none;
            -moz-appearance: textfield;
        }
        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

        /* Fix Tab Visibility */
        [x-cloak] { display: none !important; }
    </style>
@endpush
@section('content')

    <!-- Inject Data Safely -->
    <script>
        window.productData = {
            product: @json($product),
            variants: @json($product->variants),
            inWishlist: @json($inWishlist),
            defaultImage: "{{ $product->getFirstMediaUrl('product_images') ?: asset('images/placeholder.jpg') }}",
            currency: "{{ settings('currency_symbol', 'AED') }}",
            galleryImages: @json($product->getMedia('product_images')->skip(1)->map(fn($m) => ['thumb' => $m->getUrl('thumb'), 'full' => $m->getUrl()]))
        };
    </script>

    <div class="container" x-data="productComponent()" x-cloak>

        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <a href="{{ url('/') }}">Home</a> <span>/</span>
            @if($product->category && $product->category->parent)
                <a href="{{ route('category.show', $product->category->parent->slug) }}">{{ $product->category->parent->name }}</a> <span>/</span>
            @endif
            @if($product->category)
                <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a> <span>/</span>
            @endif
            <span style="color:var(--text-main); font-weight:600;">{{ $product->name }}</span>
        </div>

        <div class="product-wrapper">

            <!-- Gallery Section -->
            <div class="gallery-section">
                <div class="main-image">
                    <img :src="activeImage" :alt="product.name">
                </div>

                <div class="thumbnails">
                    <div class="thumb"
                         :class="activeImage === defaultImage ? 'active' : ''"
                         @click="activeImage = defaultImage">
                        <img :src="defaultImage" alt="Main">
                    </div>
                    <template x-for="img in galleryImages">
                        <div class="thumb"
                             :class="activeImage === img.full ? 'active' : ''"
                             @click="activeImage = img.full">
                            <img :src="img.thumb" alt="Gallery">
                        </div>
                    </template>
                </div>
            </div>

            <!-- Info Section -->
            <div class="info-section">
                @if($product->brand)
                    <div class="p-brand">{{ $product->brand->name }}</div>
                @endif

                <h1 class="p-title">{{ $product->name }}</h1>

                <div class="p-rating">
                    <div class="stars">
                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-half-line"></i>
                    </div>
                    <span>4.5 Star Rating</span>
                    <span style="color:#cbd5e1">|</span>
                    <span class="text-xs text-gray-500">SKU: <span x-text="currentSku"></span></span>
                </div>

                <!-- Price Box -->
                <div class="p-price-box">
                    <div class="current-price">
                        <span x-text="formatPrice(currentPrice)"></span>
                        <span style="font-size: 0.6em; margin-left:5px" x-text="currency"></span>

                        <span class="old-price" x-show="isOnSale">
                        <span x-text="formatPrice(currentOldPrice)"></span> <span x-text="currency"></span>
                    </span>
                    </div>
                    <span style="font-size: 12px; color: var(--text-muted); display: block; margin-top: 5px;">Inclusive of VAT</span>
                </div>

                <!-- Variants -->
                <template x-if="isVariable && variants.length > 0">
                    <div class="option-group">
                        <span class="option-title">Select Option:</span>
                        <div class="options-grid">
                            <template x-for="(variant, index) in variants" :key="variant.id">
                                <div>
                                    <input type="radio"
                                           name="variant_id"
                                           :id="'var_' + variant.id"
                                           :value="variant.id"
                                           class="opt-radio"
                                           x-model="selectedVariantId"
                                           @change="updateVariant()">

                                    <label :for="'var_' + variant.id" class="opt-label">
                                        <span x-text="variant.variant_name"></span>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Quantity & Actions -->
                <div class="action-box" style="align-items: stretch;">

                    <!-- NEW: Quantity Selector -->
                    <div class="qty-wrapper">
                        <button type="button" class="qty-btn" @click="decrementQty()">-</button>
                        <input type="number" class="qty-input" x-model="qty" min="1" readonly>
                        <button type="button" class="qty-btn" @click="incrementQty()">+</button>
                    </div>

                    <!-- Add to Cart -->
                    <button type="button"
                            class="btn-main"
                            @click="addToCart(product.id, qty, selectedVariantId)"
                            :disabled="!isInStock"
                            :style="!isInStock ? 'background: #94a3b8; cursor: not-allowed;' : ''">
                        <i class="ri-shopping-cart-2-fill"></i>
                        <span x-text="isInStock ? 'Add to Cart' : 'Out of Stock'"></span>
                    </button>

                    <button type="button"
                            class="btn-wish"
                            :class="{ 'active': inWishlist }"
                            @click="toggleWishlist()">
                        <!-- Dynamic Icon: Line vs Fill -->
                        <i :class="inWishlist ? 'ri-heart-3-fill' : 'ri-heart-3-line'"></i>
                    </button>
                </div>

                <div class="trust-box">
                    <div class="trust-item">
                        <i class="ri-truck-line t-icon"></i>
                        <div class="t-text"><h4>Fast Delivery</h4><p>Order within 4 hrs.</p></div>
                    </div>
                    <div class="trust-item">
                        <i class="ri-shield-check-line t-icon"></i>
                        <div class="t-text"><h4>Official Warranty</h4><p>1 Year Warranty included.</p></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="tabs-container">
            <div class="tabs-head">
                <div class="tab-link" :class="activeTab === 'desc' ? 'active' : ''" @click="activeTab = 'desc'">Description</div>
                <div class="tab-link" :class="activeTab === 'specs' ? 'active' : ''" @click="activeTab = 'specs'">Specifications</div>
                <div class="tab-link" :class="activeTab === 'reviews' ? 'active' : ''" @click="activeTab = 'reviews'">Reviews (0)</div>
            </div>

            <!-- Description Tab -->
            <div class="tab-content" x-show="activeTab === 'desc'">
                {!! $product->description ?? '<p>No description available.</p>' !!}
            </div>

            <!-- Specifications Tab (Fixed) -->
            <div class="tab-content" x-show="activeTab === 'specs'">
                @if(!empty($product->specifications))
                    <div class="prose max-w-none">
                        {!! $product->specifications !!}
                    </div>
                @else
                    <p class="text-gray-500 italic">No specific details available for this product.</p>
                @endif
            </div>

            <!-- Reviews Tab -->
            <div class="tab-content" x-show="activeTab === 'reviews'">
                <p class="text-gray-500">No reviews yet.</p>
            </div>
        </div>

        <!-- Related Products -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <h3 class="related-title">You might also like</h3>
            <div class="grid-5" style="margin-bottom: 60px;">
                @foreach($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        @endif
    </div>

    <!-- Alpine Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productComponent', () => ({
                product: window.productData.product,
                variants: window.productData.variants,
                galleryImages: window.productData.galleryImages,
                defaultImage: window.productData.defaultImage,
                currency: window.productData.currency,

                // State
                activeImage: window.productData.defaultImage,
                activeTab: 'desc',
                selectedVariantId: null,
                qty: 1, // Quantity State

                inWishlist: window.productData.inWishlist,

                // Reactive
                currentPrice: 0,
                currentOldPrice: 0,
                currentSku: '',
                isOnSale: false,
                isInStock: true,
                isVariable: false,
                currentStockLimit: 0, // Track max stock

                init() {
                    this.isVariable = this.product.type === 'variable';
                    this.currentSku = this.product.sku;

                    if (this.isVariable && this.variants.length > 0) {
                        this.selectedVariantId = this.variants[0].id;
                        this.updateVariant();
                    } else {
                        this.setupSimpleProduct();
                    }
                },
                async toggleWishlist() {
                    try {
                        const response = await fetch("{{ route('wishlist.toggle') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ product_id: this.product.id })
                        });

                        const data = await response.json();

                        if (data.status === 'guest') {
                            // Redirect to login or show error
                            toastr.warning(data.message);
                            // Optional: window.location.href = "{{ route('customer.login') }}";
                        } else if (data.status === 'added') {
                            this.inWishlist = true;
                            toastr.success(data.message);
                        } else if (data.status === 'removed') {
                            this.inWishlist = false;
                            toastr.info(data.message);
                        }
                    } catch (error) {
                        console.error(error);
                        toastr.error('Something went wrong.');
                    }
                },
                setupSimpleProduct() {
                    const regular = parseFloat(this.product.selling_price || 0);
                    const sale = parseFloat(this.product.sale_price || 0);

                    this.currentOldPrice = regular;
                    this.currentStockLimit = parseInt(this.product.stock_quantity);

                    if (sale > 0 && sale < regular) {
                        this.currentPrice = sale;
                        this.isOnSale = true;
                    } else {
                        this.currentPrice = regular;
                        this.isOnSale = false;
                    }

                    this.isInStock = this.currentStockLimit > 0;
                },

                updateVariant() {
                    const variant = this.variants.find(v => v.id == this.selectedVariantId);
                    if (!variant) return;

                    const regular = parseFloat(variant.selling_price || 0);
                    const sale = parseFloat(variant.sale_price || 0);

                    this.currentOldPrice = regular;
                    this.currentSku = variant.sku;
                    this.currentStockLimit = parseInt(variant.stock_quantity);

                    if (sale > 0 && sale < regular) {
                        this.currentPrice = sale;
                        this.isOnSale = true;
                    } else {
                        this.currentPrice = regular;
                        this.isOnSale = false;
                    }

                    this.isInStock = this.currentStockLimit > 0;

                    // Reset qty if it exceeds new stock limit (optional)
                    if (this.qty > this.currentStockLimit) this.qty = 1;
                },

                incrementQty() {
                    if (this.qty < this.currentStockLimit) {
                        this.qty++;
                    }
                },

                decrementQty() {
                    if (this.qty > 1) {
                        this.qty--;
                    }
                },

                formatPrice(value) {
                    return new Intl.NumberFormat('en-US').format(value);
                }
            }));
        });
    </script>


@endsection
