@extends('layouts.frontend')

@section('title', isset($page) && $page->meta_title ? $page->meta_title : ($currentCategory ? ($currentCategory->meta_title ?: $currentCategory->name) : 'Shop All Products'))

@if($currentCategory)
    @if($currentCategory->meta_description) @section('meta_description', $currentCategory->meta_description) @endif
    @if($currentCategory->meta_keywords) @section('meta_keywords', $currentCategory->meta_keywords) @endif
@endif

@if(isset($page) && is_object($page))
    @if($page->meta_description) @section('meta_description', $page->meta_description) @endif
    @if($page->meta_keywords) @section('meta_keywords', $page->meta_keywords) @endif
    @if($page->canonical_url) @section('canonical', $page->canonical_url) @endif
@endif

@push('styles')
    <style>
        /* --- BREADCRUMBS --- */
        .breadcrumbs { margin: 20px 0; font-size: 12px; color: var(--text-muted); }
        .breadcrumbs span { margin: 0 5px; }
        .breadcrumbs a:hover { color: var(--brand-deep-blue); }

        /* Layout */
        .shop-layout { display: grid; grid-template-columns: 260px 1fr; gap: 30px; margin-bottom: 60px; }
        .sidebar { align-self: start; position: sticky; top: 100px; z-index: 40; }

        /* Filters Container */
        .filter-box {
            background: white; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow);
        }

        .filter-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }

        /* Collapsible Sections */
        details { margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
        details:last-of-type { border-bottom: none; }
        summary { cursor: pointer; font-weight: 700; font-size: 13px; display: flex; justify-content: space-between; color: var(--brand-deep-blue); outline: none; }
        summary::-webkit-details-marker { display: none; }
        summary::after { content: '+'; font-size: 16px; font-weight: 400; color: var(--text-muted); }
        details[open] summary::after { content: '-'; }

        .filter-options { margin-top: 10px; max-height: 250px; overflow-y: auto; }

        /* Checkboxes */
        .checkbox-group { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-muted); cursor: pointer; margin-bottom: 6px; }
        .checkbox-group:hover { color: var(--brand-emerald); }
        .checkbox-group input { accent-color: var(--brand-emerald); width: 16px; height: 16px; border-radius: 4px; border: 1px solid #cbd5e1; }

        /* Price Inputs */
        .price-inputs { display: flex; gap: 5px; margin-bottom: 10px; }
        .price-inputs input { width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 4px; font-size: 12px; }

        /* Buttons */
        .btn-filter-apply {
            width: 100%; background: var(--brand-deep-blue); color: white; padding: 12px;
            border: none; border-radius: var(--radius); font-weight: 700; cursor: pointer; transition: 0.2s; margin-top: 10px;
        }
        .btn-filter-apply:hover { background: var(--brand-emerald); }

        /* Toolbar */
        .toolbar { background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: var(--shadow); }
        .sort-select { padding: 8px 10px; border: 1px solid var(--border); border-radius: 4px; font-size: 13px; cursor: pointer; }

        /* Mobile Filter Button (Hidden on Desktop) */
        .mobile-filter-btn { display: none; }
        .mobile-close-btn { display: none; }
        .offcanvas-overlay { display: none; }

        @media (max-width: 750px) {
            .p-price{
                font-size: 14px;
            }
        }
        /* --- RESPONSIVE STYLES --- */
        @media (max-width: 900px) {
            .shop-layout { grid-template-columns: 1fr; }

            /* Offcanvas Sidebar */
            .sidebar {
                position: fixed; top: 0; left: 0; bottom: 0;
                width: 300px;
                height: 100%;
                background: white;
                transform: translateX(-100%); transition: transform 0.3s ease-in-out;
                z-index: 1000; overflow-y: auto; padding: 25px 20px; /* Increased top padding */
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }
            .sidebar.open { transform: translateX(0); }

            /* Overlay */
            .offcanvas-overlay {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
                display: none;
            }
            .offcanvas-overlay.open { display: block; }

            /* Fix Overlap: Mobile Close Button */
            .mobile-close-btn {
                display: flex; align-items: center; justify-content: center;
                position: absolute;
                top: 15px;
                right: 15px;
                background: #f1f5f9; /* Gray background */
                border: none;
                width: 32px; height: 32px; border-radius: 50%; /* Circular shape */
                font-size: 20px; color: var(--text-muted);
                cursor: pointer;
                z-index: 1001; /* Ensure on top */
            }
            .mobile-close-btn:hover { background: #e2e8f0; color: #ef4444; }

            /* Fix Overlap: Add padding to the header inside sidebar so text doesn't touch the X */
            .sidebar .filter-header {
                padding-right: 40px; /* Make space for the absolute close button */
            }

            /* Mobile Filter Trigger Button */
            .mobile-filter-btn {
                display: flex; align-items: center; gap: 5px;
                background: white; border: 1px solid var(--border);
                padding: 8px 15px; border-radius: 4px; font-weight: 600; font-size: 13px;
                margin-bottom: 20px; width: 100%; justify-content: center;
                color: var(--brand-deep-blue);
            }

            /* 2-Column Grid for Mobile */
            .grid-5 {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }

            /* Compact Product Card for Mobile */
            .product-card .card-content { padding: 10px; }
            .product-card .card-title { font-size: 13px; margin-bottom: 5px; }
            .product-card .card-price { font-size: 15px; }
            .product-card .btn-plus { width: 30px; height: 30px; font-size: 16px; }
            .product-card .card-img-wrap { height: 140px; padding: 10px; }
            .product-card .card-cat { font-size: 10px; margin-bottom: 4px; }
            .product-card .card-rating { margin-bottom: 5px; }
            .product-card .card-footer { align-items: center; }
            .product-card .vat-text { display: none; } /* Hide VAT text on small screens to save space */
        }
    </style>
@endpush

@section('content')
    <!-- Wrap everything in Alpine x-data to control sidebar state -->
    <div class="container" x-data="{ showFilters: false }">

        <div class="breadcrumbs">
            <a href="{{ url('/') }}">Home</a> <span>/</span>
            <span style="color:var(--text-main); font-weight:600;">Shop</span>
        </div>

        <!-- Mobile Filter Button Trigger -->
        <button class="mobile-filter-btn" @click="showFilters = true">
            <i class="ri-filter-3-line"></i> Filter & Sort
        </button>

        <!-- Overlay Background -->
        <div class="offcanvas-overlay" :class="{ 'open': showFilters }" @click="showFilters = false"></div>

        <div class="shop-layout">

            <!-- SIDEBAR FILTER FORM -->
            <aside class="sidebar" :class="{ 'open': showFilters }">

                <!-- Close Button (Mobile Only) -->
                <button type="button" class="mobile-close-btn" @click="showFilters = false">&times;</button>

                <form id="filterForm" action="{{ route('shop.index') }}" method="GET">

                    <!-- Hidden Sort Input -->
                    <input type="hidden" name="sort" id="hiddenSort" value="{{ request('sort', 'popular') }}">

                    <div class="filter-box">
                        <div class="filter-header">
                            <span style="font-weight:800; font-size:1.1rem; color: var(--brand-navy);">Filters</span>
                            <a href="{{ route('shop.index') }}" style="font-size:12px; color:#ef4444; text-decoration:underline;">Clear All</a>
                        </div>

                        <!-- 1. CATEGORIES -->
                        <details open>
                            <summary>Categories</summary>
                            <div class="filter-options">
                                @foreach($categories as $cat)
                                    <label class="checkbox-group">
                                        <input type="checkbox"
                                               name="categories[]"
                                               value="{{ $cat->id }}"
                                            {{ (in_array($cat->id, request('categories', [])) || ($currentCategory && $currentCategory->id == $cat->id)) ? 'checked' : '' }}>
                                        {{ $cat->name }}
                                    </label>

                                    @if($cat->children->count() > 0)
                                        <div style="margin-left: 20px; border-left: 2px solid #f1f5f9; padding-left: 10px;">
                                            @foreach($cat->children as $child)
                                                <label class="checkbox-group">
                                                    <input type="checkbox"
                                                           name="categories[]"
                                                           value="{{ $child->id }}"
                                                        {{ in_array($child->id, request('categories', [])) ? 'checked' : '' }}>
                                                    {{ $child->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </details>

                        <!-- 2. BRANDS -->
                        <details open>
                            <summary>Brands</summary>
                            <div class="filter-options">
                                @foreach($brands as $brand)
                                    <label class="checkbox-group">
                                        <input type="checkbox" name="brands[]" value="{{ $brand->id }}"
                                            {{ in_array($brand->id, request('brands', [])) ? 'checked' : '' }}>
                                        {{ $brand->name }}
                                    </label>
                                @endforeach
                            </div>
                        </details>

                        <!-- 3. ATTRIBUTES -->
                        @foreach($attributes as $attribute)
                            @if($attribute->values->count() > 0)
                                <details {{ request()->has('attribute_values') ? 'open' : '' }}>
                                    <summary>{{ $attribute->name }}</summary>
                                    <div class="filter-options">
                                        @foreach($attribute->values as $value)
                                            <label class="checkbox-group">
                                                <input type="checkbox"
                                                       name="attribute_values[]"
                                                       value="{{ $value->id }}"
                                                    {{ in_array($value->id, request('attribute_values', [])) ? 'checked' : '' }}>
                                                {{ $value->value }}
                                            </label>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        @endforeach

                        <!-- 4. PRICE -->
                        <details open>
                            <summary>Price (AED)</summary>
                            <div class="filter-options">
                                <div class="price-inputs">
                                    <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                                    <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </details>

                        <!-- APPLY BUTTON -->
                        <button type="submit" class="btn-filter-apply">Apply Filters</button>

                    </div>
                </form>
            </aside>

            <!-- PRODUCT LISTING -->
            <section class="product-listing">

                <div class="toolbar">
                    <span class="result-count">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</span>

                    <div class="sort-wrap">
                        <span style="font-size:13px; color:#64748b; display:none; @media(min-width:900px){display:inline;}">Sort by:</span>
                        <select class="sort-select" onchange="updateSort(this.value)">
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        </select>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="grid-5" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="shop-pagination mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div style="text-align:center; padding: 60px; background:white; border-radius:8px; border:1px solid #e2e8f0;">
                        <i class="ri-search-line" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
                        <h3 style="font-weight:700; color:#64748b;">No products found</h3>
                        <p style="color:#94a3b8; font-size:0.9rem;">Try adjusting your filters.</p>
                        <a href="{{ route('shop.index') }}" style="margin-top:15px; display:inline-block; color:var(--brand-emerald); font-weight:600; font-size:0.9rem;">Clear Filters</a>
                    </div>
                @endif

            </section>
        </div>
    </div>

    <!-- Ensure Alpine is loaded -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        function updateSort(value) {
            document.getElementById('hiddenSort').value = value;
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection
