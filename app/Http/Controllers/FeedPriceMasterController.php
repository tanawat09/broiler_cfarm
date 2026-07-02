<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedPriceMasterRequest;
use App\Models\FeedPriceMaster;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedPriceMasterController extends Controller
{
    public function index(Request $request): View
    {
        $prices = FeedPriceMaster::query()
            ->orderByDesc('effective_date')
            ->orderBy('feed_code')
            ->paginate(20);

        return view('price-masters.feed-prices.index', [
            'prices' => $prices,
            'price' => new FeedPriceMaster(['effective_date' => now()->toDateString(), 'is_active' => true]),
        ]);
    }

    public function store(StoreFeedPriceMasterRequest $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $validated = $request->validated();

        FeedPriceMaster::query()->updateOrCreate(
            ['feed_code' => $validated['feed_code'], 'effective_date' => $validated['effective_date']],
            $validated
        );

        return redirect()
            ->route('feed-price-masters.index')
            ->with('status', 'บันทึกราคาอาหารเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, FeedPriceMaster $feedPriceMaster): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $feedPriceMaster->delete();

        return redirect()
            ->route('feed-price-masters.index')
            ->with('status', 'ลบราคาอาหารเรียบร้อยแล้ว');
    }
}
