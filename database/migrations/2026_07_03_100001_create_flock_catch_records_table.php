<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_catch_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained('houses')->cascadeOnDelete();
            $table->date('catch_date');
            $table->unsignedInteger('sequence');
            $table->string('license_plate');
            $table->string('vehicle_type')->nullable();
            $table->string('catching_team')->nullable();
            $table->decimal('catching_fee', 10, 2);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['farm_id', 'flock_id', 'house_id', 'catch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_catch_records');
    }
};
