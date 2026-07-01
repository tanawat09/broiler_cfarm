@csrf

@php
    $selectedRole = old('role', $user->role ?: \App\Models\User::ROLE_FARM_MANAGER);
    $selectedFarmId = old('farm_id', $user->farm_id);
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="ชื่อผู้ใช้" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" value="อีเมล" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="role" value="สิทธิ์" />
        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}" @selected($selectedRole === \App\Models\User::ROLE_SUPER_ADMIN)>Super Admin</option>
            <option value="{{ \App\Models\User::ROLE_FARM_MANAGER }}" @selected($selectedRole === \App\Models\User::ROLE_FARM_MANAGER)>Farm Manager</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('role')" />
    </div>

    <div>
        <x-input-label for="farm_id" value="ฟาร์ม" />
        <select id="farm_id" name="farm_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">ไม่ระบุ</option>
            @foreach ($farms as $farm)
                <option value="{{ $farm->id }}" @selected((int) $selectedFarmId === (int) $farm->id)>{{ $farm->farm_name }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Farm Manager ต้องเลือกฟาร์ม 1 ฟาร์ม ส่วน Super Admin ไม่ต้องเลือก</p>
        <x-input-error class="mt-2" :messages="$errors->get('farm_id')" />
    </div>

    <div>
        <x-input-label for="password" :value="$user->exists ? 'รหัสผ่านใหม่' : 'รหัสผ่าน'" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="! $user->exists" autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div>
        <x-input-label for="password_confirmation" value="ยืนยันรหัสผ่าน" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="! $user->exists" autocomplete="new-password" />
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ยกเลิก
    </a>
    <x-primary-button>บันทึก</x-primary-button>
</div>
