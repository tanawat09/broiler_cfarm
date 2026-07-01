<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlockSaleRecordRequest;
use App\Http\Requests\UpdateFlockSaleRecordRequest;
use App\Models\DailyHouseRecord;
use App\Models\Flock;
use App\Models\FlockSaleRecord;
use App\Models\SalePriceMaster;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FlockSaleRecordController extends Controller
{
    public function index(Request $request, Flock $flock): View
    {
        $user = $request->user();

        FarmAccess::ensureFlock($user, $flock);
        $flock->load(['farm', 'flockHouseStarts.house']);

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $houseId = $request->integer('house_id') ?: null;
        $farms = FarmAccess::farmsQuery($user)
            ->orderBy('farm_name')
            ->get();
        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();
        $saleSelectorFlocks = $flockOptions
            ->map(fn (Flock $option) => [
                'value' => (string) $option->id,
                'text' => $option->flock_code.($option->farm ? ' - '.$option->farm->farm_name : ''),
                'farmId' => (string) $option->farm_id,
                'saleUrl' => route('flocks.sale-records.index', $option),
                'selected' => (int) $option->id === (int) $flock->id,
            ])
            ->values();
        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();

        $saleRecords = FlockSaleRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->when($startDate, fn ($query) => $query->whereDate('sale_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('sale_date', '<=', $endDate))
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = FlockSaleRecord::query()
            ->where('flock_id', $flock->id)
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->when($startDate, fn ($query) => $query->whereDate('sale_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('sale_date', '<=', $endDate));

        $summary = [
            'birds_sold' => (int) (clone $summaryQuery)->sum('birds_sold'),
            'total_weight' => (float) (clone $summaryQuery)->sum('total_weight'),
            'total_amount' => (float) (clone $summaryQuery)->sum('total_amount'),
        ];

        return view('sale-records.index', compact(
            'flock',
            'farms',
            'flockOptions',
            'saleSelectorFlocks',
            'availableStarts',
            'saleRecords',
            'summary',
            'startDate',
            'endDate',
            'houseId',
        ));
    }

    public function create(Request $request, Flock $flock): View
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        $flock->load(['farm', 'flockHouseStarts.house']);

        $saleDate = $request->query('sale_date', now()->toDateString());
        $houseSaleContext = $this->houseSaleContext($flock);

        return view('sale-records.create', [
            'flock' => $flock,
            'saleRecord' => new FlockSaleRecord([
                'sale_date' => $saleDate,
                'price_per_kg' => $this->latestSalePrice($flock, $saleDate),
            ]),
            'availableStarts' => $flock->flockHouseStarts->sortBy('house.house_no')->values(),
            'houseSaleContext' => $houseSaleContext,
        ]);
    }

    public function store(StoreFlockSaleRecordRequest $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');
        $validated = $this->validatedPayload($request->validated(), $flock);

        $record = FlockSaleRecord::query()->create($validated + [
            'flock_id' => $flock->id,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'close') {
            return redirect()
                ->route('flocks.close.show', $flock)
                ->with('status', 'บันทึกจับไก่ขายเรียบร้อยแล้ว');
        }

        return redirect()
            ->route('flocks.sale-records.index', $flock)
            ->with('status', 'บันทึกจับไก่ขายเรียบร้อยแล้ว');
    }

    public function edit(Request $request, Flock $flock, FlockSaleRecord $saleRecord): View
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        $this->ensureSaleRecordBelongsToFlock($saleRecord, $flock);
        $flock->load(['farm', 'flockHouseStarts.house']);

        return view('sale-records.edit', [
            'flock' => $flock,
            'saleRecord' => $saleRecord,
            'availableStarts' => $flock->flockHouseStarts->sortBy('house.house_no')->values(),
            'houseSaleContext' => $this->houseSaleContext($flock),
        ]);
    }

    public function update(UpdateFlockSaleRecordRequest $request, Flock $flock, FlockSaleRecord $saleRecord): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');
        $this->ensureSaleRecordBelongsToFlock($saleRecord, $flock);

        $saleRecord->update($this->validatedPayload($request->validated(), $flock) + [
            'updated_by' => $request->user()?->id,
        ]);

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'close') {
            return redirect()
                ->route('flocks.close.show', $flock)
                ->with('status', 'แก้ไขบันทึกจับไก่ขายเรียบร้อยแล้ว');
        }

        return redirect()
            ->route('flocks.sale-records.index', $flock)
            ->with('status', 'แก้ไขบันทึกจับไก่ขายเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, Flock $flock, FlockSaleRecord $saleRecord): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถลบข้อมูลได้');
        $this->ensureSaleRecordBelongsToFlock($saleRecord, $flock);
        $saleRecord->delete();

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'close') {
            return redirect()
                ->route('flocks.close.show', $flock)
                ->with('status', 'ลบบันทึกจับไก่ขายเรียบร้อยแล้ว');
        }

        return redirect()
            ->route('flocks.sale-records.index', $flock)
            ->with('status', 'ลบบันทึกจับไก่ขายเรียบร้อยแล้ว');
    }

    private function validatedPayload(array $validated, Flock $flock): array
    {
        $allowedHouseIds = $flock->flockHouseStarts()->pluck('house_id')->map(fn ($id) => (int) $id)->all();
        $houseId = (int) $validated['house_id'];

        if (! in_array($houseId, $allowedHouseIds, true)) {
            throw ValidationException::withMessages(['house_id' => 'เล้านี้ไม่อยู่ในรุ่นการเลี้ยงที่เลือก']);
        }

        $birdsSold = (int) $validated['birds_sold'];
        $totalWeight = (float) $validated['total_weight'];
        $pricePerKg = (float) $validated['price_per_kg'];

        return [
            'house_id' => $houseId,
            'sale_date' => CarbonImmutable::parse($validated['sale_date'])->toDateString(),
            'birds_sold' => $birdsSold,
            'total_weight' => $totalWeight,
            'avg_weight' => $birdsSold > 0 ? round($totalWeight / $birdsSold, 3) : 0,
            'price_per_kg' => $pricePerKg,
            'total_amount' => round($totalWeight * $pricePerKg, 2),
            'note' => $validated['note'] ?? null,
        ];
    }

    public function latestSalePrice(Flock $flock, string $saleDate): float
    {
        return (float) (SalePriceMaster::query()
            ->where('is_active', true)
            ->whereDate('effective_date', '<=', $saleDate)
            ->orderByDesc('effective_date')
            ->value('price_per_kg') ?? 0);
    }

    public function houseSaleContext(Flock $flock)
    {
        $flock->loadMissing('flockHousePlacements');
        $placementsByHouse = $flock->flockHousePlacements->keyBy('house_id');

        $lossesByHouse = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->selectRaw('
                house_id,
                COALESCE(SUM(dead_morning + dead_evening + cull_morning + cull_evening), 0) as loss_total,
                MAX(record_date) as last_record_date
            ')
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        $latestWeights = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->whereNotNull('avg_weight')
            ->orderBy('record_date')
            ->get(['house_id', 'avg_weight'])
            ->groupBy('house_id')
            ->map(fn ($records) => (float) $records->last()->avg_weight);

        $salesByHouse = FlockSaleRecord::query()
            ->where('flock_id', $flock->id)
            ->selectRaw('house_id, COALESCE(SUM(birds_sold), 0) as birds_sold, COUNT(*) as sale_trips')
            ->groupBy('house_id')
            ->get()
            ->keyBy('house_id');

        return $flock->flockHouseStarts
            ->sortBy('house.house_no')
            ->mapWithKeys(function ($start) use ($lossesByHouse, $latestWeights, $salesByHouse, $placementsByHouse, $flock) {
                $loss = (int) ($lossesByHouse->get($start->house_id)?->loss_total ?? 0);
                $sold = (int) ($salesByHouse->get($start->house_id)?->birds_sold ?? 0);
                $remaining = max(0, (int) $start->initial_birds - $loss - $sold);
                $avgWeight = $latestWeights->get($start->house_id);
                $placement = $placementsByHouse->get($start->house_id);
                $catchDate = $placement?->catch_date ? $placement->catch_date->toDateString() : null;
                $placementDate = $placement?->placement_date ?: ($start->start_date ?: $flock->start_date);
                $placementDateStr = $placementDate ? $placementDate->toDateString() : null;

                return [
                    $start->house_id => [
                        'house_no' => (int) $start->house->house_no,
                        'initial_birds' => (int) $start->initial_birds,
                        'loss_total' => $loss,
                        'birds_sold' => $sold,
                        'remaining_birds' => $remaining,
                        'sale_trips' => (int) ($salesByHouse->get($start->house_id)?->sale_trips ?? 0),
                        'latest_avg_weight' => $avgWeight,
                        'estimated_total_weight' => $avgWeight ? round($remaining * $avgWeight, 2) : 0,
                        'last_record_date' => $lossesByHouse->get($start->house_id)?->last_record_date,
                        'catch_date' => $catchDate,
                        'placement_date' => $placementDateStr,
                    ],
                ];
            });
    }

    private function ensureSaleRecordBelongsToFlock(FlockSaleRecord $saleRecord, Flock $flock): void
    {
        abort_unless((int) $saleRecord->flock_id === (int) $flock->id, 404);
    }
}
