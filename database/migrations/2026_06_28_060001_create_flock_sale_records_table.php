<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_sale_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->date('sale_date');
            $table->unsignedInteger('birds_sold');
            $table->decimal('total_weight', 12, 2);
            $table->decimal('avg_weight', 8, 3)->default(0);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['flock_id', 'house_id', 'sale_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_sale_records');
    }
};
