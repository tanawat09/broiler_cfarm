<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('flocks')
            ->select('farm_id', 'flock_code', DB::raw('COUNT(*) as total'))
            ->groupBy('farm_id', 'flock_code')
            ->havingRaw('COUNT(*) > 1')
            ->limit(10)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $summary = $duplicates
                ->map(fn ($row) => "farm_id={$row->farm_id}, flock_code={$row->flock_code}, total={$row->total}")
                ->implode('; ');

            throw new RuntimeException("Cannot add unique flock code index. Duplicate records found: {$summary}");
        }

        Schema::table('flocks', function (Blueprint $table) {
            $table->unique(['farm_id', 'flock_code'], 'flocks_farm_id_flock_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('flocks', function (Blueprint $table) {
            $table->dropUnique('flocks_farm_id_flock_code_unique');
        });
    }
};
