<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_receipt_house_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_receipt_id')->constrained('feed_receipts')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->decimal('quantity_kg', 12, 2)->unsigned();
            $table->timestamps();

            $table->unique(['feed_receipt_id', 'house_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_receipt_house_items');
    }
};
