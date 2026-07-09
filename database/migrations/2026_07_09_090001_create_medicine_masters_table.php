<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('medicine_masters')) {
            Schema::create('medicine_masters', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('default_unit')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('note')->nullable();
                $table->timestamps();
            });

            DB::table('medicine_masters')->insert([
                [
                    'name' => 'Soludox',
                    'default_unit' => 'กรัม',
                    'is_active' => true,
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Poulvac IB QX+Hipraviar-CLON',
                    'default_unit' => 'โดส',
                    'is_active' => true,
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_masters');
    }
};
