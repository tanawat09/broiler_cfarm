<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedIntakeMasterRequest;
use App\Models\FeedIntakeMaster;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedIntakeMasterController extends Controller
{
    public function index(Request $request): View
    {
        $records = FeedIntakeMaster::query()
            ->orderBy('age')
            ->get();

        return view('price-masters.feed-intakes.index', [
            'records' => $records,
            'newRecord' => new FeedIntakeMaster(),
        ]);
    }

    public function store(StoreFeedIntakeMasterRequest $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $validated = $request->validated();

        FeedIntakeMaster::query()->updateOrCreate(
            ['age' => $validated['age']],
            $validated
        );

        return redirect()
            ->route('feed-intake-masters.index')
            ->with('status', 'บันทึกเกณฑ์การกินอาหารเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, FeedIntakeMaster $feedIntakeMaster): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $feedIntakeMaster->delete();

        return redirect()
            ->route('feed-intake-masters.index')
            ->with('status', 'ลบเกณฑ์การกินอาหารเรียบร้อยแล้ว');
    }
}
