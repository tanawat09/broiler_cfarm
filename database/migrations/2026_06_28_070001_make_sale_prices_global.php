<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE sale_price_masters DROP FOREIGN KEY sale_price_masters_farm_id_foreign');
        } catch (Throwable) {
        }

        try {
            DB::statement('ALTER TABLE sale_price_masters DROP INDEX sale_price_masters_farm_id_effective_date_unique');
        } catch (Throwable) {
        }

        DB::statement('ALTER TABLE sale_price_masters MODIFY farm_id BIGINT UNSIGNED NULL');

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

        DB::statement('ALTER TABLE sale_price_masters MODIFY farm_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE sale_price_masters ADD CONSTRAINT sale_price_masters_farm_id_foreign FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE CASCADE');
        DB::statement('CREATE UNIQUE INDEX sale_price_masters_farm_id_effective_date_unique ON sale_price_masters (farm_id, effective_date)');
    }
};
