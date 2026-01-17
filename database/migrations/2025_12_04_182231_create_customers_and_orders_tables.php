<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('trn_number')->nullable()->comment('For UAE B2B Customers');
            $table->text('address')->nullable();
            $table->string('password');
            $table->timestamps();
        });

        // 2. Orders (Sales Headers)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // e.g. INV-2025-001
            $table->enum('channel', ['online', 'pos']);
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); // Nullable for Guest Walk-ins
            $table->string('customer_name')->nullable(); // Snapshot in case customer deleted

            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat_amount', 10, 2); // 5%
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            $table->string('payment_method')->default('cash'); // cash, card, online
            $table->enum('status', ['pending','processing','shipped','completed','cancelled','returned'])->default('completed');
            $table->foreignId('user_id')->constrained(); // Cashier/Admin who made sale
            $table->timestamps();
        });

        // 3. Order Items (Sales Details)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained();

            $table->string('product_name'); // Snapshot
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
    }
};
