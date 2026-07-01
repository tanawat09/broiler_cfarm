<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFarmRequest;
use App\Http\Requests\UpdateFarmRequest;
use App\Models\Farm;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FarmController extends Controller
{
    public function index(): View
    {
        $farms = FarmAccess::farmsQuery(request()->user())
            ->withCount(['houses', 'flocks'])
            ->latest()
            ->paginate(10);

        return view('farms.index', compact('farms'));
    }

    public function create(): View
    {
        FarmAccess::ensureSuperAdmin(request()->user());

        return view('farms.create', [
            'farm' => new Farm(),
        ]);
    }

    public function store(StoreFarmRequest $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $farm = Farm::query()->create($request->validated());

        return redirect()
            ->route('farms.show', $farm)
            ->with('status', 'เพิ่มข้อมูลฟาร์มเรียบร้อยแล้ว');
    }

    public function show(Farm $farm): View
    {
        FarmAccess::ensureFarm(request()->user(), $farm);

        $farm->loadCount(['houses', 'flocks']);

        return view('farms.show', compact('farm'));
    }

    public function edit(Farm $farm): View
    {
        FarmAccess::ensureFarm(request()->user(), $farm);

        return view('farms.edit', compact('farm'));
    }

    public function update(UpdateFarmRequest $request, Farm $farm): RedirectResponse
    {
        FarmAccess::ensureFarm($request->user(), $farm);

        $farm->update($request->validated());

        return redirect()
            ->route('farms.show', $farm)
            ->with('status', 'แก้ไขข้อมูลฟาร์มเรียบร้อยแล้ว');
    }

    public function destroy(Farm $farm): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin(request()->user());

        $farm->delete();

        return redirect()
            ->route('farms.index')
            ->with('status', 'ลบข้อมูลฟาร์มเรียบร้อยแล้ว');
    }
}
