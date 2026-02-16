<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CLEANUP (Disable FK checks to truncate tables)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Category::truncate();
        Brand::truncate();
        Supplier::truncate();
        Attribute::truncate();
        AttributeValue::truncate();
        Product::truncate();
        ProductVariant::truncate();
        DB::table('product_variant_attribute_values')->truncate();
        DB::table('product_serials')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. RUN SUB-SEEDERS
        $this->call([
            CategorySeeder::class,
        ]);

        // 3. ADMIN USER
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'), // Login: admin@admin.com / password
            'email_verified_at' => now(),
        ]);
        $this->command->info('User Created: admin@admin.com / password');

        // 4. CATEGORIES (Fallbacks for products)
        $catMobiles = Category::where('slug', 'smartphones')->first();
        $catLaptops = Category::where('slug', 'computers-laptops')->first();
        $catAccessories = Category::where('slug', 'accessories')->first();

        // 5. BRANDS
        $brandApple = \App\Models\Brand::create(['name' => 'Apple']);
        $brandSamsung = \App\Models\Brand::create(['name' => 'Samsung']);
        $brandSony = \App\Models\Brand::create(['name' => 'Sony']);
        $brandDell = \App\Models\Brand::create(['name' => 'Dell']);

        // 6. SUPPLIERS
        \App\Models\Supplier::create([
            'name' => 'Tech Distributors LLC',
            'company_name' => 'Tech Distro UAE',
            'trn_number' => '100200300400500',
            'email' => 'sales@techdistro.ae',
            'phone' => '+971500000000',
            'address' => 'Warehouse 4, Al Quoz, Dubai'
        ]);

        // 6. ATTRIBUTES (For Variable Products)
        $attrColor = Attribute::create(['name' => 'Color', 'slug' => 'color']);
        $attrStorage = Attribute::create(['name' => 'Storage', 'slug' => 'storage']);

        // Attribute Values
        $valBlack = $attrColor->values()->create(['value' => 'Black']);
        $valWhite = $attrColor->values()->create(['value' => 'White']);
        $valBlue = $attrColor->values()->create(['value' => 'Blue']);

        $val128 = $attrStorage->values()->create(['value' => '128GB']);
        $val256 = $attrStorage->values()->create(['value' => '256GB']);

        // ==========================================
        // SCENARIO A: SIMPLE PRODUCT (No Variants, No Serial)
        // ==========================================
        Product::create([
            'name' => 'Sony WH-1000XM5 Headphones',
            'slug' => 'sony-headphones',
            'brand_id' => $brandSony->id,
            'category_id' => $catAccessories->id,
            'description' => '<p>Noise cancelling headphones.</p>',
            'type' => 'simple',
            'has_serial_number' => false,
            'tax_method' => 'inclusive',
            'tax_rate' => 5.00,
            'sku' => 'SONY-XM5-BLK',
            'barcode' => '4548736132580',
            'cost_price' => 800.00,
            'selling_price' => 1199.00,
            'stock_quantity' => 20,
            'alert_quantity' => 5
        ]);
        $this->command->info('Simple Product Created: Sony Headphones');

        // ==========================================
        // SCENARIO B: SIMPLE PRODUCT (With Serial Numbers)
        // ==========================================
        Product::create([
            'name' => 'Dell XPS 13 Laptop',
            'slug' => 'dell-xps-13',
            'brand_id' => $brandDell->id,
            'category_id' => $catLaptops->id,
            'description' => '<p>High performance ultrabook.</p>',
            'type' => 'simple',
            'has_serial_number' => true, // <--- Key Feature
            'warranty_type' => 'years',
            'warranty_duration' => 1,
            'tax_method' => 'inclusive',
            'sku' => 'DELL-XPS-13',
            'barcode' => '884116368990',
            'cost_price' => 3500.00,
            'selling_price' => 4500.00,
            'stock_quantity' => 5,
            'alert_quantity' => 2
        ]);
        $this->command->info('Serialized Product Created: Dell XPS');

        // ==========================================
        // SCENARIO C: VARIABLE PRODUCT (iPhone 15 - Complex)
        // ==========================================
        $iphone = Product::create([
            'name' => 'iPhone 15',
            'slug' => 'iphone-15',
            'brand_id' => $brandApple->id,
            'category_id' => $catMobiles->id,
            'description' => '<p>The latest iPhone.</p>',
            'specifications' => '<table class="table"><tr><td>Screen</td><td>6.1 inch</td></tr></table>',
            'type' => 'variable',
            'has_serial_number' => true, // Mobile phones need IMEI
            'warranty_type' => 'months',
            'warranty_duration' => 12,
            'tax_method' => 'inclusive',
            'tax_rate' => 5.00
        ]);

        // VARIANT 1: Black / 128GB
        $v1 = ProductVariant::create([
            'product_id' => $iphone->id,
            'variant_name' => 'Black / 128GB',
            'sku' => 'IP15-BLK-128',
            'barcode' => '19425300001',
            'cost_price' => 2800.00,
            'selling_price' => 3399.00,
            'stock_quantity' => 10,
            'alert_quantity' => 3
        ]);
        // Link Attributes
        DB::table('product_variant_attribute_values')->insert([
            ['product_variant_id' => $v1->id, 'attribute_id' => $attrColor->id, 'attribute_value_id' => $valBlack->id],
            ['product_variant_id' => $v1->id, 'attribute_id' => $attrStorage->id, 'attribute_value_id' => $val128->id],
        ]);

        // VARIANT 2: Black / 256GB
        $v2 = ProductVariant::create([
            'product_id' => $iphone->id,
            'variant_name' => 'Black / 256GB',
            'sku' => 'IP15-BLK-256',
            'barcode' => '19425300002',
            'cost_price' => 3200.00,
            'selling_price' => 3899.00,
            'stock_quantity' => 5,
            'alert_quantity' => 2
        ]);
        // Link Attributes
        DB::table('product_variant_attribute_values')->insert([
            ['product_variant_id' => $v2->id, 'attribute_id' => $attrColor->id, 'attribute_value_id' => $valBlack->id],
            ['product_variant_id' => $v2->id, 'attribute_id' => $attrStorage->id, 'attribute_value_id' => $val256->id],
        ]);

        // VARIANT 3: White / 128GB
        $v3 = ProductVariant::create([
            'product_id' => $iphone->id,
            'variant_name' => 'White / 128GB',
            'sku' => 'IP15-WHT-128',
            'barcode' => '19425300003',
            'cost_price' => 2800.00,
            'selling_price' => 3399.00,
            'stock_quantity' => 8,
            'alert_quantity' => 2
        ]);
        // Link Attributes
        DB::table('product_variant_attribute_values')->insert([
            ['product_variant_id' => $v3->id, 'attribute_id' => $attrColor->id, 'attribute_value_id' => $valWhite->id],
            ['product_variant_id' => $v3->id, 'attribute_id' => $attrStorage->id, 'attribute_value_id' => $val128->id],
        ]);

        $this->command->info('Variable Product Created: iPhone 15 with 3 variants');

        // 7. AMC SEEDERS
        $this->call([
            AmcTemplateSeeder::class,
            AmcSeeder::class,
        ]);

        $this->command->info('Seed Complete!');
    }
}
