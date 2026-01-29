@extends('layouts.frontend')

@section('title', 'My Wishlist | Tech Hub')

@push('styles')
    <style>
        .account-layout {
            display: grid; grid-template-columns: 280px 1fr; gap: 30px; margin-bottom: 60px;
        }

        /* WISHLIST GRID */
        .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }

        .wish-card {
            background: white; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden;
            position: relative; transition: 0.2s; display: flex; flex-direction: column;
        }
        .wish-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); border-color: var(--brand-emerald); }

        .wish-remove {
            position: absolute; top: 10px; right: 10px; z-index: 2;
            width: 30px; height: 30px; background: rgba(255,255,255,0.9); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: var(--accent-red); cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.2s; border: none;
        }
        .wish-remove:hover { background: var(--accent-red); color: white; }

        .wish-img {
            height: 180px; padding: 20px; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border); background: #fff;
        }
        .wish-img img { max-height: 100%; mix-blend-mode: multiply; }

        .wish-body { padding: 15px; flex: 1; display: flex; flex-direction: column; }
        .wish-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 8px; line-height: 1.4; color: var(--text-main); }
        .wish-title a:hover { color: var(--brand-deep-blue); text-decoration: underline; }

        .wish-price { font-size: 1.1rem; font-weight: 800; color: var(--brand-deep-blue); margin-bottom: 5px; }

        .stock-in { font-size: 0.75rem; color: #16a34a; font-weight: 600; margin-bottom: 15px; }
        .stock-out { font-size: 0.75rem; color: #ef4444; font-weight: 600; margin-bottom: 15px; }

        .btn-cart {
            margin-top: auto; width: 100%; padding: 10px; background: white;
            border: 1px solid var(--brand-deep-blue); color: var(--brand-deep-blue);
            border-radius: var(--radius); font-weight: 600; cursor: pointer; transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .btn-cart:hover { background: var(--brand-deep-blue); color: white; }
        .btn-disabled { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }
        .btn-disabled:hover { background: #f1f5f9; color: #94a3b8; }

        @media (max-width: 900px) {
            .account-layout { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <div class="container">

        <div class="page-header" style="margin: 30px 0;">
            <h1 style="font-size: 1.8rem; font-weight: 800;">My Wishlist ({{ $wishlistItems->count() }})</h1>
        </div>

        <div class="account-layout">

            <!-- Sidebar Partial -->
            @include('frontend.customer.partials.sidebar')

            <div class="wishlist-content">

                @if($wishlistItems->count() > 0)
                    <div class="wishlist-grid">

                        @foreach($wishlistItems as $item)
                            <div class="wish-card">

                                <!-- Remove Button Form -->
                                <form action="{{ route('customer.wishlist.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="wish-remove" title="Remove">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>

                                <div class="wish-img">
                                    <img src="{{ $item->product->getFirstMediaUrl('product_image', 'thumb') ?: asset('images/placeholder.jpg') }}" alt="{{ $item->product->name }}">
                                </div>

                                <div class="wish-body">
                                    <h3 class="wish-title">
                                        <a href="{{ route('product.show', $item->product->slug) }}">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>

                                    <div class="wish-price">
                                        {{ number_format($item->product->active_price) }} AED
                                    </div>

                                    @if($item->product->stock_quantity > 0)
                                        <div class="stock-in"><i class="ri-check-line"></i> In Stock</div>

                                        <!-- Add to Cart (Global Function) -->
                                        <button class="btn-cart" onclick="addToCart({{ $item->product->id }})">
                                            <i class="ri-shopping-cart-2-line"></i> Add to Cart
                                        </button>
                                    @else
                                        <div class="stock-out"><i class="ri-close-line"></i> Out of Stock</div>
                                        <button class="btn-cart btn-disabled" disabled>Out of Stock</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                @else
                    <div style="text-align:center; padding: 60px; background:white; border-radius:8px; border:1px solid #e2e8f0;">
                        <i class="ri-heart-line" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px; display:block;"></i>
                        <h3 style="font-weight:700; color:#64748b;">Your wishlist is empty</h3>
                        <p style="color:#94a3b8; font-size:0.9rem;">Save items you love to view them later.</p>
                        <a href="{{ route('shop.index') }}" style="margin-top:15px; display:inline-block; padding:10px 20px; background:var(--brand-deep-blue); color:white; border-radius:6px; text-decoration:none; font-weight:600;">Start Shopping</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
