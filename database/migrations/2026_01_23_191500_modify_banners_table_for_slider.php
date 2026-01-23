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
        Schema::table('banners', function (Blueprint $table) {
            // Drop the unique constraint on position
            // In Laravel/MySQL, the name is usually 'banners_position_unique'
            $table->dropUnique(['position']);
            
            // Add order column
            $table->integer('order')->default(0)->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->unique('position');
            $table->dropColumn('order');
        });
    }
};
