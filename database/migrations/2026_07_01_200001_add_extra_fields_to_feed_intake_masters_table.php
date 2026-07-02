<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_intake_masters', function (Blueprint $table) {
            $table->decimal('cum_feed_ah', 8, 2)->nullable()->after('feed_female');
            $table->decimal('cum_feed_male', 8, 2)->nullable()->after('cum_feed_ah');
            $table->decimal('cum_feed_female', 8, 2)->nullable()->after('cum_feed_male');

            $table->decimal('weight_ah', 8, 2)->nullable()->after('cum_feed_female');
            $table->decimal('weight_male', 8, 2)->nullable()->after('weight_ah');
            $table->decimal('weight_female', 8, 2)->nullable()->after('weight_male');

            $table->decimal('mortality_ah', 5, 2)->nullable()->after('weight_female');
            $table->decimal('mortality_male', 5, 2)->nullable()->after('mortality_ah');
            $table->decimal('mortality_female', 5, 2)->nullable()->after('mortality_male');

            $table->decimal('fcr_ah', 6, 3)->nullable()->after('mortality_female');
            $table->decimal('fcr_male', 6, 3)->nullable()->after('fcr_ah');
            $table->decimal('fcr_female', 6, 3)->nullable()->after('fcr_male');

            $table->integer('pi_ah')->nullable()->after('fcr_female');
            $table->integer('pi_male')->nullable()->after('pi_ah');
            $table->integer('pi_female')->nullable()->after('pi_male');
        });
    }

    public function down(): void
    {
        Schema::table('feed_intake_masters', function (Blueprint $table) {
            $table->dropColumn([
                'cum_feed_ah', 'cum_feed_male', 'cum_feed_female',
                'weight_ah', 'weight_male', 'weight_female',
                'mortality_ah', 'mortality_male', 'mortality_female',
                'fcr_ah', 'fcr_male', 'fcr_female',
                'pi_ah', 'pi_male', 'pi_female'
            ]);
        });
    }
};
