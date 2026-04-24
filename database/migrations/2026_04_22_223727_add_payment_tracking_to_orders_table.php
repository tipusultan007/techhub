<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $customer) {
            $customer->decimal('paid_amount', 15, 2)->default(0)->after('total');
            $customer->decimal('due_amount', 15, 2)->default(0)->after('paid_amount');
        });

        // Sync old orders: If status is completed, it's fully paid. Otherwise, it's fully due.
        DB::table('orders')->where('status', 'completed')->update([
            'paid_amount' => DB::raw('total'),
            'due_amount' => 0,
        ]);

        DB::table('orders')->where('status', '!=', 'completed')->update([
            'paid_amount' => 0,
            'due_amount' => DB::raw('total'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'due_amount']);
        });
    }
};
