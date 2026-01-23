<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToModel, WithHeadingRow
{
    protected $category;
    protected $brand;

    public function __construct()
    {
        // Find the target category
        $this->category = Category::where('name', 'Computers & Laptops')->first() 
            ?? Category::create(['name' => 'Computers & Laptops', 'slug' => 'computers-laptops']);
        
        // Use a default brand (e.g., 'Dell' from seeder, or generic)
        $this->brand = Brand::firstOrCreate(['name' => 'Generic']);
    }

    public function model(array $row)
    {
        // Expected columns: Title, Price, Old Price, Image URL
        // Map keys are strictly lowercase/slugged by default in Maatwebsite?
        // Usually 'Title' -> 'title', 'Old Price' -> 'old_price'
        
        $title = $row['title'] ?? null;
        if (!$title) return null;

        $price = $this->parsePrice($row['price'] ?? 0);
        $oldPrice = $this->parsePrice($row['old_price'] ?? 0);
        $imageUrl = $row['image'] ?? null;

        // Pricing Logic
        if ($oldPrice > $price) {
            $sellingPrice = $oldPrice;
            $salePrice = $price;
        } else {
            $sellingPrice = $price;
            $salePrice = null;
        }

        // Create Product
        $product = Product::create([
            'name'           => $title,
            'slug'           => Str::slug($title) . '-' . Str::random(5), // Ensure unique slug
            'brand_id'       => $this->brand->id,
            'category_id'    => $this->category->id,
            'description'    => '<p>' . $title . '</p>',
            'type'           => 'simple',
            'sku'            => 'IMP-' . strtoupper(Str::random(8)),
            'cost_price'     => $sellingPrice * 0.7, // Estimate cost
            'selling_price'  => $sellingPrice,
            'sale_price'     => $salePrice,
            'stock_quantity' => 10,
            'alert_quantity' => 2,
            'tax_method'     => 'inclusive',
        ]);

        // Attach Image
        if ($imageUrl) {
            try {
                $product->addMediaFromUrl($imageUrl)->toMediaCollection('product_image');
            } catch (\Exception $e) {
                Log::warning("Failed to download image for product {$title}: " . $e->getMessage());
            }
        }

        return $product;
    }

    private function parsePrice($value)
    {
        // dynamic cleaning of price string (e.g. "$1,200.00" -> 1200.00)
        return (float) preg_replace('/[^0-9.]/', '', (string) $value);
    }
}
