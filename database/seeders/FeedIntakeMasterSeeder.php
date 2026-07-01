<?php

namespace Database\Seeders;

use App\Models\FeedIntakeMaster;
use Illuminate\Database\Seeder;

class FeedIntakeMasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['age' => 0, 'feed_ah' => 4.50, 'feed_male' => 0.00, 'feed_female' => 0.00],
            ['age' => 1, 'feed_ah' => 11.70, 'feed_male' => 16.20, 'feed_female' => 14.40],
            ['age' => 2, 'feed_ah' => 18.90, 'feed_male' => 18.90, 'feed_female' => 17.10],
            ['age' => 3, 'feed_ah' => 21.60, 'feed_male' => 20.70, 'feed_female' => 20.70],
            ['age' => 4, 'feed_ah' => 23.40, 'feed_male' => 22.50, 'feed_female' => 23.40],
            ['age' => 5, 'feed_ah' => 25.20, 'feed_male' => 25.20, 'feed_female' => 25.20],
            ['age' => 6, 'feed_ah' => 27.00, 'feed_male' => 27.90, 'feed_female' => 27.00],
            ['age' => 7, 'feed_ah' => 29.70, 'feed_male' => 32.40, 'feed_female' => 30.60],
            ['age' => 8, 'feed_ah' => 36.00, 'feed_male' => 36.00, 'feed_female' => 36.00],
            ['age' => 9, 'feed_ah' => 39.60, 'feed_male' => 40.50, 'feed_female' => 37.80],
            ['age' => 10, 'feed_ah' => 45.00, 'feed_male' => 46.80, 'feed_female' => 43.20],
            ['age' => 11, 'feed_ah' => 51.30, 'feed_male' => 54.00, 'feed_female' => 48.60],
            ['age' => 12, 'feed_ah' => 57.60, 'feed_male' => 61.20, 'feed_female' => 54.00],
            ['age' => 13, 'feed_ah' => 65.70, 'feed_male' => 70.20, 'feed_female' => 60.30],
            ['age' => 14, 'feed_ah' => 72.00, 'feed_male' => 81.00, 'feed_female' => 64.80],
            ['age' => 15, 'feed_ah' => 75.60, 'feed_male' => 80.10, 'feed_female' => 72.00],
            ['age' => 16, 'feed_ah' => 81.90, 'feed_male' => 86.40, 'feed_female' => 77.40],
            ['age' => 17, 'feed_ah' => 88.20, 'feed_male' => 92.70, 'feed_female' => 83.70],
            ['age' => 18, 'feed_ah' => 94.50, 'feed_male' => 99.00, 'feed_female' => 89.10],
            ['age' => 19, 'feed_ah' => 99.90, 'feed_male' => 105.30, 'feed_female' => 95.40],
            ['age' => 20, 'feed_ah' => 106.20, 'feed_male' => 111.60, 'feed_female' => 100.80],
            ['age' => 21, 'feed_ah' => 112.50, 'feed_male' => 117.90, 'feed_female' => 106.20],
            ['age' => 22, 'feed_ah' => 117.90, 'feed_male' => 124.20, 'feed_female' => 111.60],
            ['age' => 23, 'feed_ah' => 123.30, 'feed_male' => 129.60, 'feed_female' => 117.00],
            ['age' => 24, 'feed_ah' => 128.70, 'feed_male' => 135.90, 'feed_female' => 122.40],
            ['age' => 25, 'feed_ah' => 134.10, 'feed_male' => 141.30, 'feed_female' => 126.90],
            ['age' => 26, 'feed_ah' => 138.60, 'feed_male' => 145.80, 'feed_female' => 131.40],
            ['age' => 27, 'feed_ah' => 144.00, 'feed_male' => 151.20, 'feed_female' => 135.90],
            ['age' => 28, 'feed_ah' => 148.50, 'feed_male' => 155.70, 'feed_female' => 140.40],
            ['age' => 29, 'feed_ah' => 152.10, 'feed_male' => 160.20, 'feed_female' => 144.90],
            ['age' => 30, 'feed_ah' => 156.60, 'feed_male' => 164.70, 'feed_female' => 148.50],
            ['age' => 31, 'feed_ah' => 160.20, 'feed_male' => 169.20, 'feed_female' => 152.10],
            ['age' => 32, 'feed_ah' => 164.70, 'feed_male' => 172.80, 'feed_female' => 155.70],
            ['age' => 33, 'feed_ah' => 168.30, 'feed_male' => 176.40, 'feed_female' => 159.30],
            ['age' => 34, 'feed_ah' => 171.90, 'feed_male' => 180.00, 'feed_female' => 162.90],
            ['age' => 35, 'feed_ah' => 174.60, 'feed_male' => 183.60, 'feed_female' => 166.50],
            ['age' => 36, 'feed_ah' => 178.20, 'feed_male' => 187.20, 'feed_female' => 169.20],
            ['age' => 37, 'feed_ah' => 181.80, 'feed_male' => 190.80, 'feed_female' => 172.80],
            ['age' => 38, 'feed_ah' => 185.40, 'feed_male' => 193.50, 'feed_female' => 176.40],
            ['age' => 39, 'feed_ah' => 188.10, 'feed_male' => 197.10, 'feed_female' => 179.10],
            ['age' => 40, 'feed_ah' => 191.70, 'feed_male' => 200.70, 'feed_female' => 182.70],
            ['age' => 41, 'feed_ah' => 195.30, 'feed_male' => 203.40, 'feed_female' => 186.30],
            ['age' => 42, 'feed_ah' => 198.00, 'feed_male' => 207.00, 'feed_female' => 189.00],
            ['age' => 43, 'feed_ah' => 201.60, 'feed_male' => 210.60, 'feed_female' => 192.60],
            ['age' => 44, 'feed_ah' => 205.20, 'feed_male' => 213.30, 'feed_female' => 196.20],
            ['age' => 45, 'feed_ah' => 208.80, 'feed_male' => 216.90, 'feed_female' => 199.80],
            ['age' => 46, 'feed_ah' => 212.40, 'feed_male' => 220.50, 'feed_female' => 203.40],
            ['age' => 47, 'feed_ah' => 215.10, 'feed_male' => 223.20, 'feed_female' => 207.00],
            ['age' => 48, 'feed_ah' => 218.70, 'feed_male' => 226.80, 'feed_female' => 210.60],
            ['age' => 49, 'feed_ah' => 222.30, 'feed_male' => 229.50, 'feed_female' => 214.20],
            ['age' => 50, 'feed_ah' => 225.00, 'feed_male' => 232.20, 'feed_female' => 217.80],
            ['age' => 51, 'feed_ah' => 227.70, 'feed_male' => 234.90, 'feed_female' => 220.50],
            ['age' => 52, 'feed_ah' => 230.40, 'feed_male' => 236.70, 'feed_female' => 224.10],
            ['age' => 53, 'feed_ah' => 232.20, 'feed_male' => 238.50, 'feed_female' => 226.80],
            ['age' => 54, 'feed_ah' => 234.00, 'feed_male' => 239.40, 'feed_female' => 228.60],
            ['age' => 55, 'feed_ah' => 234.90, 'feed_male' => 239.40, 'feed_female' => 230.40],
            ['age' => 56, 'feed_ah' => 235.80, 'feed_male' => 239.40, 'feed_female' => 231.30],
        ];

        // Clear existing master data first to avoid duplicates
        FeedIntakeMaster::query()->truncate();

        foreach ($data as $item) {
            FeedIntakeMaster::query()->create($item);
        }
    }
}
