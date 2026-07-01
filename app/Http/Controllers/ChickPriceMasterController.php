<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChickPriceMasterRequest;
use App\Models\ChickPriceMaster;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChickPriceMasterController extends Controller
{
    public function index(Request $request): View
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $prices = ChickPriceMaster::query()
            ->orderByDesc('effective_date')
            ->orderBy('sex')
            ->orderBy('grade')
            ->paginate(20);

        return view('price-masters.chick-prices.index', [
            'prices' => $prices,
            'price' => new ChickPriceMaster(['effective_date' => now()->toDateString(), 'is_active' => true]),
            'sexes' => ChickPriceMaster::SEXES,
            'grades' => ChickPriceMaster::GRADES,
        ]);
    }

    public function store(StoreChickPriceMasterRequest $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        ChickPriceMaster::query()->updateOrCreate(
            [
                'sex' => $request->validated('sex'),
                'grade' => $request->validated('grade'),
                'effective_date' => $request->validated('effective_date'),
            ],
            $request->validated(),
        );

        return redirect()
            ->route('chick-price-masters.index')
            ->with('status', 'บันทึกราคาลูกไก่เรียบร้อยแล้ว');
    }

    public function destroy(Request $request, ChickPriceMaster $chickPriceMaster): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $chickPriceMaster->delete();

        return redirect()
            ->route('chick-price-masters.index')
            ->with('status', 'ลบราคาลูกไก่เรียบร้อยแล้ว');
    }
}
