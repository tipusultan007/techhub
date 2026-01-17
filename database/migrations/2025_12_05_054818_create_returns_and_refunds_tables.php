<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Orders table to track return status
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_status')->nullable()->after('status'); // e.g., partially_returned, fully_returned
        });

        // 2. Returns Header (Credit Note)
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->comment('The original sale');
            $table->string('credit_note_no')->unique(); // e.g., CRN-2025-001
            $table->decimal('total_refund', 10, 2);
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->constrained(); // Who processed it
            $table->timestamps();
        });

        // 3. Return Items Details
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->comment('Original item sold');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained();
            
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Price it was sold at
            $table->decimal('subtotal', 10, 2);
            $table->enum('restock_status', ['restockable', 'defective'])->default('restockable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('return_status');
        });
    }
};
