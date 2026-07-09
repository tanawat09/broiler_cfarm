<?php

namespace App\Http\Controllers;

use App\Models\Flock;
use App\Models\Farm;
use App\Models\ChickSource;
use App\Models\FlockHousePlacement;
use App\Models\FlockHouseStart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\FarmAccess;

class FlockPlacementController extends Controller
{
    public function shortcut()
    {
        $flock = FarmAccess::activeFlockFor(request()->user());
        if ($flock) {
            return redirect()->route('flocks.placements.index', $flock);
        }

        $farms = FarmAccess::farmsQuery(request()->user())->orderBy('farm_name')->get();

        return view('flocks.placements_create', compact('farms'));
    }

    public function createFlockForm()
    {
        $farms = FarmAccess::farmsQuery(request()->user())->orderBy('farm_name')->get();

        return view('flocks.placements_create', compact('farms'));
    }

    public function createFlock(Request $request)
    {
        $request->merge([
            'flock_code' => trim((string) $request->input('flock_code')),
            'chicken_type' => trim((string) $request->input('chicken_type')),
        ]);

        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'flock_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flocks', 'flock_code')
                    ->where(fn ($query) => $query->where('farm_id', $request->input('farm_id'))),
            ],
            'start_date' => 'required|date',
            'chicken_type' => 'required|string|max:255',
        ], [
            'flock_code.unique' => 'ฟาร์มนี้มีรุ่นการเลี้ยงนี้อยู่แล้ว กรุณาใช้รหัสรุ่นอื่น',
        ]);

        $farm = Farm::findOrFail($validated['farm_id']);
        FarmAccess::ensureFarm($request->user(), $farm);

        $flock = DB::transaction(function () use ($validated, $farm) {
            $flock = Flock::create([
                'farm_id' => $validated['farm_id'],
                'flock_code' => $validated['flock_code'],
                'chicken_type' => $validated['chicken_type'],
                'start_date' => $validated['start_date'],
                'initial_birds' => 0,
                'status' => 'active',
                'note' => null,
            ]);

            $houses = $farm->houses()->where('is_active', true)->orderBy('house_no')->get();
            foreach ($houses as $house) {
                FlockHouseStart::create([
                    'flock_id' => $flock->id,
                    'house_id' => $house->id,
                    'initial_birds' => 0,
                    'start_date' => $validated['start_date'],
                ]);

                FlockHousePlacement::create([
                    'flock_id' => $flock->id,
                    'house_id' => $house->id,
                    'placement_date' => $validated['start_date'],
                    'chicks_in' => 0,
                    'male_count' => 0,
                    'female_count' => 0,
                    'male_grade_a_count' => 0,
                    'male_grade_b_count' => 0,
                    'female_grade_a_count' => 0,
                    'female_grade_b_count' => 0,
                ]);
            }

            return $flock;
        });

        return redirect()
            ->route('flocks.placements.index', $flock)
            ->with('status', 'เปิดรุ่นการเลี้ยงและเตรียมบันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function index(Flock $flock)
    {
        FarmAccess::ensureFlock(request()->user(), $flock);

        $farms = FarmAccess::farmsQuery(request()->user())->get();
        $flockOptions = FarmAccess::flocksQuery(request()->user())
            ->with('farm')
            ->orderByDesc('start_date')
            ->get();

        $flock->load(['farm', 'flockHouseStarts.house', 'flockHousePlacements.house']);
        
        $chickSources = ChickSource::orderBy('name')->get();
        
        // Group placements by house_id
        $placementsByHouse = $flock->flockHousePlacements->groupBy('house_id');

        return view('flocks.placements', [
            'flock' => $flock,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'chickSources' => $chickSources,
            'placementsByHouse' => $placementsByHouse,
        ]);
    }

    public function store(Flock $flock, Request $request)
    {
        FarmAccess::ensureFlock($request->user(), $flock);

        $request->validate([
            'placements' => 'nullable|array',
            'placements.*.*.placement_date' => 'nullable|date',
            'placements.*.*.male_count' => 'nullable|integer|min:0',
            'placements.*.*.female_count' => 'nullable|integer|min:0',
            'placements.*.*.amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($flock, $request) {
            // Delete existing placements
            $flock->flockHousePlacements()->delete();

            $placementsData = $request->input('placements', []);
            
            foreach ($flock->flockHouseStarts as $start) {
                $houseId = $start->house_id;
                $rows = $placementsData[$houseId] ?? [];
                
                $totalHouseChicks = 0;

                foreach ($rows as $row) {
                    $maleCount = (int)($row['male_count'] ?? 0);
                    $femaleCount = (int)($row['female_count'] ?? 0);
                    $chicksIn = $maleCount + $femaleCount;
                    
                    if ($chicksIn === 0 && !empty($row['chicks_in'])) {
                        $chicksIn = (int)$row['chicks_in'];
                    }

                    if ($chicksIn > 0 || !empty($row['chick_code']) || !empty($row['chick_source'])) {
                        $totalHouseChicks += $chicksIn;

                        // Auto-assign sub-grade counts based on sex and grade
                        $grade = $row['chick_grade'] ?? 'A';
                        $sexVal = $row['sex'] ?? 'คละ';
                        
                        $maleGradeA = 0;
                        $maleGradeB = 0;
                        $femaleGradeA = 0;
                        $femaleGradeB = 0;

                        if ($sexVal === 'ผู้') {
                            if ($grade === 'A') $maleGradeA = $chicksIn;
                            else $maleGradeB = $chicksIn;
                        } elseif ($sexVal === 'เมีย') {
                            if ($grade === 'A') $femaleGradeA = $chicksIn;
                            else $femaleGradeB = $chicksIn;
                        } else {
                            // Mixed sex, split 50/50 or by input male/female count
                            if ($maleCount > 0 || $femaleCount > 0) {
                                if ($grade === 'A') {
                                    $maleGradeA = $maleCount;
                                    $femaleGradeA = $femaleCount;
                                } else {
                                    $maleGradeB = $maleCount;
                                    $femaleGradeB = $femaleCount;
                                }
                            } else {
                                // Default half male / half female Grade A
                                $half = (int)($chicksIn / 2);
                                if ($grade === 'A') {
                                    $maleGradeA = $half;
                                    $femaleGradeA = $chicksIn - $half;
                                } else {
                                    $maleGradeB = $half;
                                    $femaleGradeB = $chicksIn - $half;
                                }
                            }
                        }

                        $pDate = $row['placement_date'] ?: $flock->start_date->toDateString();
                        if ($earliestPlacementDate === null || $pDate < $earliestPlacementDate) {
                            $earliestPlacementDate = $pDate;
                        }

                        $flock->flockHousePlacements()->create([
                            'house_id' => $houseId,
                            'placement_date' => $pDate,
                            'chicks_in' => $chicksIn,
                            'male_count' => $maleCount,
                            'female_count' => $femaleCount,
                            'male_grade_a_count' => $maleGradeA,
                            'male_grade_b_count' => $maleGradeB,
                            'female_grade_a_count' => $femaleGradeA,
                            'female_grade_b_count' => $femaleGradeB,
                            'amount' => (float)($row['amount'] ?? 0),
                            'chick_source' => $row['chick_source'] ?? null,
                            'chick_grade' => $grade,
                            'chick_code' => $row['chick_code'] ?? null,
                            'batch_no' => $row['batch_no'] ?? null,
                            'sex' => $sexVal,
                            'breed' => $row['breed'] ?? null,
                            'remarks' => $row['remarks'] ?? null,
                        ]);
                    }
                }

                // Update start initial birds and start_date (+1 day after placement) for daily loss calculations
                $startUpdateData = [
                    'initial_birds' => $totalHouseChicks
                ];
                if ($earliestPlacementDate) {
                    $startUpdateData['start_date'] = \Carbon\CarbonImmutable::parse($earliestPlacementDate)->addDay()->toDateString();
                }
                $start->update($startUpdateData);
            }

            // Recalculate total initial birds and earliest start date for the entire flock
            $totalFlockBirds = (int) $flock->flockHouseStarts()->sum('initial_birds');
            $earliestFlockStart = $flock->flockHouseStarts()->whereNotNull('start_date')->min('start_date');

            $flockUpdateData = [
                'initial_birds' => $totalFlockBirds
            ];
            if ($earliestFlockStart) {
                $flockUpdateData['start_date'] = $earliestFlockStart;
            }
            $flock->update($flockUpdateData);
        });

        return redirect()
            ->route('flocks.placements.index', $flock)
            ->with('status', 'บันทึกข้อมูลลูกไก่เรียบร้อยแล้ว');
    }
}
