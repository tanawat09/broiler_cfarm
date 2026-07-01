<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\Flock;
use App\Models\FlockSaleRecord;
use App\Services\PoultryCalculationService;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FlockCloseController extends Controller
{
    public function show(Request $request, Flock $flock, PoultryCalculationService $calculator): View
    {
        $user = $request->user();

        FarmAccess::ensureFlock($user, $flock);

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
        $closeSelectorFlocks = $flockOptions
            ->map(fn (Flock $option) => [
                'value' => (string) $option->id,
                'text' => $option->flock_code.($option->farm ? ' - '.$option->farm->farm_name : ''),
                'farmId' => (string) $option->farm_id,
                'closeUrl' => route('flocks.close.show', $option),
                'selected' => (int) $option->id === (int) $flock->id,
            ])
            ->values();
        $rows = $this->closingRows($flock, $calculator);
        $summary = $this->summary($rows);

        $saleDate = $request->query('sale_date', now()->toDateString());
        $saleRecordController = app(FlockSaleRecordController::class);
        $houseSaleContext = $saleRecordController->houseSaleContext($flock);
        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();
        $saleRecord = new FlockSaleRecord([
            'sale_date' => $saleDate,
            'price_per_kg' => $saleRecordController->latestSalePrice($flock, $saleDate),
        ]);

        $saleRecords = FlockSaleRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->get();

        return view('flocks.close', compact(
            'flock', 'rows', 'summary', 'farms', 'flockOptions', 'closeSelectorFlocks',
            'availableStarts', 'houseSaleContext', 'saleRecord', 'saleDate', 'saleRecords'
        ));
    }

    public function close(Request $request, Flock $flock, PoultryCalculationService $calculator): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        $flock->load(['farm', 'flockHouseStarts.house']);
        $rows = $this->closingRows($flock, $calculator);
        $unbalancedRows = $rows->filter(fn (array $row) => $row['balance_diff'] !== 0);

        if ($unbalancedRows->isNotEmpty()) {
            $messages = $unbalancedRows
                ->map(fn (array $row) => 'เล้า '.$row['house_no'].' '.($row['balance_diff'] > 0 ? 'ยังขาด ' : 'เกิน ').number_format(abs($row['balance_diff'])).' ตัว')
                ->implode(' / ');

            throw ValidationException::withMessages([
                'close' => 'ยังปิดรุ่นไม่ได้ เพราะยอดไก่เข้า ตาย คัดทิ้ง และจับขายยังไม่ตรงกัน: '.$messages,
            ]);
        }

        $flock->update(['status' => 'closed']);

        return redirect()
            ->route('flocks.close.show', $flock)
            ->with('status', 'ปิดรุ่นการเลี้ยงเรียบร้อยแล้ว');
    }

    public function unlock(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_unless($request->user()?->isSuperAdmin(), 403, 'เฉพาะผู้ดูแลระบบ (Admin) เท่านั้นที่สามารถปลดล็อกรุ่นการเลี้ยงได้');

        $flock->update(['status' => 'active']);

        return redirect()
            ->route('flocks.close.show', $flock)
            ->with('status', 'ปลดล็อกรุ่นการเลี้ยงเรียบร้อยแล้ว');
    }

    private function closingRows(Flock $flock, PoultryCalculationService $calculator): Collection
    {
        $lossesByHouse = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->selectRaw('
                house_id,
                COALESCE(SUM(dead_morning), 0) as dead_morning,
                COALESCE(SUM(dead_evening), 0) as dead_evening,
                COALESCE(SUM(cull_morning), 0) as cull_morning,
                COALESCE(SUM(cull_evening), 0) as cull_evening,
                COALESCE(SUM(feed_used), 0) as feed_used,
                COALESCE(SUM(water_used), 0) as water_used
            ')
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $salesByHouse = FlockSaleRecord::query()
            ->where('flock_id', $flock->id)
            ->selectRaw('
                house_id,
                COALESCE(SUM(birds_sold), 0) as birds_sold,
                COALESCE(SUM(total_weight), 0) as total_weight,
                COALESCE(SUM(total_amount), 0) as total_amount,
                COUNT(*) as sale_trips
            ')
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        return $flock->flockHouseStarts
            ->sortBy('house.house_no')
            ->map(function ($start) use ($lossesByHouse, $salesByHouse, $calculator): array {
                $loss = $lossesByHouse->get($start->house_id);
                $sale = $salesByHouse->get($start->house_id);
                $deadTotal = $calculator->calculateDeadTotal((int) ($loss?->dead_morning ?? 0), (int) ($loss?->dead_evening ?? 0));
                $cullTotal = $calculator->calculateCullTotal((int) ($loss?->cull_morning ?? 0), (int) ($loss?->cull_evening ?? 0));
                $lossTotal = $deadTotal + $cullTotal;
                $initialBirds = (int) $start->initial_birds;
                $birdsSold = (int) ($sale?->birds_sold ?? 0);
                $balanceDiff = $initialBirds - $lossTotal - $birdsSold;
                $totalWeight = (float) ($sale?->total_weight ?? 0);

                return [
                    'house_no' => (int) $start->house->house_no,
                    'house_name' => $start->house->house_name,
                    'initial_birds' => $initialBirds,
                    'dead_total' => $deadTotal,
                    'cull_total' => $cullTotal,
                    'loss_total' => $lossTotal,
                    'birds_sold' => $birdsSold,
                    'balance_diff' => $balanceDiff,
                    'sale_trips' => (int) ($sale?->sale_trips ?? 0),
                    'total_weight' => $totalWeight,
                    'avg_sale_weight' => $birdsSold > 0 ? round($totalWeight / $birdsSold, 3) : null,
                    'total_amount' => (float) ($sale?->total_amount ?? 0),
                    'feed_used' => (float) ($loss?->feed_used ?? 0),
                    'water_used' => (int) ($loss?->water_used ?? 0),
                    'is_balanced' => $balanceDiff === 0,
                ];
            })
            ->values();
    }

    private function summary(Collection $rows): array
    {
        $totalInitial = (int) $rows->sum('initial_birds');
        $totalLoss = (int) $rows->sum('loss_total');
        $totalSold = (int) $rows->sum('birds_sold');
        $totalWeight = (float) $rows->sum('total_weight');

        return [
            'initial_birds' => $totalInitial,
            'dead_total' => (int) $rows->sum('dead_total'),
            'cull_total' => (int) $rows->sum('cull_total'),
            'loss_total' => $totalLoss,
            'birds_sold' => $totalSold,
            'balance_diff' => $totalInitial - $totalLoss - $totalSold,
            'unbalanced_houses' => $rows->where('is_balanced', false)->count(),
            'sale_trips' => (int) $rows->sum('sale_trips'),
            'total_weight' => $totalWeight,
            'avg_sale_weight' => $totalSold > 0 ? round($totalWeight / $totalSold, 3) : null,
            'total_amount' => (float) $rows->sum('total_amount'),
            'feed_used' => (float) $rows->sum('feed_used'),
            'water_used' => (int) $rows->sum('water_used'),
        ];
    }
}
