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
        // Fetch banners by position
        $banners = Banner::where('is_active', true)->get()->keyBy('position');

        $featuredCategories = Category::where('is_featured', true)
            ->orderBy('id', 'desc')
            ->take(16) // Limit to grid size
            ->get();

        $products = Product::with(['category', 'variants', 'media'])
            ->latest() // Get newest first
            ->take(10) // Limit to 10 items
            ->get();

        return view('home', [
            'mainBanner'   => $banners->get('main'),
            'sideTop'      => $banners->get('side_top'),
            'sideBottom'   => $banners->get('side_bottom'),
            'featuredCategories' => $featuredCategories,
            'products'      => $products,
        ]);
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['category', 'brand', 'variants', 'media'])
            ->firstOrFail();

        // Get Related Products (Same category, excluding current)
        $relatedProducts = Product::where('category_id', $product->category_id)
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
