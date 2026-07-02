<?php

namespace App\Http\Controllers;

use App\Models\Flock;
use App\Models\DailyHouseRecord;
use App\Support\FarmAccess;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DailyRecordImportController extends Controller
{
    public function showImportPage(Request $request): View
    {
        $user = $request->user();
        $farms = FarmAccess::farmsQuery($user)->orderBy('farm_name')->get();
        
        $flocks = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->get();

        return view('daily-records.import', [
            'farms' => $farms,
            'flocks' => $flocks,
        ]);
    }

    public function handleImport(Request $request): RedirectResponse
    {
        $request->validate([
            'flock_id' => ['required', 'exists:flocks,id'],
            'file' => ['required', 'file', 'mimes:txt,csv'],
        ], [
            'flock_id.required' => 'กรุณาเลือกรุ่นการเลี้ยง',
            'flock_id.exists' => 'รุ่นการเลี้ยงไม่ถูกต้อง',
            'file.required' => 'กรุณาเลือกไฟล์ที่ต้องการนำเข้า',
            'file.file' => 'ไฟล์ที่อัปโหลดไม่ถูกต้อง',
            'file.mimes' => 'รองรับเฉพาะไฟล์สกุล .txt หรือ .csv เท่านั้น',
        ]);

        $flock = Flock::findOrFail($request->input('flock_id'));
        FarmAccess::ensureFlock($request->user(), $flock);

        if ($flock->status === 'closed') {
            return back()->withErrors(['flock_id' => 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถนำเข้าข้อมูลใหม่ได้']);
        }

        $file = $request->file('file');
        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            return back()->withErrors(['file' => 'ไม่สามารถเปิดอ่านไฟล์ได้']);
        }

        if (str_starts_with($contents, "\xff\xfe") || str_starts_with($contents, "\xfe\xff")) {
            $contents = mb_convert_encoding($contents, 'UTF-8', 'UTF-16');
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $contents);
        rewind($handle);

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        $flockHouseStarts = $flock->flockHouseStarts()->with('house')->get();
        $placements = $flock->flockHousePlacements()->get();
        $houseMap = [];
        $houseStartsMap = [];
        $placementsMap = [];

        foreach ($flockHouseStarts as $start) {
            $houseMap[$start->house->house_no] = $start->house_id;
            $houseStartsMap[$start->house_id] = $start;
        }
        foreach ($placements as $p) {
            $placementsMap[$p->house_id] = $p;
        }

        try {
            while (($row = fgetcsv($handle, 4096, ',', '"')) !== false) {
                if (count($row) < 17) {
                    $errorCount++;
                    continue;
                }

                $fileHouseCode = trim($row[2]);
                $houseNo = (int)substr($fileHouseCode, -2);
                $ageDay = (int) $row[6];
                $recordDate = trim($row[7]);

                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $recordDate)) {
                    $errorCount++;
                    continue;
                }

                // คำนวณวันเริ่มเลี้ยงของเล้านี้จาก record_date และ age_day
                $daysToSubtract = max(0, $ageDay - 1);
                $calculatedStartDate = \Carbon\CarbonImmutable::parse($recordDate)->subDays($daysToSubtract)->toDateString();

                // หากยังไม่มีเล้านี้ในระบบรุ่นเลี้ยง ให้สร้างอัตโนมัติ
                if (!isset($houseMap[$houseNo])) {
                    $house = $flock->farm->houses()->where('house_no', $houseNo)->first();
                    if ($house) {
                        $newStart = \App\Models\FlockHouseStart::create([
                            'flock_id' => $flock->id,
                            'house_id' => $house->id,
                            'initial_birds' => 0,
                            'start_date' => $calculatedStartDate,
                        ]);
                        $newPlacement = \App\Models\FlockHousePlacement::create([
                            'flock_id' => $flock->id,
                            'house_id' => $house->id,
                            'placement_date' => $calculatedStartDate,
                            'chicks_in' => 0,
                            'male_count' => 0,
                            'female_count' => 0,
                            'male_grade_a_count' => 0,
                            'male_grade_b_count' => 0,
                            'female_grade_a_count' => 0,
                            'female_grade_b_count' => 0,
                        ]);

                        $houseMap[$houseNo] = $house->id;
                        $houseStartsMap[$house->id] = $newStart;
                        $placementsMap[$house->id] = $newPlacement;
                    } else {
                        $skipCount++;
                        continue;
                    }
                }

                $houseId = $houseMap[$houseNo];

                // อัปเดต start_date ของเล้าหากยังไม่ตรง
                $startObj = $houseStartsMap[$houseId] ?? null;
                if ($startObj && (!$startObj->start_date || $startObj->start_date->toDateString() !== $calculatedStartDate)) {
                    $startObj->update(['start_date' => $calculatedStartDate]);
                    $startObj->start_date = \Carbon\CarbonImmutable::parse($calculatedStartDate);
                }

                // อัปเดต placement_date ของข้อมูลลูกไก่หากยังไม่ตรง
                $placementObj = $placementsMap[$houseId] ?? null;
                if ($placementObj && (!$placementObj->placement_date || $placementObj->placement_date->toDateString() !== $calculatedStartDate)) {
                    $placementObj->update(['placement_date' => $calculatedStartDate]);
                    $placementObj->placement_date = \Carbon\CarbonImmutable::parse($calculatedStartDate);
                }

                $exists = DailyHouseRecord::where('flock_id', $flock->id)
                    ->where('house_id', $houseId)
                    ->where('record_date', $recordDate)
                    ->exists();

                if ($exists) {
                    $skipCount++;
                    continue;
                }

                $feedCode = trim($row[8]) ?: null;
                $feedUsed = (float) $row[9];
                $tempMin = (float) $row[10];
                $tempMax = (float) $row[11];
                $humidity = (float) $row[12];
                $waterUsed = (int) $row[13];
                $deadTotal = (int) $row[14];
                $cullTotal = (int) $row[15];
                $avgWeightGrams = (float) $row[16];
                $avgWeightKg = $avgWeightGrams > 0 ? ($avgWeightGrams / 1000) : null;

                DailyHouseRecord::create([
                    'flock_id' => $flock->id,
                    'house_id' => $houseId,
                    'record_date' => $recordDate,
                    'age_day' => $ageDay,
                    'feed_code' => $feedCode,
                    'feed_used' => $feedUsed,
                    'water_used' => $waterUsed,
                    'temp_min' => $tempMin,
                    'temp_max' => $tempMax,
                    'humidity' => $humidity,
                    'dead_morning' => $deadTotal,
                    'dead_evening' => 0,
                    'cull_morning' => $cullTotal,
                    'cull_evening' => 0,
                    'avg_weight' => $avgWeightKg,
                    'created_by' => auth()->id(),
                ]);

                $successCount++;
            }
        } finally {
            fclose($handle);
        }

        // อัปเดต start_date ของรุ่นเลี้ยงภาพรวมให้เป็นวันที่เริ่มของเล้าที่เร็วที่สุด
        $earliestStartDate = $flock->flockHouseStarts()->whereNotNull('start_date')->min('start_date');
        if ($earliestStartDate && $flock->start_date->toDateString() !== $earliestStartDate) {
            $flock->update(['start_date' => $earliestStartDate]);
        }

        $message = "นำเข้าข้อมูลประจำวันเรียบร้อยแล้ว {$successCount} รายการ";
        if ($skipCount > 0) {
            $message .= " (ข้ามรายการซ้ำ/ไม่พบเล้า {$skipCount} รายการ)";
        }
        if ($errorCount > 0) {
            $message .= " (พบบรรทัดชำรุด {$errorCount} รายการ)";
        }

        // หากยังไม่ได้ป้อนข้อมูลจำนวนลูกไก่เข้าเลี้ยง (มีค่าเริ่มต้น 0 ตัว) ให้แจ้งเตือนผู้ใช้ด้วย
        $hasZeroInitialBirds = $flock->flockHouseStarts()->where('initial_birds', 0)->exists();
        if ($hasZeroInitialBirds) {
            $message .= " *** คำแนะนำ: กรุณาไปป้อนจำนวนตัวผู้/เมียในหน้า 'ข้อมูลลูกไก่' เพื่อให้ระบบคำนวณอัตราสูญเสียและ FCR ทำงานได้สมบูรณ์";
        }

        return redirect()->route('flocks.daily-records.show', [$flock, $recordDate ?: now()->toDateString()])
            ->with('status', $message);
    }
}
