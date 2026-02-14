<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    protected $uncategorized;

    protected $brand;

    public function __construct()
    {
        // Find or create the "Uncategorized" category
        $this->uncategorized = Category::where('name', 'Uncategorized')->first()
            ?? Category::create(['name' => 'Uncategorized', 'slug' => 'uncategorized']);

        // Use a default brand
        $this->brand = Brand::firstOrCreate(['name' => 'Generic']);
    }

    public function model(array $row)
    {
        /**
         * Expected columns from user request:
         * PNO, Title, Cost Price, Selling Price, Sale Price, Stock, category, Image
         *
         * Maatwebsite WithHeadingRow usually slugs the headers:
         * 'pno', 'title', 'cost_price', 'selling_price', 'sale_price', 'stock', 'category', 'image'
         */
        $name = $row['title'] ?? null;
        if (! $name) {
            return null;
        }

        // Prevent duplicates based on title
        if (Product::where('name', $name)->exists()) {
            return null;
        }

        // Required prices with defaults (0)
        $costPrice = $this->parsePrice($row['cost_price'] ?? 0);
        $sellingPrice = $this->parsePrice($row['selling_price'] ?? 0);

        // Sale price can be null
        $salePrice = isset($row['sale_price']) ? $this->parsePrice($row['sale_price']) : null;

        $pno = $row['pno'] ?? null;
        $stock = $row['stock'] ?? 0;
        $imageUrl = $row['image'] ?? null;
        $categoryName = $row['category'] ?? null;

        // Category lookup
        $category = null;
        if ($categoryName) {
            $category = Category::where('name', 'like', $categoryName)->first();
        }

        if (! $category) {
            $category = $this->uncategorized;
        }

        // Create Product
        $product = Product::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'pno' => $pno,
            'brand_id' => $this->brand->id,
            'category_id' => $category->id,
            'description' => '<div>'.$name.'</div>',
            'type' => 'simple',
            'sku' => $pno ?: 'TH-'.strtoupper(Str::random(8)), // Use PNO as SKU if available
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'sale_price' => $salePrice,
            'stock_quantity' => $stock,
            'alert_quantity' => 2,
            'tax_method' => 'exclusive',
            'status' => 'published',
        ]);

        // Attach Image
        if ($imageUrl) {
            try {
                $product->addMediaFromUrl($imageUrl)->toMediaCollection('product_image');
            } catch (\Exception $e) {
                Log::warning("Failed to download image for product {$name}: ".$e->getMessage());
            }
        }

        return $product;
    }

    private function parsePrice($value)
    {
        if (empty($value)) {
            return 0;
        }

        // dynamic cleaning of price string (e.g. "$1,200.00" -> 1200.00)
        return (float) preg_replace('/[^0-9.]/', '', (string) $value);
    }
}
