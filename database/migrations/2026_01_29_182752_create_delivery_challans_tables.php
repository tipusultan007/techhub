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
        Schema::create('delivery_challans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('challan_number')->unique();
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_challan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_challan_id')->constrained()->onDelete('cascade');
            $table->foreignId('quotation_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->integer('delivered_qty')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn('delivered_qty');
        });
        Schema::dropIfExists('delivery_challan_items');
        Schema::dropIfExists('delivery_challans');
    }
};
