<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_intake_masters', function (Blueprint $table) {
            $table->id();
            $table->integer('age')->unique();
            $table->decimal('feed_ah', 8, 2);
            $table->decimal('feed_male', 8, 2);
            $table->decimal('feed_female', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_intake_masters');
    }
};
