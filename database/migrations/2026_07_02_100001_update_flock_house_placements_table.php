<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('flock_house_placements', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('flock_house_placements_flock_id_house_id_unique');
            
            // Add normal indexes to satisfy foreign keys
            $table->index('flock_id');
            $table->index('house_id');
            
            // Add remarks column
            $table->string('remarks')->nullable();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('flock_house_placements', function (Blueprint $table) {
            $table->unique(['flock_id', 'house_id']);
            $table->dropIndex(['flock_id']);
            $table->dropIndex(['house_id']);
            $table->dropColumn('remarks');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
