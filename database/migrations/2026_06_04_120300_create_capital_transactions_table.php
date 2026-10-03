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
        Schema::create('capital_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['investment', 'withdrawal']);
            $table->decimal('amount', 12, 2);
            $table->foreignId('bank_account_id')->constrained('accounts');
            $table->foreignId('equity_account_id')->constrained('accounts');
            $table->string('reference_no')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_transactions');
    }
};
