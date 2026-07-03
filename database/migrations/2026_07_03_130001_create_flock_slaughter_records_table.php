<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_slaughter_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->date('slaughter_date');
            $table->string('raw_house_name')->nullable();
            
            // Excel data columns
            $table->unsignedInteger('slaughter_birds')->default(0); // Column K
            $table->decimal('actual_weight', 10, 2)->default(0.00); // Column R
            $table->unsignedInteger('doa_birds')->default(0); // Column AA
            $table->unsignedInteger('net_birds')->default(0); // Column AB
            $table->unsignedInteger('condemned_birds')->default(0); // Column AF
            $table->decimal('condemned_percent', 5, 2)->default(0.00); // Column AG
            $table->unsignedInteger('problem_birds')->default(0); // Column AH
            $table->decimal('problem_percent', 5, 2)->default(0.00); // Column AI
            $table->decimal('dead_weight', 10, 2)->default(0.00); // Column AJ

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_slaughter_records');
    }
};
