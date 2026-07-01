<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Farm;
use App\Models\Flock;
use App\Models\House;
use App\Models\FlockHousePlacement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // Find or create the Farm
    $farm = Farm::firstOrCreate(
        ['farm_name' => 'โคกสนวน'],
        ['farm_name' => 'โคกสนวน']
    );

    // Ensure all 12 houses exist for this farm
    $houses = [];
    for ($i = 1; $i <= 12; $i++) {
        $houses[$i] = House::firstOrCreate(
            ['farm_id' => $farm->id, 'house_no' => $i],
            ['farm_id' => $farm->id, 'house_no' => $i, 'is_active' => true]
        );
    }

    // Create the Flock 3/69
    $flock = Flock::updateOrCreate(
        ['farm_id' => $farm->id, 'flock_code' => '3/69'],
        [
            'chicken_type' => 'ไก่เนื้อ',
            'start_date' => '2026-06-09',
            'status' => 'active'
        ]
    );

    // Data from the image
    // Note: Years are in Buddhist Era format in the image (69 = 2569 BE = 2026 AD)
    // Actually, 2569 BE is 2026 AD. Wait. 2569 - 543 = 2026. Yes.
    $data = [
        [1, '2026-06-09', '2026-07-21', 42, 22848, 0, 22848, 0, 324800, 'FM', 'A', 'AA', 'BF,BFI', 'AW05-0658960955,AW07-0957960955,AO0764960957', '129221055021'],
        [2, '2026-06-09', '2026-07-21', 42, 23460, 2754, 20706, 44550, 274050, 'AH', 'B', 'CB', 'BFI', 'CN01-0427960940,CN05-0626960940,CN0826960940,CN1126960940', '129221055021'],
        [3, '2026-06-10', '2026-07-21', 41, 22950, 0, 22950, 0, 303750, 'FM', 'B', 'CB', 'BFI', 'CN01-0527961040', '129221055021'],
        [4, '2026-06-10', '2026-07-22', 42, 22848, 0, 22848, 0, 302400, 'FM', 'B', 'CB', 'BFI', 'CN05-0627961040,CN0827961040,CN11-1327961040,AN0726961040,AN09-1026961040', '129221055021'],
        [5, '2026-06-10', '2026-07-22', 42, 22950, 0, 22950, 0, 326250, 'FM', 'A', 'AA', 'BFN', 'JH13-1648961054,JC13-1636961050,AT0634961049,AT0834961049', '129221055021'],
        [6, '2026-06-09', '2026-07-16', 37, 20400, 20400, 0, 330000, 0, 'M', 'B', 'CB', 'BFI', 'CN05-0626960940,CN0826960940,CN11-1326960940,AN0726960940,AN09-1026960940', '129221055021'],
        [7, '2026-06-09', '2026-07-16', 37, 19788, 19788, 0, 339500, 0, 'M', 'A', 'AA', 'BF', 'AW03-0558960955,AW01-0259960955', '129221055021'],
        [8, '2026-06-09', '2026-07-16', 37, 19788, 19788, 0, 339500, 0, 'M', 'A', 'AA', 'BF', 'AW05-0958960955', '129221055021'],
        [9, '2026-06-09', '2026-07-16', 37, 19992, 19992, 0, 343000, 0, 'M', 'A', 'CB,AA', 'BFN', 'JC13-1535960950,JC14-1636961050,AT0634961049', '129221055021'],
        [10, '2026-06-10', '2026-07-17', 37, 19890, 14994, 4896, 257250, 69600, 'AH', 'A', 'CB,AA', 'BFN,BFI', 'AT0634961049,AT0834961049,AT1233961049,CQ0953961054', '129221055021'],
        [11, '2026-06-11', '2026-07-17', 36, 20400, 20400, 0, 350000, 0, 'M', 'A', 'CB,AA', 'BFI', 'CQ03-0853961154,AO1363961157', '129221055021'],
        [12, '2026-06-11', '2026-07-17', 36, 20400, 17850, 2550, 306250, 36250, 'AH', 'A', 'AA', 'BFN', 'JH01-0451961154,JH05-0950961154,JH1049961154,JH15-1648961154', '129221055021'],
    ];

    $totalBirds = 0;
    foreach ($data as $row) {
        $house_num = $row[0];
        $totalBirds += $row[4];
        
        FlockHousePlacement::updateOrCreate(
            ['flock_id' => $flock->id, 'house_id' => $houses[$house_num]->id],
            [
                'placement_date' => $row[1],
                'catch_date' => $row[2],
                'catch_age' => $row[3],
                'chicks_in' => $row[4],
                'male_count' => $row[5],
                'female_count' => $row[6],
                'amount' => $row[7] + $row[8],
                'sex' => $row[9],
                'chick_grade' => $row[10],
                'breed' => $row[11],
                'chick_source' => $row[12],
                'chick_code' => $row[13],
                'batch_no' => $row[14],
            ]
        );
        
        // Ensure flock_house_starts exists
        DB::table('flock_house_starts')->updateOrInsert(
            ['flock_id' => $flock->id, 'house_id' => $houses[$house_num]->id],
            ['start_date' => $row[1], 'initial_birds' => $row[4], 'created_at' => now(), 'updated_at' => now()]
        );
    }

    $flock->update(['initial_birds' => $totalBirds]);

    DB::commit();
    echo "Import completed successfully!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
