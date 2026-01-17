@extends('layouts.frontend')

@section('title', 'Home | Tech Hub Computer Trading')
@section('meta_description', 'Best deals on Gaming PCs, Laptops, and Components in UAE.')

@section('content')
    <div class="container">

        <div class="hero">

            <!-- MAIN BANNER -->
            @if($mainBanner)
                <div class="hero-banner">
                    <!-- Background Image -->
                    <img src="{{ $mainBanner->getFirstMediaUrl('banner_image') }}" alt="{{ $mainBanner->title }}">

                    <div class="banner-content">
                        @if($mainBanner->badge_text)
                            <span style="background:var(--brand-magenta); padding:4px 10px; font-size:11px; border-radius:20px; font-weight:700;">
                    {{ $mainBanner->badge_text }}
                </span>
                        @endif

                        {{-- Use {!! !!} to allow <br> tags in the title --}}
                        <h2>{!! $mainBanner->title !!}</h2>

                        <p style="margin-bottom:25px; font-size:15px; opacity:0.9;">
                            {{ $mainBanner->subtitle }}
                        </p>

                        @if($mainBanner->button_text)
                            <a href="{{ $mainBanner->link }}" class="btn btn-brand">
                                {{ $mainBanner->button_text }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- SIDE BANNERS -->
            <div class="hero-side">

                <!-- Side Top -->
                @if($sideTop)
                    <a href="{{ $sideTop->link }}" class="promo-box">
                        <div class="promo-text">
                            <h4>{{ $sideTop->title }}</h4>
                            <p>{{ $sideTop->subtitle }}</p>
                        </div>
                        <img src="{{ $sideTop->getFirstMediaUrl('banner_image') }}" width="70" alt="{{ $sideTop->title }}">
                    </a>
                @endif

                <!-- Side Bottom -->
                @if($sideBottom)
                    <a href="{{ $sideBottom->link }}" class="promo-box">
                        <div class="promo-text">
                            <h4>{{ $sideBottom->title }}</h4>
                            <p>{{ $sideBottom->subtitle }}</p>
                        </div>
                        <img src="{{ $sideBottom->getFirstMediaUrl('banner_image') }}" width="70" alt="{{ $sideBottom->title }}">
                    </a>
                @endif

            </div>
        </div>

        <!-- NEW: SERVICE FEATURES ROW -->
        <div class="section-wrapper">
            <div class="services-row">
                <div class="service-box">
                    <div class="s-icon"><i class="ri-macbook-line"></i></div>
                    <div class="s-info">
                        <h4>Laptop Finder</h4>
                        <p>Find Your Laptop Easily</p>
                    </div>
                </div>
                <div class="service-box">
                    <div class="s-icon"><i class="ri-chat-voice-line"></i></div>
                    <div class="s-info">
                        <h4>Raise a Complain</h4>
                        <p>Share your experience</p>
                    </div>
                </div>
                <div class="service-box">
                    <div class="s-icon"><i class="ri-home-gear-line"></i></div>
                    <div class="s-info">
                        <h4>Home Service</h4>
                        <p>Get expert help.</p>
                    </div>
                </div>
                <div class="service-box">
                    <div class="s-icon"><i class="ri-settings-3-line"></i></div>
                    <div class="s-info">
                        <h4>Servicing Center</h4>
                        <p>Repair Your Device</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FEATURED CATEGORIES GRID -->
        <div class="section-wrapper">
            <div class="section-center-header">
                <h3>Featured Category</h3>
                <p>Get Your Desired Product from Featured Category!</p>
            </div>

            <div class="category-grid">
                @forelse($featuredCategories as $category)
                    <a href="{{ route('category.show', ['slug' => $category->slug]) }}" class="cat-item">
                        <!-- Dynamic Icon with Fallback -->
                        <i class="{{ $category->icon_class ?? 'ri-function-line' }}"></i>
                        <span>{{ $category->name }}</span>
                    </a>
                @empty
                    <div class="col-span-full text-center text-gray-400 py-4">
                        No featured categories found.
                    </div>
                @endforelse
            </div>
        </div>
        <!-- FLASH DEALS -->
        <div class="section-wrapper">
            <div class="section-header">
                <div class="sec-title">
                    <i class="ri-flashlight-fill" style="color: #ef4444;"></i> Flash Sale
                    <div class="deal-header">
                        Ends in:
                        <div class="timer-box">
                            <span class="time-block">02</span> : <span class="time-block">45</span> : <span class="time-block">10</span>
                        </div>
                    </div>
                </div>
                <a href="#" class="view-all">See All Offers <i class="ri-arrow-right-s-line"></i></a>
            </div>

            <div class="grid-5">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full text-center p-10">
                        <p>No products found.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- EXPERT IT SOLUTIONS -->
        <div class="section-wrapper">
            <div class="section-center-header">
                <h3 style="color: var(--brand-magenta); font-size: 14px; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; margin-bottom: 8px;">Professional Services</h3>
                <h2 style="font-size: 28px; font-weight: 800; color: #0f172a;">Expert IT Solutions</h2>
                <p style="color: #64748b; margin-top: 10px;">Enterprise-ready IT services delivering reliability across the UAE.</p>
            </div>

            <div class="solutions-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 40px;">
                @php 
                    $homeSolutions = \App\Models\Solution::where('is_active', '=', 1)->orderBy('order', 'asc')->take(6)->get();
                @endphp
                @foreach($homeSolutions as $solution)
                    <a href="{{ route('solutions.show', $solution->slug) }}" class="sol-card" style="background: white; border: 1px solid var(--border); padding: 30px; border-radius: 20px; transition: all 0.3s ease; text-decoration: none; display: block; box-shadow: var(--shadow);">
                        <div class="sol-icon" style="width: 50px; height: 50px; background: rgba(3, 166, 150, 0.1); color: var(--brand-emerald); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 20px;">
                            <i class="{{ $solution->icon_class }}"></i>
                        </div>
                        <h4 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin-bottom: 12px;">{{ $solution->title }}</h4>
                        <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 15px;">{{ $solution->summary }}</p>
                        <span style="color: var(--brand-navy); font-weight: 700; font-size: 13px; display: flex; align-items: center;">
                            Learn More <i class="ri-arrow-right-line" style="margin-left: 8px;"></i>
                        </span>
                    </a>
                @endforeach
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('solutions.index') }}" class="btn btn-brand" style="padding: 12px 35px; border-radius: 30px; font-weight: 700;">View All Solutions</a>
            </div>
        </div>

        <div class="mid-banner">
            <div class="mb-text">
                <h3>Upgrade Your Office</h3>
                <p>Exclusive corporate deals on Workstations, Servers, and Networking Gear.</p>
            </div>
            <button class="mb-btn">Request Quote</button>
        </div>

    </div>
@endsection
