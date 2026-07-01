<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->unsignedTinyInteger('house_count')->default(20)->after('farm_code');
        });

        DB::table('farms')->orderBy('id')->get()->each(function ($farm): void {
            $houseCount = DB::table('houses')->where('farm_id', $farm->id)->count();

            DB::table('farms')
                ->where('id', $farm->id)
                ->update(['house_count' => $houseCount > 0 ? $houseCount : 20]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('farm_manager')->after('password');
            $table->foreignId('farm_id')->nullable()->after('role')->constrained('farms')->nullOnDelete();
        });

        DB::table('users')->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('farm_id');
            $table->dropColumn('role');
        });

        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn('house_count');
        });
    }
};
