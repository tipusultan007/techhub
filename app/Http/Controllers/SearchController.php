<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * AJAX Search for header dropdown
     */
    public function ajaxSearch(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        // Search products with basic security
        $products = Product::physical()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->where('stock_quantity', '>', 0)
            ->with(['media', 'variants'])
            ->select(['id', 'name', 'slug', 'selling_price', 'sale_price', 'type'])
            ->limit(7)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('product.show', $product->slug),
                'price' => number_format($product->active_price, 2) . ' AED',
                'image' => $product->getFirstMediaUrl('product_image', 'thumb') ?: asset('images/placeholder.jpg'),
                'is_sale' => $product->is_on_sale,
                'original_price' => $product->is_on_sale ? number_format($product->selling_price, 2) . ' AED' : null,
            ];
        });

        return response()->json($results);
    }

    /**
     * Full search results page
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $products = Product::physical()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'media', 'variants'])
            ->paginate(12)
            ->withQueryString();

        return view('frontend.shop', [
            'products' => $products,
            'searchQuery' => $query,
            'brands' => \App\Models\Brand::whereHas('products')->get(),
            'categories' => \App\Models\Category::whereNull('parent_id')->with('children')->get(),
            'attributes' => \App\Models\Attribute::with('values')->get(),
            'currentCategory' => null
        ]);
    }
}
