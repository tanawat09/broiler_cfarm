@csrf

<div class="grid gap-5">
    <div>
        <x-input-label for="name" value="ชื่อทีมจับไก่" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $catchingTeam->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <label class="flex items-center gap-3 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $catchingTeam->is_active)) class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
        <span class="text-sm font-medium text-gray-700">เปิดใช้งาน</span>
    </label>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('catching-teams.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ยกเลิก
    </a>
    <x-primary-button>บันทึก</x-primary-button>
</div>
