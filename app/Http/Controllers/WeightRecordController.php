<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\Flock;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WeightRecordController extends Controller
{
    public function index(Request $request, Flock $flock): View
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);

        $recordDate = $request->query('date')
            ? CarbonImmutable::parse($request->query('date'))->toDateString()
            : now()->toDateString();

        $flock->load(['farm', 'flockHouseStarts.house', 'flockHousePlacements']);

        $farms = FarmAccess::farmsQuery($user)->orderBy('farm_name')->get();
        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();

        $houses = $flock->flockHouseStarts
            ->sortBy('house.house_no')
            ->pluck('house')
            ->values();

        $startsByHouse = $flock->flockHouseStarts->keyBy('house_id');

        $ageByHouse = $startsByHouse->map(
            fn ($start) => $this->ageDayForHouse($flock, $start, $recordDate)
        );

        $recordsByHouse = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->whereDate('record_date', $recordDate)
            ->get()
            ->keyBy('house_id');

        // Gather latest weight before this date per house (for reference)
        $previousWeightByHouse = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->whereDate('record_date', '<', $recordDate)
            ->whereNotNull('avg_weight')
            ->orderByDesc('record_date')
            ->get()
            ->unique('house_id')
            ->keyBy('house_id');

        $placementsByHouse = $flock->flockHousePlacements->keyBy('house_id');

        $ageDay = $this->ageDay($flock, $recordDate);

        return view('weight-records.index', [
            'flock' => $flock,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'houses' => $houses,
            'recordDate' => $recordDate,
            'ageDay' => $ageDay,
            'ageByHouse' => $ageByHouse,
            'recordsByHouse' => $recordsByHouse,
            'previousWeightByHouse' => $previousWeightByHouse,
            'placementsByHouse' => $placementsByHouse,
        ]);
    }

    public function store(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validate([
            'record_date' => ['required', 'date'],
            'weights' => ['required', 'array'],
            'weights.*' => ['nullable', 'numeric', 'min:0', 'max:99.999'],
        ], [], [
            'record_date' => 'วันที่บันทึก',
            'weights.*' => 'น้ำหนักเฉลี่ย',
        ]);

        $recordDate = CarbonImmutable::parse($validated['record_date'])->toDateString();
        $flock->load(['flockHouseStarts.house']);
        $houseIds = $flock->flockHouseStarts->pluck('house_id')->map(fn ($id) => (int) $id)->all();
        $weights = collect($validated['weights'] ?? []);
        $userId = $request->user()?->id;
        $startsByHouse = $flock->flockHouseStarts->keyBy('house_id');

        DB::transaction(function () use ($flock, $recordDate, $houseIds, $weights, $userId, $startsByHouse): void {
            foreach ($houseIds as $houseId) {
                $ageDay = $this->ageDayForHouse($flock, $startsByHouse->get($houseId), $recordDate);

                $existingRecord = DailyHouseRecord::query()
                    ->where('flock_id', $flock->id)
                    ->where('house_id', $houseId)
                    ->whereDate('record_date', $recordDate)
                    ->first();

                $hasSubmittedWeight = $weights->has($houseId) || $weights->has((string) $houseId);
                $rawWeight = $weights->get($houseId, $weights->get((string) $houseId));
                $avgWeight = $hasSubmittedWeight
                    ? ($rawWeight === null || $rawWeight === '' ? null : round((float) $rawWeight, 3))
                    : $existingRecord?->avg_weight;

                DailyHouseRecord::query()->updateOrCreate(
                    [
                        'flock_id' => $flock->id,
                        'house_id' => $houseId,
                        'record_date' => $recordDate,
                    ],
                    [
                        'age_day' => $ageDay,
                        'feed_code' => $existingRecord?->feed_code,
                        'feed_in' => (float) ($existingRecord?->feed_in ?? 0),
                        'feed_used' => (float) ($existingRecord?->feed_used ?? 0),
                        'water_meter_reading' => $existingRecord?->water_meter_reading,
                        'water_used' => (int) ($existingRecord?->water_used ?? 0),
                        'temp_min' => $existingRecord?->temp_min,
                        'temp_max' => $existingRecord?->temp_max,
                        'humidity' => $existingRecord?->humidity,
                        'dead_morning' => (int) ($existingRecord?->dead_morning ?? 0),
                        'dead_evening' => (int) ($existingRecord?->dead_evening ?? 0),
                        'cull_morning' => (int) ($existingRecord?->cull_morning ?? 0),
                        'cull_evening' => (int) ($existingRecord?->cull_evening ?? 0),
                        'avg_weight' => $avgWeight,
                        'medicine_note' => $existingRecord?->medicine_note,
                        'remark' => $existingRecord?->remark,
                        'created_by' => $existingRecord?->created_by ?: $userId,
                        'updated_by' => $userId,
                    ],
                );
            }
        });

        return redirect()
            ->route('flocks.weight-records.index', [
                'flock' => $flock,
                'date' => $recordDate,
            ])
            ->with('status', 'บันทึกน้ำหนักไก่เรียบร้อยแล้ว');
    }

    private function ageDay(Flock $flock, string $recordDate): int
    {
        return (int) max(1, CarbonImmutable::parse($flock->start_date)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;

        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }
}
