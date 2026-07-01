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
        $houseMap = [];
        foreach ($flockHouseStarts as $start) {
            $houseMap[$start->house->house_no] = $start->house_id;
        }

        try {
            while (($row = fgetcsv($handle, 4096, ',', '"')) !== false) {
                if (count($row) < 17) {
                    $errorCount++;
                    continue;
                }

                $fileHouseCode = trim($row[2]);
                $houseNo = (int)substr($fileHouseCode, -2);

                if (!isset($houseMap[$houseNo])) {
                    $skipCount++;
                    continue;
                }

                $houseId = $houseMap[$houseNo];
                $ageDay = (int) $row[6];
                $recordDate = trim($row[7]);

                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $recordDate)) {
                    $errorCount++;
                    continue;
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

        $message = "นำเข้าข้อมูลประจำวันเรียบร้อยแล้ว {$successCount} รายการ";
        if ($skipCount > 0) {
            $message .= " (ข้ามรายการซ้ำ/ไม่พบเล้า {$skipCount} รายการ)";
        }
        if ($errorCount > 0) {
            $message .= " (พบบรรทัดชำรุด {$errorCount} รายการ)";
        }

        return redirect()->route('flocks.daily-records.show', [$flock, $recordDate ?: now()->toDateString()])
            ->with('status', $message);
    }
}
