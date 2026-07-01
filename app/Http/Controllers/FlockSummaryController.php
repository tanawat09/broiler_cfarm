<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\FeedReceiptHouseItem;
use App\Models\Flock;
use App\Services\PoultryCalculationService;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FlockSummaryController extends Controller
{
    public function __invoke(Request $request, Flock $flock, PoultryCalculationService $calculator): View
    {
        $user = $request->user();

        FarmAccess::ensureFlock($user, $flock);

        $flock->load(['farm', 'flockHouseStarts.house', 'flockHousePlacements.house']);

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();
        $farmIdsWithFlocks = $flockOptions
            ->pluck('farm_id')
            ->push($flock->farm_id)
            ->unique()
            ->values();
        $farms = FarmAccess::farmsQuery($user)
            ->whereIn('id', $farmIdsWithFlocks)
            ->orderBy('farm_name')
            ->get();
        $summarySelectorFlocks = $flockOptions
            ->map(fn (Flock $option) => [
                'value' => (string) $option->id,
                'text' => $option->flock_code.($option->farm ? ' - '.$option->farm->farm_name : ''),
                'farmId' => (string) $option->farm_id,
                'summaryUrl' => route('flocks.summary', $option),
                'selected' => (int) $option->id === (int) $flock->id,
            ])
            ->values();
        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();
        $selectedHouseId = $request->integer('house_id') ?: (int) optional($availableStarts->first())->house_id;

        $recordsQuery = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->when($selectedHouseId, fn ($query) => $query->where('house_id', $selectedHouseId))
            ->when($startDate, fn ($query) => $query->whereDate('record_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('record_date', '<=', $endDate))
            ->orderBy('record_date');

        $records = $recordsQuery->get();
        $allRecordsUntilEnd = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->when($selectedHouseId, fn ($query) => $query->where('house_id', $selectedHouseId))
            ->when($endDate, fn ($query) => $query->whereDate('record_date', '<=', $endDate))
            ->orderBy('record_date')
            ->get();

        $feedReceipts = FeedReceiptHouseItem::query()
            ->whereIn('house_id', $selectedHouseId ? [$selectedHouseId] : [])
            ->whereHas('feedReceipt', function ($query) use ($flock, $startDate, $endDate) {
                $query->where('farm_id', $flock->farm_id)
                    ->when($startDate, fn ($query) => $query->whereDate('receipt_date', '>=', $startDate))
                    ->when($endDate, fn ($query) => $query->whereDate('receipt_date', '<=', $endDate));
            })
            ->with('feedReceipt')
            ->get()
            ->groupBy(fn (FeedReceiptHouseItem $item) => $item->house_id.'|'.$item->feedReceipt->receipt_date->toDateString());

        $houseDailyTables = $this->buildHouseDailyTables($records, $allRecordsUntilEnd, $feedReceipts, $flock, $calculator, $selectedHouseId);

        return view('summaries.daily', [
            'flock' => $flock,
            'houseDailyTables' => $houseDailyTables,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'summarySelectorFlocks' => $summarySelectorFlocks,
            'availableStarts' => $availableStarts,
            'selectedHouseId' => $selectedHouseId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    private function buildHouseDailyTables(Collection $filteredRecords, Collection $allRecordsUntilEnd, Collection $feedReceipts, Flock $flock, PoultryCalculationService $calculator, ?int $selectedHouseId): Collection
    {
        $filteredByHouse = $filteredRecords->groupBy('house_id');
        $allByHouse = $allRecordsUntilEnd->groupBy('house_id');

        $feedIntakes = \App\Models\FeedIntakeMaster::query()->get()->keyBy('age');
        $maxAge = $feedIntakes->keys()->max() ?? 56;

        return $flock->flockHouseStarts
            ->when($selectedHouseId, fn ($starts) => $starts->where('house_id', $selectedHouseId))
            ->sortBy('house.house_no')
            ->map(function ($start) use ($filteredByHouse, $allByHouse, $feedReceipts, $flock, $calculator, $feedIntakes, $maxAge) {
                $initialBirds = (int) $start->initial_birds;
                $placement = $flock->flockHousePlacements->firstWhere('house_id', $start->house_id);
                $cumulativeRecords = $allByHouse->get($start->house_id, collect())
                    ->groupBy(fn (DailyHouseRecord $record) => $record->record_date->toDateString())
                    ->sortKeys();
                $runningLoss = 0;
                $runningFeedUsed = 0.0;
                $latestAvgWeight = null;
                $snapshots = [];

                foreach ($cumulativeRecords as $date => $records) {
                    $record = $records->first();
                    $runningLoss += $calculator->calculateLossTotal(
                        (int) $record->dead_morning,
                        (int) $record->dead_evening,
                        (int) $record->cull_morning,
                        (int) $record->cull_evening,
                    );
                    
                    $ageDay = $this->ageDayForHouse($flock, $start, $date);
                    $lookupAge = min($ageDay, $maxAge);
                    $standard = $feedIntakes->get($lookupAge);
                    $standardIntake = 0.0;
                    if ($standard) {
                        $sex = $placement?->sex;
                        if ($sex === 'ผู้') {
                            $standardIntake = (float) $standard->feed_male;
                        } elseif ($sex === 'เมีย') {
                            $standardIntake = (float) $standard->feed_female;
                        } else {
                            $standardIntake = (float) $standard->feed_ah;
                        }
                    }

                    $remainingBirds = $calculator->calculateRemainingBirds($initialBirds, $runningLoss);
                    $calculatedFeedUsed = ($standardIntake * $remainingBirds) / 1000;
                    $runningFeedUsed += $calculatedFeedUsed;

                    if ($record->avg_weight !== null) {
                        $latestAvgWeight = (float) $record->avg_weight;
                    }

                    $snapshots[$date] = [
                        'cumulative_loss' => $runningLoss,
                        'remaining_birds' => $remainingBirds,
                        'mortality_rate' => $calculator->calculateMortalityRate($initialBirds, $runningLoss),
                        'latest_avg_weight' => $latestAvgWeight,
                        'fcr' => $calculator->calculateFcr($runningFeedUsed, $remainingBirds, $latestAvgWeight),
                        'calculated_feed_used' => $calculatedFeedUsed,
                    ];
                }

                $rows = $filteredByHouse->get($start->house_id, collect())
                    ->sortBy('record_date')
                    ->map(function (DailyHouseRecord $record) use ($start, $flock, $calculator, $snapshots, $feedReceipts) {
                        $date = $record->record_date->toDateString();
                        $deadTotal = $calculator->calculateDeadTotal((int) $record->dead_morning, (int) $record->dead_evening);
                        $cullTotal = $calculator->calculateCullTotal((int) $record->cull_morning, (int) $record->cull_evening);
                        $receiptsForDate = $feedReceipts->get($start->house_id.'|'.$date, collect());
                        $feedIn = (float) $receiptsForDate->sum(fn (FeedReceiptHouseItem $item) => (float) $item->quantity_kg);
                        $feedCodes = $receiptsForDate->map(fn (FeedReceiptHouseItem $item) => $item->feedReceipt->feed_code)->filter()->unique()->implode(',');
                        $feedLots = $receiptsForDate->map(fn (FeedReceiptHouseItem $item) => $item->feedReceipt->production_lot)->filter()->unique()->implode(',');

                        return [
                            'date' => $date,
                            'age_day' => $this->ageDayForHouse($flock, $start, $date),
                            'week_no' => (int) ceil($this->ageDayForHouse($flock, $start, $date) / 7),
                            'feed_code' => $feedCodes ?: $record->feed_code,
                            'feed_lots' => $feedLots,
                            'feed_in' => $feedIn > 0 ? $feedIn : (float) $record->feed_in,
                            'feed_used' => $snapshots[$date]['calculated_feed_used'] ?? 0.0,
                            'water_used' => (int) $record->water_used,
                            'temp_min' => $record->temp_min,
                            'temp_max' => $record->temp_max,
                            'humidity' => $record->humidity,
                            'dead_morning' => (int) $record->dead_morning,
                            'dead_evening' => (int) $record->dead_evening,
                            'cull_morning' => (int) $record->cull_morning,
                            'cull_evening' => (int) $record->cull_evening,
                            'dead_total' => $deadTotal,
                            'cull_total' => $cullTotal,
                            'loss_total' => $deadTotal + $cullTotal,
                            'medicine_note' => $record->medicine_note,
                            'remark' => $record->remark,
                            'avg_weight' => $record->avg_weight !== null ? (float) $record->avg_weight : null,
                            ...($snapshots[$date] ?? [
                                'cumulative_loss' => 0,
                                'remaining_birds' => $start->initial_birds,
                                'mortality_rate' => 0,
                                'latest_avg_weight' => null,
                                'fcr' => null,
                            ]),
                        ];
                    })
                    ->values();

                return [
                    'house' => $start->house,
                    'start_date' => $start->start_date ?: $flock->start_date,
                    'initial_birds' => $initialBirds,
                    'placement' => $placement,
                    'rows' => $rows,
                    'weekly_summaries' => $this->weeklySummaries($rows),
                ];
            })
            ->values();
    }

    private function weeklySummaries(Collection $rows): Collection
    {
        $runningFeedIn = 0;
        $runningFeedUsed = 0.0;
        $runningWater = 0;
        $runningDeadMorning = 0;
        $runningDeadEvening = 0;
        $runningCullMorning = 0;
        $runningCullEvening = 0;
        $runningLossTotal = 0;

        return $rows
            ->groupBy('week_no')
            ->map(function (Collection $weekRows, int $weekNo) use (
                &$runningFeedIn, &$runningFeedUsed, &$runningWater,
                &$runningDeadMorning, &$runningDeadEvening, &$runningCullMorning, &$runningCullEvening, &$runningLossTotal
            ) {
                // Weekly sums
                $weekFeedIn = $weekRows->sum('feed_in');
                $weekFeedUsed = $weekRows->sum('feed_used');
                $weekWater = $weekRows->sum('water_used');
                $weekDeadMorning = $weekRows->sum('dead_morning');
                $weekDeadEvening = $weekRows->sum('dead_evening');
                $weekCullMorning = $weekRows->sum('cull_morning');
                $weekCullEvening = $weekRows->sum('cull_evening');
                $weekLossTotal = $weekRows->sum('loss_total');

                // Accumulate
                $runningFeedIn += $weekFeedIn;
                $runningFeedUsed += $weekFeedUsed;
                $runningWater += $weekWater;
                $runningDeadMorning += $weekDeadMorning;
                $runningDeadEvening += $weekDeadEvening;
                $runningCullMorning += $weekCullMorning;
                $runningCullEvening += $weekCullEvening;
                $runningLossTotal += $weekLossTotal;

                return [
                    'week_no' => $weekNo,
                    
                    // Weekly totals
                    'feed_in' => $weekFeedIn,
                    'feed_used' => $weekFeedUsed,
                    'water_used' => $weekWater,
                    'dead_morning' => $weekDeadMorning,
                    'dead_evening' => $weekDeadEvening,
                    'cull_morning' => $weekCullMorning,
                    'cull_evening' => $weekCullEvening,
                    'dead_total' => $weekRows->sum('dead_total'),
                    'cull_total' => $weekRows->sum('cull_total'),
                    'loss_total' => $weekLossTotal,
                    'remaining_birds' => $weekRows->last()['remaining_birds'] ?? null,
                    'fcr' => $weekRows->last()['fcr'] ?? null,

                    // Cumulative totals
                    'cum_feed_in' => $runningFeedIn,
                    'cum_feed_used' => $runningFeedUsed,
                    'cum_water_used' => $runningWater,
                    'cum_dead_morning' => $runningDeadMorning,
                    'cum_dead_evening' => $runningDeadEvening,
                    'cum_cull_morning' => $runningCullMorning,
                    'cum_cull_evening' => $runningCullEvening,
                    'cum_loss_total' => $runningLossTotal,
                ];
            });
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;

        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }
}
