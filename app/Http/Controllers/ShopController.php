<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $page = null)
    {
        // 1. Base Query
        $query = Product::published()->physical()->inStock()->with(['category', 'media', 'variants']);

        // 2. Handle Category Filter (Slug or Checkboxes)
        // If accessed via /category/{slug}
        $currentCategory = null;
        if ($categorySlug) {
            $currentCategory = Category::where('slug', $categorySlug)->firstOrFail();
            // Get all children categories IDs to include them
            $categoryIds = $currentCategory->descendants()->pluck('id')->push($currentCategory->id);
            $query->whereIn('category_id', $categoryIds);
        }

        // If filtered via Checkboxes (?categories=1,2,3)
        if ($request->filled('categories')) {
            $query->whereIn('category_id', $request->categories);
        }

        // 3. Brand Filter
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', $request->brands);
        }

        // 4. Price Filter
        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        // Filter products that have variants with the selected attribute values
        if ($request->filled('attribute_values')) {
            $selectedValues = $request->attribute_values; // Array of IDs e.g. [5, 12, 20]

            $query->whereHas('variants', function ($q) use ($selectedValues) {
                $q->whereHas('attributeValues', function ($subQ) use ($selectedValues) {
                    $subQ->whereIn('attribute_values.id', $selectedValues);
                });
            });
        }

        // 5. Sorting Logic
        // We use 'selling_price' column.
        // Note: Ensure your Variable Products have the 'selling_price' column filled
        // with the minimum variant price when you save/update the product.
        switch ($request->sort) {
            case 'price_low':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default: // Popularity
                $query->orderBy('id', 'desc');
                break;
        }

        // 6. Pagination
        $products = $query->paginate(12)->withQueryString();

        // 7. Sidebar Data
        $brands = Brand::whereHas('products')->get();
        // Get only Top Level categories
        $categories = Category::whereNull('parent_id')->with('children')->get();

        $attributes = Attribute::with('values')->get();

        return view('frontend.shop', compact('products', 'brands', 'categories', 'currentCategory','attributes', 'page'));
    }
}
