<?php

namespace App\Http\Controllers;

use App\Models\Flock;
use App\Models\FlockMedicineRecord;
use App\Models\MedicineMaster;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FlockMedicineRecordController extends Controller
{
    public function shortcut(Request $request): RedirectResponse
    {
        $flock = FarmAccess::activeFlockFor($request->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนบันทึกการให้ยา');
        }

        return redirect()->route('flocks.medicine-records.index', $flock);
    }

    public function index(Request $request, Flock $flock): View
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        $flock->load(['farm', 'flockHouseStarts.house', 'medicineRecords.house']);

        $farms = FarmAccess::farmsQuery($request->user())
            ->orderBy('farm_name')
            ->get();
        $flockOptions = FarmAccess::flocksQuery($request->user())
            ->with('farm')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();
        $medicines = MedicineMaster::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $recordsByHouse = $flock->medicineRecords
            ->sortBy([['record_date', 'asc'], ['id', 'asc']])
            ->groupBy('house_id');
        $latestMedicineDate = $flock->medicineRecords->max('record_date');

        return view('medicine-records.index', compact(
            'flock',
            'farms',
            'flockOptions',
            'medicines',
            'recordsByHouse',
            'latestMedicineDate',
        ));
    }

    public function store(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถบันทึกหรือแก้ไขข้อมูลได้');

        $validated = $request->validate([
            'records' => ['nullable', 'array'],
            'records.*.*.record_date' => ['nullable', 'date'],
            'records.*.*.medicine_master_id' => ['nullable', 'exists:medicine_masters,id'],
            'records.*.*.medicine_name' => ['nullable', 'string', 'max:255'],
            'records.*.*.quantity' => ['nullable', 'string', 'max:100'],
            'records.*.*.dose_per_1000_birds' => ['nullable', 'numeric', 'min:0'],
            'records.*.*.unit' => ['nullable', 'string', 'max:50'],
            'records.*.*.note' => ['nullable', 'string', 'max:1000'],
        ]);

        $medicineNames = MedicineMaster::query()
            ->pluck('name', 'id');

        DB::transaction(function () use ($flock, $request, $validated, $medicineNames) {
            $flock->medicineRecords()->delete();
            $recordsData = $validated['records'] ?? [];

            foreach ($flock->flockHouseStarts as $start) {
                foreach (($recordsData[$start->house_id] ?? []) as $row) {
                    $medicineName = trim((string) ($row['medicine_name'] ?? ''));
                    $medicineId = $row['medicine_master_id'] ?? null;

                    if ($medicineName === '' && $medicineId) {
                        $medicineName = (string) ($medicineNames[$medicineId] ?? '');
                    }

                    if ($medicineName === '' && empty($row['quantity']) && empty($row['dose_per_1000_birds']) && empty($row['note'])) {
                        continue;
                    }

                    $recordDate = $row['record_date'] ?: now()->toDateString();

                    FlockMedicineRecord::query()->create([
                        'flock_id' => $flock->id,
                        'house_id' => $start->house_id,
                        'medicine_master_id' => $medicineId ?: null,
                        'record_date' => $recordDate,
                        'age_day' => $this->ageDay($flock, $start, $recordDate),
                        'medicine_name' => $medicineName ?: '-',
                        'quantity' => $row['quantity'] ?? null,
                        'dose_per_1000_birds' => $row['dose_per_1000_birds'] ?? null,
                        'unit' => $row['unit'] ?? null,
                        'note' => $row['note'] ?? null,
                        'created_by' => $request->user()?->id,
                        'updated_by' => $request->user()?->id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('flocks.medicine-records.index', $flock)
            ->with('status', 'บันทึกการให้ยาเรียบร้อยแล้ว');
    }

    private function ageDay(Flock $flock, mixed $start, string $recordDate): int
    {
        $houseStartDate = $start?->start_date ?: $flock->start_date;

        return (int) max(1, CarbonImmutable::parse($houseStartDate)->diffInDays(CarbonImmutable::parse($recordDate)) + 1);
    }
}
