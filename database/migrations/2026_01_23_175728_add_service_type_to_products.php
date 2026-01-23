<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL to modify enum column to include 'service'
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('simple', 'variable', 'service') DEFAULT 'simple'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting enum to original values
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('simple', 'variable') DEFAULT 'simple'");
    }
};
