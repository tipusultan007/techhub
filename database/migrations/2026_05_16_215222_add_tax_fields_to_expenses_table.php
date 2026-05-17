<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('tax_method', ['inclusive', 'exclusive'])->default('inclusive')->after('amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_method');
            $table->decimal('net_amount', 10, 2)->nullable()->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['tax_method', 'tax_amount', 'net_amount']);
        });
    }
};
