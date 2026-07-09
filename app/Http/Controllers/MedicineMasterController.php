<?php

namespace App\Http\Controllers;

use App\Models\MedicineMaster;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineMasterController extends Controller
{
    public function index(Request $request): View
    {
        $medicines = MedicineMaster::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(30);

        return view('medicine-masters.index', [
            'medicines' => $medicines,
            'medicine' => new MedicineMaster(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_unit' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        MedicineMaster::query()->updateOrCreate(
            ['name' => trim($validated['name'])],
            [
                'default_unit' => $validated['default_unit'] ?? null,
                'is_active' => (bool) ($validated['is_active'] ?? false),
                'note' => $validated['note'] ?? null,
            ],
        );

        return redirect()
            ->route('medicine-masters.index')
            ->with('status', 'บันทึกมาสเตอร์ยาเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, MedicineMaster $medicineMaster): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $medicineMaster->delete();

        return redirect()
            ->route('medicine-masters.index')
            ->with('status', 'ลบมาสเตอร์ยาเรียบร้อยแล้ว');
    }
}
