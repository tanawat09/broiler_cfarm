<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flock_slaughter_records', function (Blueprint $table) {
            $table->unsignedInteger('sequence')->default(0)->after('slaughter_date');
        });
    }

    public function down(): void
    {
        Schema::table('flock_slaughter_records', function (Blueprint $table) {
            $table->dropColumn('sequence');
        });
    }
};
