<?php

namespace Database\Seeders;

use App\Models\FeedIntakeMaster;
use Illuminate\Database\Seeder;

class FeedIntakeMasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['age' => 0, 'feed_ah' => 5.0, 'feed_male' => 5.0, 'feed_female' => 5.0],
            ['age' => 1, 'feed_ah' => 12.0, 'feed_male' => 16.0, 'feed_female' => 14.0],
            ['age' => 2, 'feed_ah' => 19.0, 'feed_male' => 19.0, 'feed_female' => 17.0],
            ['age' => 3, 'feed_ah' => 22.0, 'feed_male' => 21.0, 'feed_female' => 21.0],
            ['age' => 4, 'feed_ah' => 23.0, 'feed_male' => 23.0, 'feed_female' => 23.0],
            ['age' => 5, 'feed_ah' => 25.0, 'feed_male' => 25.0, 'feed_female' => 25.0],
            ['age' => 6, 'feed_ah' => 27.0, 'feed_male' => 28.0, 'feed_female' => 27.0],
            ['age' => 7, 'feed_ah' => 30.0, 'feed_male' => 32.0, 'feed_female' => 31.0],
            ['age' => 8, 'feed_ah' => 36.0, 'feed_male' => 36.0, 'feed_female' => 36.0],
            ['age' => 9, 'feed_ah' => 40.0, 'feed_male' => 41.0, 'feed_female' => 38.0],
            ['age' => 10, 'feed_ah' => 45.0, 'feed_male' => 47.0, 'feed_female' => 43.0],
            ['age' => 11, 'feed_ah' => 51.0, 'feed_male' => 54.0, 'feed_female' => 49.0],
            ['age' => 12, 'feed_ah' => 58.0, 'feed_male' => 61.0, 'feed_female' => 54.0],
            ['age' => 13, 'feed_ah' => 66.0, 'feed_male' => 70.0, 'feed_female' => 60.0],
            ['age' => 14, 'feed_ah' => 72.0, 'feed_male' => 81.0, 'feed_female' => 65.0],
            ['age' => 15, 'feed_ah' => 76.0, 'feed_male' => 80.0, 'feed_female' => 72.0],
            ['age' => 16, 'feed_ah' => 82.0, 'feed_male' => 86.0, 'feed_female' => 77.0],
            ['age' => 17, 'feed_ah' => 88.0, 'feed_male' => 93.0, 'feed_female' => 84.0],
            ['age' => 18, 'feed_ah' => 95.0, 'feed_male' => 99.0, 'feed_female' => 89.0],
            ['age' => 19, 'feed_ah' => 100.0, 'feed_male' => 105.0, 'feed_female' => 95.0],
            ['age' => 20, 'feed_ah' => 106.0, 'feed_male' => 112.0, 'feed_female' => 101.0],
            ['age' => 21, 'feed_ah' => 113.0, 'feed_male' => 118.0, 'feed_female' => 106.0],
            ['age' => 22, 'feed_ah' => 118.0, 'feed_male' => 124.0, 'feed_female' => 112.0],
            ['age' => 23, 'feed_ah' => 123.0, 'feed_male' => 130.0, 'feed_female' => 117.0],
            ['age' => 24, 'feed_ah' => 129.0, 'feed_male' => 136.0, 'feed_female' => 122.0],
            ['age' => 25, 'feed_ah' => 134.0, 'feed_male' => 141.0, 'feed_female' => 127.0],
            ['age' => 26, 'feed_ah' => 139.0, 'feed_male' => 146.0, 'feed_female' => 131.0],
            ['age' => 27, 'feed_ah' => 144.0, 'feed_male' => 151.0, 'feed_female' => 136.0],
            ['age' => 28, 'feed_ah' => 149.0, 'feed_male' => 156.0, 'feed_female' => 140.0],
            ['age' => 29, 'feed_ah' => 152.0, 'feed_male' => 160.0, 'feed_female' => 145.0],
            ['age' => 30, 'feed_ah' => 157.0, 'feed_male' => 165.0, 'feed_female' => 149.0],
            ['age' => 31, 'feed_ah' => 160.0, 'feed_male' => 169.0, 'feed_female' => 152.0],
            ['age' => 32, 'feed_ah' => 165.0, 'feed_male' => 173.0, 'feed_female' => 156.0],
            ['age' => 33, 'feed_ah' => 168.0, 'feed_male' => 176.0, 'feed_female' => 159.0],
            ['age' => 34, 'feed_ah' => 172.0, 'feed_male' => 180.0, 'feed_female' => 163.0],
            ['age' => 35, 'feed_ah' => 175.0, 'feed_male' => 184.0, 'feed_female' => 167.0],
            ['age' => 36, 'feed_ah' => 178.0, 'feed_male' => 187.0, 'feed_female' => 169.0],
            ['age' => 37, 'feed_ah' => 182.0, 'feed_male' => 191.0, 'feed_female' => 173.0],
            ['age' => 38, 'feed_ah' => 185.0, 'feed_male' => 194.0, 'feed_female' => 176.0],
            ['age' => 39, 'feed_ah' => 188.0, 'feed_male' => 197.0, 'feed_female' => 179.0],
            ['age' => 40, 'feed_ah' => 192.0, 'feed_male' => 201.0, 'feed_female' => 183.0],
            ['age' => 41, 'feed_ah' => 195.0, 'feed_male' => 203.0, 'feed_female' => 186.0],
            ['age' => 42, 'feed_ah' => 198.0, 'feed_male' => 207.0, 'feed_female' => 189.0],
            ['age' => 43, 'feed_ah' => 202.0, 'feed_male' => 211.0, 'feed_female' => 193.0],
            ['age' => 44, 'feed_ah' => 205.0, 'feed_male' => 213.0, 'feed_female' => 196.0],
            ['age' => 45, 'feed_ah' => 209.0, 'feed_male' => 217.0, 'feed_female' => 200.0],
            ['age' => 46, 'feed_ah' => 212.0, 'feed_male' => 221.0, 'feed_female' => 203.0],
            ['age' => 47, 'feed_ah' => 215.0, 'feed_male' => 223.0, 'feed_female' => 207.0],
            ['age' => 48, 'feed_ah' => 219.0, 'feed_male' => 227.0, 'feed_female' => 211.0],
            ['age' => 49, 'feed_ah' => 222.0, 'feed_male' => 230.0, 'feed_female' => 214.0],
            ['age' => 50, 'feed_ah' => 225.0, 'feed_male' => 232.0, 'feed_female' => 218.0],
            ['age' => 51, 'feed_ah' => 228.0, 'feed_male' => 235.0, 'feed_female' => 221.0],
            ['age' => 52, 'feed_ah' => 230.0, 'feed_male' => 237.0, 'feed_female' => 224.0],
            ['age' => 53, 'feed_ah' => 232.0, 'feed_male' => 239.0, 'feed_female' => 227.0],
            ['age' => 54, 'feed_ah' => 234.0, 'feed_male' => 239.0, 'feed_female' => 229.0],
            ['age' => 55, 'feed_ah' => 235.0, 'feed_male' => 239.0, 'feed_female' => 230.0],
            ['age' => 56, 'feed_ah' => 236.0, 'feed_male' => 239.0, 'feed_female' => 231.0],
        ];

        foreach ($data as $item) {
            FeedIntakeMaster::query()->updateOrCreate(
                ['age' => $item['age']],
                $item
            );
        }
    }
}
