<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCategories = Category::where('is_featured', '=', true)
            ->orderBy('id', 'desc')
            ->take(16)
            ->get();

        $products = Product::published()->physical()
            ->with(['category', 'variants', 'media'])
            ->latest()
            ->take(10)
            ->get();

        return view('home', [
            'mainBanners'  => Banner::where('position', '=', 'main')->where('is_active', '=', true)->orderBy('order')->get(),
            'sideTop'      => Banner::where('position', '=', 'side_top')->where('is_active', '=', true)->first(),
            'sideBottom'   => Banner::where('position', '=', 'side_bottom')->where('is_active', '=', true)->first(),
            'featuredCategories' => $featuredCategories,
            'products'      => $products,
        ]);
    }

    public function product($slug)
    {
        $product = Product::published()->where('slug', $slug)
            ->with(['category', 'brand', 'variants', 'media'])
            ->firstOrFail();

        // Get Related Products (Same category, excluding current)
        $relatedProducts = Product::published()->physical()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('media')
            ->take(4)
            ->get();


        $inWishlist = false;
        if (Auth::guard('customer')->check()) {
            $inWishlist = Wishlist::where('customer_id', Auth::guard('customer')->id())
                ->where('product_id', $product->id)
                ->exists();
        }

        return view('frontend.product', compact('product', 'relatedProducts','inWishlist'));
    }
}
