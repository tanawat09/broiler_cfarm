<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Flock;
use App\Models\FlockCatchRecord;
use App\Models\FlockCatchTeamCost;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Mpdf\Mpdf;

class FlockCatchRecordController extends Controller
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

        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();

        $catchRecords = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->when($startDate, fn ($query) => $query->whereDate('catch_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('catch_date', '<=', $endDate))
            ->orderBy('sequence')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->when($startDate, fn ($query) => $query->whereDate('catch_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('catch_date', '<=', $endDate));

        $summary = [
            'total_fee' => (float) (clone $summaryQuery)->sum('catching_fee'),
            'total_trips' => (int) (clone $summaryQuery)->count(),
            'total_birds' => (int) (clone $summaryQuery)->sum('birds_count'),
            'total_boxes' => (int) (clone $summaryQuery)->sum('boxes_count'),
        ];

        [$teamPaymentRows, $teamPaymentSummary] = $this->teamPaymentData($flock);

        return view('catch-records.index', compact(
            'flock',
            'farms',
            'flockOptions',
            'availableStarts',
            'catchRecords',
            'summary',
            'teamPaymentRows',
            'teamPaymentSummary',
            'startDate',
            'endDate',
            'houseId'
        ));
    }

    public function create(Request $request, Flock $flock): View
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        $flock->load(['farm', 'flockHouseStarts.house']);

        $catchDate = $request->query('catch_date', now()->toDateString());
        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();

        $nextSequence = ((int) FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->max('sequence')) + 1;

        $catchingTeams = \App\Models\CatchingTeam::query()->where('is_active', true)->orderBy('name')->get();

        return view('catch-records.create', compact(
            'flock',
            'catchDate',
            'availableStarts',
            'nextSequence',
            'catchingTeams'
        ));
    }

    public function store(Request $request, Flock $flock): RedirectResponse
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.house_id' => 'required|exists:houses,id',
            'items.*.catch_date' => 'required|date',
            'items.*.sequence' => 'required|integer|min:1',
            'items.*.license_plate' => 'required|string|max:50',
            'items.*.birds_count' => 'required|integer|min:0',
            'items.*.boxes_count' => 'required|integer|min:0',
            'items.*.vehicle_type' => 'nullable|string|max:50',
            'items.*.catching_team' => 'nullable|string|max:100',
            'items.*.catching_fee' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string',
        ]);

        $allowedHouseIds = $flock->flockHouseStarts()->pluck('house_id')->all();
        foreach ($validated['items'] as $index => $item) {
            if (!in_array((int)$item['house_id'], $allowedHouseIds, true)) {
                throw ValidationException::withMessages([
                    "items.{$index}.house_id" => 'เล้านี้ไม่อยู่ในรุ่นการเลี้ยงที่เลือก'
                ]);
            }
        }

        $nextSequence = ((int) FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->max('sequence')) + 1;

        foreach ($validated['items'] as $index => $item) {
            FlockCatchRecord::create([
                'farm_id' => $flock->farm_id,
                'flock_id' => $flock->id,
                'house_id' => $item['house_id'],
                'catch_date' => CarbonImmutable::parse($item['catch_date'])->toDateString(),
                'sequence' => $nextSequence + $index,
                'license_plate' => $item['license_plate'],
                'birds_count' => $item['birds_count'],
                'boxes_count' => $item['boxes_count'],
                'vehicle_type' => $item['vehicle_type'] ?? null,
                'catching_team' => $item['catching_team'] ?? null,
                'catching_fee' => $item['catching_fee'],
                'note' => $item['note'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
        }

        return redirect()
            ->route('flocks.catch-records.index', $flock)
            ->with('status', 'บันทึกคิดค่าจับไก่เรียบร้อยแล้ว');
    }

    public function edit(Request $request, Flock $flock, FlockCatchRecord $catchRecord): View
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_unless((int)$catchRecord->flock_id === (int)$flock->id, 404);

        $flock->load(['farm', 'flockHouseStarts.house']);
        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();

        $catchingTeams = \App\Models\CatchingTeam::query()->where('is_active', true)->orderBy('name')->get();

        return view('catch-records.edit', compact(
            'flock',
            'catchRecord',
            'availableStarts',
            'catchingTeams'
        ));
    }

    public function update(Request $request, Flock $flock, FlockCatchRecord $catchRecord): RedirectResponse
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        abort_unless((int)$catchRecord->flock_id === (int)$flock->id, 404);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validate([
            'house_id' => 'required|exists:houses,id',
            'catch_date' => 'required|date',
            'sequence' => 'required|integer|min:1',
            'license_plate' => 'required|string|max:50',
            'birds_count' => 'required|integer|min:0',
            'boxes_count' => 'required|integer|min:0',
            'vehicle_type' => 'nullable|string|max:50',
            'catching_team' => 'nullable|string|max:100',
            'catching_fee' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $allowedHouseIds = $flock->flockHouseStarts()->pluck('house_id')->all();
        if (!in_array((int)$validated['house_id'], $allowedHouseIds, true)) {
            throw ValidationException::withMessages(['house_id' => 'เล้านี้ไม่อยู่ในรุ่นการเลี้ยงที่เลือก']);
        }

        $catchRecord->update([
            'house_id' => $validated['house_id'],
            'catch_date' => CarbonImmutable::parse($validated['catch_date'])->toDateString(),
            'sequence' => $validated['sequence'],
            'license_plate' => $validated['license_plate'],
            'birds_count' => $validated['birds_count'],
            'boxes_count' => $validated['boxes_count'],
            'vehicle_type' => $validated['vehicle_type'],
            'catching_team' => $validated['catching_team'],
            'catching_fee' => $validated['catching_fee'],
            'note' => $validated['note'],
            'updated_by' => $user->id,
        ]);

        return redirect()
            ->route('flocks.catch-records.index', $flock)
            ->with('status', 'แก้ไขรายละเอียดการจับไก่เรียบร้อยแล้ว');
    }

    public function destroy(Request $request, Flock $flock, FlockCatchRecord $catchRecord): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_unless((int)$catchRecord->flock_id === (int)$flock->id, 404);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถลบข้อมูลได้');

        $catchRecord->delete();

        return redirect()
            ->route('flocks.catch-records.index', $flock)
            ->with('status', 'ลบรายการจับไก่เรียบร้อยแล้ว');
    }

    public function updateTeamCosts(Request $request, Flock $flock): RedirectResponse
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถแก้ไขข้อมูลได้');

        $validated = $request->validate([
            'costs' => 'nullable|array',
            'costs.*.catching_team' => 'required|string|max:100',
            'costs.*.fuel_cost' => 'nullable|numeric|min:0',
            'costs.*.forklift_cost' => 'nullable|numeric|min:0',
        ]);

        $usedTeams = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->whereNotNull('catching_team')
            ->where('catching_team', '!=', '')
            ->pluck('catching_team')
            ->unique()
            ->values()
            ->all();

        foreach ($validated['costs'] ?? [] as $item) {
            if (! in_array($item['catching_team'], $usedTeams, true)) {
                continue;
            }

            FlockCatchTeamCost::query()->updateOrCreate(
                [
                    'flock_id' => $flock->id,
                    'catching_team' => $item['catching_team'],
                ],
                [
                    'farm_id' => $flock->farm_id,
                    'fuel_cost' => $item['fuel_cost'] ?? 0,
                    'forklift_cost' => $item['forklift_cost'] ?? 0,
                    'created_by' => $user?->id,
                    'updated_by' => $user?->id,
                ]
            );
        }

        return redirect()
            ->route('flocks.catch-records.index', $flock)
            ->with('status', 'บันทึกรายละเอียดทีมจับไก่เรียบร้อยแล้ว');
    }

    public function paymentReportPdf(Request $request, Flock $flock): Response
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        $flock->load('farm');

        [$teamPaymentRows, $teamPaymentSummary] = $this->teamPaymentData($flock);
        [$housePaymentRows, $housePaymentSummary] = $this->housePaymentData($flock);
        $catchRecords = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();

        $html = view('catch-records.payment-report-pdf', [
            'flock' => $flock,
            'teamPaymentRows' => $teamPaymentRows,
            'teamPaymentSummary' => $teamPaymentSummary,
            'housePaymentRows' => $housePaymentRows,
            'housePaymentSummary' => $housePaymentSummary,
            'catchRecords' => $catchRecords,
            'withholdingRate' => 0.03,
            'generatedAt' => now(),
        ])->render();

        if (! is_dir(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'garuda',
            'tempDir' => storage_path('app/mpdf'),
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetTitle('รายงานสรุปค่าจับไก่');
        $mpdf->WriteHTML($html);

        $fileName = 'catch-payment-report-flock-'.$flock->id.'.pdf';

        return response($mpdf->Output($fileName, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function shortcut(Request $request): RedirectResponse
    {
        $flock = FarmAccess::activeFlockFor($request->user());

        if (!$flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนบันทึกค่าจับไก่');
        }

        return redirect()->route('flocks.catch-records.index', $flock);
    }

    private function teamPaymentData(Flock $flock): array
    {
        $teamSummaries = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->whereNotNull('catching_team')
            ->where('catching_team', '!=', '')
            ->selectRaw('catching_team, COUNT(*) as total_trips, SUM(birds_count) as total_birds, SUM(boxes_count) as total_boxes, SUM(catching_fee) as total_fee')
            ->groupBy('catching_team')
            ->orderBy('catching_team')
            ->get();

        $teamCosts = FlockCatchTeamCost::query()
            ->where('flock_id', $flock->id)
            ->get()
            ->keyBy('catching_team');

        $teamPaymentRows = $teamSummaries->map(function ($team) use ($teamCosts) {
            $cost = $teamCosts->get($team->catching_team);
            $fuelCost = (float) ($cost?->fuel_cost ?? 0);
            $forkliftCost = (float) ($cost?->forklift_cost ?? 0);
            $catchingFee = (float) $team->total_fee;
            $paymentTotal = $catchingFee + $fuelCost + $forkliftCost;
            $withholdingAmount = round($paymentTotal * 0.03, 2);

            return [
                'catching_team' => $team->catching_team,
                'total_trips' => (int) $team->total_trips,
                'total_birds' => (int) $team->total_birds,
                'total_boxes' => (int) $team->total_boxes,
                'total_fee' => $catchingFee,
                'fuel_cost' => $fuelCost,
                'forklift_cost' => $forkliftCost,
                'payment_total' => $paymentTotal,
                'withholding_amount' => $withholdingAmount,
                'net_payment' => $paymentTotal - $withholdingAmount,
            ];
        });

        $teamPaymentSummary = [
            'total_fee' => (float) $teamPaymentRows->sum('total_fee'),
            'total_fuel_cost' => (float) $teamPaymentRows->sum('fuel_cost'),
            'total_forklift_cost' => (float) $teamPaymentRows->sum('forklift_cost'),
            'payment_total' => (float) $teamPaymentRows->sum('payment_total'),
            'withholding_amount' => (float) $teamPaymentRows->sum('withholding_amount'),
            'net_payment' => (float) $teamPaymentRows->sum('net_payment'),
        ];

        return [$teamPaymentRows, $teamPaymentSummary];
    }

    private function housePaymentData(Flock $flock): array
    {
        $housePaymentRows = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->join('houses', 'houses.id', '=', 'flock_catch_records.house_id')
            ->selectRaw('houses.house_no, COUNT(*) as total_trips, SUM(flock_catch_records.birds_count) as total_birds, SUM(flock_catch_records.boxes_count) as total_boxes, SUM(flock_catch_records.catching_fee) as total_fee')
            ->groupBy('houses.house_no')
            ->orderByRaw('CAST(houses.house_no AS UNSIGNED)')
            ->get()
            ->map(fn ($row) => [
                'house_no' => $row->house_no,
                'total_trips' => (int) $row->total_trips,
                'total_birds' => (int) $row->total_birds,
                'total_boxes' => (int) $row->total_boxes,
                'total_fee' => (float) $row->total_fee,
            ]);

        $housePaymentSummary = [
            'total_trips' => (int) $housePaymentRows->sum('total_trips'),
            'total_birds' => (int) $housePaymentRows->sum('total_birds'),
            'total_boxes' => (int) $housePaymentRows->sum('total_boxes'),
            'total_fee' => (float) $housePaymentRows->sum('total_fee'),
        ];

        return [$housePaymentRows, $housePaymentSummary];
    }
}
