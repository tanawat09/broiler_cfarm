<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedReceiptRequest;
use App\Models\DailyHouseRecord;
use App\Models\Farm;
use App\Models\FeedReceipt;
use App\Models\FeedReceiptHouseItem;
use App\Models\Flock;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FeedReceiptController extends Controller
{
    public function index(): View
    {
        $startDate = request('start_date');
        $endDate = request('end_date');
        $selectedFarmId = request()->integer('farm_id');
        $selectedFlockId = request()->integer('flock_id');
        $farmIds = FarmAccess::farmsQuery(request()->user())->pluck('id');
        $farms = FarmAccess::farmsQuery(request()->user())
            ->orderBy('farm_name')
            ->get();
        $flockOptions = FarmAccess::flocksQuery(request()->user())
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();
        $selectedFlock = $selectedFlockId
            ? $flockOptions->firstWhere('id', $selectedFlockId)
            : null;
        $flockEndDates = DailyHouseRecord::query()
            ->whereIn('flock_id', $flockOptions->pluck('id'))
            ->selectRaw('flock_id, MAX(record_date) as end_date')
            ->groupBy('flock_id')
            ->pluck('end_date', 'flock_id');

        if ($selectedFlock) {
            $selectedFarmId = (int) $selectedFlock->farm_id;
            $startDate = $startDate ?: $selectedFlock->start_date->toDateString();
            $endDate = $endDate ?: $flockEndDates->get($selectedFlock->id);
        }

        $receipts = FeedReceipt::query()
            ->with(['farm', 'flock', 'items.house'])
            ->withSum('items as total_quantity_kg', 'quantity_kg')
            ->whereIn('farm_id', $farmIds)
            ->when($selectedFarmId, fn ($query) => $query->where('farm_id', $selectedFarmId))
            ->when($startDate, fn ($query) => $query->whereDate('receipt_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('receipt_date', '<=', $endDate))
            ->latest('receipt_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $this->attachFlockCodesToReceipts($receipts->getCollection(), $flockOptions, $flockEndDates);

        $summaryQuery = FeedReceipt::query()
            ->join('feed_receipt_house_items', 'feed_receipts.id', '=', 'feed_receipt_house_items.feed_receipt_id')
            ->whereIn('feed_receipts.farm_id', $farmIds)
            ->when($selectedFarmId, fn ($query) => $query->where('feed_receipts.farm_id', $selectedFarmId))
            ->when($startDate, fn ($query) => $query->whereDate('feed_receipts.receipt_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('feed_receipts.receipt_date', '<=', $endDate));

        $dailySummaries = (clone $summaryQuery)
            ->selectRaw('feed_receipts.receipt_date, feed_receipts.feed_code, COUNT(DISTINCT feed_receipts.id) as receipt_count, COUNT(DISTINCT feed_receipt_house_items.house_id) as house_count, SUM(feed_receipt_house_items.quantity_kg) as total_quantity_kg')
            ->groupBy('feed_receipts.receipt_date', 'feed_receipts.feed_code')
            ->orderByDesc('feed_receipts.receipt_date')
            ->orderBy('feed_receipts.feed_code')
            ->get()
            ->groupBy(fn ($row) => (string) $row->receipt_date);

        $feedCodeTotals = (clone $summaryQuery)
            ->selectRaw('feed_receipts.feed_code, COUNT(DISTINCT feed_receipts.id) as receipt_count, COUNT(DISTINCT feed_receipt_house_items.house_id) as house_count, SUM(feed_receipt_house_items.quantity_kg) as total_quantity_kg')
            ->groupBy('feed_receipts.feed_code')
            ->orderBy('feed_receipts.feed_code')
            ->get()
            ->keyBy('feed_code');

        $overallSummary = [
            'receipt_count' => (clone $summaryQuery)->distinct('feed_receipts.id')->count('feed_receipts.id'),
            'house_count' => (clone $summaryQuery)->distinct('feed_receipt_house_items.house_id')->count('feed_receipt_house_items.house_id'),
            'total_quantity_kg' => (float) (clone $summaryQuery)->sum('feed_receipt_house_items.quantity_kg'),
        ];

        return view('feed-receipts.index', compact(
            'receipts',
            'dailySummaries',
            'feedCodeTotals',
            'overallSummary',
            'farms',
            'flockOptions',
            'selectedFarmId',
            'selectedFlockId',
            'startDate',
            'endDate',
        ));
    }

    public function create(): View
    {
        $hasFlockColumn = $this->feedReceiptsHasFlockColumn();
        $farms = FarmAccess::farmsQuery(request()->user())->orderBy('farm_name')->get();
        $flockOptions = FarmAccess::flocksQuery(request()->user())
            ->with('farm', 'flockHouseStarts')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();
        $selectedFlock = $this->selectedFlock($flockOptions);
        $selectedFarm = $selectedFlock?->farm ?: $this->selectedFarm($farms);
        $receiptDate = request('receipt_date');
        $activeFlock = $selectedFlock;

        if (! $activeFlock && $selectedFarm) {
            $activeFlock = $flockOptions
                ->where('farm_id', $selectedFarm->id)
                ->where('status', 'active')
                ->sortByDesc('start_date')
                ->first();
        }

        $dateExplanation = '';
        if ($activeFlock) {
            $latestReceiptQuery = FeedReceipt::query()
                ->where('farm_id', $selectedFarm->id)
                ->whereDate('receipt_date', '>=', $activeFlock->start_date->toDateString());

            if ($hasFlockColumn) {
                $latestReceiptQuery->where(function ($query) use ($activeFlock) {
                    $query->where('flock_id', $activeFlock->id)
                        ->orWhereNull('flock_id');
                });
            }

            $latestReceipt = $latestReceiptQuery
                ->latest('receipt_date')
                ->latest('id')
                ->first();

            if ($latestReceipt) {
                $latestDate = \Carbon\Carbon::parse($latestReceipt->receipt_date);
                $suggestedDate = $latestDate->copy()->addDay();
                $dateExplanation = "แนะนำตามวันถัดไปของการรับอาหารล่าสุดในรุ่นนี้: " . thai_date($suggestedDate->toDateString()) . " (คำนวณจาก วันรับล่าสุด " . thai_date($latestDate->toDateString()) . " + 1 วัน)";
                if (!$receiptDate) {
                    $receiptDate = $suggestedDate->toDateString();
                }
            } else {
                $flockStartDate = \Carbon\Carbon::parse($activeFlock->start_date);
                $dateExplanation = "แนะนำตามวันที่เริ่มต้นรุ่นการเลี้ยง: " . thai_date($flockStartDate->toDateString()) . " (คำนวณจาก วันเริ่มรุ่น " . thai_date($flockStartDate->toDateString()) . " เนื่องจากยังไม่มีบันทึกรับอาหารในรุ่นนี้)";
                if (!$receiptDate) {
                    $receiptDate = $flockStartDate->toDateString();
                }
            }
        } else {
            $dateExplanation = "แนะนำตามวันที่ปัจจุบัน (เนื่องจากไม่พบรุ่นการเลี้ยงที่กำลังเลี้ยงอยู่)";
            if (!$receiptDate) {
                $receiptDate = now()->toDateString();
            }
        }

        $houses = $selectedFarm
            ? $selectedFarm->houses()->where('is_active', true)->orderBy('house_no')->get()
            : collect();
        $startsByHouse = $activeFlock?->flockHouseStarts->keyBy('house_id') ?? collect();
        $ageByHouse = $houses->mapWithKeys(fn ($house) => [
            $house->id => $activeFlock ? $this->ageDayForHouse($activeFlock, $startsByHouse->get($house->id), $receiptDate) : null,
        ]);

        return view('feed-receipts.create', [
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'selectedFarm' => $selectedFarm,
            'activeFlock' => $activeFlock,
            'receiptDate' => $receiptDate,
            'dateExplanation' => $dateExplanation,
            'houses' => $houses,
            'ageByHouse' => $ageByHouse,
        ]);
    }

    public function store(StoreFeedReceiptRequest $request): RedirectResponse
    {
        $hasFlockColumn = $this->feedReceiptsHasFlockColumn();
        $validated = $request->validated();
        $farm = Farm::query()->findOrFail($validated['farm_id']);
        FarmAccess::ensureFarm($request->user(), $farm);
        $flock = null;

        if ($hasFlockColumn && ! empty($validated['flock_id'])) {
            $flock = Flock::query()->findOrFail($validated['flock_id']);
            FarmAccess::ensureFlock($request->user(), $flock);
            abort_if($flock->status === 'closed', 403, 'กรุณาให้ผู้ดูแลระบบปลดล็อกรุ่นก่อนบันทึกรับอาหาร');

            if ((int) $flock->farm_id !== (int) $farm->id) {
                throw ValidationException::withMessages([
                    'flock_id' => 'รุ่นการเลี้ยงต้องอยู่ในฟาร์มเดียวกับรายการรับอาหาร',
                ]);
            }
        }

        $allowedHouseIds = $farm->houses()->where('is_active', true)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $items = collect($validated['items'])
            ->mapWithKeys(fn ($item, $houseId) => [(int) $houseId => (float) ($item['quantity_kg'] ?? 0)])
            ->filter(fn ($quantity, $houseId) => $quantity > 0 && in_array($houseId, $allowedHouseIds, true));

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'กรุณาระบุปริมาณอาหารอย่างน้อย 1 เล้า',
            ]);
        }

        $receipt = DB::transaction(function () use ($validated, $items, $request, $hasFlockColumn) {
            $receiptData = [
                'farm_id' => $validated['farm_id'],
                'receipt_date' => $validated['receipt_date'],
                'feed_code' => $validated['feed_code'],
                'production_lot' => $validated['production_lot'],
                'remark' => null,
                'created_by' => $request->user()?->id,
                'updated_by' => $request->user()?->id,
            ];

            if ($hasFlockColumn) {
                $receiptData['flock_id'] = $validated['flock_id'] ?? null;
            }

            $receipt = FeedReceipt::query()->create($receiptData);

            foreach ($items as $houseId => $quantityKg) {
                FeedReceiptHouseItem::query()->create([
                    'feed_receipt_id' => $receipt->id,
                    'house_id' => $houseId,
                    'quantity_kg' => $quantityKg,
                ]);
            }

            return $receipt;
        });

        return redirect()
            ->route('feed-receipts.show', $receipt)
            ->with('status', 'บันทึกรับอาหารเรียบร้อยแล้ว');
    }

    public function show(FeedReceipt $feedReceipt): View
    {
        FarmAccess::ensureFeedReceipt(request()->user(), $feedReceipt);

        $feedReceipt->load(['farm', 'flock', 'items.house', 'creator']);

        return view('feed-receipts.show', compact('feedReceipt'));
    }

    public function destroy(FeedReceipt $feedReceipt): RedirectResponse
    {
        FarmAccess::ensureFeedReceipt(request()->user(), $feedReceipt);

        $feedReceipt->delete();

        return redirect()
            ->route('feed-receipts.index')
            ->with('status', 'ลบบันทึกรับอาหารเรียบร้อยแล้ว');
    }

    private function selectedFarm($farms): ?Farm
    {
        $farmId = request()->integer('farm_id');

        if ($farmId) {
            return $farms->firstWhere('id', $farmId);
        }

        return $farms->first();
    }

    private function selectedFlock($flockOptions): ?Flock
    {
        $flockId = request()->integer('flock_id');

        if ($flockId) {
            return $flockOptions->firstWhere('id', $flockId);
        }

        return null;
    }

    private function feedReceiptsHasFlockColumn(): bool
    {
        try {
            return Schema::hasColumn('feed_receipts', 'flock_id');
        } catch (\Throwable) {
            return false;
        }
    }

    private function attachFlockCodesToReceipts($receipts, $flockOptions, $flockEndDates): void
    {
        $flocksByFarm = $flockOptions
            ->groupBy('farm_id')
            ->map(fn ($flocks) => $flocks->sortByDesc('start_date')->values());

        $receipts->each(function (FeedReceipt $receipt) use ($flocksByFarm, $flockEndDates): void {
            $receiptDate = $receipt->receipt_date->toDateString();
            if ($receipt->flock) {
                $receipt->matched_flock_code = $receipt->flock->flock_code;
                return;
            }

            $matchedFlock = $flocksByFarm
                ->get($receipt->farm_id, collect())
                ->first(function (Flock $flock) use ($receiptDate, $flockEndDates): bool {
                    $startDate = $flock->start_date->toDateString();
                    $endDate = $flockEndDates->get($flock->id) ?: $startDate;

                    return $receiptDate >= $startDate && $receiptDate <= $endDate;
                });

            $receipt->matched_flock_code = $matchedFlock?->flock_code;
        });
    }

    private function ageDayForHouse(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;

        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }
}
