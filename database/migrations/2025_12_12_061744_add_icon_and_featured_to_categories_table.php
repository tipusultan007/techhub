<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Stores 'ri-smartphone-line', etc.
            $table->string('icon_class')->nullable()->after('name');

            // To toggle if this category appears in the "Featured Category" grid
            $table->boolean('is_featured')->default(false)->after('icon_class');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['icon_class', 'is_featured']);
        });
    }
};
