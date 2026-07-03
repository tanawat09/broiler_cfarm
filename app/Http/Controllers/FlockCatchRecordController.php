<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Flock;
use App\Models\FlockCatchRecord;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
            ->orderByDesc('catch_date')
            ->orderByDesc('sequence')
            ->orderByDesc('id')
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
        ];

        return view('catch-records.index', compact(
            'flock',
            'farms',
            'flockOptions',
            'availableStarts',
            'catchRecords',
            'summary',
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

        // Calculate next sequence for each house to help pre-fill
        $nextSequences = FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->selectRaw('house_id, COALESCE(MAX(sequence), 0) + 1 as next_seq')
            ->groupBy('house_id')
            ->pluck('next_seq', 'house_id');

        return view('catch-records.create', compact(
            'flock',
            'catchDate',
            'availableStarts',
            'nextSequences'
        ));
    }

    public function store(Request $request, Flock $flock): RedirectResponse
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validate([
            'house_id' => 'required|exists:houses,id',
            'catch_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sequence' => 'required|integer|min:1',
            'items.*.license_plate' => 'required|string|max:50',
            'items.*.vehicle_type' => 'nullable|string|max:50',
            'items.*.catching_team' => 'nullable|string|max:100',
            'items.*.catching_fee' => 'required|numeric|min:0',
            'items.*.note' => 'nullable|string',
        ]);

        $allowedHouseIds = $flock->flockHouseStarts()->pluck('house_id')->all();
        if (!in_array((int)$validated['house_id'], $allowedHouseIds, true)) {
            throw ValidationException::withMessages(['house_id' => 'เล้านี้ไม่อยู่ในรุ่นการเลี้ยงที่เลือก']);
        }

        foreach ($validated['items'] as $item) {
            FlockCatchRecord::create([
                'farm_id' => $flock->farm_id,
                'flock_id' => $flock->id,
                'house_id' => $validated['house_id'],
                'catch_date' => CarbonImmutable::parse($validated['catch_date'])->toDateString(),
                'sequence' => $item['sequence'],
                'license_plate' => $item['license_plate'],
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

        return view('catch-records.edit', compact(
            'flock',
            'catchRecord',
            'availableStarts'
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
}
