<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyRecordRequest;
use App\Models\DailyHouseRecord;
use App\Models\Flock;
use App\Services\PoultryCalculationService;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DailyHouseRecordController extends Controller
{
    public function index(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        $date = $request->query('date', now()->toDateString());

        return redirect()->route('flocks.daily-records.show', [$flock, $date]);
    }

    public function show(Request $request, Flock $flock, string $date): View
    {
        $user = $request->user();

        FarmAccess::ensureFlock($user, $flock);

        $recordDate = CarbonImmutable::parse($date)->toDateString();
        $flock->load(['farm', 'flockHouseStarts.house']);
        $farms = FarmAccess::farmsQuery($user)
            ->orderBy('farm_name')
            ->get();
        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();
        $starts = $flock->flockHouseStarts->sortBy('house.house_no')->values();
        $ageByHouse = $starts->mapWithKeys(fn ($start) => [
            $start->house_id => $this->ageDayForHouse($flock, $start, $recordDate),
        ]);
        $recordsByHouse = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->whereDate('record_date', $recordDate)
            ->get()
            ->keyBy('house_id');

        $ageDay = (int) max(1, $flock->start_date->diffInDays(CarbonImmutable::parse($recordDate)) + 1);

        return view('daily-records.show', [
            'flock' => $flock,
            'recordDate' => $recordDate,
            'ageDay' => $ageDay,
            'ageByHouse' => $ageByHouse,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'starts' => $starts,
            'recordsByHouse' => $recordsByHouse,
        ]);
    }

    public function store(StoreDailyRecordRequest $request, Flock $flock, PoultryCalculationService $calculator): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validated();
        $recordDate = CarbonImmutable::parse($validated['record_date'])->toDateString();
        $starts = $flock->flockHouseStarts()->with('house')->get();
        $allowedHouseIds = $starts->pluck('house_id')->map(fn ($id) => (int) $id)->all();
        $rows = collect($validated['records'] ?? []);
        $userId = $request->user()?->id;
        $waterErrors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $houseId => $row) {
                $houseId = (int) $houseId;

                if (! in_array($houseId, $allowedHouseIds, true)) {
                    continue;
                }

                $existingRecord = DailyHouseRecord::query()
                    ->where('flock_id', $flock->id)
                    ->where('house_id', $houseId)
                    ->whereDate('record_date', $recordDate)
                    ->first();
                $currentMeter = array_key_exists('water_meter_reading', $row)
                    ? $this->nullableInteger($row['water_meter_reading'])
                    : $existingRecord?->water_meter_reading;

                try {
                    $waterUsed = array_key_exists('water_meter_reading', $row)
                        ? $calculator->calculateWaterUsed($flock->id, $houseId, $recordDate, $currentMeter)
                        : (int) ($existingRecord?->water_used ?? 0);
                } catch (\InvalidArgumentException $exception) {
                    $waterErrors["records.$houseId.water_meter_reading"] = $exception->getMessage();
                    continue;
                }

                DailyHouseRecord::query()->updateOrCreate(
                    [
                        'flock_id' => $flock->id,
                        'house_id' => $houseId,
                        'record_date' => $recordDate,
                    ],
                    [
                        'age_day' => $this->ageDayForHouse($flock, $starts->firstWhere('house_id', $houseId), $recordDate),
                        'feed_code' => array_key_exists('feed_code', $row) ? $this->nullableString($row['feed_code']) : $existingRecord?->feed_code,
                        'feed_in' => array_key_exists('feed_in', $row) ? $this->decimalOrZero($row['feed_in']) : (float) ($existingRecord?->feed_in ?? 0),
                        'feed_used' => array_key_exists('feed_used', $row) ? $this->decimalOrZero($row['feed_used']) : (float) ($existingRecord?->feed_used ?? 0),
                        'water_meter_reading' => $currentMeter,
                        'water_used' => $waterUsed,
                        'temp_min' => $this->nullableDecimal($row['temp_min'] ?? null),
                        'temp_max' => $this->nullableDecimal($row['temp_max'] ?? null),
                        'humidity' => $this->nullableDecimal($row['humidity'] ?? null),
                        'dead_morning' => $this->integerOrZero($row['dead_morning'] ?? null),
                        'dead_evening' => $this->integerOrZero($row['dead_evening'] ?? null),
                        'cull_morning' => $this->integerOrZero($row['cull_morning'] ?? null),
                        'cull_evening' => $this->integerOrZero($row['cull_evening'] ?? null),
                        'avg_weight' => array_key_exists('avg_weight', $row) ? $this->nullableDecimal($row['avg_weight']) : $existingRecord?->avg_weight,
                        'medicine_note' => array_key_exists('medicine_note', $row) ? $this->nullableString($row['medicine_note']) : $existingRecord?->medicine_note,
                        'remark' => array_key_exists('remark', $row) ? $this->nullableString($row['remark']) : $existingRecord?->remark,
                        'created_by' => $existingRecord?->created_by ?: $userId,
                        'updated_by' => $userId,
                    ],
                );
            }

            if ($waterErrors !== []) {
                DB::rollBack();
                throw ValidationException::withMessages($waterErrors);
            }

            DB::commit();
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return redirect()
            ->route('flocks.daily-records.show', [$flock, $recordDate])
            ->with('status', 'บันทึกข้อมูลประจำวันเรียบร้อยแล้ว');
    }

    private function nullableString(mixed $value): ?string
    {
        return $value === null || $value === '' ? null : (string) $value;
    }

    private function nullableInteger(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    private function integerOrZero(mixed $value): int
    {
        return $value === null || $value === '' ? 0 : (int) $value;
    }

    private function decimalOrZero(mixed $value): float
    {
        return $value === null || $value === '' ? 0.0 : (float) $value;
    }

    private function nullableDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;

        return (int) max(1, \Carbon\CarbonImmutable::parse($houseStartDate)->diffInDays(\Carbon\CarbonImmutable::parse($recordDate)) + 1);
    }
}
