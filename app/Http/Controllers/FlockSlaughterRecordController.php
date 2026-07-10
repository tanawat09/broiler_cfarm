<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Flock;
use App\Models\FlockCatchRecord;
use App\Models\FlockSlaughterRecord;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FlockSlaughterRecordController extends Controller
{
    private const MAX_IMPORT_ROWS = 10000;

    private const TEMP_IMPORT_TTL_SECONDS = 7200;

    public function index(Request $request, Flock $flock): View
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        $flock->load(['farm', 'flockHouseStarts.house']);

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $houseId = $request->integer('house_id') ?: null;

        $farms = FarmAccess::farmsQuery($user)->orderBy('farm_name')->get();
        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();

        $availableStarts = $flock->flockHouseStarts->sortBy('house.house_no')->values();

        $slaughterRecords = FlockSlaughterRecord::query()
            ->where('flock_id', $flock->id)
            ->with('house')
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->when($startDate, fn ($query) => $query->whereDate('slaughter_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('slaughter_date', '<=', $endDate))
            ->orderByDesc('slaughter_date')
            ->orderBy('sequence')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = FlockSlaughterRecord::query()
            ->where('flock_id', $flock->id)
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->when($startDate, fn ($query) => $query->whereDate('slaughter_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('slaughter_date', '<=', $endDate));

        $summary = [
            'total_birds' => (int) (clone $summaryQuery)->sum('slaughter_birds'),
            'total_weight' => (float) (clone $summaryQuery)->sum('actual_weight'),
            'total_doa' => (int) (clone $summaryQuery)->sum('doa_birds'),
            'total_net' => (int) (clone $summaryQuery)->sum('net_birds'),
            'total_condemned' => (int) (clone $summaryQuery)->sum('condemned_birds'),
            'total_problem' => (int) (clone $summaryQuery)->sum('problem_birds'),
        ];

        return view('slaughter-records.index', compact(
            'flock',
            'farms',
            'flockOptions',
            'availableStarts',
            'slaughterRecords',
            'summary',
            'startDate',
            'endDate',
            'houseId'
        ));
    }

    public function showUploadPage(Request $request, Flock $flock): View
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        $flock->load('farm');

        return view('slaughter-records.import_step1', compact('flock'));
    }

    public function handleUpload(Request $request, Flock $flock): View|RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถนำเข้าข้อมูลได้');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'file.required' => 'กรุณาเลือกไฟล์ Excel ที่ต้องการอัปโหลด',
            'file.mimes' => 'รองรับเฉพาะไฟล์ Excel (.xlsx, .xls) เท่านั้น',
            'file.max' => 'ไฟล์ Excel ต้องมีขนาดไม่เกิน 10 MB',
        ]);

        $file = $request->file('file');
        $uploadToken = (string) Str::uuid();
        $path = $file->store('temp/'.$request->user()->id.'/'.$flock->id);

        if (! $path) {
            return back()->withErrors(['file' => 'ไม่สามารถจัดเก็บไฟล์ชั่วคราวได้ กรุณาลองใหม่อีกครั้ง']);
        }

        try {
            $realPath = Storage::path($path);
            $reader = IOFactory::createReaderForFile($realPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($realPath);
            $sheetNames = $spreadsheet->getSheetNames();
            $spreadsheet->disconnectWorksheets();
        } catch (\Throwable $e) {
            Storage::delete($path);
            report($e);

            return back()->withErrors(['file' => 'ไม่สามารถอ่านไฟล์ Excel นี้ได้ กรุณาตรวจสอบรูปแบบไฟล์แล้วลองใหม่']);
        }

        $request->session()->put($this->importSessionKey($uploadToken), [
            'path' => $path,
            'user_id' => (int) $request->user()->id,
            'flock_id' => (int) $flock->id,
            'created_at' => time(),
        ]);

        return view('slaughter-records.import_step2', compact(
            'flock',
            'uploadToken',
            'sheetNames'
        ));
    }

    public function handleConfig(Request $request, Flock $flock): View|RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว');

        $validated = $request->validate([
            'upload_token' => ['required', 'uuid'],
            'sheet_name' => ['required', 'string', 'max:255'],
            'start_row' => ['required', 'integer', 'min:1', 'max:'.self::MAX_IMPORT_ROWS],
            'col_house' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_birds' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_weight' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_doa' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_net' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_condemned' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_condemned_percent' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_problem' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_problem_percent' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
            'col_dead_weight' => ['required', 'regex:/^[A-Za-z]{1,3}$/'],
        ]);

        $uploadToken = $validated['upload_token'];
        $tempPath = $this->validatedImportPath($request, $flock, $uploadToken);

        if ($tempPath === null) {
            return redirect()
                ->route('flocks.slaughter-records.upload', $flock)
                ->withErrors(['file' => 'ไฟล์ชั่วคราวหมดอายุหรือถูกลบแล้ว กรุณาอัปโหลดใหม่อีกครั้ง']);
        }

        $realPath = Storage::path($tempPath);
        $spreadsheet = null;

        try {
            $reader = IOFactory::createReaderForFile($realPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($realPath);
            $sheet = $spreadsheet->getSheetByName($validated['sheet_name']);
            if (!$sheet) {
                return redirect()
                    ->route('flocks.slaughter-records.upload', $flock)
                    ->withErrors(['file' => 'ไม่พบ Sheet ชื่อที่เลือกในไฟล์ Excel กรุณาอัปโหลดและเลือก Sheet ใหม่อีกครั้ง']);
            }

            $highestRow = $sheet->getHighestRow();
            $startRow = (int) $validated['start_row'];

            if ($highestRow > self::MAX_IMPORT_ROWS) {
                return redirect()
                    ->route('flocks.slaughter-records.upload', $flock)
                    ->withErrors(['file' => 'ไฟล์มีข้อมูลเกิน '.number_format(self::MAX_IMPORT_ROWS).' แถว กรุณาแบ่งไฟล์แล้วนำเข้าใหม่']);
            }

            $columns = collect([
                'house' => $validated['col_house'],
                'birds' => $validated['col_birds'],
                'weight' => $validated['col_weight'],
                'doa' => $validated['col_doa'],
                'net' => $validated['col_net'],
                'condemned' => $validated['col_condemned'],
                'condemned_percent' => $validated['col_condemned_percent'],
                'problem' => $validated['col_problem'],
                'problem_percent' => $validated['col_problem_percent'],
                'dead_weight' => $validated['col_dead_weight'],
            ])->map(fn (string $column) => strtoupper($column));

            $parsedRows = [];
            $flock->load('flockHouseStarts.house');
            $availableHouses = $flock->flockHouseStarts->map(fn($s) => $s->house)->sortBy('house_no');
            $nextSequence = ((int) FlockSlaughterRecord::query()
                ->where('flock_id', $flock->id)
                ->max('sequence')) + 1;

            for ($row = $startRow; $row <= $highestRow; $row++) {
                // Get values
                $rawHouse = trim((string) $sheet->getCell($columns['house'].$row)->getValue());
                
                // If rawHouse is empty, skip this row (usually blank row at the bottom)
                if (empty($rawHouse)) {
                    continue;
                }

                $birds = (int) $sheet->getCell($columns['birds'].$row)->getValue();
                $weight = (float) $sheet->getCell($columns['weight'].$row)->getValue();
                $doa = (int) $sheet->getCell($columns['doa'].$row)->getValue();
                $net = (int) $sheet->getCell($columns['net'].$row)->getValue();
                $condemned = (int) $sheet->getCell($columns['condemned'].$row)->getValue();
                $condemned_percent = (float) $sheet->getCell($columns['condemned_percent'].$row)->getValue();
                $problem = (int) $sheet->getCell($columns['problem'].$row)->getValue();
                $problem_percent = (float) $sheet->getCell($columns['problem_percent'].$row)->getValue();
                $dead_weight = (float) $sheet->getCell($columns['dead_weight'].$row)->getValue();

                // Simple house matcher logic: e.g. "เล้า 1" -> 1, "1001" -> 1, "1012" -> 12
                $matchedHouseId = null;
                $cleanedHouseName = preg_replace('/[^0-9]/', '', $rawHouse);
                if (!empty($cleanedHouseName)) {
                    $houseNoInt = (int)$cleanedHouseName;
                    
                    // If it is in 1000s format (e.g., 1001, 1002, 1012...)
                    if ($houseNoInt >= 1001 && $houseNoInt < 1100) {
                        $houseNoInt = $houseNoInt % 1000;
                    }
                    
                    foreach ($availableHouses as $h) {
                        if ((int)$h->house_no === $houseNoInt) {
                            $matchedHouseId = $h->id;
                            break;
                        }
                    }
                }

                $sequence = $nextSequence + count($parsedRows);
                $catchRecord = $matchedHouseId
                    ? $this->catchRecordForImportRow($flock, (int) $matchedHouseId, $sequence)
                    : null;

                $parsedRows[] = [
                    'sequence' => $sequence,
                    'catch_date' => $catchRecord?->catch_date?->toDateString(),
                    'raw_house_name' => $rawHouse,
                    'matched_house_id' => $matchedHouseId,
                    'slaughter_birds' => $birds,
                    'actual_weight' => $weight,
                    'doa_birds' => $doa,
                    'net_birds' => $net,
                    'condemned_birds' => $condemned,
                    'condemned_percent' => $condemned_percent,
                    'problem_birds' => $problem,
                    'problem_percent' => $problem_percent,
                    'dead_weight' => $dead_weight,
                ];
            }

            if (empty($parsedRows)) {
                return redirect()
                    ->route('flocks.slaughter-records.upload', $flock)
                    ->withErrors(['file' => 'ไม่พบข้อมูลใน Sheet หรือช่วงแถวที่เลือก กรุณาอัปโหลดและตรวจสอบแถวเริ่มต้นใหม่อีกครั้ง']);
            }

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('flocks.slaughter-records.upload', $flock)
                ->withErrors(['file' => 'เกิดข้อผิดพลาดในการอ่านข้อมูลจาก Excel กรุณาตรวจสอบไฟล์แล้วลองใหม่']);
        } finally {
            $spreadsheet?->disconnectWorksheets();
        }

        return view('slaughter-records.import_step3', compact(
            'flock',
            'uploadToken',
            'parsedRows',
            'availableHouses'
        ));
    }

    public function configFallback(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        return redirect()
            ->route('flocks.slaughter-records.upload', $flock)
            ->withErrors(['file' => 'กรุณาอัปโหลดไฟล์ Excel ใหม่อีกครั้งก่อนตั้งค่าการดึงข้อมูล']);
    }

    public function handleImport(Request $request, Flock $flock): View|RedirectResponse
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว');

        $validator = Validator::make($request->all(), [
            'upload_token' => ['required', 'uuid'],
            'records' => ['required', 'array', 'min:1', 'max:'.self::MAX_IMPORT_ROWS],
            'records.*.house_id' => 'required|exists:houses,id',
            'records.*.sequence' => 'required|integer|min:1|max:1000000',
            'records.*.raw_house_name' => 'nullable|string|max:255',
            'records.*.slaughter_birds' => 'required|integer|min:0|max:10000000',
            'records.*.actual_weight' => 'required|numeric|min:0|max:1000000000',
            'records.*.doa_birds' => 'required|integer|min:0|max:10000000',
            'records.*.net_birds' => 'required|integer|min:0|max:10000000',
            'records.*.condemned_birds' => 'required|integer|min:0|max:10000000',
            'records.*.condemned_percent' => 'required|numeric|min:0|max:100',
            'records.*.problem_birds' => 'required|integer|min:0|max:10000000',
            'records.*.problem_percent' => 'required|numeric|min:0|max:100',
            'records.*.dead_weight' => 'required|numeric|min:0|max:1000000000',
        ], [
            'records.*.house_id.required' => 'กรุณาจับคู่เล้าในระบบให้ครบทุกแถวก่อนบันทึก',
            'records.*.house_id.exists' => 'พบเล้าที่เลือกไม่ถูกต้อง กรุณาตรวจสอบการจับคู่เล้าอีกครั้ง',
        ]);

        $allowedHouseIds = $flock->flockHouseStarts()->pluck('house_id')->all();
        $validator->after(function ($validator) use ($request, $allowedHouseIds, $flock) {
            foreach ((array) $request->input('records', []) as $index => $record) {
                if (!empty($record['house_id']) && !in_array((int) $record['house_id'], $allowedHouseIds, true)) {
                    $validator->errors()->add(
                        "records.{$index}.house_id",
                        'เล้าที่เลือกไม่อยู่ในรุ่นการเลี้ยงปัจจุบัน'
                    );
                }

                if (!empty($record['house_id']) && !empty($record['sequence'])
                    && in_array((int) $record['house_id'], $allowedHouseIds, true)
                    && !$this->catchRecordForImportRow($flock, (int) $record['house_id'], (int) $record['sequence'])) {
                    $validator->errors()->add(
                        "records.{$index}.sequence",
                        "ไม่พบบันทึกจับไก่ของคันที่ {$record['sequence']} ในเล้าที่เลือก กรุณาบันทึกข้อมูลจับไก่ก่อนนำเข้าโรงงาน"
                    );
                }
            }
        });

        if ($validator->fails()) {
            return $this->importValidationView($request, $flock, $validator);
        }

        $validated = $validator->validated();
        $uploadToken = $validated['upload_token'];
        $tempPath = $this->validatedImportPath($request, $flock, $uploadToken);

        if ($tempPath === null) {
            return redirect()
                ->route('flocks.slaughter-records.upload', $flock)
                ->withErrors(['file' => 'ไฟล์ชั่วคราวหมดอายุหรือไม่ใช่ไฟล์ของผู้ใช้ กรุณาอัปโหลดใหม่อีกครั้ง']);
        }

        DB::transaction(function () use ($validated, $flock, $user): void {
            foreach ($validated['records'] as $record) {
                $catchRecord = $this->catchRecordForImportRow(
                    $flock,
                    (int) $record['house_id'],
                    (int) $record['sequence']
                );

                FlockSlaughterRecord::create([
                    'farm_id' => $flock->farm_id,
                    'flock_id' => $flock->id,
                    'house_id' => $record['house_id'],
                    'slaughter_date' => $catchRecord->catch_date->toDateString(),
                    'sequence' => $record['sequence'],
                    'raw_house_name' => $record['raw_house_name'] ?? null,
                    'slaughter_birds' => $record['slaughter_birds'],
                    'actual_weight' => $record['actual_weight'],
                    'doa_birds' => $record['doa_birds'],
                    'net_birds' => $record['net_birds'],
                    'condemned_birds' => $record['condemned_birds'],
                    'condemned_percent' => $record['condemned_percent'],
                    'problem_birds' => $record['problem_birds'],
                    'problem_percent' => $record['problem_percent'],
                    'dead_weight' => $record['dead_weight'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        });

        // Clean up temporary file
        Storage::delete($tempPath);
        $request->session()->forget($this->importSessionKey($uploadToken));

        return redirect()
            ->route('flocks.slaughter-records.index', $flock)
            ->with('status', 'นำเข้าข้อมูลไก่เข้าเชือด นน.หน้าโรงงาน เรียบร้อยแล้ว');
    }

    private function importValidationView(Request $request, Flock $flock, $errors): View
    {
        $flock->load('flockHouseStarts.house');

        $availableHouses = $flock->flockHouseStarts
            ->map(fn ($start) => $start->house)
            ->filter()
            ->sortBy('house_no')
            ->values();

        $uploadToken = (string) $request->input('upload_token', '');
        $parsedRows = collect($request->input('records', []))
            ->map(function ($record, $index) use ($flock) {
                $catchRecord = (!empty($record['house_id']) && !empty($record['sequence']))
                    ? $this->catchRecordForImportRow($flock, (int) $record['house_id'], (int) $record['sequence'])
                    : null;

                return [
                    'sequence' => $record['sequence'] ?? ($index + 1),
                    'catch_date' => $catchRecord?->catch_date?->toDateString(),
                    'raw_house_name' => $record['raw_house_name'] ?? '',
                    'matched_house_id' => $record['house_id'] ?? null,
                    'slaughter_birds' => $record['slaughter_birds'] ?? 0,
                    'actual_weight' => $record['actual_weight'] ?? 0,
                    'doa_birds' => $record['doa_birds'] ?? 0,
                    'net_birds' => $record['net_birds'] ?? 0,
                    'condemned_birds' => $record['condemned_birds'] ?? 0,
                    'condemned_percent' => $record['condemned_percent'] ?? 0,
                    'problem_birds' => $record['problem_birds'] ?? 0,
                    'problem_percent' => $record['problem_percent'] ?? 0,
                    'dead_weight' => $record['dead_weight'] ?? 0,
                ];
            })
            ->values()
            ->all();

        return view('slaughter-records.import_step3', compact(
            'flock',
            'uploadToken',
            'parsedRows',
            'availableHouses'
        ))->withErrors($errors);
    }

    private function catchRecordForImportRow(Flock $flock, int $houseId, int $sequence): ?FlockCatchRecord
    {
        return FlockCatchRecord::query()
            ->where('flock_id', $flock->id)
            ->where('house_id', $houseId)
            ->where('sequence', $sequence)
            ->first();
    }

    private function validatedImportPath(Request $request, Flock $flock, string $uploadToken): ?string
    {
        if (! Str::isUuid($uploadToken)) {
            return null;
        }

        $sessionKey = $this->importSessionKey($uploadToken);
        $metadata = $request->session()->get($sessionKey);

        if (! is_array($metadata)
            || (int) ($metadata['user_id'] ?? 0) !== (int) $request->user()->id
            || (int) ($metadata['flock_id'] ?? 0) !== (int) $flock->id) {
            return null;
        }

        $path = (string) ($metadata['path'] ?? '');
        $expectedPrefix = 'temp/'.$request->user()->id.'/'.$flock->id.'/';

        if (! str_starts_with($path, $expectedPrefix)) {
            return null;
        }

        if (time() - (int) ($metadata['created_at'] ?? 0) > self::TEMP_IMPORT_TTL_SECONDS) {
            Storage::delete($path);
            $request->session()->forget($sessionKey);

            return null;
        }

        return Storage::exists($path) ? $path : null;
    }

    private function importSessionKey(string $uploadToken): string
    {
        return 'slaughter_imports.'.$uploadToken;
    }

    public function destroy(Request $request, Flock $flock, FlockSlaughterRecord $slaughterRecord): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_unless((int)$slaughterRecord->flock_id === (int)$flock->id, 404);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถลบข้อมูลได้');

        $slaughterRecord->delete();

        return redirect()
            ->route('flocks.slaughter-records.index', $flock)
            ->with('status', 'ลบรายการข้อมูลไก่เข้าเชือดเรียบร้อยแล้ว');
    }

    public function shortcut(Request $request): RedirectResponse
    {
        $flock = FarmAccess::activeFlockFor($request->user());

        if (!$flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนเข้าดูข้อมูลไก่เข้าเชือด');
        }

        return redirect()->route('flocks.slaughter-records.index', $flock);
    }

    public function destroyByFlock(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'รุ่นการเลี้ยงนี้ถูกปิดไปแล้ว ไม่สามารถลบข้อมูลได้');

        $count = FlockSlaughterRecord::where('flock_id', $flock->id)->count();
        FlockSlaughterRecord::where('flock_id', $flock->id)->delete();

        return redirect()
            ->route('flocks.slaughter-records.index', $flock)
            ->with('status', "ลบข้อมูลไก่เข้าเชือดทั้งหมดของรุ่นนี้เรียบร้อยแล้ว ({$count} รายการ)");
    }
}
