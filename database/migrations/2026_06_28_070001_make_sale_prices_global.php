<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('sale_price_masters', function (Blueprint $table): void {
                $table->dropUnique('sale_price_masters_farm_id_effective_date_unique');
                $table->dropForeign(['farm_id']);
                $table->unsignedBigInteger('farm_id')->nullable()->change();
            });
        } else {
            try {
                DB::statement('ALTER TABLE sale_price_masters DROP FOREIGN KEY sale_price_masters_farm_id_foreign');
            } catch (Throwable) {
            }

            try {
                DB::statement('ALTER TABLE sale_price_masters DROP INDEX sale_price_masters_farm_id_effective_date_unique');
            } catch (Throwable) {
            }

            DB::statement('ALTER TABLE sale_price_masters MODIFY farm_id BIGINT UNSIGNED NULL');
        }

        $keepIds = DB::table('sale_price_masters')
            ->selectRaw('MIN(id) as id')
            ->groupBy('effective_date')
            ->pluck('id')
            ->all();

        if ($keepIds !== []) {
            DB::table('sale_price_masters')->whereNotIn('id', $keepIds)->delete();
        }

        DB::table('sale_price_masters')->update(['farm_id' => null]);
    }

    public function down(): void
    {
        $firstFarmId = DB::table('farms')->orderBy('id')->value('id');

        if ($firstFarmId) {
            DB::table('sale_price_masters')
                ->whereNull('farm_id')
                ->update(['farm_id' => $firstFarmId]);
        }

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('sale_price_masters', function (Blueprint $table): void {
                $table->unsignedBigInteger('farm_id')->nullable(false)->change();
                $table->foreign('farm_id')->references('id')->on('farms')->cascadeOnDelete();
                $table->unique(['farm_id', 'effective_date']);
            });
        } else {
            DB::statement('ALTER TABLE sale_price_masters MODIFY farm_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE sale_price_masters ADD CONSTRAINT sale_price_masters_farm_id_foreign FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE CASCADE');
            DB::statement('CREATE UNIQUE INDEX sale_price_masters_farm_id_effective_date_unique ON sale_price_masters (farm_id, effective_date)');
        }
    }
};
