<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Flock;
use App\Models\DailyHouseRecord;
use App\Models\FeedIntakeMaster;

$flock = Flock::find(6);
$start = $flock->flockHouseStarts->first(fn($s) => $s->house->house_no == 1);
$houseId = $start->house_id;
$initialBirds = $start->initial_birds;
$placement = $flock->flockHousePlacements->firstWhere('house_id', $houseId);
$sex = $placement?->sex ?? 'คละ';

// Load feed intake standard
$feedIntakes = FeedIntakeMaster::get()->keyBy('age');
$maxAge = $feedIntakes->keys()->max() ?? 56;

$records = DailyHouseRecord::where('flock_id', 6)
    ->where('house_id', $houseId)
    ->orderBy('record_date')
    ->get();

$runningLoss = 0;
$totalFeedUsed = 0.0;
$totalWaterUsed = 0;
$latestWeight = null;

echo "=== House 1 calculations for Flock 6 (Nong Thanon 2/69) ===\n";
echo "Initial Birds (ไก่ลงเริ่มต้น): " . number_format($initialBirds) . " ตัว\n";
echo "Sex (เพศ): " . $sex . "\n";
echo "Rearing Area (พื้นที่การเลี้ยง): 1,200 ตร.ม.\n";

$density = $initialBirds / 1200;
echo "1. Density calculation (ความหนาแน่น):\n";
echo "   Formula: ไก่ลงเริ่มต้น / พื้นที่เล้า\n";
echo "   Calculation: " . number_format($initialBirds) . " / 1,200 = " . number_format($density, 2) . " ตัว/ตร.ม.\n\n";

foreach ($records as $r) {
    $deadTotal = (int) ($r->dead_morning + $r->dead_evening);
    $cullTotal = (int) ($r->cull_morning + $r->cull_evening);
    $runningLoss += ($deadTotal + $cullTotal);
    
    $remaining = max(0, $initialBirds - $runningLoss);
    
    $ageDay = $r->age_day;
    $lookupAge = min($ageDay, $maxAge);
    $standard = $feedIntakes->get($lookupAge);
    $standardIntake = 0.0;
    if ($standard) {
        if ($sex === 'ผู้') {
            $standardIntake = (float) $standard->feed_male;
        } elseif ($sex === 'เมีย') {
            $standardIntake = (float) $standard->feed_female;
        } else {
            $standardIntake = (float) $standard->feed_ah;
        }
    }
    
    $calculatedFeed = ($standardIntake * $remaining) / 1000;
    $totalFeedUsed += $calculatedFeed;
    $totalWaterUsed += (int) $r->water_used;
    
    if ($r->avg_weight !== null) {
        $latestWeight = (float) $r->avg_weight;
    }
}

$remainingBirds = $initialBirds - $runningLoss;
echo "2. Remaining Birds calculation (ไก่คงเหลือปัจจุบัน):\n";
echo "   Formula: ไก่ลงเริ่มต้น - ตาย/คัดทิ้งสะสม\n";
echo "   Calculation: " . number_format($initialBirds) . " - " . number_format($runningLoss) . " = " . number_format($remainingBirds) . " ตัว\n\n";

echo "3. Total Feed Consumed (อาหารใช้สะสมทั้งหมด):\n";
echo "   Sum of calculated daily feed intake based on standards: " . number_format($totalFeedUsed, 2) . " กก.\n\n";

echo "4. FCR calculation (อัตราแลกเนื้อ):\n";
echo "   Formula: อาหารใช้สะสม / (ไก่เหลือ * น้ำหนักตัวเฉลี่ย)\n";
if ($latestWeight !== null && $latestWeight > 0) {
    $fcr = $totalFeedUsed / ($remainingBirds * $latestWeight);
    echo "   Latest Weight (น้ำหนักเฉลี่ยล่าสุด): " . number_format($latestWeight, 3) . " กก.\n";
    echo "   Calculation: " . number_format($totalFeedUsed, 2) . " / (" . number_format($remainingBirds) . " * " . number_format($latestWeight, 3) . ") = " . number_format($fcr, 2) . "\n";
} else {
    echo "   No average weight recorded yet.\n";
}
