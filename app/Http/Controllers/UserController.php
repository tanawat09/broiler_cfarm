<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Farm;
use App\Models\User;
use App\Support\FarmAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        FarmAccess::ensureSuperAdmin($request->user());

        $users = User::query()
            ->with('farm')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(Request $request): View
    {
        FarmAccess::ensureSuperAdmin($request->user());

        return view('users.create', [
            'user' => new User(['role' => User::ROLE_FARM_MANAGER]),
            'farms' => Farm::query()->orderBy('farm_name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $payload = $this->payload($request->validated());
        $payload['password'] = Hash::make($request->validated('password'));

        User::query()->create($payload);

        return redirect()
            ->route('users.index')
            ->with('status', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    }

    public function edit(Request $request, User $user): View
    {
        FarmAccess::ensureSuperAdmin($request->user());

        return view('users.edit', [
            'user' => $user,
            'farms' => Farm::query()->orderBy('farm_name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());
        $payload = $this->payload($request->validated());

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->validated('password'));
        }

        $user->update($payload);

        return redirect()
            ->route('users.index')
            ->with('status', 'แก้ไขผู้ใช้เรียบร้อยแล้ว');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        FarmAccess::ensureSuperAdmin($request->user());

        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors(['user' => 'ไม่สามารถลบผู้ใช้ที่กำลังเข้าสู่ระบบอยู่']);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }

    private function payload(array $validated): array
    {
        $role = $validated['role'];

        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $role,
            'farm_id' => $role === User::ROLE_FARM_MANAGER ? (int) $validated['farm_id'] : null,
        ];
    }
}
