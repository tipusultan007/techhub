<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            
            // Add tax_method if it doesn't exist
            if (!Schema::hasColumn('products', 'tax_method')) {
                $table->enum('tax_method', ['inclusive', 'exclusive'])
                      ->default('inclusive')
                      ->after('description')
                      ->comment('UAE VAT: Inclusive means tax is inside the price');
            }

            // Add tax_rate if it doesn't exist
            if (!Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)
                      ->default(5.00)
                      ->after('tax_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['tax_method', 'tax_rate']);
        });
    }
};
