<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic SEO Tags --}}
    <title>@yield('title', ($settings['meta_title'] ?? 'Tech Hub') . ' | Computer Trading')</title>
    <meta name="description" content="@yield('meta_description', $settings['meta_description'] ?? 'Your premier destination for high-performance computing, custom gaming builds, and enterprise IT solutions.')">
    <meta name="keywords" content="@yield('meta_keywords', $settings['meta_keywords'] ?? 'computer, gaming pc, laptop, dubai, tech hub')">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ settings('site_favicon') ? asset(settings('site_favicon')) : asset('favicon.ico') }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', ($settings['meta_title'] ?? 'Tech Hub') . ' | Computer Trading')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:image" content="@yield('meta_image', asset('images/default-share-image.jpg'))">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Tailwind CSS (for modern UI components) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        /* Animation for Cart Update */
        @keyframes bump {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .action-item.bump {
            animation: bump 0.3s ease-out;
            color: var(--brand-magenta); /* Flash color */
        }
        /* --- OFFCANVAS CART --- */
        .cart-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5); z-index: 1040;
            opacity: 0; pointer-events: none; transition: 0.3s;
        }
        .cart-overlay.open { opacity: 1; pointer-events: all; }

        .cart-sidebar {
            position: fixed; top: 0; right: 0; bottom: 0;
            width: 350px; background: white; z-index: 1050;
            box-shadow: -5px 0 25px rgba(0,0,0,0.15);
            transform: translateX(100%); transition: transform 0.3s ease-in-out;
            display: flex; flex-direction: column;
        }
        .cart-sidebar.open { transform: translateX(0); }

        .cart-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .cart-header h3 { font-size: 1.1rem; font-weight: 700; margin: 0; }
        .btn-close { font-size: 1.5rem; cursor: pointer; color: var(--text-muted); transition: 0.2s; }
        .btn-close:hover { color: var(--accent-red); }

        .cart-body { flex: 1; overflow-y: auto; padding: 20px; }
        .cart-item-mini { display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; position: relative; }
        .cart-item-mini:last-child { border-bottom: none; }
        .mini-img { width: 60px; height: 60px; border: 1px solid var(--border); border-radius: 6px; padding: 5px; display: flex; align-items: center; justify-content: center; }
        .mini-img img { max-height: 100%; }
        .mini-info h4 { font-size: 0.9rem; font-weight: 600; margin: 0 0 5px; line-height: 1.3; }
        .mini-meta { font-size: 0.8rem; color: var(--text-muted); }
        .mini-price { font-weight: 700; color: var(--brand-deep-blue); margin-top: 5px; display: block; }
        /* --- UPDATED SIDEBAR CSS --- */
        .cart-item-mini {
            position: relative; /* Needed for absolute positioning of remove btn */
            display: flex; gap: 15px; margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;
            transition: background 0.2s;
        }

        .btn-remove-mini {
            position: absolute;
            top: 0;
            right: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #fff1f2; /* Light Red Background */
            color: #ef4444;       /* Red Icon */
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .btn-remove-mini:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }

        .cart-footer { padding: 20px; border-top: 1px solid var(--border); background: #f8fafc; }
        .mini-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.1rem; margin-bottom: 15px; }
        .btn-cart-view, .btn-checkout-mini { display: block; width: 100%; text-align: center; padding: 12px; border-radius: var(--radius); font-weight: 600; transition: 0.2s; margin-bottom: 10px; }
        .btn-cart-view { background: white; border: 1px solid var(--border); color: var(--text-main); }
        .btn-cart-view:hover { border-color: var(--brand-deep-blue); color: var(--brand-deep-blue); }
        .btn-checkout-mini { background: var(--brand-gradient); color: white; border: none; }
        .btn-checkout-mini:hover { opacity: 0.9; }

        /* --- AJAX SEARCH RESULTS --- */
        .search-results {
            position: absolute; top: calc(100% + 5px); left: 0; right: 0;
            background: white; border-radius: var(--radius);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 1060; overflow: hidden;
            border: 1px solid var(--border);
            display: none;
        }
        .search-results.open { display: block; }

        .result-item {
            display: flex; gap: 12px; padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            transition: 0.2s; cursor: pointer;
            align-items: center;
        }
        .result-item:last-child { border-bottom: none; }
        .result-item:hover { background: #f8fafc; }

        .res-img { width: 45px; height: 45px; border-radius: 4px; border: 1px solid #f1f5f9; padding: 3px; display: flex; align-items: center; justify-content: center; background: #fff; flex-shrink: 0; }
        .res-img img { max-height: 100%; object-fit: contain; }

        .res-info { flex: 1; min-width: 0; }
        .res-name { font-size: 0.9rem; font-weight: 600; color: var(--text-main); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .res-price { font-size: 0.8rem; font-weight: 700; color: var(--brand-deep-blue); }
        .res-old { font-size: 0.75rem; color: var(--text-muted); text-decoration: line-through; margin-left: 5px; font-weight: 400; }

        .no-results { padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem; }
        .search-loader { position: absolute; right: 45px; top: 18px; color: var(--brand-magenta); font-size: 1.2rem; display: none; }
        .search-loader.active { display: block; }
        .search-box input{
            color: var(--text-main);
        }
    </style>
    <!-- Dynamic Google Analytics -->
    @if(settings('google_analytics_id'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ settings('google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ settings('google_analytics_id') }}');
        </script>
    @endif

    <!-- Meta Pixel Code -->
    @if(settings('facebook_pixel_id'))
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ settings('facebook_pixel_id') }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ settings('facebook_pixel_id') }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Meta Pixel Code -->
    @endif

    @stack('styles')
</head>
<body x-data="{ isCartOpen: false, isNavOpen: false, cartCount: {{ count(session('cart', [])) }} }"
      @cart-updated.window="cartCount = $event.detail.count; $dispatch('refresh-mini-cart');">

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-links">
            <a href="tel:{{ $settings['contact_phone'] ?? '+971 4 000 0000' }}"><i class="ri-phone-line"></i> {{ $settings['contact_phone'] ?? '+971 4 000 0000' }}</a>
            <a href="mailto:{{ $settings['contact_email'] ?? 'sales@techhub.ae' }}"><i class="ri-mail-line"></i> {{ $settings['contact_email'] ?? 'sales@techhub.ae' }}</a>
        </div>
        <div class="top-links">
            <a href="{{ route('store.locator') }}">Store Locator</a>
            <a href="{{ url('/track-order') }}">Track Order</a>
            <a href="#"><b>AED</b></a>
        </div>
    </div>
</div>

<!-- Header -->
<header>
    <div class="container header-wrapper">
        <a href="{{ url('/') }}" class="logo">
            @if(settings('site_logo'))
                <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" class="logo-img logo-primary">
                @if(settings('site_logo_scrolled'))
                    <img src="{{ settings('site_logo_scrolled') }}" alt="{{ settings('site_name') }}" class="logo-img logo-scrolled">
                @endif
            @else
                <div class="logo-main">
                    <span class="logo-tech">TECH</span>
                    <span class="logo-hub">HUB</span>
                </div>
                <div class="logo-sub">COMPUTER TRADING</div>
            @endif
        </a>

        <button class="nav-toggle" @click="isNavOpen = true">
            <i class="ri-menu-line"></i>
        </button>

        <div class="search-box" x-data="searchHandler()" @click.away="isOpen = false">
            <form action="{{ url('/search') }}" method="GET" @submit="isLoading = true">
                <input type="text" name="q" 
                       placeholder="Search for components, laptops, or accessories..."
                       x-model="query"
                       @input.debounce.300ms="search()"
                       @focus="if(query.length > 1) isOpen = true"
                       autocomplete="off">
                <button type="submit" class="search-btn"><i class="ri-search-line"></i></button>
            </form>

            <!-- Loader Icon -->
            <div class="search-loader" :class="{ 'active': isLoading }">
                <i class="ri-loader-4-line animate-spin"></i>
            </div>

            <!-- Results Dropdown -->
            <div class="search-results" :class="{ 'open': isOpen }">
                <template x-if="results.length > 0">
                    <div>
                        <template x-for="item in results" :key="item.id">
                            <a :href="item.url" class="result-item">
                                <div class="res-img">
                                    <img :src="item.image" :alt="item.name">
                                </div>
                                <div class="res-info">
                                    <div class="res-name" x-text="item.name"></div>
                                    <div class="res-price">
                                        <span x-text="item.price"></span>
                                        <template x-if="item.is_sale">
                                            <span class="res-old" x-text="item.original_price"></span>
                                        </template>
                                    </div>
                                </div>
                            </a>
                        </template>
                        <a :href="'{{ url('/search') }}?q=' + query" class="result-item" style="justify-content: center; background: #f1f5f9; font-weight: 700; color: var(--brand-deep-blue);">
                            View All Results
                        </a>
                    </div>
                </template>
                <template x-if="results.length === 0 && query.length > 1 && !isLoading">
                    <div class="no-results">
                        <i class="ri-search-line" style="font-size: 1.5rem; display: block; margin-bottom: 5px; opacity: 0.5;"></i>
                        No products found for "<span x-text="query"></span>"
                    </div>
                </template>
            </div>
        </div>

        <div class="user-actions">

            <!-- 1. Dynamic Login / Account Link -->
            @auth('customer')
                <a href="{{ route('customer.dashboard') }}" class="action-item">
                    <i class="ri-user-smile-line"></i>
                    <span>Account</span>
                </a>
            @else
                <a href="{{ route('customer.login') }}" class="action-item">
                    <i class="ri-user-line"></i>
                    <span>Login</span>
                </a>
            @endauth

            <!-- 2. Dynamic Wishlist Icon -->
            <a href="{{ route('customer.wishlist') }}"
               class="action-item"
               x-data="{ count: {{ $wishlistCount ?? 0 }} }"
               @wishlist-updated.window="count = $event.detail.count; $el.classList.add('bump'); setTimeout(() => $el.classList.remove('bump'), 300)">

                <i class="ri-heart-line"></i>
                <span>Saved</span>

                <!-- Badge -->
                <div class="cart-count"
                     x-show="count > 0"
                     x-text="count"
                     style="display: none;"
                     :style="count > 0 ? 'display:flex' : 'display:none'">
                    {{ $wishlistCount ?? 0 }}
                </div>
            </a>

            <!-- 3. Existing Cart Icon -->
            <a href="#" class="action-item" @click.prevent="isCartOpen = true; $dispatch('refresh-mini-cart')">
                <i class="ri-shopping-cart-2-line"></i>
                <span>Cart</span>
                <div class="cart-count" x-show="cartCount > 0" x-text="cartCount" x-transition.scale>
                    {{ count(session('cart', [])) }}
                </div>
            </a>
        </div>
    </div>
</header>

<!-- Navigation -->
<div class="nav-bar">
    <div class="container">
        <ul class="nav-list">
            <li class="{{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}">Home</a>
            </li>
             <li class="{{ request()->is('solutions*') ? 'active' : '' }}">
                <a href="{{ route('solutions.index') }}">IT Solutions</a>
            </li>
            @foreach($headerCategories as $category)
                <li class="{{ request()->route('id') == $category->id ? 'active' : '' }}">
                    <a href="{{ route('category.show', ['slug' => $category->slug]) }}">
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- Main Dynamic Content -->
<main>
    @yield('content')
</main>

<!-- OFFCANVAS CART COMPONENT -->
<div class="cart-overlay" :class="{ 'open': isCartOpen }" @click="isCartOpen = false"></div>

<div class="cart-sidebar" :class="{ 'open': isCartOpen }">

    <!-- Header -->
    <div class="cart-header">
        <h3>Shopping Cart (<span x-text="cartCount"></span>)</h3>
        <span class="btn-close" @click="isCartOpen = false">&times;</span>
    </div>

    <!-- Body (Dynamic Content) -->
    <div class="cart-body"
         x-data="{ cartHtml: '<div class=\'text-center p-5 text-gray-500\'>Loading...</div>' }"
         @refresh-mini-cart.window="fetch('{{ route('cart.mini') }}').then(r => r.text()).then(h => cartHtml = h)">

        <div x-html="cartHtml"></div>
    </div>

    <!-- Footer -->
    <div class="cart-footer">
        <a href="{{ route('cart.index') }}" class="btn-cart-view">View Cart</a>
        <a href="{{ route('checkout.index') }}" class="btn-checkout-mini">Checkout</a>
    </div>
</div>

<!-- OFFCANVAS NAV MENU -->
<div class="nav-overlay" :class="{ 'open': isNavOpen }" @click="isNavOpen = false"></div>

<div class="nav-sidebar" :class="{ 'open': isNavOpen }">
    <div class="nav-sidebar-header">
        <div class="logo">
            @if(settings('site_logo_scrolled'))
                <img src="{{ settings('site_logo_scrolled') }}" alt="{{ settings('site_name') }}" class="logo-img" style="max-height: 35px;">
            @elseif(settings('site_logo'))
                <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" class="logo-img" style="max-height: 35px;">
            @else
                <div class="logo-main">
                    <span class="logo-tech">TECH</span>
                    <span class="logo-hub">HUB</span>
                </div>
            @endif
        </div>
        <span class="btn-close" @click="isNavOpen = false">&times;</span>
    </div>

    <div class="nav-sidebar-body">
        <ul class="mobile-nav-list">
            <li class="{{ request()->is('/') ? 'active' : '' }}">
                <a href="{{ url('/') }}">Home</a>
            </li>
            @foreach($headerCategories as $category)
                <li class="{{ request()->route('id') == $category->id ? 'active' : '' }}">
                    <a href="{{ route('category.show', ['slug' => $category->slug]) }}">
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach

            <li class="{{ request()->is('solutions*') ? 'active' : '' }}">
                <a href="{{ route('solutions.index') }}">IT Solutions</a>
            </li>

            <li>
                <a href="{{ url('/clearance') }}" style="color: #ef4444">Clearance</a>
            </li>
        </ul>
        
        <div class="mobile-nav-contact" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border);">
            <a href="tel:{{ $settings['contact_phone'] ?? '+971 4 000 0000' }}" style="display: block; margin-bottom: 10px;"><i class="ri-phone-line"></i> {{ $settings['contact_phone'] ?? '+971 4 000 0000' }}</a>
            <a href="mailto:{{ $settings['contact_email'] ?? 'sales@techhub.ae' }}" style="display: block;"><i class="ri-mail-line"></i> {{ $settings['contact_email'] ?? 'sales@techhub.ae' }}</a>
        </div>
    </div>
</div>
<!-- Footer -->
<footer>
    <div class="container">
        <div class="foot-grid">
            <div class="f-col">
                <div class="logo" style="margin-bottom:15px; font-size:18px;">
                    @if(settings('site_logo_footer'))
                        <img src="{{ settings('site_logo_footer') }}" alt="{{ settings('site_name') }}" class="logo-img" style="max-height: 66px;">
                    @elseif(settings('site_logo_scrolled'))
                        <img src="{{ settings('site_logo_scrolled') }}" alt="{{ settings('site_name') }}" class="logo-img" style="max-height: 66px;">
                    @elseif(settings('site_logo'))
                        <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" class="logo-img" style="max-height: 66px;">
                    @else
                        <div class="logo-main">
                            <span class="logo-tech">TECH</span>
                            <span class="logo-hub">HUB</span>
                        </div>
                    @endif
                </div>
                <p style="color:#94a3b8; font-size:13px; line-height:1.6; margin-bottom:20px;">
                    {!! nl2br(e(settings('footer_description', 'Your premier destination for high-performance computing, custom gaming builds, and enterprise IT solutions in the UAE.'))) !!}
                </p>
                <div class="social-icons">
                    @if(settings('social_instagram'))
                        <a href="{{ settings('social_instagram') }}" target="_blank"><i class="ri-instagram-fill"></i></a>
                    @endif
                    @if(settings('social_facebook'))
                        <a href="{{ settings('social_facebook') }}" target="_blank"><i class="ri-facebook-circle-fill"></i></a>
                    @endif
                    @if(settings('social_linkedin'))
                        <a href="{{ settings('social_linkedin') }}" target="_blank"><i class="ri-linkedin-box-fill"></i></a>
                    @endif
                    @if(settings('social_twitter'))
                        <a href="{{ settings('social_twitter') }}" target="_blank"><i class="ri-twitter-x-fill"></i></a>
                    @endif
                </div>
            </div>

            <div class="f-col">
                <h4>Categories</h4>
                <ul>
                    @foreach($footerCategories as $cat)
                        <li><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="f-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="{{ url('/track-order') }}">Track Order</a></li>
                    <li><a href="{{ route('store.locator') }}">Store Locator</a></li>
                    @foreach($footerPages as $page)
                        <li><a href="{{ route('pages.show', $page->slug) }}">{{ $page->title }}</a></li>
                    @endforeach
                    <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                </ul>
            </div>

            <div class="f-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="#"><i class="ri-map-pin-line"></i> {{ $settings['contact_address'] ?? 'Computer Street, Bur Dubai, UAE' }}</a></li>
                    <li><a href="tel:{{ $settings['contact_phone'] ?? '+971 4 000 0000' }}"><i class="ri-phone-fill"></i> {{ $settings['contact_phone'] ?? '+971 4 000 0000' }}</a></li>
                    <li><a href="mailto:{{ $settings['contact_email'] ?? 'support@techhub.ae' }}"><i class="ri-mail-fill"></i> {{ $settings['contact_email'] ?? 'support@techhub.ae' }}</a></li>
                </ul>
                <div class="payment-icons">
                    <i class="ri-visa-line"></i>
                    <i class="ri-mastercard-line"></i>
                    <i class="ri-paypal-line"></i>
                </div>
            </div>
        </div>

        <div style="text-align:center; border-top:1px solid #1e293b; padding-top:20px; color:#64748b; font-size:12px;">
            &copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Tech Hub' }} Computer Trading LLC. All Rights Reserved.
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-bottom-right", // or toast-top-right
        "timeOut": "3000",
    };

    // GLOBAL ADD TO CART FUNCTION
    async function addToCart(productId, quantity = 1, variantId = null) {

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('_token', '{{ csrf_token() }}');

        if (variantId) {
            formData.append('variant_id', variantId);
        }

        try {
            const response = await fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                // 1. Show Success Toast
                toastr.success(data.message);

                // 2. Dispatch Event to Update Header Badge
                // This talks to the x-data in the header we set up in Step B
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { count: data.cartCount }
                }));

            } else {
                // Show Error Toast (e.g., Out of Stock)
                toastr.error(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            toastr.error('Something went wrong. Please try again.');
        }
    }

    // GLOBAL REMOVE FROM CART FUNCTION
    async function removeFromCart(cartKey) {

        const formData = new FormData();
        formData.append('key', cartKey);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const response = await fetch("{{ route('cart.remove') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                // 1. Show Toast
                toastr.info(data.message);

                // 2. Update Header Badge
                window.dispatchEvent(new CustomEvent('cart-updated', {
                    detail: { count: data.cartCount }
                }));

                // 3. Refresh Sidebar Content
                window.dispatchEvent(new CustomEvent('refresh-mini-cart'));

                // 4. (Optional) If on the main cart page, reload to update the big table
                if (window.location.pathname === '/cart') {
                    window.location.reload();
                }

            } else {
                toastr.error('Could not remove item.');
            }
        } catch (error) {
            console.error('Error:', error);
            toastr.error('Something went wrong.');
        }
    }

    // Toggle Wishlist Function
    async function toggleWishlist(productId, btnElement = null) {
        try {
            const response = await fetch("{{ route('wishlist.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();

            if (data.status === 'guest') {
                toastr.warning(data.message);
                // Optional: window.location.href = "{{ route('customer.login') }}";
            } else {
                // Success Message
                data.status === 'added' ? toastr.success(data.message) : toastr.info(data.message);

                // 1. UPDATE HEADER BADGE (Realtime)
                window.dispatchEvent(new CustomEvent('wishlist-updated', {
                    detail: { count: data.count }
                }));

                // 2. TOGGLE BUTTON VISUALS (If button element passed)
                if (btnElement) {
                    if (data.status === 'added') {
                        btnElement.classList.add('active');
                        if(btnElement.querySelector('i'))
                            btnElement.querySelector('i').classList.replace('ri-heart-line', 'ri-heart-fill');
                    } else {
                        btnElement.classList.remove('active');
                        if(btnElement.querySelector('i'))
                            btnElement.querySelector('i').classList.replace('ri-heart-fill', 'ri-heart-line');
                    }
                }
            }
        } catch (error) {
            console.error(error);
            toastr.error('Something went wrong.');
        }
    }
    // Alpine.js Search Handler
    function searchHandler() {
        return {
            query: '',
            results: [],
            isOpen: false,
            isLoading: false,
            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    this.isOpen = false;
                    return;
                }

                this.isLoading = true;
                this.isOpen = true;

                try {
                    const response = await fetch(`{{ route('search.ajax') }}?q=${encodeURIComponent(this.query)}`);
                    this.results = await response.json();
                } catch (error) {
                    console.error('Search error:', error);
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }

    // Header Scroll Effect
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>
<script src="//unpkg.com/alpinejs" defer></script>

@include('frontend.partials.offer-popup')

@stack('scripts')

</body>
</html>
