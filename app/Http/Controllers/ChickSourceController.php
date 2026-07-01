<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChickSourceRequest;
use App\Http\Requests\UpdateChickSourceRequest;
use App\Models\ChickSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChickSourceController extends Controller
{
    public function index(): View
    {
        $chickSources = ChickSource::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15);

        return view('chick-sources.index', compact('chickSources'));
    }

    public function create(): View
    {
        return view('chick-sources.create', [
            'chickSource' => new ChickSource(['is_active' => true]),
        ]);
    }

    public function store(StoreChickSourceRequest $request): RedirectResponse
    {
        ChickSource::query()->create([
            'name' => $request->validated('name'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('chick-sources.index')
            ->with('status', 'เพิ่มแหล่งลูกไก่เรียบร้อยแล้ว');
    }

    public function edit(ChickSource $chickSource): View
    {
        return view('chick-sources.edit', compact('chickSource'));
    }

    public function update(UpdateChickSourceRequest $request, ChickSource $chickSource): RedirectResponse
    {
        $chickSource->update([
            'name' => $request->validated('name'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('chick-sources.index')
            ->with('status', 'แก้ไขแหล่งลูกไก่เรียบร้อยแล้ว');
    }

    public function destroy(ChickSource $chickSource): RedirectResponse
    {
        $chickSource->delete();

        return redirect()
            ->route('chick-sources.index')
            ->with('status', 'ลบแหล่งลูกไก่เรียบร้อยแล้ว');
    }
}
