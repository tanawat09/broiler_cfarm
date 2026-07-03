<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flock_catch_records', function (Blueprint $table) {
            $table->unsignedInteger('birds_count')->default(0)->after('license_plate');
            $table->unsignedInteger('boxes_count')->default(0)->after('birds_count');
        });
    }

    public function down(): void
    {
        Schema::table('flock_catch_records', function (Blueprint $table) {
            $table->dropColumn(['birds_count', 'boxes_count']);
        });
    }
};
