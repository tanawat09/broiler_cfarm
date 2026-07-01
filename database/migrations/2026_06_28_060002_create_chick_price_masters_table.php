<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chick_price_masters', function (Blueprint $table) {
            $table->id();
            $table->enum('sex', ['ผู้', 'เมีย', 'คละ']);
            $table->enum('grade', ['A', 'B']);
            $table->decimal('price_per_bird', 10, 2);
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['sex', 'grade', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chick_price_masters');
    }
};
