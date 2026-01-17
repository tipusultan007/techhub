<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('trn_number')->nullable()->comment('UAE VAT Registration No');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 2. Purchase Orders (Stock In Header)
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('reference_no')->unique(); // e.g., PO-2024-001
            $table->date('date');
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0); // Input VAT
            $table->enum('status', ['pending', 'received'])->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Purchase Order Items (Stock In Details)
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            // Nullable because Simple products don't have variant_id
            $table->foreignId('product_variant_id')->nullable()->constrained(); 
            
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2); // Cost at time of purchase
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};
