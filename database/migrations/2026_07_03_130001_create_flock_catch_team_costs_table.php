<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flock_catch_team_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignId('flock_id')->constrained('flocks')->cascadeOnDelete();
            $table->string('catching_team');
            $table->decimal('fuel_cost', 10, 2)->default(0);
            $table->decimal('forklift_cost', 10, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['flock_id', 'catching_team']);
            $table->index(['farm_id', 'flock_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flock_catch_team_costs');
    }
};
