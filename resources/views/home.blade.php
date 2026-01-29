@extends('layouts.frontend')

@section('title', 'Home | Tech Hub Computer Trading')
@section('meta_description', 'Best deals on Gaming PCs, Laptops, and Components in UAE.')

@section('content')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .hero {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        @media (max-width: 991px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }
        .hero-banner-main {
            height: 420px;
            border-radius: 12px;
            overflow: hidden !important; /* Ensure content stays inside rounded corners */
            position: relative;
        }
        .swiper-main {
            width: 100%;
            height: 420px; /* Explicit height */
            border-radius: 12px;
        }
        .swiper-wrapper {
            height: 100% !important;
        }
        .swiper-slide {
            position: relative;
            width: 100%;
            height: 420px !important;
            overflow: hidden;
        }
        .swiper-slide img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center;
        }
        .banner-content {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            padding: 40px 80px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important; /* Center vertically */
            align-items: flex-start !important; /* Left align */
            background: linear-gradient(90deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 60%, rgba(0,0,0,0) 100%) !important;
            color: white !important;
            z-index: 10 !important;
        }
        .banner-content h2 {
            font-size: 50px !important;
            margin: 15px 0 !important;
            line-height: 1 !important;
            font-weight: 800 !important;
            text-shadow: 0 5px 15px rgba(0,0,0,0.5) !important;
            max-width: 700px;
        }
        .banner-content p {
            font-size: 20px !important;
            line-height: 1.4 !important;
            opacity: 1 !important;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5) !important;
            max-width: 550px;
            margin-bottom: 30px !important;
        }
        /* Style Swiper Pagination */
        .swiper-pagination {
            bottom: 30px !important;
            z-index: 50 !important;
        }
        .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.5) !important;
            opacity: 1 !important;
            width: 12px !important;
            height: 12px !important;
        }
        .swiper-pagination-bullet-active {
            background: var(--brand-magenta) !important;
            width: 40px !important;
            border-radius: 6px !important;
        }
        /* Navigation Buttons */
        .custom-nav-btn {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 56px !important;
            height: 56px !important;
            background: rgba(255,255,255,0.2) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 50% !important;
            color: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            border: 2px solid rgba(255,255,255,0.3) !important;
            z-index: 100 !important;
            cursor: pointer !important;
        }
        .custom-nav-btn:hover {
            background: var(--brand-magenta) !important;
            border-color: var(--brand-magenta) !important;
            transform: translateY(-50%) scale(1.1) !important;
        }
        .custom-nav-btn i {
            font-size: 32px !important;
        }
        .custom-prev { left: 30px !important; }
        .custom-next { right: 30px !important; }
        
        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .hero-banner-main, .swiper-main, .swiper-slide {
                height: 350px !important;
            }
            .banner-content {
                padding: 30px !important;
            }
            .banner-content h2 {
                font-size: 32px !important;
                max-width: 100%;
                line-height: 1.3;
            }
            .banner-content p {
                font-size: 16px !important;
                max-width: 100%;
                margin-bottom: 20px !important;
            }
            .custom-nav-btn {
                width: 40px !important;
                height: 40px !important;
            }
            .custom-nav-btn i {
                font-size: 24px !important;
            }
            .custom-prev { left: 10px !important; }
            .custom-next { right: 10px !important; }
        }

        @media (max-width: 480px) {
            .hero-banner-main, .swiper-main, .swiper-slide {
                height: 280px !important;
            }
            .banner-content h2 {
                font-size: 26px !important;
                line-height: 1.3;
            }
            .banner-content p {
                font-size: 14px !important;
            }
            .btn-brand {
                padding: 10px 25px !important;
                font-size: 14px !important;
            }
        }
        
        /* Hide default swiper classes to avoid conflicts */
        .swiper-button-next, .swiper-button-prev {
            display: none !important;
        }

        /* Animated Border for Side Banners */
        .animated-border-box {
            position: relative;
            padding: 2px;
            border-radius: 14px;
            overflow: hidden;
            background: #1e293b;
            display: flex;
            height: 100%;
        }

        .animated-border-box::before {
            content: '';
            position: absolute;
            width: 300%;
            height: 300%;
            top: -100%;
            left: -100%;
            background: conic-gradient(
                from 0deg,
                transparent 20%,
                var(--brand-magenta) 40%,
                transparent 50%,
                #0ea5e9 60%,
                transparent 80%
            );
            animation: borderRotate 3s linear infinite;
            z-index: 0;
        }

        @keyframes borderRotate {
            100% { transform: rotate(360deg); }
        }

        .animated-border-box .promo-box {
            width: 100%;
            height: 100%;
            margin: 0 !important;
            border: none !important;
            z-index: 1;
            background: #000;
            border-radius: 12px;
        }
        
        @media (max-width: 991px) {
            .hero-side {
                margin-top: 20px;
                height: auto;
            }
            .animated-border-box {
                height: 200px;
            }
        }
    </style>

    <div class="container">

        <div class="hero">

            <!-- MAIN BANNER SLIDER -->
            <div class="hero-banner-main">
                <div class="swiper swiper-main">
                    <div class="swiper-wrapper">
                        @foreach($mainBanners as $banner)
                            <div class="swiper-slide">
                                <img src="{{ $banner->getFirstMediaUrl('banner_image') }}" alt="{{ $banner->title }}">
                                <div class="banner-content">
                                    @if($banner->badge_text)
                                        <div class="banner-badge">
                                            <span style="background:var(--brand-magenta); padding:4px 12px; font-size:12px; border-radius:30px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                                                {{ $banner->badge_text }}
                                            </span>
                                        </div>
                                    @endif

                                    <h2>{!! $banner->title !!}</h2>

                                    <p style="margin-bottom:30px; font-size:16px; opacity:0.9; max-width:500px;">
                                        {{ $banner->subtitle }}
                                    </p>

                                    @if($banner->button_text)
                                        <div>
                                            <a href="{{ $banner->link }}" class="btn btn-brand" style="padding: 12px 35px; border-radius: 30px; font-weight: 700;">
                                                {{ $banner->button_text }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                    <!-- Add Navigation -->
                    <div class="custom-nav-btn custom-next">
                        <i class="ri-arrow-right-s-line"></i>
                    </div>
                    <div class="custom-nav-btn custom-prev">
                        <i class="ri-arrow-left-s-line"></i>
                    </div>
                </div>
            </div>

            <!-- SIDE BANNERS -->
            <div class="hero-side">

                <!-- Side Top -->
                @if($sideTop)
                    <div class="animated-border-box">
                        <a href="{{ $sideTop->link }}" class="promo-box">
                            <img src="{{ $sideTop->getFirstMediaUrl('banner_image') }}" alt="{{ $sideTop->title }}">
                            <div class="promo-text">
                                <h4>{{ $sideTop->title }}</h4>
                                <p>{{ $sideTop->subtitle }}</p>
                            </div>
                        </a>
                    </div>
                @endif

                <!-- Side Bottom -->
                @if($sideBottom)
                    <div class="animated-border-box">
                        <a href="{{ $sideBottom->link }}" class="promo-box">
                            <img src="{{ $sideBottom->getFirstMediaUrl('banner_image') }}" alt="{{ $sideBottom->title }}">
                            <div class="promo-text">
                                <h4>{{ $sideBottom->title }}</h4>
                                <p>{{ $sideBottom->subtitle }}</p>
                            </div>
                        </a>
                    </div>
                @endif

            </div>
        </div>

        {{-- <!-- NEW: SERVICE FEATURES ROW -->
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
        </div> --}}


         <!-- EXPERT IT SOLUTIONS -->
        <div class="section-wrapper">
            <div class="section-center-header">
                <h3 style="color: var(--brand-magenta); font-size: 14px; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; margin-bottom: 8px;">Professional Services</h3>
                <h2 style="font-size: 28px; font-weight: 800; color: #0f172a;">Expert IT Solutions</h2>
                <p style="color: #64748b; margin-top: 10px;">Enterprise-ready IT services delivering reliability across the UAE.</p>
            </div>

            <div class="solutions-grid">
                @php 
                    $homeSolutions = \App\Models\Solution::where('is_active', true)->orderBy('order', 'asc')->take(6)->get();
                @endphp
                @foreach($homeSolutions as $solution)
                    <a href="{{ route('solutions.show', $solution->slug) }}" class="sol-card">
                        <div class="sol-icon">
                            <i class="{{ $solution->icon_class }}"></i>
                        </div>
                        <h4>{{ $solution->title }}</h4>
                        <p>{{ $solution->summary }}</p>
                        <span class="learn-more">
                            Learn More <i class="ri-arrow-right-line"></i>
                        </span>
                    </a>
                @endforeach
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('solutions.index') }}" class="btn btn-brand" style="padding: 12px 35px; border-radius: 30px; font-weight: 700;">View All Solutions</a>
            </div>
        </div>

        <!-- FLASH DEALS -->
        <div class="section-wrapper">
            <div class="section-header">
                <div class="sec-title">
                    <i class="ri-flashlight-fill" style="color: #ef4444;"></i> Featured Products
                </div>
                <a href="{{ route('shop.index') }}" class="view-all">More Products <i class="ri-arrow-right-s-line"></i></a>
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

       
        <div class="mid-banner">
            <div class="mb-text">
                <h3>Upgrade Your Office</h3>
                <p>Exclusive corporate deals on Workstations, Servers, and Networking Gear.</p>
            </div>
            <button class="mb-btn">Request Quote</button>
        </div>

    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var swiper = new Swiper(".swiper-main", {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".custom-next",
                    prevEl: ".custom-prev",
                },
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },
            });

            // Navigation buttons remain visible in this version for debugging
        });
    </script>
@endsection
