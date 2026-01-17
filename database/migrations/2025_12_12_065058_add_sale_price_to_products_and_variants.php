<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add to Products Table (Simple Products)
        Schema::table('products', function (Blueprint $table) {
            // nullable because not everything is always on sale
            $table->decimal('sale_price', 10, 2)->nullable()->after('selling_price');
        });

        // 2. Add to Product Variants Table (Variable Products)
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('sale_price', 10, 2)->nullable()->after('selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });
    }
};
