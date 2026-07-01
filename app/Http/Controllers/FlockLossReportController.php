<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\Flock;
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

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'total'); // total, dead, cull

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

        // Get all records for this flock
        $records = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->when($startDate, fn ($query) => $query->whereDate('record_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('record_date', '<=', $endDate))
            ->orderBy('record_date')
            ->get();

        // Group records by record_date string
        $recordsByDate = $records->groupBy(fn ($r) => $r->record_date->toDateString());

        // We want a list of unique dates
        $dates = $recordsByDate->keys()->sort()->values();

        // Build report matrix
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
                
                if ($houseRecord) {
                    $dead = (int)$houseRecord->dead_morning + (int)$houseRecord->dead_evening;
                    $cull = (int)$houseRecord->cull_morning + (int)$houseRecord->cull_evening;
                    
                    if ($type === 'dead') {
                        $val = $dead;
                    } elseif ($type === 'cull') {
                        $val = $cull;
                    } else {
                        $val = $dead + $cull;
                    }
                    
                    $age = $this->ageDayForHouse($flock, $start, $dateStr);
                }

                $row['houses'][$start->house_id] = [
                    'value' => $val,
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
        ]);
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;
        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }
}
