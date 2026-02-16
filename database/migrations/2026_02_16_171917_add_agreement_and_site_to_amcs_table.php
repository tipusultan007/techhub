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
        Schema::table('amcs', function (Blueprint $table) {
            $table->string('site_name')->nullable()->after('customer_id');
            $table->enum('agreement_type', ['template', 'custom'])->default('template')->after('frequency');
            $table->foreignId('template_id')->nullable()->after('agreement_type')->constrained('amc_templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amcs', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['site_name', 'agreement_type', 'template_id']);
        });
    }
};
