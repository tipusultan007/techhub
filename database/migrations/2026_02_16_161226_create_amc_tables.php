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
        // 1. AMC Agreements Template
        Schema::create('amc_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('content');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 2. AMCs (Contracts)
        Schema::create('amcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->enum('frequency', ['monthly', 'quarterly', 'semi-annually', 'annually'])->default('quarterly');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. AMC Items (Covered Products)
        Schema::create('amc_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_serial_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 4. AMC Services (Maintenance Visits)
        Schema::create('amc_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->date('actual_service_date')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->text('service_notes')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amc_services');
        Schema::dropIfExists('amc_items');
        Schema::dropIfExists('amcs');
        Schema::dropIfExists('amc_templates');
    }
};
