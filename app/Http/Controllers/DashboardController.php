<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Flock;
use App\Models\House;
use App\Models\DailyHouseRecord;
use App\Models\FlockSaleRecord;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $databaseReady = true;

        try {
            $farmIds = FarmAccess::farmsQuery($user)->pluck('id');
            $activeFlocksCount = Flock::query()->whereIn('farm_id', $farmIds)->where('status', 'active')->count();
            $housesCount = House::query()->whereIn('farm_id', $farmIds)->count();

            // Selectable farms and flocks
            $farms = FarmAccess::farmsQuery($user)->orderBy('farm_name')->get();
            $flockOptions = FarmAccess::flocksQuery($user)
                ->with('farm')
                ->orderBy('farm_id')
                ->latest('start_date')
                ->latest('id')
                ->get();

            // Find current flock (active or latest)
            $flockId = $request->integer('flock_id');
            $selectedHouseId = $request->integer('house_id'); // 0 or null = all houses

            if ($flockId) {
                $flock = Flock::find($flockId);
                if ($flock) {
                    FarmAccess::ensureFlock($user, $flock);
                }
            }

            if (!isset($flock) || !$flock) {
                $flock = FarmAccess::activeFlockFor($user);
            }
            if (!$flock) {
                $flock = FarmAccess::flocksQuery($user)->latest('id')->first();
            }

            if (!$flock) {
                return view('dashboard', [
                    'databaseReady' => $databaseReady,
                    'flock' => null,
                    'stats' => [
                        'farms' => $farmIds->count(),
                        'activeFlocks' => $activeFlocksCount,
                        'houses' => $housesCount,
                    ]
                ]);
            }

            $flock->load(['farm', 'flockHouseStarts.house', 'flockHousePlacements.house']);

            // Available houses for selection
            $availableHouses = $flock->flockHouseStarts->sortBy('house.house_no')->map(fn($start) => $start->house);

            // Get initial birds (filtered by selected house if any)
            $filteredStarts = $flock->flockHouseStarts
                ->when($selectedHouseId, fn($query) => $query->where('house_id', $selectedHouseId));
            $initialBirds = (int) $filteredStarts->sum('initial_birds');

            // Load feed intake master table
            $feedIntakes = \App\Models\FeedIntakeMaster::query()->get()->keyBy('age');
            $maxAge = $feedIntakes->keys()->max() ?? 56;

            $housesData = [];
            
            // Loop through each house start to calculate daily feed consumption dynamically
            foreach ($flock->flockHouseStarts as $start) {
                $houseId = $start->house_id;
                
                // If a specific house is selected, skip other houses in calculations
                if ($selectedHouseId && $houseId !== $selectedHouseId) {
                    continue;
                }
                
                $houseInitialBirds = (int) $start->initial_birds;
                $placement = $flock->flockHousePlacements->firstWhere('house_id', $houseId);
                
                $records = DailyHouseRecord::where('flock_id', $flock->id)
                    ->where('house_id', $houseId)
                    ->orderBy('record_date')
                    ->get();
                
                $runningLoss = 0;
                
                foreach ($records as $r) {
                    $date = $r->record_date->toDateString();
                    $deadTotal = (int) ($r->dead_morning + $r->dead_evening);
                    $cullTotal = (int) ($r->cull_morning + $r->cull_evening);
                    $runningLoss += ($deadTotal + $cullTotal);
                    
                    $remaining = max(0, $houseInitialBirds - $runningLoss);
                    
                    $ageDay = $r->age_day;
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
                    
                    $calculatedFeed = ($standardIntake * $remaining) / 1000;
                    
                    $housesData[$date][$houseId] = [
                        'feed_used' => $calculatedFeed,
                        'remaining_birds' => $remaining,
                    ];
                }
            }

            // Daily aggregates for the flock
            $dailyAggregates = DailyHouseRecord::where('flock_id', $flock->id)
                ->when($selectedHouseId, fn($query) => $query->where('house_id', $selectedHouseId))
                ->selectRaw('
                    record_date,
                    MAX(age_day) as age_day,
                    SUM(water_used) as water_used,
                    AVG(temp_min) as temp_min,
                    AVG(temp_max) as temp_max,
                    AVG(humidity) as humidity,
                    SUM(dead_morning) as dead_morning,
                    SUM(dead_evening) as dead_evening,
                    SUM(cull_morning) as cull_morning,
                    SUM(cull_evening) as cull_evening,
                    MAX(avg_weight) as avg_weight
                ')
                ->groupBy('record_date')
                ->orderBy('record_date')
                ->get();

            // Total sales birds
            $totalBirdsSold = (int) FlockSaleRecord::where('flock_id', $flock->id)
                ->when($selectedHouseId, fn($query) => $query->where('house_id', $selectedHouseId))
                ->sum('birds_sold');

            // Total feed consumed
            $totalFeedUsed = 0.0;
            // Total water consumed
            $totalWaterUsed = 0;
            // Total dead/culls
            $totalLoss = 0;

            // Generate running totals for chart
            $chartData = [];
            $runningFeedUsed = 0.0;
            $latestWeight = null;
            $remainingBirds = $initialBirds;

            foreach ($dailyAggregates as $r) {
                $dateStr = $r->record_date->toDateString();
                
                $dateFeed = 0.0;
                $dateRemaining = 0;
                if (isset($housesData[$dateStr])) {
                    foreach ($housesData[$dateStr] as $hId => $hVal) {
                        $dateFeed += $hVal['feed_used'];
                        $dateRemaining += $hVal['remaining_birds'];
                    }
                } else {
                    $dateRemaining = $remainingBirds;
                }
                
                $runningFeedUsed += $dateFeed;
                $totalFeedUsed += $dateFeed;
                $totalWaterUsed += (int) $r->water_used;
                
                $dayLoss = (int) ($r->dead_morning + $r->dead_evening + $r->cull_morning + $r->cull_evening);
                $totalLoss += $dayLoss;
                $remainingBirds = $dateRemaining;

                if ($r->avg_weight !== null) {
                    $latestWeight = (float) $r->avg_weight;
                }

                $chartData[] = [
                    'date' => $dateStr,
                    'thai_date' => thai_date($dateStr),
                    'age_day' => (int) $r->age_day,
                    'feed_used' => $dateFeed,
                    'cum_feed' => $runningFeedUsed,
                    'water_used' => (int) $r->water_used,
                    'temp_min' => $r->temp_min !== null ? round((float) $r->temp_min, 1) : null,
                    'temp_max' => $r->temp_max !== null ? round((float) $r->temp_max, 1) : null,
                    'humidity' => $r->humidity !== null ? round((float) $r->humidity, 1) : null,
                    'dead_morning' => (int) $r->dead_morning,
                    'dead_evening' => (int) $r->dead_evening,
                    'cull_morning' => (int) $r->cull_morning,
                    'cull_evening' => (int) $r->cull_evening,
                    'mortality' => $dayLoss,
                ];
            }

            $mortalityRate = $initialBirds > 0 ? ($totalLoss / $initialBirds) * 100 : 0;

            // Calculate current FCR
            $fcr = null;
            if ($latestWeight !== null && $latestWeight > 0 && $remainingBirds > 0) {
                $totalWeight = $remainingBirds * $latestWeight;
                $fcr = round($totalFeedUsed / $totalWeight, 2);
            }

            // Get current week's logs (latest 7 days with details)
            $latest7Dates = DailyHouseRecord::where('flock_id', $flock->id)
                ->when($selectedHouseId, fn($query) => $query->where('house_id', $selectedHouseId))
                ->select('record_date')
                ->groupBy('record_date')
                ->orderByDesc('record_date')
                ->take(7)
                ->pluck('record_date');

            $dailyLogs = DailyHouseRecord::where('flock_id', $flock->id)
                ->when($selectedHouseId, fn($query) => $query->where('house_id', $selectedHouseId))
                ->whereIn('record_date', $latest7Dates)
                ->orderBy('record_date')
                ->get()
                ->groupBy(fn($record) => $record->record_date->toDateString())
                ->map(function ($dayRecords, $date) use ($flock, $housesData, $remainingBirds) {
                    $firstRec = $dayRecords->first();
                    $feedCode = $dayRecords->pluck('feed_code')->filter()->unique()->implode(', ') ?: '-';
                    
                    $feedUsed = 0.0;
                    $remaining = 0;
                    if (isset($housesData[$date])) {
                        foreach ($housesData[$date] as $hId => $hVal) {
                            $feedUsed += $hVal['feed_used'];
                            $remaining += $hVal['remaining_birds'];
                        }
                    } else {
                        $remaining = $remainingBirds;
                    }
                    
                    $tempMin = $dayRecords->avg('temp_min');
                    $tempMax = $dayRecords->avg('temp_max');
                    
                    $medNote = $dayRecords->pluck('medicine_note')->filter()->unique()->implode(', ');

                    return [
                        'date' => $date,
                        'thai_date' => thai_date($date),
                        'age_day' => $firstRec->age_day,
                        'feed_code' => $feedCode,
                        'feed_used' => $feedUsed,
                        'temp_range' => ($tempMin !== null && $tempMax !== null) ? round($tempMin, 1) . ' - ' . round($tempMax, 1) . ' °C' : '-',
                        'remaining_birds' => $remaining,
                        'medicine_note' => $medNote ?: null,
                    ];
                })
                ->values();

            // Weekly breakdowns
            $areaPerHouse = $flock->farm->rearing_area ?: 1200;
            if ($selectedHouseId) {
                $totalArea = $areaPerHouse;
            } else {
                $totalArea = $areaPerHouse * $flock->flockHouseStarts->count();
            }
            $birdsPerSqm = $totalArea > 0 ? round($initialBirds / $totalArea, 2) : 0;

            // Calculate Standard and Comparison metrics
            $saleRecords = FlockSaleRecord::where('flock_id', $flock->id)
                ->when($selectedHouseId, fn($query) => $query->where('house_id', $selectedHouseId))
                ->get();

            $totalBirdsSold = (int) $saleRecords->sum('birds_sold');
            $totalWeightSold = (float) $saleRecords->sum('total_weight');

            if ($saleRecords->isNotEmpty()) {
                $weightedAgeSum = 0.0;
                foreach ($saleRecords as $sr) {
                    $days = $flock->start_date->diffInDays($sr->sale_date);
                    $weightedAgeSum += $days * $sr->birds_sold;
                }
                $age = $totalBirdsSold > 0 ? $weightedAgeSum / $totalBirdsSold : (float) ($dailyAggregates->max('age_day') ?: 0);
            } else {
                $age = (float) ($dailyAggregates->max('age_day') ?: 0);
            }

            $dPrev = (int) floor($age);
            $dNext = (int) ceil($age);
            if ($dNext === $dPrev) {
                $dNext = $dPrev + 1; // standard interval is 1 day
            }
            $fraction = $age - $dPrev;

            // Load feed intake master table
            $stdPrev = $feedIntakes->get($dPrev);
            $stdNext = $feedIntakes->get($dNext) ?? $stdPrev;

            // Determine gender for lookup
            $sex = 'ah';
            $placement = $flock->flockHousePlacements->first();
            if ($placement) {
                if ($placement->sex === 'ผู้') {
                    $sex = 'male';
                } elseif ($placement->sex === 'เมีย') {
                    $sex = 'female';
                }
            }

            // Actual values
            $actualSurvival = $initialBirds > 0 ? (($remainingBirds + $totalBirdsSold) / $initialBirds) * 100 : 0.0;
            $actualFeed = $initialBirds > 0 ? ($totalFeedUsed * 1000) / $initialBirds : 0.0;
            
            $actualWeightKg = $totalBirdsSold > 0 ? $totalWeightSold / $totalBirdsSold : ($latestWeight ?: 0.0);
            $actualWeight = $actualWeightKg * 1000;

            $totalWeightProduced = ($remainingBirds * $actualWeightKg) + $totalWeightSold;
            $actualFcr = $totalWeightProduced > 0 ? $totalFeedUsed / $totalWeightProduced : 0.0;

            $actualPi = 0.0;
            if ($actualFcr > 0 && $age > 0) {
                $actualPi = ($actualSurvival * $actualWeightKg) / ($age * $actualFcr) * 100;
            }

            // STD values (Prev, Next, Delta, Interpolated)
            $interpolate = function ($valPrev, $valNext, $frac, $isPi = false) {
                if ($valPrev === null || $valNext === null) {
                    return 0.0;
                }
                if ($isPi) {
                    return $valPrev + (($valPrev - $valNext) * $frac);
                }
                return $valPrev + (($valNext - $valPrev) * $frac);
            };

            // Liveability
            $stdPrevSurvival = 100.0 - (float) ($stdPrev?->{'mortality_' . $sex} ?? 0.0);
            $stdNextSurvival = 100.0 - (float) ($stdNext?->{'mortality_' . $sex} ?? 0.0);
            $deltaSurvival = $stdNextSurvival - $stdPrevSurvival;
            $stdSurvival = $interpolate($stdPrevSurvival, $stdNextSurvival, $fraction);

            // Feed Intake
            $stdPrevFeed = (float) ($stdPrev?->{'cum_feed_' . $sex} ?? 0.0);
            $stdNextFeed = (float) ($stdNext?->{'cum_feed_' . $sex} ?? 0.0);
            $deltaFeed = $stdNextFeed - $stdPrevFeed;
            $stdFeed = $interpolate($stdPrevFeed, $stdNextFeed, $fraction);

            // Body Weight
            $stdPrevWeight = (float) ($stdPrev?->{'weight_' . $sex} ?? 0.0);
            $stdNextWeight = (float) ($stdNext?->{'weight_' . $sex} ?? 0.0);
            $deltaWeight = $stdNextWeight - $stdPrevWeight;
            $stdWeight = $interpolate($stdPrevWeight, $stdNextWeight, $fraction);

            // FCR
            $stdPrevFcr = (float) ($stdPrev?->{'fcr_' . $sex} ?? 0.0);
            $stdNextFcr = (float) ($stdNext?->{'fcr_' . $sex} ?? 0.0);
            $deltaFcr = $stdNextFcr - $stdPrevFcr;
            $stdFcr = $interpolate($stdPrevFcr, $stdNextFcr, $fraction);

            // PI
            $stdPrevPi = (float) ($stdPrev?->{'pi_' . $sex} ?? 0.0);
            $stdNextPi = (float) ($stdNext?->{'pi_' . $sex} ?? 0.0);
            $deltaPi = $stdPrevPi - $stdNextPi;
            $stdPi = $interpolate($stdPrevPi, $stdNextPi, $fraction, true);

            // Compute %STD percentages
            $survivalPct = $stdSurvival > 0 ? ($actualSurvival / $stdSurvival) * 100 : 0.0;
            $feedPct = $stdFeed > 0 ? ($actualFeed / $stdFeed) * 100 : 0.0;
            $weightPct = $stdWeight > 0 ? ($actualWeight / $stdWeight) * 100 : 0.0;
            $fcrPct = $actualFcr > 0 ? ($stdFcr / $actualFcr) * 100 : 0.0;
            $piPct = $stdPi > 0 ? ($actualPi / $stdPi) * 100 : 0.0;

            $comparisonTable = [
                'age' => [
                    'label' => 'อายุ (วัน)',
                    'actual' => number_format($age, 2),
                    'std_prev_day' => $dPrev,
                    'std_next_day' => $dNext,
                    'std_prev' => number_format($dPrev, 0),
                    'std_next' => number_format($dNext, 0),
                    'delta' => number_format($dNext - $dPrev, 2),
                    'fraction' => number_format($fraction, 2),
                    'std_interpolated' => number_format($age, 2),
                    'pct' => '-'
                ],
                'liveability' => [
                    'label' => 'Liveability (%)',
                    'actual' => number_format($actualSurvival, 2),
                    'std_prev' => number_format($stdPrevSurvival, 2),
                    'std_next' => number_format($stdNextSurvival, 2),
                    'delta' => number_format($deltaSurvival, 2),
                    'fraction' => number_format($fraction, 2),
                    'std_interpolated' => number_format($stdSurvival, 2),
                    'pct' => number_format($survivalPct, 2) . '%'
                ],
                'feed' => [
                    'label' => 'FI (กรัม/ตัว)',
                    'actual' => number_format($actualFeed, 0),
                    'std_prev' => number_format($stdPrevFeed, 0),
                    'std_next' => number_format($stdNextFeed, 0),
                    'delta' => number_format($deltaFeed, 2),
                    'fraction' => number_format($fraction, 2),
                    'std_interpolated' => number_format($stdFeed, 1),
                    'pct' => number_format($feedPct, 2) . '%'
                ],
                'weight' => [
                    'label' => 'BW (กรัม/ตัว)',
                    'actual' => number_format($actualWeight, 0),
                    'std_prev' => number_format($stdPrevWeight, 0),
                    'std_next' => number_format($stdNextWeight, 0),
                    'delta' => number_format($deltaWeight, 2),
                    'fraction' => number_format($fraction, 2),
                    'std_interpolated' => number_format($stdWeight, 1),
                    'pct' => number_format($weightPct, 2) . '%'
                ],
                'fcr' => [
                    'label' => 'FCR',
                    'actual' => number_format($actualFcr, 3),
                    'std_prev' => number_format($stdPrevFcr, 3),
                    'std_next' => number_format($stdNextFcr, 3),
                    'delta' => number_format($deltaFcr, 3),
                    'fraction' => number_format($fraction, 2),
                    'std_interpolated' => number_format($stdFcr, 3),
                    'pct' => number_format($fcrPct, 2) . '%'
                ],
                'pi' => [
                    'label' => 'PI',
                    'actual' => number_format($actualPi, 0),
                    'std_prev' => number_format($stdPrevPi, 0),
                    'std_next' => number_format($stdNextPi, 0),
                    'delta' => number_format($deltaPi, 2),
                    'fraction' => number_format($fraction, 2),
                    'std_interpolated' => number_format($stdPi, 1),
                    'pct' => number_format($piPct, 2) . '%'
                ],
            ];

            return view('dashboard', [
                'databaseReady' => $databaseReady,
                'stats' => [
                    'farms' => $farmIds->count(),
                    'activeFlocks' => $activeFlocksCount,
                    'houses' => $housesCount,
                ],
                'farms' => $farms,
                'flockOptions' => $flockOptions,
                'availableHouses' => $availableHouses,
                'selectedHouseId' => $selectedHouseId,
                'flock' => $flock,
                'kpis' => [
                    'remainingBirds' => $remainingBirds,
                    'mortality' => $totalLoss,
                    'mortalityRate' => $mortalityRate,
                    'survivalRate' => $actualSurvival,
                    'totalFeedConsumed' => $totalFeedUsed,
                    'fcr' => $fcr,
                ],
                'chartData' => $chartData,
                'dailyLogs' => $dailyLogs,
                'birdsPerSqm' => $birdsPerSqm,
                'comparisonTable' => $comparisonTable,
                'totalArea' => $totalArea,
            ]);

        } catch (\Throwable $e) {
            return view('dashboard', [
                'databaseReady' => false,
                'stats' => [
                    'farms' => 0,
                    'activeFlocks' => 0,
                    'houses' => 0,
                ],
                'error' => $e->getMessage()
            ]);
        }
    }
}
