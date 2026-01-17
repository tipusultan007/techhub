<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Products Table (Settings)
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_serial_number')->default(false)->after('type'); // Does this need scanning?
            $table->string('warranty_type')->nullable()->after('alert_quantity'); // e.g., 'Days', 'Months', 'Years'
            $table->integer('warranty_duration')->nullable()->after('warranty_type'); // e.g., 12
        });

        // 2. Create Serial Numbers Table (The Tracking)
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // Nullable variant for variable products
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            
            $table->string('serial_number')->unique(); // The IMEI / S/N
            $table->enum('status', ['available', 'sold', 'returned', 'defective'])->default('available');
            
            // Link to Purchase (Source) and Order (Destination)
            $table->foreignId('purchase_order_id')->nullable()->constrained();
            $table->foreignId('order_id')->nullable()->constrained(); 
            
            $table->timestamps();
        });

        // 3. Update Order Items to store the sold serial info
        Schema::table('order_items', function (Blueprint $table) {
            $table->text('serial_numbers')->nullable(); // JSON array or comma separated if qty > 1
            $table->date('warranty_end_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_serials');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_serial_number', 'warranty_type', 'warranty_duration']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['serial_numbers', 'warranty_end_date']);
        });
    }
};
