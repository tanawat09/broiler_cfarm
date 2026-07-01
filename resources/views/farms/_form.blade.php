@csrf

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <x-input-label for="farm_name" value="ชื่อฟาร์ม" />
        <x-text-input id="farm_name" name="farm_name" type="text" class="mt-1 block w-full" :value="old('farm_name', $farm->farm_name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('farm_name')" />
    </div>

    <div>
        <x-input-label for="farm_code" value="รหัสฟาร์ม" />
        <x-text-input id="farm_code" name="farm_code" type="text" class="mt-1 block w-full" :value="old('farm_code', $farm->farm_code)" />
        <x-input-error class="mt-2" :messages="$errors->get('farm_code')" />
    </div>

    <div>
        <x-input-label for="house_count" value="จำนวนเล้า" />
        <x-text-input id="house_count" name="house_count" type="number" min="1" max="20" class="mt-1 block w-full text-right" :value="old('house_count', $farm->house_count ?: 20)" required />
        <x-input-error class="mt-2" :messages="$errors->get('house_count')" />
    </div>

    <div>
        <x-input-label for="rearing_area" value="พื้นที่การเลี้ยง (ตารางเมตร ต่อเล้า)" />
        <x-text-input id="rearing_area" name="rearing_area" type="number" min="1" class="mt-1 block w-full text-right" :value="old('rearing_area', $farm->rearing_area)" />
        <x-input-error class="mt-2" :messages="$errors->get('rearing_area')" />
    </div>

    <div>
        <x-input-label for="company_name" value="ชื่อบริษัท" />
        <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $farm->company_name)" />
        <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
    </div>

    <div>
        <x-input-label for="owner_name" value="ชื่อเจ้าของ" />
        <x-text-input id="owner_name" name="owner_name" type="text" class="mt-1 block w-full" :value="old('owner_name', $farm->owner_name)" />
        <x-input-error class="mt-2" :messages="$errors->get('owner_name')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="address" value="ที่อยู่" />
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $farm->address) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="note" value="หมายเหตุ" />
        <textarea id="note" name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note', $farm->note) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('note')" />
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('farms.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ยกเลิก
    </a>
    <x-primary-button>
        บันทึก
    </x-primary-button>
</div>
