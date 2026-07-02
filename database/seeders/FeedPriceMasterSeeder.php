<?php

namespace Database\Seeders;

use App\Models\FeedPriceMaster;
use Illuminate\Database\Seeder;

class FeedPriceMasterSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'feed_code' => '203',
                'price_per_kg' => 18.58,
                'effective_date' => '2026-01-01',
                'is_active' => true,
                'note' => 'ราคาตั้งต้นระบบ',
            ],
            [
                'feed_code' => '204',
                'price_per_kg' => 18.25,
                'effective_date' => '2026-01-01',
                'is_active' => true,
                'note' => 'ราคาตั้งต้นระบบ',
            ],
            [
                'feed_code' => '205',
                'price_per_kg' => 17.22,
                'effective_date' => '2026-01-01',
                'is_active' => true,
                'note' => 'ราคาตั้งต้นระบบ',
            ],
        ];

        foreach ($prices as $price) {
            FeedPriceMaster::query()->updateOrCreate(
                ['feed_code' => $price['feed_code'], 'effective_date' => $price['effective_date']],
                $price
            );
        }
    }
}
