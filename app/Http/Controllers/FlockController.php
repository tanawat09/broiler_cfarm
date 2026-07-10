<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlockRequest;
use App\Http\Requests\UpdateFlockRequest;
use App\Models\ChickSource;
use App\Models\Farm;
use App\Models\Flock;
use App\Models\FlockHousePlacement;
use App\Models\FlockHouseStart;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FlockController extends Controller
{
    public function index(): View
    {
        $flocks = FarmAccess::flocksQuery(request()->user())
            ->with('farm')
            ->withCount('flockHouseStarts')
            ->withSum('saleRecords as birds_sold_total', 'birds_sold')
            ->withSum('saleRecords as sale_weight_total', 'total_weight')
            ->withSum('saleRecords as sale_amount_total', 'total_amount')
            ->latest()
            ->paginate(10);

        $flocks->getCollection()->load([
            'flockHouseStarts' => fn ($query) => $query->select('id', 'flock_id', 'house_id', 'initial_birds', 'start_date'),
        ]);

        return view('flocks.index', compact('flocks'));
    }

    public function create(): View
    {
        $farms = FarmAccess::farmsQuery(request()->user())->orderBy('farm_name')->get();
        $selectedFarm = $this->selectedFarmForCreate($farms);
        $houses = $selectedFarm
            ? $selectedFarm->houses()->where('is_active', true)->orderBy('house_no')->get()
            : collect();
        $chickSources = $this->chickSourcesForForm();

        return view('flocks.create', [
            'flock' => new Flock(['chicken_type' => 'ไก่เนื้อ']),
            'farms' => $farms,
            'selectedFarm' => $selectedFarm,
            'houses' => $houses,
            'startValues' => collect(),
            'startDates' => collect(),
            'placementValues' => collect(),
            'chickSources' => $chickSources,
        ]);
    }

    public function store(StoreFlockRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $farm = Farm::query()->findOrFail($validated['farm_id']);
        FarmAccess::ensureFarm($request->user(), $farm);

        $houses = $farm->houses()->where('is_active', true)->orderBy('house_no')->get();
        $placementsByHouse = collect($validated['placements'] ?? []);
        $initialBirdsByHouse = $this->initialBirdsByHouse($houses, $placementsByHouse, collect($validated['house_initial_birds'] ?? []));
        $startDatesByHouse = collect($validated['house_start_dates'] ?? []);
        $flockStartDate = $this->flockStartDate($validated, $placementsByHouse);
        $initialBirdsTotal = $houses->sum(fn ($house) => (int) $initialBirdsByHouse->get($house->id, 0));

        $flock = DB::transaction(function () use ($validated, $houses, $initialBirdsByHouse, $startDatesByHouse, $placementsByHouse, $flockStartDate, $initialBirdsTotal) {
            $flock = Flock::query()->create([
                'farm_id' => $validated['farm_id'],
                'flock_code' => $validated['flock_code'],
                'chicken_type' => $validated['chicken_type'],
                'start_date' => $flockStartDate,
                'initial_birds' => $initialBirdsTotal,
                'status' => 'active',
                'note' => null,
            ]);

            foreach ($houses as $house) {
                $placement = collect($placementsByHouse->get((string) $house->id, []));
                $placementDate = $this->nullableDate($placement->get('placement_date'));
                $hStartDate = $placementDate
                    ? \Carbon\CarbonImmutable::parse($placementDate)->addDay()->toDateString()
                    : $this->nullableDate($startDatesByHouse->get((string) $house->id));

                FlockHouseStart::query()->create([
                    'flock_id' => $flock->id,
                    'house_id' => $house->id,
                    'initial_birds' => (int) $initialBirdsByHouse->get($house->id, 0),
                    'start_date' => $hStartDate,
                ]);

                FlockHousePlacement::query()->create($this->placementPayload($flock->id, $house->id, $placement));
            }

            return $flock;
        });

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo === 'placements') {
            return redirect()
                ->route('flocks.placements.index', $flock)
                ->with('status', 'เปิดรุ่นการเลี้ยงเรียบร้อยแล้ว');
        }

        return redirect()
            ->route('flocks.show', $flock)
            ->with('status', 'เปิดรุ่นการเลี้ยงเรียบร้อยแล้ว');
    }

    public function show(Flock $flock): View
    {
        FarmAccess::ensureFlock(request()->user(), $flock);

        $flock->load(['farm', 'flockHouseStarts.house', 'flockHousePlacements.house']);

        return view('flocks.show', compact('flock'));
    }

    public function edit(Flock $flock): View
    {
        FarmAccess::ensureFlock(request()->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'กรุณาให้ผู้ดูแลระบบปลดล็อกรุ่นก่อนแก้ไขข้อมูล');

        $flock->load('farm');
        $houses = $flock->farm->houses()->where('is_active', true)->orderBy('house_no')->get();
        $startValues = $flock->flockHouseStarts()->pluck('initial_birds', 'house_id');
        $startDates = $flock->flockHouseStarts()->pluck('start_date', 'house_id');
        $placementValues = $this->aggregatePlacementValues($flock->flockHousePlacements()->get());
        $chickSources = $this->chickSourcesForForm();

        return view('flocks.edit', [
            'flock' => $flock,
            'houses' => $houses,
            'startValues' => $startValues,
            'startDates' => $startDates,
            'placementValues' => $placementValues,
            'chickSources' => $chickSources,
        ]);
    }

    public function update(UpdateFlockRequest $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureFlock($request->user(), $flock);
        abort_if($flock->status === 'closed', 403, 'กรุณาให้ผู้ดูแลระบบปลดล็อกรุ่นก่อนแก้ไขข้อมูล');

        $validated = $request->validated();
        $flock->load('farm');
        $houses = $flock->farm->houses()->where('is_active', true)->orderBy('house_no')->get();
        $placementsByHouse = collect($validated['placements'] ?? []);
        $initialBirdsByHouse = $this->initialBirdsByHouse($houses, $placementsByHouse, collect($validated['house_initial_birds'] ?? []));
        $startDatesByHouse = collect($validated['house_start_dates'] ?? []);
        $flockStartDate = $this->flockStartDate($validated, $placementsByHouse);
        $initialBirdsTotal = $houses->sum(fn ($house) => (int) $initialBirdsByHouse->get($house->id, 0));
        $existingPlacementCounts = $flock->flockHousePlacements()
            ->select('house_id', DB::raw('COUNT(*) as row_count'))
            ->groupBy('house_id')
            ->pluck('row_count', 'house_id');

        DB::transaction(function () use ($flock, $validated, $houses, $initialBirdsByHouse, $startDatesByHouse, $placementsByHouse, $flockStartDate, $initialBirdsTotal, $existingPlacementCounts): void {
            $flock->update([
                'flock_code' => $validated['flock_code'],
                'chicken_type' => $validated['chicken_type'],
                'start_date' => $flockStartDate,
                'initial_birds' => $initialBirdsTotal,
                'note' => null,
            ]);

            foreach ($houses as $house) {
                $placement = collect($placementsByHouse->get((string) $house->id, []));
                $placementDate = $this->nullableDate($placement->get('placement_date'));

                $hStartDate = $placementDate
                    ? \Carbon\CarbonImmutable::parse($placementDate)->addDay()->toDateString()
                    : $this->nullableDate($startDatesByHouse->get((string) $house->id));

                FlockHouseStart::query()->updateOrCreate(
                    [
                        'flock_id' => $flock->id,
                        'house_id' => $house->id,
                    ],
                    [
                        'initial_birds' => (int) $initialBirdsByHouse->get($house->id, 0),
                        'start_date' => $hStartDate,
                    ],
                );

                if ((int) $existingPlacementCounts->get($house->id, 0) > 1) {
                    continue;
                }

                FlockHousePlacement::query()->updateOrCreate(
                    [
                        'flock_id' => $flock->id,
                        'house_id' => $house->id,
                    ],
                    $this->placementPayload($flock->id, $house->id, $placement),
                );
            }
        });

        return redirect()
            ->route('flocks.show', $flock)
            ->with('status', 'แก้ไขข้อมูลรุ่นการเลี้ยงเรียบร้อยแล้ว');
    }

    public function destroy(\Illuminate\Http\Request $request, Flock $flock): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        FarmAccess::ensureFlock($request->user(), $flock);

        $request->validateWithBag('flockDeletion_' . $flock->id, [
            'password' => ['required', 'current_password'],
        ]);

        $flock->delete();

        return redirect()
            ->route('flocks.index')
            ->with('status', 'ลบรุ่นการเลี้ยงเรียบร้อยแล้ว');
    }

    private function selectedFarmForCreate($farms): ?Farm
    {
        $farmId = request()->integer('farm_id');

        if ($farmId) {
            return $farms->firstWhere('id', $farmId);
        }

        return $farms->first();
    }

    private function chickSourcesForForm()
    {
        $defaultSources = collect(['BF', 'BFI', 'BF,เนืองนามฟาร์ม', 'เนืองนามฟาร์ม', 'เบทาโกร']);

        return ChickSource::query()
            ->where('is_active', true)
            ->pluck('name')
            ->merge($defaultSources)
            ->unique()
            ->sort()
            ->values()
            ->map(fn (string $name) => (object) ['name' => $name]);
    }

    private function defaultPlacementValues($houses)
    {
        $rows = collect([
            1 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-22', 'catch_age' => 44, 'chicks_in' => 36006, 'male_count' => 0, 'female_count' => 36006, 'male_grade_a_count' => 0, 'male_grade_b_count' => 0, 'female_grade_a_count' => 32946, 'female_grade_b_count' => 3060, 'amount' => 516263, 'chick_source' => 'BF,BFI,เนืองนามฟาร์ม', 'chick_grade' => 'A,B', 'chick_code' => 'AT10626940843,AP01-0333940848,AP0632940848,AP131940843,CV1264940853,CV14-1763940853,CQ01-0445940852,AL44-4541940851', 'batch_no' => '129221055015', 'sex' => 'เมีย', 'breed' => 'AA,CB'],
            2 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-20', 'catch_age' => 42, 'chicks_in' => 30600, 'male_count' => 30600, 'female_count' => 0, 'male_grade_a_count' => 30600, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 531300, 'chick_source' => 'เนืองนามฟาร์ม', 'chick_grade' => 'A', 'chick_code' => 'EU59-6236940851,AL43-4441940851', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'RS,AA'],
            3 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-22', 'catch_age' => 44, 'chicks_in' => 35190, 'male_count' => 0, 'female_count' => 35190, 'male_grade_a_count' => 0, 'male_grade_b_count' => 0, 'female_grade_a_count' => 35190, 'female_grade_b_count' => 0, 'amount' => 507495, 'chick_source' => 'เนืองนามฟาร์ม', 'chick_grade' => 'A', 'chick_code' => 'EU59-6236940851,AL43-4441940851', 'batch_no' => '129221055015', 'sex' => 'เมีย', 'breed' => 'RS,AA'],
            4 => ['placement_date' => '2026-04-09', 'catch_date' => '2026-05-21', 'catch_age' => 42, 'chicks_in' => 31110, 'male_count' => 31110, 'female_count' => 0, 'male_grade_a_count' => 31110, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 540155, 'chick_source' => 'BFI', 'chick_grade' => 'A', 'chick_code' => 'AP01-0633940948,AP07-0932940948', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'AA'],
            5 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-23', 'catch_age' => 45, 'chicks_in' => 36312, 'male_count' => 0, 'female_count' => 36312, 'male_grade_a_count' => 0, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 36312, 'amount' => 488076, 'chick_source' => 'BF', 'chick_grade' => 'B', 'chick_code' => 'AT06-1026940843', 'batch_no' => '129221055015', 'sex' => 'เมีย', 'breed' => 'AA'],
            6 => ['placement_date' => '2026-04-09', 'catch_date' => '2026-05-21', 'catch_age' => 42, 'chicks_in' => 30906, 'male_count' => 30906, 'female_count' => 0, 'male_grade_a_count' => 30906, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 536613, 'chick_source' => 'BFI', 'chick_grade' => 'A', 'chick_code' => 'CQ07-0944940952,CQ0645940952,AP09-1132940948,AP12-1331940948', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'AA,CB'],
            7 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-20', 'catch_age' => 42, 'chicks_in' => 36312, 'male_count' => 25194, 'female_count' => 11118, 'male_grade_a_count' => 0, 'male_grade_b_count' => 25194, 'female_grade_a_count' => 0, 'female_grade_b_count' => 11118, 'amount' => 562176, 'chick_source' => 'BF', 'chick_grade' => 'B', 'chick_code' => 'AT06-1026940843,AT1225940843', 'batch_no' => '129221055015', 'sex' => 'คละ', 'breed' => 'AA'],
            8 => ['placement_date' => '2026-04-09', 'catch_date' => '2026-05-21', 'catch_age' => 42, 'chicks_in' => 30600, 'male_count' => 30600, 'female_count' => 0, 'male_grade_a_count' => 30600, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 531300, 'chick_source' => 'BFI', 'chick_grade' => 'A', 'chick_code' => 'A001-0557940956,AO06-0756940956,CQ01-0445940952', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'AA,CB'],
            9 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-20', 'catch_age' => 42, 'chicks_in' => 32334, 'male_count' => 25908, 'female_count' => 6426, 'male_grade_a_count' => 25908, 'male_grade_b_count' => 0, 'female_grade_a_count' => 6426, 'female_grade_b_count' => 0, 'amount' => 449834, 'chick_source' => 'BFI,เนืองนามฟาร์ม', 'chick_grade' => 'A,B', 'chick_code' => 'CV1264940853,CV14-1963940853,AL4341940851,AL44-4541940851', 'batch_no' => '129221055015', 'sex' => 'คละ', 'breed' => 'AA,CB'],
            10 => ['placement_date' => '2026-04-10', 'catch_date' => '2026-05-21', 'catch_age' => 41, 'chicks_in' => 30600, 'male_count' => 30600, 'female_count' => 0, 'male_grade_a_count' => 30600, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 531300, 'chick_source' => 'เนืองนามฟาร์ม', 'chick_grade' => 'A', 'chick_code' => 'EU63-6431941051,EU65-6630941051,BM09-1043941051', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'RS'],
            11 => ['placement_date' => '2026-04-08', 'catch_date' => '2026-05-23', 'catch_age' => 45, 'chicks_in' => 35190, 'male_count' => 0, 'female_count' => 35190, 'male_grade_a_count' => 0, 'male_grade_b_count' => 0, 'female_grade_a_count' => 35190, 'female_grade_b_count' => 0, 'amount' => 507495, 'chick_source' => 'BFI', 'chick_grade' => 'A', 'chick_code' => 'CV15-1663940853,AP08-1132940948,AP1331940948,CQ01-0645940952,CQ0744940952', 'batch_no' => '129221055015', 'sex' => 'เมีย', 'breed' => 'AA,CB'],
            12 => ['placement_date' => '2026-04-10', 'catch_date' => '2026-05-22', 'catch_age' => 42, 'chicks_in' => 30906, 'male_count' => 30906, 'female_count' => 0, 'male_grade_a_count' => 6120, 'male_grade_b_count' => 24786, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 512313, 'chick_source' => 'BF,เนืองนามฟาร์ม', 'chick_grade' => 'A,B', 'chick_code' => 'CT03-0527941043,EU6331941051', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'CB,RS'],
            13 => ['placement_date' => '2026-04-09', 'catch_date' => '2026-05-23', 'catch_age' => 44, 'chicks_in' => 35292, 'male_count' => 0, 'female_count' => 35292, 'male_grade_a_count' => 0, 'male_grade_b_count' => 0, 'female_grade_a_count' => 35292, 'female_grade_b_count' => 0, 'amount' => 508966, 'chick_source' => 'BFI', 'chick_grade' => 'A', 'chick_code' => 'CQ07-0944940952,A001-0557940956,AO06-0955940956', 'batch_no' => '129221055015', 'sex' => 'เมีย', 'breed' => 'AA,CB'],
            14 => ['placement_date' => '2026-04-10', 'catch_date' => '2026-05-22', 'catch_age' => 42, 'chicks_in' => 30906, 'male_count' => 30906, 'female_count' => 0, 'male_grade_a_count' => 10200, 'male_grade_b_count' => 20706, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 516313, 'chick_source' => 'BFI,BF', 'chick_grade' => 'A,B', 'chick_code' => 'CT01-0228941043,CT0327941043,AP1132941048,AP12-1331941048', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'AA,CB'],
            15 => ['placement_date' => '2026-04-10', 'catch_date' => '2026-05-24', 'catch_age' => 44, 'chicks_in' => 35496, 'male_count' => 12852, 'female_count' => 22644, 'male_grade_a_count' => 12852, 'male_grade_b_count' => 0, 'female_grade_a_count' => 13260, 'female_grade_b_count' => 9384, 'amount' => 540508, 'chick_source' => 'BF', 'chick_grade' => 'A,B', 'chick_code' => 'AW01-0250941053,AW03-0449941053,CT0527941043', 'batch_no' => '129221055015', 'sex' => 'คละ', 'breed' => 'AA,CB'],
            16 => ['placement_date' => '2026-04-11', 'catch_date' => '2026-05-23', 'catch_age' => 42, 'chicks_in' => 31518, 'male_count' => 29070, 'female_count' => 2448, 'male_grade_a_count' => 0, 'male_grade_b_count' => 29070, 'female_grade_a_count' => 0, 'female_grade_b_count' => 2448, 'amount' => 509139, 'chick_source' => 'BF', 'chick_grade' => 'B', 'chick_code' => 'AT06-0727941143,AT08-1226941143', 'batch_no' => '129221055015', 'sex' => 'คละ', 'breed' => 'AA'],
            17 => ['placement_date' => '2026-04-10', 'catch_date' => '2026-05-25', 'catch_age' => 45, 'chicks_in' => 35496, 'male_count' => 1632, 'female_count' => 33864, 'male_grade_a_count' => 0, 'male_grade_b_count' => 1632, 'female_grade_a_count' => 16830, 'female_grade_b_count' => 17034, 'amount' => 498408, 'chick_source' => 'BF,เนืองนามฟาร์ม', 'chick_grade' => 'A,B', 'chick_code' => 'CT03-0527941043,EU63-6431941051', 'batch_no' => '129221055015', 'sex' => 'คละ', 'breed' => 'CB,RS'],
            18 => ['placement_date' => '2026-04-11', 'catch_date' => '2026-05-22', 'catch_age' => 41, 'chicks_in' => 30600, 'male_count' => 30600, 'female_count' => 0, 'male_grade_a_count' => 30600, 'male_grade_b_count' => 0, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 531300, 'chick_source' => 'เนืองนามฟาร์ม', 'chick_grade' => 'A', 'chick_code' => 'EU59-6136941151', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'RS'],
            19 => ['placement_date' => '2026-04-10', 'catch_date' => '2026-05-25', 'catch_age' => 45, 'chicks_in' => 35190, 'male_count' => 0, 'female_count' => 35190, 'male_grade_a_count' => 0, 'male_grade_b_count' => 0, 'female_grade_a_count' => 35190, 'female_grade_b_count' => 0, 'amount' => 507495, 'chick_source' => 'BFI,เนืองนามฟาร์ม', 'chick_grade' => 'A', 'chick_code' => 'EU6431941051,EU65-6630941051,BM09-10439410,AP10-1132941048,AP12-1331941048', 'batch_no' => '129221055015', 'sex' => 'เมีย', 'breed' => 'RS,AA'],
            20 => ['placement_date' => '2026-04-11', 'catch_date' => '2026-05-22', 'catch_age' => 41, 'chicks_in' => 30804, 'male_count' => 30804, 'female_count' => 0, 'male_grade_a_count' => 22440, 'male_grade_b_count' => 8364, 'female_grade_a_count' => 0, 'female_grade_b_count' => 0, 'amount' => 526642, 'chick_source' => 'BF,เนืองนามฟาร์ม', 'chick_grade' => 'A,B', 'chick_code' => 'EU61-6236941151,BL3951941151', 'batch_no' => '129221055015', 'sex' => 'ผู้', 'breed' => 'RS,AA'],
        ]);

        return $houses->mapWithKeys(function ($house) use ($rows) {
            return [$house->id => $rows->get((int) $house->house_no, [])];
        });
    }

    private function aggregatePlacementValues($placements)
    {
        return $placements
            ->groupBy('house_id')
            ->map(function ($housePlacements) {
                $housePlacements = collect($housePlacements);
                $firstPlacement = $housePlacements->sortBy('placement_date')->first();
                $lastPlacement = $housePlacements->whereNotNull('catch_date')->sortByDesc('catch_date')->first();
                $maleTotal = (int) $housePlacements->sum('male_count');
                $femaleTotal = (int) $housePlacements->sum('female_count');
                $maleGradeA = (int) $housePlacements->sum('male_grade_a_count');
                $maleGradeB = (int) $housePlacements->sum('male_grade_b_count');
                $femaleGradeA = (int) $housePlacements->sum('female_grade_a_count');
                $femaleGradeB = (int) $housePlacements->sum('female_grade_b_count');

                $aggregate = collect([
                    '_is_aggregate' => $housePlacements->count() > 1,
                    'placement_date' => $firstPlacement?->placement_date,
                    'catch_date' => $lastPlacement?->catch_date,
                    'catch_age' => $housePlacements->whereNotNull('catch_age')->max('catch_age'),
                    'chicks_in' => (int) $housePlacements->sum('chicks_in'),
                    'male_count' => $maleTotal,
                    'female_count' => $femaleTotal,
                    'male_grade_a_count' => $maleGradeA,
                    'male_grade_b_count' => $maleGradeB,
                    'female_grade_a_count' => $femaleGradeA,
                    'female_grade_b_count' => $femaleGradeB,
                    'chick_source' => $housePlacements->pluck('chick_source')->filter()->unique()->implode(', '),
                    'chick_code' => $housePlacements->pluck('chick_code')->filter()->unique()->implode(', '),
                    'batch_no' => $housePlacements->pluck('batch_no')->filter()->unique()->implode(', '),
                    'breed' => $housePlacements->pluck('breed')->filter()->unique()->implode(', '),
                ]);

                $aggregate->put('chick_grade', $this->chickGrade($aggregate));
                $aggregate->put('sex', $this->sex($aggregate));

                return $aggregate->all();
            });
    }

    private function nullableDate(mixed $value): ?string
    {
        return $value === null || $value === '' ? null : (string) $value;
    }

    private function flockStartDate(array $validated, $placementsByHouse): string
    {
        $placementDates = $placementsByHouse
            ->pluck('placement_date')
            ->filter(fn ($date) => $date !== null && $date !== '')
            ->sort()
            ->values();

        if ($placementDates->isNotEmpty()) {
            return \Carbon\CarbonImmutable::parse($placementDates->first())->addDay()->toDateString();
        }

        return (string) ($validated['start_date'] ?? now()->toDateString());
    }

    private function initialBirdsByHouse($houses, $placementsByHouse, $fallbackByHouse)
    {
        return $houses->mapWithKeys(function ($house) use ($placementsByHouse, $fallbackByHouse) {
            $placement = collect($placementsByHouse->get((string) $house->id, []));
            $chicksIn = $placement->get('chicks_in');

            return [$house->id => $this->integerOrZero($chicksIn === null ? $fallbackByHouse->get((string) $house->id, 0) : $chicksIn)];
        });
    }

    private function placementPayload(int $flockId, int $houseId, $placement): array
    {
        return [
            'flock_id' => $flockId,
            'house_id' => $houseId,
            'placement_date' => $this->nullableDate($placement->get('placement_date')),
            'catch_date' => $this->nullableDate($placement->get('catch_date')),
            'catch_age' => $this->catchAge($placement),
            'chicks_in' => $this->integerOrZero($placement->get('chicks_in')),
            'male_count' => $this->integerOrZero($placement->get('male_count')),
            'female_count' => $this->integerOrZero($placement->get('female_count')),
            'male_grade_a_count' => $this->integerOrZero($placement->get('male_grade_a_count')),
            'male_grade_b_count' => $this->integerOrZero($placement->get('male_grade_b_count')),
            'female_grade_a_count' => $this->integerOrZero($placement->get('female_grade_a_count')),
            'female_grade_b_count' => $this->integerOrZero($placement->get('female_grade_b_count')),
            'chick_source' => $this->nullableString($placement->get('chick_source')),
            'chick_grade' => $this->chickGrade($placement),
            'chick_code' => $this->nullableString($placement->get('chick_code')),
            'batch_no' => $this->nullableString($placement->get('batch_no')),
            'sex' => $this->sex($placement),
            'breed' => $this->nullableString($placement->get('breed')),
        ];
    }

    private function catchAge($placement): ?int
    {
        $placementDate = $this->nullableDate($placement->get('placement_date'));
        $catchDate = $this->nullableDate($placement->get('catch_date'));

        if ($placementDate && $catchDate) {
            return (int) max(0, \Carbon\CarbonImmutable::parse($placementDate)->diffInDays(\Carbon\CarbonImmutable::parse($catchDate)));
        }

        return $this->nullableInteger($placement->get('catch_age'));
    }

    private function chickGrade($placement): ?string
    {
        $gradeA = $this->integerOrZero($placement->get('male_grade_a_count')) + $this->integerOrZero($placement->get('female_grade_a_count'));
        $gradeB = $this->integerOrZero($placement->get('male_grade_b_count')) + $this->integerOrZero($placement->get('female_grade_b_count'));

        return match (true) {
            $gradeA > 0 && $gradeB > 0 => 'A,B',
            $gradeA > 0 => 'A',
            $gradeB > 0 => 'B',
            default => null,
        };
    }

    private function sex($placement): ?string
    {
        $male = $this->integerOrZero($placement->get('male_count'))
            + $this->integerOrZero($placement->get('male_grade_a_count'))
            + $this->integerOrZero($placement->get('male_grade_b_count'));
        $female = $this->integerOrZero($placement->get('female_count'))
            + $this->integerOrZero($placement->get('female_grade_a_count'))
            + $this->integerOrZero($placement->get('female_grade_b_count'));

        return match (true) {
            $male > 0 && $female > 0 => 'คละ',
            $male > 0 => 'ผู้',
            $female > 0 => 'เมีย',
            default => null,
        };
    }

    private function nullableInteger(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    private function integerOrZero(mixed $value): int
    {
        return $value === null || $value === '' ? 0 : (int) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        return $value === null || $value === '' ? null : (string) $value;
    }
}
