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
        Schema::create('flock_house_starts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->unsignedInteger('initial_birds')->default(0);
            $table->timestamps();

            $table->unique(['flock_id', 'house_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flock_house_starts');
    }
};
