<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_price_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->decimal('price_per_kg', 10, 2);
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['farm_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_price_masters');
    }
};
