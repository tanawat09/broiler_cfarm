<?php

namespace App\Http\Controllers;

use App\Models\CatchingTeam;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatchingTeamController extends Controller
{
    public function index(): View
    {
        $catchingTeams = CatchingTeam::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(15);

        return view('catching-teams.index', compact('catchingTeams'));
    }

    public function create(Request $request): View
    {
        FarmAccess::ensureSuperAdmin($request->user());

        return view('catching-teams.create', [
            'catchingTeam' => new CatchingTeam(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:catching_teams,name',
            'is_active' => 'boolean',
        ]);

        CatchingTeam::query()->create([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('catching-teams.index')
            ->with('status', 'เพิ่มทีมจับไก่เรียบร้อยแล้ว');
    }

    public function edit(Request $request, CatchingTeam $catchingTeam): View
    {
        FarmAccess::ensureSuperAdmin($request->user());

        return view('catching-teams.edit', compact('catchingTeam'));
    }

    public function update(Request $request, CatchingTeam $catchingTeam): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:catching_teams,name,' . $catchingTeam->id,
            'is_active' => 'boolean',
        ]);

        $catchingTeam->update([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('catching-teams.index')
            ->with('status', 'แก้ไขทีมจับไก่เรียบร้อยแล้ว');
    }

    public function destroy(Request $request, CatchingTeam $catchingTeam): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $catchingTeam->delete();

        return redirect()
            ->route('catching-teams.index')
            ->with('status', 'ลบทีมจับไก่เรียบร้อยแล้ว');
    }
}
