<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Brands
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 3. Products (Parent)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->text('description')->nullable();
            $table->enum('type', ['simple', 'variable'])->default('simple');
            // UAE VAT: Standard 5%
            $table->decimal('tax_rate', 5, 2)->default(5.00);
            $table->timestamps();
        });

        // 4. Product Variants (SKUs - e.g., iPhone 13 Black 128GB)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('variant_name'); // e.g. "Black / 128GB"
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable(); // For POS Scanner

            $table->decimal('cost_price', 10, 2); // For Profit Report
            $table->decimal('selling_price', 10, 2); // VAT Inclusive Price

            $table->integer('stock_quantity')->default(0);
            $table->timestamps();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
