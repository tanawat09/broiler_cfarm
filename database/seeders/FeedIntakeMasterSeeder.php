<?php

namespace Database\Seeders;

use App\Models\FeedIntakeMaster;
use Illuminate\Database\Seeder;

class FeedIntakeMasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['age' => 0, 'feed_ah' => 5, 'feed_male' => 0, 'feed_female' => 0],
            ['age' => 1, 'feed_ah' => 12, 'feed_male' => 16, 'feed_female' => 14],
            ['age' => 2, 'feed_ah' => 19, 'feed_male' => 19, 'feed_female' => 17],
            ['age' => 3, 'feed_ah' => 22, 'feed_male' => 21, 'feed_female' => 21],
            ['age' => 4, 'feed_ah' => 23, 'feed_male' => 22, 'feed_female' => 23],
            ['age' => 5, 'feed_ah' => 25, 'feed_male' => 25, 'feed_female' => 25],
            ['age' => 6, 'feed_ah' => 27, 'feed_male' => 28, 'feed_female' => 27],
            ['age' => 7, 'feed_ah' => 30, 'feed_male' => 32, 'feed_female' => 31],
            ['age' => 8, 'feed_ah' => 36, 'feed_male' => 36, 'feed_female' => 36],
            ['age' => 9, 'feed_ah' => 40, 'feed_male' => 41, 'feed_female' => 38],
            ['age' => 10, 'feed_ah' => 45, 'feed_male' => 47, 'feed_female' => 43],
            ['age' => 11, 'feed_ah' => 51, 'feed_male' => 54, 'feed_female' => 49],
            ['age' => 12, 'feed_ah' => 58, 'feed_male' => 61, 'feed_female' => 54],
            ['age' => 13, 'feed_ah' => 66, 'feed_male' => 70, 'feed_female' => 60],
            ['age' => 14, 'feed_ah' => 72, 'feed_male' => 81, 'feed_female' => 65],
            ['age' => 15, 'feed_ah' => 76, 'feed_male' => 80, 'feed_female' => 72],
            ['age' => 16, 'feed_ah' => 82, 'feed_male' => 86, 'feed_female' => 77],
            ['age' => 17, 'feed_ah' => 88, 'feed_male' => 93, 'feed_female' => 84],
            ['age' => 18, 'feed_ah' => 95, 'feed_male' => 99, 'feed_female' => 89],
            ['age' => 19, 'feed_ah' => 100, 'feed_male' => 105, 'feed_female' => 95],
            ['age' => 20, 'feed_ah' => 106, 'feed_male' => 112, 'feed_female' => 101],
            ['age' => 21, 'feed_ah' => 113, 'feed_male' => 118, 'feed_female' => 106],
            ['age' => 22, 'feed_ah' => 118, 'feed_male' => 124, 'feed_female' => 112],
            ['age' => 23, 'feed_ah' => 123, 'feed_male' => 130, 'feed_female' => 117],
            ['age' => 24, 'feed_ah' => 129, 'feed_male' => 136, 'feed_female' => 122],
            ['age' => 25, 'feed_ah' => 134, 'feed_male' => 141, 'feed_female' => 127],
            ['age' => 26, 'feed_ah' => 139, 'feed_male' => 146, 'feed_female' => 131],
            ['age' => 27, 'feed_ah' => 144, 'feed_male' => 151, 'feed_female' => 136],
            ['age' => 28, 'feed_ah' => 148, 'feed_male' => 156, 'feed_female' => 140],
            ['age' => 29, 'feed_ah' => 152, 'feed_male' => 160, 'feed_female' => 145],
            ['age' => 30, 'feed_ah' => 157, 'feed_male' => 165, 'feed_female' => 149],
            ['age' => 31, 'feed_ah' => 160, 'feed_male' => 169, 'feed_female' => 152],
            ['age' => 32, 'feed_ah' => 165, 'feed_male' => 173, 'feed_female' => 156],
            ['age' => 33, 'feed_ah' => 168, 'feed_male' => 176, 'feed_female' => 159],
            ['age' => 34, 'feed_ah' => 172, 'feed_male' => 180, 'feed_female' => 163],
            ['age' => 35, 'feed_ah' => 175, 'feed_male' => 184, 'feed_female' => 167],
            ['age' => 36, 'feed_ah' => 178, 'feed_male' => 187, 'feed_female' => 169],
            ['age' => 37, 'feed_ah' => 182, 'feed_male' => 191, 'feed_female' => 173],
            ['age' => 38, 'feed_ah' => 185, 'feed_male' => 194, 'feed_female' => 176],
            ['age' => 39, 'feed_ah' => 188, 'feed_male' => 197, 'feed_female' => 179],
            ['age' => 40, 'feed_ah' => 192, 'feed_male' => 201, 'feed_female' => 183],
            ['age' => 41, 'feed_ah' => 195, 'feed_male' => 203, 'feed_female' => 186],
            ['age' => 42, 'feed_ah' => 198, 'feed_male' => 207, 'feed_female' => 189],
            ['age' => 43, 'feed_ah' => 202, 'feed_male' => 211, 'feed_female' => 193],
            ['age' => 44, 'feed_ah' => 205, 'feed_male' => 213, 'feed_female' => 196],
            ['age' => 45, 'feed_ah' => 209, 'feed_male' => 217, 'feed_female' => 200],
            ['age' => 46, 'feed_ah' => 212, 'feed_male' => 221, 'feed_female' => 203],
            ['age' => 47, 'feed_ah' => 215, 'feed_male' => 223, 'feed_female' => 207],
            ['age' => 48, 'feed_ah' => 219, 'feed_male' => 227, 'feed_female' => 211],
            ['age' => 49, 'feed_ah' => 222, 'feed_male' => 230, 'feed_female' => 214],
            ['age' => 50, 'feed_ah' => 225, 'feed_male' => 232, 'feed_female' => 218],
            ['age' => 51, 'feed_ah' => 228, 'feed_male' => 235, 'feed_female' => 221],
            ['age' => 52, 'feed_ah' => 230, 'feed_male' => 237, 'feed_female' => 224],
            ['age' => 53, 'feed_ah' => 232, 'feed_male' => 239, 'feed_female' => 227],
            ['age' => 54, 'feed_ah' => 234, 'feed_male' => 239, 'feed_female' => 229],
            ['age' => 55, 'feed_ah' => 235, 'feed_male' => 239, 'feed_female' => 230],
            ['age' => 56, 'feed_ah' => 236, 'feed_male' => 239, 'feed_female' => 231],
        ];

        // Clear existing master data first to avoid duplicates
        FeedIntakeMaster::query()->truncate();

        foreach ($data as $item) {
            FeedIntakeMaster::query()->create($item);
        }
    }
}
