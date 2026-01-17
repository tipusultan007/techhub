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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable(); // Used for description or small text
            $table->string('badge_text')->nullable(); // "NEW ARRIVALS"
            $table->string('button_text')->nullable(); // "Shop Now"
            $table->string('link')->default('#');
            // Position determines where it shows: 'main', 'side_top', 'side_bottom'
            $table->enum('position', ['main', 'side_top', 'side_bottom'])->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
