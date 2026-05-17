<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Changing the enum to include 'no_tax'
            $table->enum('tax_method', ['inclusive', 'exclusive', 'no_tax'])->default('inclusive')->change();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('tax_method', ['inclusive', 'exclusive'])->default('inclusive')->change();
        });
    }
};
