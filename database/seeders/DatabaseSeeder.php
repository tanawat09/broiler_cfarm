<?php

namespace Database\Seeders;

use App\Models\DailyHouseRecord;
use App\Models\Farm;
use App\Models\FeedReceipt;
use App\Models\FeedReceiptHouseItem;
use App\Models\Flock;
use App\Models\FlockHousePlacement;
use App\Models\FlockHouseStart;
use App\Models\House;
use App\Models\ChickSource;
use App\Models\ChickPriceMaster;
use App\Models\SalePriceMaster;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $demoPassword = (string) env('DEMO_USER_PASSWORD', '');

        if (mb_strlen($demoPassword) < 12) {
            throw new \RuntimeException('DEMO_USER_PASSWORD must contain at least 12 characters before demo data can be seeded.');
        }

        if (app()->environment('production') && ! filter_var(env('ALLOW_DEMO_SEEDING', false), FILTER_VALIDATE_BOOLEAN)) {
            throw new \RuntimeException('Demo seeding is disabled in production. Set ALLOW_DEMO_SEEDING=true only for an isolated demo environment.');
        }

        DB::transaction(function () use ($demoPassword): void {
            $admin = User::query()->firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make($demoPassword),
                ],
            );
            $admin->forceFill([
                'role' => User::ROLE_SUPER_ADMIN,
                'farm_id' => null,
            ])->save();

            $farm = Farm::query()
                ->where('farm_code', 'FARM-001')
                ->orWhere('farm_name', 'ฟาร์มหนองถนน')
                ->orWhere('farm_name', 'หนองถนน')
                ->first() ?? new Farm();
            $farm->fill([
                'farm_name' => 'หนองถนน',
                'company_name' => 'บริษัทตัวอย่าง',
                'owner_name' => 'ผู้จัดการฟาร์ม หนองถนน',
                'address' => $farm->address ?: 'ที่อยู่ฟาร์มหนองถนน',
                'farm_code' => 'FARM-001',
                'house_count' => 20,
                'note' => $farm->note,
            ]);
            $farm->save();

            $houses = collect(range(1, 20))->mapWithKeys(function (int $houseNo) use ($farm) {
                $house = House::query()->firstOrCreate(
                    [
                        'farm_id' => $farm->id,
                        'house_no' => $houseNo,
                    ],
                    [
                        'house_name' => 'เล้า '.$houseNo,
                        'is_active' => true,
                    ],
                );

                return [$houseNo => $house];
            });

            foreach (['BF', 'BFI', 'BF,BFI,เนืองนามฟาร์ม', 'BF,เนืองนามฟาร์ม', 'BFI,BF', 'เนืองนามฟาร์ม', 'เบทาโกร'] as $sourceName) {
                ChickSource::query()->firstOrCreate(
                    ['name' => $sourceName],
                    ['is_active' => true],
                );
            }

            $farmDefinitions = [
                ['name' => 'หนองถนน', 'code' => 'FARM-001', 'houses' => 20, 'email' => 'nongthanon@example.com'],
                ['name' => 'ก้านเหลือง', 'code' => 'FARM-002', 'houses' => 18, 'email' => 'kanlueang@example.com'],
                ['name' => 'หนองบอน', 'code' => 'FARM-003', 'houses' => 14, 'email' => 'nongbon@example.com'],
                ['name' => 'โคกสนวน', 'code' => 'FARM-004', 'houses' => 12, 'email' => 'khoksanuan@example.com'],
                ['name' => 'บ้านบาตร', 'code' => 'FARM-005', 'houses' => 16, 'email' => 'banbat@example.com'],
                ['name' => 'ศรีสุข', 'code' => 'FARM-006', 'houses' => 18, 'email' => 'srisuk@example.com'],
                ['name' => 'นรินทร์', 'code' => 'FARM-007', 'houses' => 20, 'email' => 'narin@example.com'],
            ];

            $farmsByName = collect($farmDefinitions)->mapWithKeys(function (array $definition) use ($demoPassword): array {
                $farm = Farm::query()
                    ->where('farm_name', $definition['name'])
                    ->orWhere('farm_name', 'ฟาร์ม'.$definition['name'])
                    ->orWhere('farm_code', $definition['code'])
                    ->first();

                if (! $farm && $definition['name'] === 'หนองถนน') {
                    $farm = Farm::query()->orderBy('id')->first();
                }

                $farm ??= new Farm();
                $farm->fill([
                    'farm_name' => $definition['name'],
                    'company_name' => 'บริษัทตัวอย่าง',
                    'owner_name' => 'ผู้จัดการฟาร์ม '.$definition['name'],
                    'farm_code' => $definition['code'],
                    'house_count' => $definition['houses'],
                ]);
                $farm->save();

                $manager = User::query()->firstOrCreate(
                    ['email' => $definition['email']],
                    [
                        'name' => 'ผู้จัดการฟาร์ม '.$definition['name'],
                        'password' => Hash::make($demoPassword),
                    ],
                );
                $manager->forceFill([
                    'role' => User::ROLE_FARM_MANAGER,
                    'farm_id' => $farm->id,
                ])->save();

                foreach (range(1, $definition['houses']) as $houseNo) {
                    House::query()->firstOrCreate(
                        [
                            'farm_id' => $farm->id,
                            'house_no' => $houseNo,
                        ],
                        [
                            'house_name' => 'เล้า '.$houseNo,
                            'is_active' => true,
                        ],
                    );
                }

                House::query()
                    ->where('farm_id', $farm->id)
                    ->where('house_no', '>', $definition['houses'])
                    ->update(['is_active' => false]);

                return [$definition['name'] => $farm];
            });

            $farm = $farmsByName['หนองถนน'];
            $houses = $farm->houses()->orderBy('house_no')->limit(20)->get()->keyBy('house_no');

            foreach (ChickPriceMaster::SEXES as $sex) {
                foreach (ChickPriceMaster::GRADES as $grade) {
                    ChickPriceMaster::query()->firstOrCreate(
                        [
                            'sex' => $sex,
                            'grade' => $grade,
                            'effective_date' => '2026-04-01',
                        ],
                        [
                            'price_per_bird' => $grade === 'A' ? 15.50 : 14.75,
                            'is_active' => true,
                            'note' => 'ราคาเริ่มต้น',
                        ],
                    );
                }
            }

            SalePriceMaster::query()->updateOrCreate(
                ['effective_date' => '2026-04-01'],
                [
                    'farm_id' => null,
                    'price_per_kg' => 41.50,
                    'is_active' => true,
                    'note' => 'ราคาเริ่มต้น',
                ],
            );

            $initialBirdsByHouse = collect(range(1, 20))->mapWithKeys(
                fn (int $houseNo) => [$houseNo => $houseNo % 2 === 0 ? 1800 : 1700],
            );

            $flock = Flock::query()->firstOrCreate(
                ['flock_code' => 'FLOCK-001'],
                [
                    'farm_id' => $farm->id,
                    'chicken_type' => 'ไก่เนื้อ',
                    'start_date' => '2026-04-09',
                    'initial_birds' => $initialBirdsByHouse->sum(),
                    'status' => 'active',
                    'note' => 'รุ่นตัวอย่างสำหรับทดสอบสูตรคำนวณ',
                ],
            );

            $houses->each(function (House $house, int $houseNo) use ($flock, $initialBirdsByHouse): void {
                $houseStartDate = match (true) {
                    $houseNo >= 11 => '2026-04-11',
                    $houseNo >= 6 => '2026-04-10',
                    default => null,
                };
                $placementDate = $houseStartDate ?: '2026-04-09';
                $femaleCount = $houseNo % 3 === 0 ? 0 : $initialBirdsByHouse[$houseNo];
                $maleCount = $femaleCount === 0 ? $initialBirdsByHouse[$houseNo] : 0;

                FlockHouseStart::query()->firstOrCreate(
                    [
                        'flock_id' => $flock->id,
                        'house_id' => $house->id,
                    ],
                    [
                        'initial_birds' => $initialBirdsByHouse[$houseNo],
                        'start_date' => $houseStartDate,
                    ],
                );

                FlockHousePlacement::query()->firstOrCreate(
                    [
                        'flock_id' => $flock->id,
                        'house_id' => $house->id,
                    ],
                    [
                        'placement_date' => $placementDate,
                        'catch_date' => '2026-05-22',
                        'catch_age' => 44,
                        'chicks_in' => $initialBirdsByHouse[$houseNo],
                        'male_count' => $maleCount,
                        'female_count' => $femaleCount,
                        'male_grade_a_count' => $maleCount > 0 ? max(0, $maleCount - 120) : 0,
                        'male_grade_b_count' => $maleCount > 0 ? 120 : 0,
                        'female_grade_a_count' => $femaleCount > 0 ? max(0, $femaleCount - 100) : 0,
                        'female_grade_b_count' => $femaleCount > 0 ? 100 : 0,
                        'amount' => 500000 + ($houseNo * 1850),
                        'chick_source' => $houseNo % 4 === 0 ? 'BFI' : 'BF',
                        'chick_grade' => $houseNo % 5 === 0 ? 'A,B' : 'A',
                        'chick_code' => 'AT'.str_pad((string) $houseNo, 2, '0', STR_PAD_LEFT).'-ตัวอย่างรหัสลูกไก่',
                        'batch_no' => '129221055015',
                        'sex' => $femaleCount > 0 ? 'เมีย' : 'ผู้',
                        'breed' => $houseNo % 2 === 0 ? 'AA,CB' : 'AA',
                    ],
                );
            });

            $recordDates = [
                '2026-04-09' => ['age_day' => 1, 'feed_code' => '203T'],
                '2026-04-10' => ['age_day' => 2, 'feed_code' => '203T'],
                '2026-04-11' => ['age_day' => 3, 'feed_code' => '204T'],
            ];

            $houses->each(function (House $house, int $houseNo) use ($admin, $flock, $recordDates): void {
                $previousMeter = null;

                foreach ($recordDates as $recordDate => $recordInfo) {
                    $dayIndex = $recordInfo['age_day'] - 1;
                    $currentMeter = 100000 + ($houseNo * 1000) + ($dayIndex * (620 + $houseNo));
                    $waterUsed = $previousMeter === null ? 0 : $currentMeter - $previousMeter;

                    DailyHouseRecord::query()->firstOrCreate(
                        [
                            'flock_id' => $flock->id,
                            'house_id' => $house->id,
                            'record_date' => $recordDate,
                        ],
                        [
                            'age_day' => $recordInfo['age_day'],
                            'feed_code' => $recordInfo['feed_code'],
                            'feed_in' => $dayIndex === 0 ? 1200 : 0,
                            'feed_used' => 85 + ($houseNo * 2) + ($dayIndex * 12),
                            'water_meter_reading' => $currentMeter,
                            'water_used' => $waterUsed,
                            'temp_min' => 27.00,
                            'temp_max' => 32.50,
                            'humidity' => 65.00,
                            'dead_morning' => $houseNo % 5 === 0 ? 1 : 0,
                            'dead_evening' => $dayIndex === 2 && $houseNo % 4 === 0 ? 1 : 0,
                            'cull_morning' => $dayIndex === 1 && $houseNo % 6 === 0 ? 1 : 0,
                            'cull_evening' => 0,
                            'avg_weight' => $dayIndex === 2 ? 0.145 : null,
                            'medicine_note' => $dayIndex === 1 ? 'วิตามินละลายน้ำ' : null,
                            'remark' => null,
                            'created_by' => $admin->id,
                            'updated_by' => $admin->id,
                        ],
                    );

                    $previousMeter = $currentMeter;
                }
            });

            $sampleReceipts = [
                [
                    'receipt_date' => '2026-04-09',
                    'feed_code' => '203',
                    'production_lot' => 'LOT-203-001',
                    'items' => [
                        1 => 1200,
                        2 => 1200,
                        3 => 1000,
                    ],
                ],
                [
                    'receipt_date' => '2026-04-10',
                    'feed_code' => '204',
                    'production_lot' => 'LOT-204-001',
                    'items' => [
                        1 => 800,
                        2 => 900,
                        4 => 850,
                    ],
                ],
            ];

            foreach ($sampleReceipts as $receiptData) {
                $receipt = FeedReceipt::query()->firstOrCreate(
                    [
                        'farm_id' => $farm->id,
                        'receipt_date' => $receiptData['receipt_date'],
                        'feed_code' => $receiptData['feed_code'],
                        'production_lot' => $receiptData['production_lot'],
                    ],
                    [
                        'remark' => 'ข้อมูลตัวอย่างรับอาหาร',
                        'created_by' => $admin->id,
                        'updated_by' => $admin->id,
                    ],
                );

                foreach ($receiptData['items'] as $houseNo => $quantityKg) {
                    FeedReceiptHouseItem::query()->firstOrCreate(
                        [
                            'feed_receipt_id' => $receipt->id,
                            'house_id' => $houses[$houseNo]->id,
                        ],
                        [
                            'quantity_kg' => $quantityKg,
                        ],
                    );
                }
            }
        });

        $this->call(FeedIntakeMasterSeeder::class);
        $this->call(FeedPriceMasterSeeder::class);
    }
}
