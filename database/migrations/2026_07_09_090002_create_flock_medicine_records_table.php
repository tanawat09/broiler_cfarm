<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('flock_medicine_records')) {
            Schema::create('flock_medicine_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
                $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
                $table->foreignId('medicine_master_id')->nullable()->constrained('medicine_masters')->nullOnDelete();
                $table->date('record_date');
                $table->unsignedInteger('age_day')->default(0);
                $table->string('medicine_name');
                $table->string('quantity')->nullable();
                $table->decimal('dose_per_1000_birds', 10, 2)->nullable();
                $table->string('unit')->nullable();
                $table->text('note')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['flock_id', 'house_id', 'record_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_medicine_records');
    }
};
