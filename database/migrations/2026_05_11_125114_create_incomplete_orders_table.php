<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incomplete_orders', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->json('customer_data');
            $table->json('cart_data');
            $table->json('totals_data');
            $table->json('coupon_data')->nullable();
            $table->string('payment_method')->default('rakbank');
            $table->string('customer_ip')->nullable();
            $table->string('visitor_ip')->nullable();
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomplete_orders');
    }
};
