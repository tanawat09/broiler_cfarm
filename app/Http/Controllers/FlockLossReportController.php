<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\Flock;
use App\Models\FeedReceiptHouseItem;
use App\Models\FlockSaleRecord;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlockLossReportController extends Controller
{
    public function __invoke(Request $request, Flock $flock): View
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);

        $flock->load(['farm', 'flockHouseStarts.house']);

        // Determine tabs
        $tab = $request->query('tab', 'summary');

        // Gathers selector lists
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

        $lossSelectorFlocks = $flockOptions
            ->map(fn (Flock $option) => [
                'value' => (string) $option->id,
                'text' => $option->flock_code.($option->farm ? ' - '.$option->farm->farm_name : ''),
                'farmId' => (string) $option->farm_id,
                'url' => route('flocks.losses', $option),
                'selected' => (int) $option->id === (int) $flock->id,
            ])
            ->values();

        $houses = $flock->flockHouseStarts->sortBy('house.house_no')->values();

        // 1. Determine selected_date for summary tab (default to max daily record date, or today)
        $selectedDateStr = $request->query('selected_date');
        if ($selectedDateStr) {
            $selectedDate = CarbonImmutable::parse($selectedDateStr);
        } else {
            $maxRecordDate = DailyHouseRecord::where('flock_id', $flock->id)->max('record_date');
            $selectedDate = $maxRecordDate ? CarbonImmutable::parse($maxRecordDate) : CarbonImmutable::today();
            $selectedDateStr = $selectedDate->toDateString();
        }

        $placementsByHouse = $flock->flockHousePlacements->groupBy('house_id');

        // 2. Summary Tab Calculations
        $summaryRows = [];
        $summaryTotals = [
            'initial_birds' => 0,
            'loss_7' => 0,
            'loss_14' => 0,
            'loss_21' => 0,
            'loss_28' => 0,
            'loss_35' => 0,
            'loss_42' => 0,
            'loss_49' => 0,
            'loss_cumulative' => 0,
            'remaining' => 0,
            'feed_used' => 0.0,
            'feed_received' => 0.0,
        ];

        foreach ($houses as $start) {
            $housePlacements = $placementsByHouse->get($start->house_id) ?: collect();
            $earliestPlacement = $housePlacements->sortBy('placement_date')->first();
            $houseStartDate = $earliestPlacement?->placement_date ?: ($start->start_date ?: $flock->start_date);
            $initialBirds = (int) $start->initial_birds;

            // Get records for this house up to selected date
            $records = DailyHouseRecord::where('flock_id', $flock->id)
                ->where('house_id', $start->house_id)
                ->whereDate('record_date', '<=', $selectedDate)
                ->get();

            // Age calculation
            $age = 0;
            if ($selectedDate >= $houseStartDate) {
                $age = CarbonImmutable::parse($houseStartDate)->diffInDays($selectedDate) + 1;
            }

            // Sum losses in weeks
            $loss_7 = $records->where('age_day', '<=', 7)->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
            $loss_14 = $records->whereBetween('age_day', [8, 14])->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
            $loss_21 = $records->whereBetween('age_day', [15, 21])->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
            $loss_28 = $records->whereBetween('age_day', [22, 28])->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
            $loss_35 = $records->whereBetween('age_day', [29, 35])->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
            $loss_42 = $records->whereBetween('age_day', [36, 42])->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
            $loss_49 = $records->whereBetween('age_day', [43, 49])->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);

            $loss_cumulative = $records->sum(fn($r) => $r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);

            // Feed consumed
            $feedUsed = (float) $records->sum('feed_used');

            // Feed received
            $feedReceived = (float) FeedReceiptHouseItem::where('house_id', $start->house_id)
                ->whereHas('feedReceipt', function ($query) use ($flock, $selectedDate) {
                    $query->where('farm_id', $flock->farm_id)
                          ->whereDate('receipt_date', '>=', $flock->start_date)
                          ->whereDate('receipt_date', '<=', $selectedDate);
                })
                ->sum('quantity_kg');

            // Sold
            $sales = FlockSaleRecord::where('flock_id', $flock->id)
                ->where('house_id', $start->house_id)
                ->whereDate('sale_date', '<=', $selectedDate)
                ->sum('birds_sold');

            $remaining = $initialBirds - $loss_cumulative - $sales;

            $summaryRows[] = [
                'house' => $start->house,
                'initial_birds' => $initialBirds,
                'loss_7' => $loss_7,
                'loss_14' => $loss_14,
                'loss_21' => $loss_21,
                'loss_28' => $loss_28,
                'loss_35' => $loss_35,
                'loss_42' => $loss_42,
                'loss_49' => $loss_49,
                'loss_cumulative' => $loss_cumulative,
                'age' => $age,
                'remaining' => $remaining,
                'feed_used' => $feedUsed,
                'feed_received' => $feedReceived,
            ];

            // Accumulate to totals
            $summaryTotals['initial_birds'] += $initialBirds;
            $summaryTotals['loss_7'] += $loss_7;
            $summaryTotals['loss_14'] += $loss_14;
            $summaryTotals['loss_21'] += $loss_21;
            $summaryTotals['loss_28'] += $loss_28;
            $summaryTotals['loss_35'] += $loss_35;
            $summaryTotals['loss_42'] += $loss_42;
            $summaryTotals['loss_49'] += $loss_49;
            $summaryTotals['loss_cumulative'] += $loss_cumulative;
            $summaryTotals['remaining'] += $remaining;
            $summaryTotals['feed_used'] += $feedUsed;
            $summaryTotals['feed_received'] += $feedReceived;
        }

        // 3. Daily Tab Calculations
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'total'); // total, dead, cull

        $records = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->when($startDate, fn ($query) => $query->whereDate('record_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('record_date', '<=', $endDate))
            ->orderBy('record_date')
            ->get();

        $recordsByDate = $records->groupBy(fn ($r) => $r->record_date->toDateString());
        $dates = $recordsByDate->keys()->sort()->values();

        $matrix = [];
        $totalsByHouse = [];
        foreach ($houses as $start) {
            $totalsByHouse[$start->house_id] = 0;
        }
        $grandTotal = 0;

        foreach ($dates as $dateStr) {
            $row = [
                'date' => $dateStr,
                'houses' => []
            ];
            $rowTotal = 0;

            foreach ($houses as $start) {
                $houseRecord = $recordsByDate->get($dateStr)?->firstWhere('house_id', $start->house_id);
                $val = 0;
                $age = '-';
                
                $totalLoss = 0;
                if ($houseRecord) {
                    $dead = (int)$houseRecord->dead_morning + (int)$houseRecord->dead_evening;
                    $cull = (int)$houseRecord->cull_morning + (int)$houseRecord->cull_evening;
                    $totalLoss = $dead + $cull;
                    
                    if ($type === 'dead') {
                        $val = $dead;
                    } elseif ($type === 'cull') {
                        $val = $cull;
                    } else {
                        $val = $totalLoss;
                    }
                    
                    $age = $this->ageDayForHouse($flock, $start, $dateStr);
                }

                $row['houses'][$start->house_id] = [
                    'value' => $val,
                    'total_loss' => $totalLoss,
                    'initial_birds' => (int)$start->initial_birds,
                    'age' => $age,
                    'has_record' => !empty($houseRecord)
                ];

                $totalsByHouse[$start->house_id] += $val;
                $rowTotal += $val;
            }

            $row['total'] = $rowTotal;
            $grandTotal += $rowTotal;
            $matrix[] = $row;
        }

        return view('summaries.losses', [
            'flock' => $flock,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'lossSelectorFlocks' => $lossSelectorFlocks,
            'houses' => $houses,
            'matrix' => $matrix,
            'totalsByHouse' => $totalsByHouse,
            'grandTotal' => $grandTotal,
            'selectedType' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tab' => $tab,
            'selectedDateStr' => $selectedDateStr,
            'summaryRows' => $summaryRows,
            'summaryTotals' => $summaryTotals,
        ]);
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $earliestPlacement = $flock->flockHousePlacements
            ->where('house_id', $start->house_id)
            ->sortBy('placement_date')
            ->first();
        
        $houseStartDate = $earliestPlacement?->placement_date ?: ($start?->start_date ?: $flock->start_date);
        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }
}
