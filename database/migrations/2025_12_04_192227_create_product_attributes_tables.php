<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Attributes (e.g., Color, Storage, RAM)
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Color
            $table->string('slug')->unique(); // color
            $table->timestamps();
        });

        // 2. Attribute Values (e.g., Red, Blue, 128GB, 256GB)
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value'); // Red
            $table->string('color_code')->nullable(); // #FF0000 (Optional for swatches)
            $table->timestamps();
        });

        // 3. Variant - Attribute Value Link (The Pivot Table)
        // This links a specific SKU (Variant) to specific Values (Red, 128GB)
        Schema::create('product_variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};