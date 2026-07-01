<?php

namespace Database\Seeders;

use App\Models\FeedIntakeMaster;
use Illuminate\Database\Seeder;

class FeedIntakeMasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['age' => 0, 'feed_ah' => 4.5, 'feed_male' => 0.0, 'feed_female' => 0.0],
            ['age' => 1, 'feed_ah' => 16.2, 'feed_male' => 16.2, 'feed_female' => 14.4],
            ['age' => 2, 'feed_ah' => 35.1, 'feed_male' => 35.1, 'feed_female' => 31.5],
            ['age' => 3, 'feed_ah' => 56.7, 'feed_male' => 55.8, 'feed_female' => 52.2],
            ['age' => 4, 'feed_ah' => 80.1, 'feed_male' => 78.3, 'feed_female' => 75.6],
            ['age' => 5, 'feed_ah' => 105.3, 'feed_male' => 103.5, 'feed_female' => 100.8],
            ['age' => 6, 'feed_ah' => 132.3, 'feed_male' => 131.4, 'feed_female' => 127.8],
            ['age' => 7, 'feed_ah' => 162.0, 'feed_male' => 163.8, 'feed_female' => 158.4],
            ['age' => 8, 'feed_ah' => 198.0, 'feed_male' => 199.8, 'feed_female' => 194.4],
            ['age' => 9, 'feed_ah' => 237.6, 'feed_male' => 240.3, 'feed_female' => 232.2],
            ['age' => 10, 'feed_ah' => 282.6, 'feed_male' => 287.1, 'feed_female' => 275.4],
            ['age' => 11, 'feed_ah' => 333.9, 'feed_male' => 341.1, 'feed_female' => 324.0],
            ['age' => 12, 'feed_ah' => 391.5, 'feed_male' => 402.3, 'feed_female' => 378.0],
            ['age' => 13, 'feed_ah' => 457.2, 'feed_male' => 472.5, 'feed_female' => 438.3],
            ['age' => 14, 'feed_ah' => 529.2, 'feed_male' => 553.5, 'feed_female' => 503.1],
            ['age' => 15, 'feed_ah' => 604.8, 'feed_male' => 633.6, 'feed_female' => 575.1],
            ['age' => 16, 'feed_ah' => 686.7, 'feed_male' => 720.0, 'feed_female' => 652.5],
            ['age' => 17, 'feed_ah' => 774.9, 'feed_male' => 812.7, 'feed_female' => 736.2],
            ['age' => 18, 'feed_ah' => 869.4, 'feed_male' => 911.7, 'feed_female' => 825.3],
            ['age' => 19, 'feed_ah' => 969.3, 'feed_male' => 1017.0, 'feed_female' => 920.7],
            ['age' => 20, 'feed_ah' => 1075.5, 'feed_male' => 1128.6, 'feed_female' => 1021.5],
            ['age' => 21, 'feed_ah' => 1188.0, 'feed_male' => 1246.5, 'feed_female' => 1127.7],
            ['age' => 22, 'feed_ah' => 1305.9, 'feed_male' => 1370.7, 'feed_female' => 1239.3],
            ['age' => 23, 'feed_ah' => 1429.2, 'feed_male' => 1500.3, 'feed_female' => 1356.3],
            ['age' => 24, 'feed_ah' => 1557.9, 'feed_male' => 1636.2, 'feed_female' => 1478.7],
            ['age' => 25, 'feed_ah' => 1692.0, 'feed_male' => 1777.5, 'feed_female' => 1605.6],
            ['age' => 26, 'feed_ah' => 1830.6, 'feed_male' => 1923.3, 'feed_female' => 1737.0],
            ['age' => 27, 'feed_ah' => 1974.6, 'feed_male' => 2074.5, 'feed_female' => 1872.9],
            ['age' => 28, 'feed_ah' => 2123.1, 'feed_male' => 2230.2, 'feed_female' => 2013.3],
            ['age' => 29, 'feed_ah' => 2275.2, 'feed_male' => 2390.4, 'feed_female' => 2158.2],
            ['age' => 30, 'feed_ah' => 2431.8, 'feed_male' => 2555.1, 'feed_female' => 2306.7],
            ['age' => 31, 'feed_ah' => 2592.0, 'feed_male' => 2724.3, 'feed_female' => 2458.8],
            ['age' => 32, 'feed_ah' => 2756.7, 'feed_male' => 2897.1, 'feed_female' => 2614.5],
            ['age' => 33, 'feed_ah' => 2925.0, 'feed_male' => 3073.5, 'feed_female' => 2773.8],
            ['age' => 34, 'feed_ah' => 3096.9, 'feed_male' => 3253.5, 'feed_female' => 2936.7],
            ['age' => 35, 'feed_ah' => 3271.5, 'feed_male' => 3437.1, 'feed_female' => 3103.2],
            ['age' => 36, 'feed_ah' => 3449.7, 'feed_male' => 3624.3, 'feed_female' => 3272.4],
            ['age' => 37, 'feed_ah' => 3631.5, 'feed_male' => 3815.1, 'feed_female' => 3445.2],
            ['age' => 38, 'feed_ah' => 3816.9, 'feed_male' => 4008.6, 'feed_female' => 3621.6],
            ['age' => 39, 'feed_ah' => 4005.0, 'feed_male' => 4205.7, 'feed_female' => 3800.7],
            ['age' => 40, 'feed_ah' => 4196.7, 'feed_male' => 4406.4, 'feed_female' => 3983.4],
            ['age' => 41, 'feed_ah' => 4392.0, 'feed_male' => 4609.8, 'feed_female' => 4169.7],
            ['age' => 42, 'feed_ah' => 4590.0, 'feed_male' => 4816.8, 'feed_female' => 4358.7],
            ['age' => 43, 'feed_ah' => 4791.6, 'feed_male' => 5027.4, 'feed_female' => 4551.3],
            ['age' => 44, 'feed_ah' => 4996.8, 'feed_male' => 5240.7, 'feed_female' => 4747.5],
            ['age' => 45, 'feed_ah' => 5205.6, 'feed_male' => 5457.6, 'feed_female' => 4947.3],
            ['age' => 46, 'feed_ah' => 5418.0, 'feed_male' => 5678.1, 'feed_female' => 5150.7],
            ['age' => 47, 'feed_ah' => 5633.1, 'feed_male' => 5901.3, 'feed_female' => 5357.7],
            ['age' => 48, 'feed_ah' => 5851.8, 'feed_male' => 6128.1, 'feed_female' => 5568.3],
            ['age' => 49, 'feed_ah' => 6074.1, 'feed_male' => 6357.6, 'feed_female' => 5782.5],
            ['age' => 50, 'feed_ah' => 6299.1, 'feed_male' => 6589.8, 'feed_female' => 6000.3],
            ['age' => 51, 'feed_ah' => 6526.8, 'feed_male' => 6824.7, 'feed_female' => 6220.8],
            ['age' => 52, 'feed_ah' => 6757.2, 'feed_male' => 7061.4, 'feed_female' => 6444.9],
            ['age' => 53, 'feed_ah' => 6989.4, 'feed_male' => 7299.9, 'feed_female' => 6671.7],
            ['age' => 54, 'feed_ah' => 7223.4, 'feed_male' => 7539.3, 'feed_female' => 6900.3],
            ['age' => 55, 'feed_ah' => 7458.3, 'feed_male' => 7778.7, 'feed_female' => 7130.7],
            ['age' => 56, 'feed_ah' => 7694.1, 'feed_male' => 8018.1, 'feed_female' => 7362.0],
        ];

        // Clear existing master data first to avoid leftovers if schema changed
        FeedIntakeMaster::query()->truncate();

        foreach ($data as $item) {
            FeedIntakeMaster::query()->create($item);
        }
    }
}
