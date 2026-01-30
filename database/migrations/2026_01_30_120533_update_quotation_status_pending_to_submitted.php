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
        // 1. Modify the column to include 'submitted'
        // Note: Using raw SQL for MySQL compatibility as doctrine/dbal might not be installed
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('pending', 'submitted', 'converted', 'cancelled') DEFAULT 'pending'");

        // 2. Update existing 'pending' statuses to 'submitted'
        DB::table('quotations')
            ->where('status', 'pending')
            ->update(['status' => 'submitted']);

        // 3. Update the column definition to remove 'pending' and set default to 'submitted'
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('submitted', 'converted', 'cancelled') DEFAULT 'submitted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add 'pending'
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('pending', 'submitted', 'converted', 'cancelled') DEFAULT 'submitted'");

        // 2. Revert 'submitted' to 'pending'
        DB::table('quotations')
            ->where('status', 'submitted')
            ->update(['status' => 'pending']);

        // 3. Remove 'submitted'
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('pending', 'converted', 'cancelled') DEFAULT 'pending'");
    }
};
