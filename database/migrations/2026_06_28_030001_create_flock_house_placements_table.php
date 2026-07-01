<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_house_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->date('placement_date')->nullable();
            $table->date('catch_date')->nullable();
            $table->unsignedInteger('catch_age')->nullable();
            $table->unsignedInteger('chicks_in')->default(0);
            $table->unsignedInteger('male_count')->default(0);
            $table->unsignedInteger('female_count')->default(0);
            $table->unsignedInteger('male_grade_a_count')->default(0);
            $table->unsignedInteger('male_grade_b_count')->default(0);
            $table->unsignedInteger('female_grade_a_count')->default(0);
            $table->unsignedInteger('female_grade_b_count')->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('chick_source')->nullable();
            $table->string('chick_grade')->nullable();
            $table->text('chick_code')->nullable();
            $table->string('batch_no')->nullable();
            $table->string('sex')->nullable();
            $table->string('breed')->nullable();
            $table->timestamps();

            $table->unique(['flock_id', 'house_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_house_placements');
    }
};
