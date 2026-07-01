<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalePriceMasterRequest;
use App\Models\SalePriceMaster;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalePriceMasterController extends Controller
{
    public function index(Request $request): View
    {
        $prices = SalePriceMaster::query()
            ->orderByDesc('effective_date')
            ->paginate(20);

        return view('price-masters.sale-prices.index', [
            'prices' => $prices,
            'price' => new SalePriceMaster(['effective_date' => now()->toDateString(), 'is_active' => true]),
        ]);
    }

    public function store(StoreSalePriceMasterRequest $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $validated = $request->validated();

        SalePriceMaster::query()->updateOrCreate(
            ['effective_date' => $validated['effective_date']],
            $validated + ['farm_id' => null],
        );

        return redirect()
            ->route('sale-price-masters.index')
            ->with('status', 'บันทึกราคาขายเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, SalePriceMaster $salePriceMaster): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $salePriceMaster->delete();

        return redirect()
            ->route('sale-price-masters.index')
            ->with('status', 'ลบราคาขายเรียบร้อยแล้ว');
    }
}
