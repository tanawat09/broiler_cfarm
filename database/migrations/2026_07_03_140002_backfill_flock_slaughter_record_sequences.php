<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $recordsByFlock = DB::table('flock_slaughter_records')
            ->select('id', 'flock_id')
            ->orderBy('flock_id')
            ->orderBy('slaughter_date')
            ->orderBy('id')
            ->get()
            ->groupBy('flock_id');

        foreach ($recordsByFlock as $records) {
            $sequence = 1;
            foreach ($records as $record) {
                DB::table('flock_slaughter_records')
                    ->where('id', $record->id)
                    ->update(['sequence' => $sequence]);

                $sequence++;
            }
        }
    }

    public function down(): void
    {
        DB::table('flock_slaughter_records')->update(['sequence' => 0]);
    }
};
