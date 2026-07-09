<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\Farm;
use App\Models\ChickPriceMaster;
use App\Models\FeedIntakeMaster;
use App\Models\FeedPriceMaster;
use App\Models\FeedReceiptHouseItem;
use App\Models\Flock;
use App\Models\FlockCatchRecord;
use App\Models\FlockCatchTeamCost;
use App\Models\FlockHousePlacement;
use App\Models\FlockProductionReportAdjustment;
use App\Models\FlockSlaughterRecord;
use App\Models\SalePriceMaster;
use App\Support\FarmAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FlockProductionReportController extends Controller
{
    public function show(Request $request, Flock $flock)
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        return view('production-reports.show', $this->reportData($request, $flock));
    }

    public function updateAdjustments(Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        $validated = $request->validate([
            'adjustments' => ['nullable', 'array'],
            'adjustments.*.amount' => ['nullable', 'numeric'],
            'adjustments.*.note' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $items = [];
        foreach ($this->manualAdjustmentDefinitions() as $key => $label) {
            $items[$key] = [
                'label' => $label,
                'amount' => round((float) data_get($validated, "adjustments.{$key}.amount", 0), 2),
                'note' => (string) data_get($validated, "adjustments.{$key}.note", ''),
            ];
        }

        FlockProductionReportAdjustment::query()->updateOrCreate(
            ['flock_id' => $flock->id],
            [
                'items' => $items,
                'note' => $validated['note'] ?? null,
                'created_by' => $request->user()?->id,
                'updated_by' => $request->user()?->id,
            ]
        );

        return redirect()
            ->route('flocks.production-report.show', $flock)
            ->with('status', 'บันทึกค่ากรอกเองของรายงานเรียบร้อยแล้ว');
    }

    public function pdf(Request $request, Flock $flock): Response
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        $data = $this->reportData($request, $flock);
        $data['generatedAt'] = now();

        $html = view('production-reports.pdf', $data)->render();

        if (! is_dir(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'garuda',
            'tempDir' => storage_path('app/mpdf'),
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 7,
            'margin_bottom' => 7,
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetTitle('สรุปผลการเลี้ยง รุ่น '.$flock->flock_code);
        $mpdf->WriteHTML($html);

        $fileName = 'production-report-flock-'.$flock->id.'.pdf';

        return response($mpdf->Output($fileName, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ]);
    }

    public function excel(Request $request, Flock $flock): StreamedResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        $data = $this->reportData($request, $flock);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Production Report');

        $headers = [
            'เล้า', 'วันลงไก่', 'วันจับไก่', 'อายุจับ', 'เพศ', 'เกรด', 'พันธุ์', 'แหล่งลูกไก่',
            'จน.ไก่ลง+แถม', 'ไก่จับ', 'นน.รวม', 'นน.เฉลี่ย', '%รอด', 'อาหารใช้',
            'FCR', 'PI', 'นน./พื้นที่', 'ตายขนส่ง', '%ตายขนส่ง', '%ตกกราว', '%อุ้งตีน', 'พรีเมียมอุ้งตีน',
        ];

        $sheet->mergeCells('A1:V1');
        $sheet->setCellValue('A1', $data['title']);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->fromArray($headers, null, 'A3');
        $rowNumber = 4;
        foreach ($data['rows'] as $row) {
            $sheet->fromArray([
                $row['house_no'],
                $row['placement_date_text'],
                $row['catch_date_text'],
                $row['age'],
                $row['sex'],
                $row['grade'],
                $row['breed'],
                $row['chick_source'],
                $row['initial_birds'],
                $row['net_birds'],
                $row['total_weight'],
                $row['avg_weight'],
                $row['survival_rate'],
                $row['feed_used'],
                $row['fcr'],
                $row['pi'],
                $row['weight_per_area'],
                $row['doa_birds'],
                $row['doa_rate'],
                $row['condemned_rate'],
                $row['problem_rate'],
                $row['premium_base'],
            ], null, 'A'.$rowNumber);
            $rowNumber++;
        }

        $sheet->fromArray([
            'รวม', '', '', $data['totals']['age'], '', '', '', '',
            $data['totals']['initial_birds'],
            $data['totals']['net_birds'],
            $data['totals']['total_weight'],
            $data['totals']['avg_weight'],
            $data['totals']['survival_rate'],
            $data['totals']['feed_used'],
            $data['totals']['fcr'],
            $data['totals']['pi'],
            $data['totals']['weight_per_area'],
            $data['totals']['doa_birds'],
            $data['totals']['doa_rate'],
            $data['totals']['condemned_rate'],
            $data['totals']['problem_rate'],
            $data['totals']['premium_base'],
        ], null, 'A'.$rowNumber);
        $sheet->getStyle('A'.$rowNumber.':V'.$rowNumber)->getFont()->setBold(true);

        $financeStart = $rowNumber + 3;
        $sheet->setCellValue('A'.$financeStart, 'สรุปการเงิน');
        $sheet->getStyle('A'.$financeStart)->getFont()->setBold(true);
        $financeStart++;
        foreach ($data['financeRows'] as $financeRow) {
            $sheet->fromArray([$financeRow['label'], $financeRow['amount'], $financeRow['per_bird'], $financeRow['note']], null, 'A'.$financeStart);
            $financeStart++;
        }

        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A3:V'.$rowNumber)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A3:V3')->getFont()->setBold(true);

        $fileName = 'production-report-flock-'.$flock->id.'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function shortcut(Request $request): RedirectResponse
    {
        $flock = FarmAccess::activeFlockFor($request->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนดูรายงานสรุปผลการเลี้ยง');
        }

        return redirect()->route('flocks.production-report.show', $flock);
    }

    private function reportData(Request $request, Flock $flock): array
    {
        $user = $request->user();
        $flock->load(['farm', 'flockHouseStarts.house', 'flockHousePlacements']);

        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();

        $farms = FarmAccess::farmsQuery($user)
            ->whereIn('id', $flockOptions->pluck('farm_id')->push($flock->farm_id)->unique())
            ->orderBy('farm_name')
            ->get();

        $reportSelectorFlocks = $flockOptions
            ->map(fn (Flock $option) => [
                'value' => (string) $option->id,
                'text' => $option->flock_code.($option->farm ? ' - '.$option->farm->farm_name : ''),
                'farmId' => (string) $option->farm_id,
                'url' => route('flocks.production-report.show', $option),
                'selected' => (int) $option->id === (int) $flock->id,
            ])
            ->values();

        $starts = $flock->flockHouseStarts->sortBy('house.house_no')->values();
        $houseIds = $starts->pluck('house_id')->all();
        $placements = $flock->flockHousePlacements->groupBy('house_id');
        $slaughterRecords = FlockSlaughterRecord::query()->where('flock_id', $flock->id)->get()->groupBy('house_id');
        $catchRecords = FlockCatchRecord::query()->where('flock_id', $flock->id)->get()->groupBy('house_id');
        $teamCosts = FlockCatchTeamCost::query()->where('flock_id', $flock->id)->get();
        $catchDatesByHouse = $catchRecords->map(fn (Collection $records) => $records->pluck('catch_date')->filter()->sort()->last());
        $dailyRecords = DailyHouseRecord::query()
            ->where('flock_id', $flock->id)
            ->get()
            ->groupBy('house_id')
            ->map(function (Collection $records, int $houseId) use ($catchDatesByHouse) {
                $catchDate = $catchDatesByHouse->get($houseId);

                if (! $catchDate) {
                    return $records;
                }

                $catchDateStr = CarbonImmutable::parse($catchDate)->toDateString();

                return $records
                    ->filter(fn (DailyHouseRecord $record) => $record->record_date?->toDateString() <= $catchDateStr)
                    ->values();
            });

        $farmArea = (float) ($flock->farm?->rearing_area ?? 0);
        $areaPerHouse = $farmArea > 0 && $starts->count() > 0 ? $farmArea / $starts->count() : 0.0;

        $rows = [];
        foreach ($starts as $start) {
            $housePlacements = $placements->get($start->house_id, collect());
            $houseDaily = $dailyRecords->get($start->house_id, collect());
            $houseSlaughters = $slaughterRecords->get($start->house_id, collect());
            $houseCatches = $catchRecords->get($start->house_id, collect());

            $placementDate = $housePlacements->pluck('placement_date')->filter()->sort()->first() ?: ($start->start_date ?: $flock->start_date);
            $catchDate = $housePlacements->pluck('catch_date')->filter()->sort()->last()
                ?: $houseCatches->pluck('catch_date')->filter()->sort()->last()
                ?: $houseSlaughters->pluck('slaughter_date')->filter()->sort()->last();
            $initialBirds = (int) ($housePlacements->sum('chicks_in') ?: $start->initial_birds);
            $age = (int) ($housePlacements->pluck('catch_age')->filter()->max() ?: $this->ageBetween($placementDate, $catchDate));
            $netBirds = (int) $houseSlaughters->sum('net_birds');
            $totalWeight = (float) $houseSlaughters->sum('actual_weight');
            $avgWeight = $netBirds > 0 ? $totalWeight / $netBirds : 0.0;
            $survivalRate = $initialBirds > 0 ? ($netBirds / $initialBirds) * 100 : 0.0;
            $feedUsed = (float) $houseDaily->sum('feed_used');
            $fcr = $totalWeight > 0 ? $feedUsed / $totalWeight : 0.0;
            $pi = ($fcr > 0 && $age > 0) ? ($survivalRate * $avgWeight * 100) / ($fcr * $age) : 0.0;
            $doaBirds = (int) $houseSlaughters->sum('doa_birds');
            $condemnedBirds = (int) $houseSlaughters->sum('condemned_birds');
            $problemBirds = (int) $houseSlaughters->sum('problem_birds');
            $premiumBase = max(0, $netBirds - $doaBirds);

            $rows[] = [
                'house_no' => $start->house?->house_no,
                'placement_date' => $placementDate,
                'placement_date_text' => $placementDate ? thai_date($placementDate) : '-',
                'catch_date' => $catchDate,
                'catch_date_text' => $catchDate ? thai_date($catchDate) : '-',
                'age' => $age,
                'sex' => $this->joinUnique($housePlacements, 'sex'),
                'grade' => $this->joinUnique($housePlacements, 'chick_grade'),
                'breed' => $this->joinUnique($housePlacements, 'breed'),
                'chick_source' => $this->joinUnique($housePlacements, 'chick_source'),
                'initial_birds' => $initialBirds,
                'net_birds' => $netBirds,
                'total_weight' => $totalWeight,
                'avg_weight' => $avgWeight,
                'survival_rate' => $survivalRate,
                'feed_used' => $feedUsed,
                'fcr' => $fcr,
                'pi' => $pi,
                'weight_per_area' => $areaPerHouse > 0 ? $totalWeight / $areaPerHouse : 0.0,
                'doa_birds' => $doaBirds,
                'doa_rate' => $netBirds > 0 ? ($doaBirds / $netBirds) * 100 : 0.0,
                'condemned_rate' => $netBirds > 0 ? ($condemnedBirds / $netBirds) * 100 : 0.0,
                'problem_rate' => $netBirds > 0 ? ($problemBirds / $netBirds) * 100 : 0.0,
                'premium_base' => $premiumBase,
            ];
        }

        $rows = collect($rows);
        $totals = $this->totals($rows, $areaPerHouse);
        $standards = $this->standards($totals, $rows);
        $financeRows = $this->financeRows($flock, $houseIds, $dailyRecords, $catchRecords, $teamCosts, $totals);

        return [
            'flock' => $flock,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'reportSelectorFlocks' => $reportSelectorFlocks,
            'title' => 'สรุปผลการเลี้ยง '.$flock->farm->farm_name.' รุ่น '.$flock->flock_code,
            'rows' => $rows,
            'totals' => $totals,
            'standards' => $standards,
            'financeRows' => $financeRows,
            'adjustment' => FlockProductionReportAdjustment::query()->where('flock_id', $flock->id)->first(),
            'manualDefinitions' => $this->manualAdjustmentDefinitions(),
        ];
    }

    private function totals(Collection $rows, float $areaPerHouse): array
    {
        $initialBirds = (int) $rows->sum('initial_birds');
        $netBirds = (int) $rows->sum('net_birds');
        $totalWeight = (float) $rows->sum('total_weight');
        $feedUsed = (float) $rows->sum('feed_used');
        $age = $initialBirds > 0 ? $rows->sum(fn ($row) => $row['age'] * $row['initial_birds']) / $initialBirds : 0.0;
        $avgWeight = $netBirds > 0 ? $totalWeight / $netBirds : 0.0;
        $survivalRate = $initialBirds > 0 ? ($netBirds / $initialBirds) * 100 : 0.0;
        $fcr = $totalWeight > 0 ? $feedUsed / $totalWeight : 0.0;
        $pi = ($fcr > 0 && $age > 0) ? ($survivalRate * $avgWeight * 100) / ($fcr * $age) : 0.0;
        $doaBirds = (int) $rows->sum('doa_birds');
        $premiumBase = (int) $rows->sum('premium_base');
        $condemnedWeighted = $netBirds > 0 ? ($rows->sum(fn ($row) => $row['condemned_rate'] * $row['net_birds']) / $netBirds) : 0.0;
        $problemWeighted = $netBirds > 0 ? ($rows->sum(fn ($row) => $row['problem_rate'] * $row['net_birds']) / $netBirds) : 0.0;

        return [
            'age' => $age,
            'initial_birds' => $initialBirds,
            'net_birds' => $netBirds,
            'total_weight' => $totalWeight,
            'avg_weight' => $avgWeight,
            'survival_rate' => $survivalRate,
            'feed_used' => $feedUsed,
            'feed_per_bird' => $initialBirds > 0 ? $feedUsed / $initialBirds : 0.0,
            'fcr' => $fcr,
            'pi' => $pi,
            'weight_per_area' => ($areaPerHouse > 0 && $rows->count() > 0) ? $totalWeight / ($areaPerHouse * $rows->count()) : 0.0,
            'doa_birds' => $doaBirds,
            'doa_rate' => $netBirds > 0 ? ($doaBirds / $netBirds) * 100 : 0.0,
            'condemned_rate' => $condemnedWeighted,
            'problem_rate' => $problemWeighted,
            'premium_base' => $premiumBase,
        ];
    }

    private function standards(array $totals, Collection $rows): array
    {
        $age = (int) round($totals['age']);
        $standard = FeedIntakeMaster::query()->where('age', $age)->first();
        $sexKey = 'ah';
        $sexText = $rows->pluck('sex')->implode(',');
        if (str_contains($sexText, 'ผู้') || str_contains(strtoupper($sexText), 'M')) {
            $sexKey = 'male';
        }
        if (str_contains($sexText, 'เมีย') || str_contains(strtoupper($sexText), 'FM')) {
            $sexKey = 'female';
        }

        $stdFeed = (float) ($standard?->{"cum_feed_{$sexKey}"} ?? 0);
        if ($stdFeed > 20) {
            $stdFeed = $stdFeed / 1000;
        }
        $stdWeight = (float) ($standard?->{"weight_{$sexKey}"} ?? 0);
        $stdSurvival = 100 - (float) ($standard?->{"mortality_{$sexKey}"} ?? 0);
        $stdFcr = (float) ($standard?->{"fcr_{$sexKey}"} ?? 0);
        $stdPi = (float) ($standard?->{"pi_{$sexKey}"} ?? 0);

        return [
            ['label' => 'อายุ', 'actual' => $totals['age'], 'std' => $age, 'percent' => $age > 0 ? ($totals['age'] / $age) * 100 : 0],
            ['label' => 'อาหาร', 'actual' => $totals['feed_per_bird'], 'std' => $stdFeed, 'percent' => $stdFeed > 0 ? ($totals['feed_per_bird'] / $stdFeed) * 100 : 0],
            ['label' => 'เลี้ยงรอด(%)', 'actual' => $totals['survival_rate'], 'std' => $stdSurvival, 'percent' => $stdSurvival > 0 ? ($totals['survival_rate'] / $stdSurvival) * 100 : 0],
            ['label' => 'นน.เฉลี่ย', 'actual' => $totals['avg_weight'], 'std' => $stdWeight, 'percent' => $stdWeight > 0 ? ($totals['avg_weight'] / $stdWeight) * 100 : 0],
            ['label' => 'FCR', 'actual' => $totals['fcr'], 'std' => $stdFcr, 'percent' => $stdFcr > 0 ? ($totals['fcr'] / $stdFcr) * 100 : 0],
            ['label' => 'PI', 'actual' => $totals['pi'], 'std' => $stdPi, 'percent' => $stdPi > 0 ? ($totals['pi'] / $stdPi) * 100 : 0],
        ];
    }

    private function financeRows(Flock $flock, array $houseIds, Collection $dailyRecords, Collection $catchRecords, Collection $teamCosts, array $totals): Collection
    {
        $adjustment = FlockProductionReportAdjustment::query()->where('flock_id', $flock->id)->first();
        $manualItems = collect($adjustment?->items ?: []);
        $priceDate = FlockSlaughterRecord::query()->where('flock_id', $flock->id)->max('slaughter_date') ?: now()->toDateString();
        $salePrice = (float) (SalePriceMaster::query()
            ->where(fn ($query) => $query->whereNull('farm_id')->orWhere('farm_id', $flock->farm_id))
            ->where('is_active', true)
            ->whereDate('effective_date', '<=', $priceDate)
            ->orderByDesc('effective_date')
            ->value('price_per_kg') ?? 0);

        $feedCost = 0.0;
        foreach ($dailyRecords->flatten() as $record) {
            $feedCost += (float) $record->feed_used * $this->feedPrice((string) $record->feed_code, $record->record_date?->toDateString() ?: $priceDate);
        }

        $chickCost = $this->chickCost($flock);
        $catchingFee = (float) $catchRecords->flatten()->sum('catching_fee');
        $catchingExtra = (float) $teamCosts->sum(fn ($row) => (float) $row->fuel_cost + (float) $row->forklift_cost);
        $saleIncome = $totals['total_weight'] * $salePrice;
        $footpadPremium = (float) $totals['premium_base'];
        $lrPremium = (float) $totals['total_weight'] * 0.25;

        $rows = collect([
            ['label' => 'เงินได้จากค่าขายไก่', 'amount' => $saleIncome, 'type' => 'income', 'note' => 'น้ำหนักรวม x ราคาขายล่าสุด'],
            ['label' => 'ต้นทุนค่าลูกไก่+ขนส่ง', 'amount' => -$chickCost, 'type' => 'cost', 'note' => 'จากข้อมูลลงไก่รายเล้า'],
            ['label' => 'ต้นทุนค่ารถจับไก่', 'amount' => -($catchingFee + $catchingExtra), 'type' => 'cost', 'note' => 'ค่าจับ + ค่าน้ำมัน + ค่ารถยก'],
            ['label' => 'ต้นทุนค่าอาหาร', 'amount' => -$feedCost, 'type' => 'cost', 'note' => 'อาหารใช้ x ราคาอาหาร'],
            ['label' => 'พรีเมียมอุ้งตีน', 'amount' => $footpadPremium, 'type' => 'income', 'per_bird_denominator' => $totals['initial_birds'], 'note' => 'จำนวนรวมพรีเมียมอุ้งตีน / ไก่ลง+แถม'],
            ['label' => 'พรีเมียม LR', 'amount' => $lrPremium, 'type' => 'income', 'per_bird_denominator' => $totals['initial_birds'], 'note' => '0.25 x นน.รวม และหารไก่ลง+แถม'],
        ]);

        foreach ($this->manualAdjustmentDefinitions() as $key => $label) {
            $item = $manualItems->get($key, []);
            $rows->push([
                'key' => $key,
                'label' => $label,
                'amount' => (float) ($item['amount'] ?? 0),
                'type' => 'manual',
                'note' => (string) ($item['note'] ?? 'กรอกเอง'),
            ]);
        }

        $total = (float) $rows->sum('amount');
        $rows->push(['label' => 'เงินโอนประมาณ', 'amount' => $total, 'type' => 'total', 'note' => 'รวมรายรับและต้นทุนทั้งหมด']);

        return $rows->map(function ($row) use ($totals) {
            $denominator = (float) ($row['per_bird_denominator'] ?? $totals['net_birds']);
            $row['per_bird'] = $denominator > 0 ? $row['amount'] / $denominator : 0.0;
            return $row;
        });
    }

    private function manualAdjustmentDefinitions(): array
    {
        return [
            'quality_penalty' => 'ถูกหักค่าปรับคุณภาพ',
            'accuracy_bonus' => 'พิเศษความแม่นยำ',
            'data_bonus' => 'พิเศษการบันทึกข้อมูล',
        ];
    }

    private function feedPrice(string $feedCode, string $date): float
    {
        $normalized = rtrim(trim($feedCode), 'tT');
        if ($normalized === '') {
            return 0.0;
        }

        return (float) (FeedPriceMaster::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->where('feed_code', $normalized)->orWhere('feed_code', $normalized.'T'))
            ->whereDate('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->value('price_per_kg') ?? 0);
    }

    private function chickCost(Flock $flock): float
    {
        $placements = FlockHousePlacement::query()
            ->where('flock_id', $flock->id)
            ->get();

        $enteredAmount = (float) $placements->sum('amount');

        if ($enteredAmount > 0) {
            return $enteredAmount;
        }

        return (float) $placements->sum(fn (FlockHousePlacement $placement) => $this->calculatedChickCost($placement));
    }

    private function calculatedChickCost(FlockHousePlacement $placement): float
    {
        $placementDate = $placement->placement_date?->toDateString() ?: now()->toDateString();
        $cost = 0.0;

        $cost += (int) $placement->male_grade_a_count * $this->chickPrice('ผู้', 'A', $placementDate);
        $cost += (int) $placement->male_grade_b_count * $this->chickPrice('ผู้', 'B', $placementDate);
        $cost += (int) $placement->female_grade_a_count * $this->chickPrice('เมีย', 'A', $placementDate);
        $cost += (int) $placement->female_grade_b_count * $this->chickPrice('เมีย', 'B', $placementDate);

        if ($cost > 0) {
            return $cost;
        }

        $sex = in_array($placement->sex, ['ผู้', 'เมีย', 'คละ'], true) ? $placement->sex : 'คละ';
        $grades = collect(explode(',', (string) $placement->chick_grade))
            ->map(fn (string $grade) => trim($grade))
            ->filter(fn (string $grade) => in_array($grade, ['A', 'B'], true))
            ->values();

        if ($grades->isEmpty()) {
            $grades = collect(['A']);
        }

        $quantityPerGrade = (int) $placement->chicks_in / $grades->count();

        return (float) $grades->sum(fn (string $grade) => $quantityPerGrade * $this->chickPrice($sex, $grade, $placementDate));
    }

    private function chickPrice(string $sex, string $grade, string $date): float
    {
        return (float) (ChickPriceMaster::query()
            ->where('is_active', true)
            ->where('sex', $sex)
            ->where('grade', $grade)
            ->whereDate('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->value('price_per_bird') ?? 0);
    }

    private function ageBetween(mixed $startDate, mixed $endDate): int
    {
        if (! $startDate || ! $endDate) {
            return 0;
        }

        return (int) CarbonImmutable::parse($startDate)->diffInDays(CarbonImmutable::parse($endDate));
    }

    private function joinUnique(Collection $items, string $field): string
    {
        return $items->pluck($field)->filter()->unique()->implode(', ') ?: '-';
    }
}
