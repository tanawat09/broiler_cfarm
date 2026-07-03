<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catching_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default values
        DB::table('catching_teams')->insert([
            ['name' => 'ทีมอรอนงค์', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ทีมสงกรานต์', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ทีมประหยัด', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('catching_teams');
    }
};
