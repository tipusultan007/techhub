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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'partial_received', 'completed'])->default('pending')->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->integer('received_quantity')->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'received'])->default('received')->change();
        });
    }
};
