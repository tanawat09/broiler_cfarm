<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_price_masters', function (Blueprint $table) {
            $table->id();
            $table->string('feed_code');
            $table->decimal('price_per_kg', 10, 2);
            $table->date('effective_date');
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['feed_code', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_price_masters');
    }
};
