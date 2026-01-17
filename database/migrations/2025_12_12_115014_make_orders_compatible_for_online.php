<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1. Make user_id nullable (Online orders have no Cashier)
            $table->foreignId('user_id')->nullable()->change();

            // 2. Add Online Shipping/Contact Fields
            $table->string('guest_email')->nullable()->after('customer_name');
            $table->string('guest_phone')->nullable()->after('guest_email');

            $table->text('shipping_address')->nullable()->after('total');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_area')->nullable()->after('shipping_city');

            // 3. Add Notes
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn(['guest_email', 'guest_phone', 'shipping_address', 'shipping_city', 'shipping_area', 'notes']);
        });
    }
};
