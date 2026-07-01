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
        Schema::create('daily_house_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->date('record_date');
            $table->unsignedInteger('age_day');
            $table->string('feed_code')->nullable();
            $table->decimal('feed_in', 12, 2)->unsigned()->default(0);
            $table->decimal('feed_used', 12, 2)->unsigned()->default(0);
            $table->unsignedInteger('water_meter_reading')->nullable();
            $table->unsignedInteger('water_used')->default(0);
            $table->decimal('temp_min', 5, 2)->nullable();
            $table->decimal('temp_max', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2)->unsigned()->nullable();
            $table->unsignedInteger('dead_morning')->default(0);
            $table->unsignedInteger('dead_evening')->default(0);
            $table->unsignedInteger('cull_morning')->default(0);
            $table->unsignedInteger('cull_evening')->default(0);
            $table->decimal('avg_weight', 8, 3)->unsigned()->nullable();
            $table->text('medicine_note')->nullable();
            $table->text('remark')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['flock_id', 'house_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_house_records');
    }
};
