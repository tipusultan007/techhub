<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class TikTokFeedController extends Controller
{
    /**
     * Generate a CSV feed for TikTok Catalog.
     * 
     * TikTok supports Scheduled Fetch via CSV.
     * Requirements: id, title, description, availability, condition, price, link, image_link, brand.
     */
    public function index()
    {
        $products = Product::published()
            ->physical()
            ->inStock()
            ->with(['brand', 'media'])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tiktok_products.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers for TikTok
            fputcsv($file, [
                'id',
                'title',
                'description',
                'availability',
                'condition',
                'price',
                'link',
                'image_link',
                'brand'
            ]);

            $currency = settings('currency_symbol', 'AED');

            foreach ($products as $product) {
                // Strip HTML and get a clean description
                $description = strip_tags($product->description);
                $description = mb_substr($description, 0, 5000); // TikTok limit is 5000 chars

                // Use active_price which handles sales
                $price = number_format($product->active_price, 2, '.', '') . ' ' . $currency;

                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $description ?: $product->name,
                    'in stock',
                    'new',
                    $price,
                    route('product.show', $product->slug),
                    $product->getFirstMediaUrl('product_image') ?: asset('images/placeholder.jpg'),
                    $product->brand->name ?? settings('site_name', 'Tech Hub')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
