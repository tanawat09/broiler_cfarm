<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaterMeterRecordsRequest;
use App\Models\DailyHouseRecord;
use App\Models\Flock;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WaterMeterRecordController extends Controller
{
    public function index(Request $request, Flock $flock): View
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);

        $flock->load(['farm', 'flockHouseStarts.house']);
        $recordDate = $request->query('date')
            ? CarbonImmutable::parse($request->query('date'))->toDateString()
            : $this->defaultRecordDate($flock);
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
        $ageByHouse = $startsByHouse->map(fn ($start) => $this->ageDayForHouse($flock, $start, $recordDate));
        $recordsByHouse = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->whereDate('record_date', $recordDate)
            ->get()
            ->keyBy('house_id');
        $previousRecordsByHouse = DailyHouseRecord::query()
            ->join('flocks', 'flocks.id', '=', 'daily_house_records.flock_id')
            ->where('flocks.farm_id', $flock->farm_id)
            ->whereIn('daily_house_records.house_id', $houses->pluck('id'))
            ->whereDate('daily_house_records.record_date', '<', $recordDate)
            ->whereNotNull('water_meter_reading')
            ->orderByDesc('daily_house_records.record_date')
            ->orderByDesc('daily_house_records.id')
            ->get('daily_house_records.*')
            ->unique('house_id')
            ->keyBy('house_id');
        $ageDay = $this->ageDay($flock, $recordDate);

        return view('water-meters.index', [
            'flock' => $flock,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'houses' => $houses,
            'recordDate' => $recordDate,
            'ageDay' => $ageDay,
            'ageByHouse' => $ageByHouse,
            'recordsByHouse' => $recordsByHouse,
            'previousRecordsByHouse' => $previousRecordsByHouse,
        ]);
    }

    public function store(StoreWaterMeterRecordsRequest $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validated();
        $recordDate = CarbonImmutable::parse($validated['record_date'])->toDateString();
        $flock->load(['flockHouseStarts.house']);
        $houseIds = $flock->flockHouseStarts->pluck('house_id')->map(fn ($id) => (int) $id)->all();
        $meters = collect($validated['meters'] ?? []);
        $previousMeters = collect($validated['previous_meters'] ?? []);
        $userId = $request->user()?->id;

        DB::transaction(function () use ($flock, $recordDate, $houseIds, $meters, $previousMeters, $userId): void {
            $startsByHouse = $flock->flockHouseStarts->keyBy('house_id');
            $waterErrors = [];

            foreach ($houseIds as $houseId) {
                $ageDay = $this->ageDayForHouse($flock, $startsByHouse->get($houseId), $recordDate);
                $existingRecord = DailyHouseRecord::query()
                    ->where('flock_id', $flock->id)
                    ->where('house_id', $houseId)
                    ->whereDate('record_date', $recordDate)
                    ->first();
                $hasSubmittedMeter = $meters->has($houseId) || $meters->has((string) $houseId);
                $rawMeter = $meters->get($houseId, $meters->get((string) $houseId));
                $currentMeter = $hasSubmittedMeter
                    ? ($rawMeter === null || $rawMeter === '' ? null : (int) $rawMeter)
                    : $existingRecord?->water_meter_reading;
                $previousRecord = $this->previousWaterMeterRecord($flock->farm_id, $houseId, $recordDate);
                $hasSubmittedPreviousMeter = $previousMeters->has($houseId) || $previousMeters->has((string) $houseId);
                $rawPreviousMeter = $previousMeters->get($houseId, $previousMeters->get((string) $houseId));
                $initialPreviousMeter = $hasSubmittedPreviousMeter && ($rawPreviousMeter !== null && $rawPreviousMeter !== '')
                    ? (int) $rawPreviousMeter
                    : null;
                $waterUsed = (int) ($existingRecord?->water_used ?? 0);

                if (! $previousRecord && $currentMeter !== null && $initialPreviousMeter !== null) {
                    if ($currentMeter < $initialPreviousMeter) {
                        $waterErrors["meters.$houseId"] = 'เลขมิเตอร์วันนี้ต้องไม่น้อยกว่าเลขครั้งก่อน';
                    } else {
                        $waterUsed = $currentMeter - $initialPreviousMeter;
                    }
                }

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
                        'water_meter_reading' => $currentMeter,
                        'water_used' => $waterUsed,
                        'temp_min' => $existingRecord?->temp_min,
                        'temp_max' => $existingRecord?->temp_max,
                        'humidity' => $existingRecord?->humidity,
                        'dead_morning' => (int) ($existingRecord?->dead_morning ?? 0),
                        'dead_evening' => (int) ($existingRecord?->dead_evening ?? 0),
                        'cull_morning' => (int) ($existingRecord?->cull_morning ?? 0),
                        'cull_evening' => (int) ($existingRecord?->cull_evening ?? 0),
                        'avg_weight' => $existingRecord?->avg_weight,
                        'medicine_note' => $existingRecord?->medicine_note,
                        'remark' => $existingRecord?->remark,
                        'created_by' => $existingRecord?->created_by ?: $userId,
                        'updated_by' => $userId,
                    ],
                );
            }

            if ($waterErrors !== []) {
                throw ValidationException::withMessages($waterErrors);
            }

            foreach ($houseIds as $houseId) {
                $this->recalculateWaterUsage($flock->farm_id, $houseId);
            }

            $errors = $this->waterMeterErrors($flock->farm_id, $houseIds, $recordDate);

            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        });

        return redirect()
            ->route('flocks.water-meters.index', [
                'flock' => $flock,
                'date' => $recordDate,
            ])
            ->with('status', 'บันทึกมิเตอร์น้ำรายวันเรียบร้อยแล้ว');
    }

    private function ageDay(Flock $flock, string $recordDate): int
    {
        return (int) max(1, CarbonImmutable::parse($flock->start_date)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }

    private function defaultRecordDate(Flock $flock): string
    {
        $latestRecordDate = DailyHouseRecord::query()
            ->join('flocks', 'flocks.id', '=', 'daily_house_records.flock_id')
            ->where('flocks.farm_id', $flock->farm_id)
            ->whereNotNull('daily_house_records.water_meter_reading')
            ->max('daily_house_records.record_date');

        if ($latestRecordDate) {
            return CarbonImmutable::parse($latestRecordDate)->addDay()->toDateString();
        }

        return CarbonImmutable::parse($flock->start_date)->toDateString();
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;

        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }

    private function previousWaterMeterRecord(int $farmId, int $houseId, string $recordDate): ?DailyHouseRecord
    {
        return DailyHouseRecord::query()
            ->join('flocks', 'flocks.id', '=', 'daily_house_records.flock_id')
            ->where('flocks.farm_id', $farmId)
            ->where('daily_house_records.house_id', $houseId)
            ->whereDate('daily_house_records.record_date', '<', $recordDate)
            ->whereNotNull('daily_house_records.water_meter_reading')
            ->orderByDesc('daily_house_records.record_date')
            ->orderByDesc('daily_house_records.id')
            ->first('daily_house_records.*');
    }

    private function recalculateWaterUsage(int $farmId, int $houseId): void
    {
        $records = DailyHouseRecord::query()
            ->join('flocks', 'flocks.id', '=', 'daily_house_records.flock_id')
            ->where('flocks.farm_id', $farmId)
            ->where('daily_house_records.house_id', $houseId)
            ->whereNotNull('daily_house_records.water_meter_reading')
            ->orderBy('daily_house_records.record_date')
            ->orderBy('daily_house_records.id')
            ->get('daily_house_records.*');
        $previousMeter = null;

        foreach ($records as $record) {
            $currentMeter = (int) $record->water_meter_reading;
            $record->water_used = $previousMeter === null ? (int) $record->water_used : max(0, $currentMeter - $previousMeter);
            $record->save();
            $previousMeter = $currentMeter;
        }
    }

    private function waterMeterErrors(int $farmId, array $houseIds, string $submittedDate): array
    {
        $errors = [];
        $recordsByHouse = DailyHouseRecord::query()
            ->join('flocks', 'flocks.id', '=', 'daily_house_records.flock_id')
            ->where('flocks.farm_id', $farmId)
            ->whereIn('daily_house_records.house_id', $houseIds)
            ->whereNotNull('water_meter_reading')
            ->orderBy('daily_house_records.house_id')
            ->orderBy('daily_house_records.record_date')
            ->orderBy('daily_house_records.id')
            ->get('daily_house_records.*')
            ->groupBy('house_id');

        foreach ($recordsByHouse as $houseId => $records) {
            $previousMeter = null;
            $previousDate = null;

            foreach ($records as $record) {
                $currentMeter = (int) $record->water_meter_reading;

                if ($previousMeter !== null && $currentMeter < $previousMeter) {
                    $date = $record->record_date->toDateString();
                    $field = $date === $submittedDate ? "meters.$houseId" : "meters.$date.$houseId";
                    $errors[$field] = "เลขมิเตอร์ต้องไม่น้อยกว่าวันก่อนหน้า ({$previousDate})";
                }

                $previousMeter = $currentMeter;
                $previousDate = $record->record_date->toDateString();
            }
        }

        return $errors;
    }
}
