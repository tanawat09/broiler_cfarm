<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('feed_receipts', 'flock_id')) {
            Schema::table('feed_receipts', function (Blueprint $table) {
                $table->foreignId('flock_id')
                    ->nullable()
                    ->after('farm_id')
                    ->constrained('flocks')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('feed_receipts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('flock_id');
        });
    }
};
