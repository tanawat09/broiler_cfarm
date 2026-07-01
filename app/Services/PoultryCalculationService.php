<?php

namespace App\Services;

use App\Models\DailyHouseRecord;
use InvalidArgumentException;

class PoultryCalculationService
{
    public function calculateWaterUsed(int $flockId, int $houseId, string $recordDate, ?int $currentMeter): int
    {
        if ($currentMeter === null) {
            return 0;
        }

        $previousRecord = DailyHouseRecord::query()
            ->where('flock_id', $flockId)
            ->where('house_id', $houseId)
            ->whereDate('record_date', '<', $recordDate)
            ->whereNotNull('water_meter_reading')
            ->orderByDesc('record_date')
            ->first();

        if (! $previousRecord) {
            return 0;
        }

        $previousMeter = (int) $previousRecord->water_meter_reading;

        if ($currentMeter < $previousMeter) {
            throw new InvalidArgumentException('เลขมิเตอร์น้ำวันนี้ต้องมากกว่าหรือเท่ากับเลขมิเตอร์ครั้งก่อน');
        }

        // น้ำใช้ต่อวัน = เลขมิเตอร์สะสมวันนี้ - เลขมิเตอร์สะสมครั้งก่อนของเล้าเดียวกัน
        return $currentMeter - $previousMeter;
    }

    public function calculateDeadTotal(int $deadMorning, int $deadEvening): int
    {
        return $deadMorning + $deadEvening;
    }

    public function calculateCullTotal(int $cullMorning, int $cullEvening): int
    {
        return $cullMorning + $cullEvening;
    }

    public function calculateLossTotal(int $deadMorning, int $deadEvening, int $cullMorning, int $cullEvening): int
    {
        return $this->calculateDeadTotal($deadMorning, $deadEvening)
            + $this->calculateCullTotal($cullMorning, $cullEvening);
    }

    public function calculateCumulativeLoss(int $flockId, int $houseId, string $recordDate): int
    {
        return (int) DailyHouseRecord::query()
            ->where('flock_id', $flockId)
            ->where('house_id', $houseId)
            ->whereDate('record_date', '<=', $recordDate)
            ->selectRaw('COALESCE(SUM(dead_morning + dead_evening + cull_morning + cull_evening), 0) as loss_total')
            ->value('loss_total');
    }

    public function calculateRemainingBirds(int $initialBirds, int $cumulativeLoss): int
    {
        return max(0, $initialBirds - $cumulativeLoss);
    }

    public function calculateMortalityRate(int $initialBirds, int $cumulativeLoss): float
    {
        if ($initialBirds <= 0) {
            return 0.0;
        }

        return round(($cumulativeLoss / $initialBirds) * 100, 2);
    }

    public function calculateRemainingFeed(float|int $cumulativeFeedIn, float|int $cumulativeFeedUsed): float
    {
        return round((float) $cumulativeFeedIn - (float) $cumulativeFeedUsed, 2);
    }

    public function calculateTotalWeight(int $remainingBirds, float|int|null $avgWeight): ?float
    {
        if ($avgWeight === null || (float) $avgWeight <= 0 || $remainingBirds <= 0) {
            return null;
        }

        return round($remainingBirds * (float) $avgWeight, 3);
    }

    public function calculateFcr(float|int $cumulativeFeedUsed, int $remainingBirds, float|int|null $avgWeight): ?float
    {
        $totalWeight = $this->calculateTotalWeight($remainingBirds, $avgWeight);

        if ($totalWeight === null || $totalWeight <= 0) {
            return null;
        }

        // FCR พื้นฐาน = อาหารใช้สะสม / น้ำหนักไก่รวม
        return round((float) $cumulativeFeedUsed / $totalWeight, 2);
    }
}
