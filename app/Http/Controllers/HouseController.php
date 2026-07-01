<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\House;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HouseController extends Controller
{
    public function index(Farm $farm): View
    {
        FarmAccess::ensureFarm(request()->user(), $farm);

        $farm->load(['houses' => fn ($query) => $query->orderBy('house_no')]);

        return view('houses.index', [
            'farm' => $farm,
            'housesByNo' => $farm->houses->keyBy('house_no'),
            'houseNumbers' => range(1, (int) $farm->house_count),
        ]);
    }

    public function generate(Farm $farm): RedirectResponse
    {
        FarmAccess::ensureFarm(request()->user(), $farm);

        foreach (range(1, (int) $farm->house_count) as $houseNo) {
            House::query()->firstOrCreate(
                [
                    'farm_id' => $farm->id,
                    'house_no' => $houseNo,
                ],
                [
                    'house_name' => 'เล้า '.$houseNo,
                    'is_active' => true,
                ],
            );
        }

        return redirect()
            ->route('farms.houses.index', $farm)
            ->with('status', 'สร้างเล้าตามจำนวนของฟาร์มเรียบร้อยแล้ว');
    }

    public function update(Request $request, Farm $farm, House $house): RedirectResponse
    {
        FarmAccess::ensureFarm($request->user(), $farm);
        abort_unless($house->farm_id === $farm->id, 404);

        $validated = $request->validate(
            [
                'house_name' => ['nullable', 'string', 'max:255'],
                'is_active' => ['required', 'boolean'],
            ],
            [],
            [
                'house_name' => 'ชื่อเล้า',
                'is_active' => 'สถานะใช้งาน',
            ],
        );

        $house->update($validated);

        return redirect()
            ->route('farms.houses.index', $farm)
            ->with('status', 'แก้ไขข้อมูลเล้าเรียบร้อยแล้ว');
    }
}
