<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">แก้ไขบันทึกการจับไก่</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[800px] px-3 sm:px-4 lg:px-6">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <span class="font-bold">กรุณาตรวจสอบข้อมูล:</span>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('flocks.catch-records.update', [$flock, $catchRecord]) }}">
                @csrf
                @method('PUT')

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="house_id" value="เลือกเล้า" class="font-semibold text-slate-700 text-xs" />
                            <select id="house_id" name="house_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                @foreach ($availableStarts as $start)
                                    <option value="{{ $start->house_id }}" @selected(old('house_id', $catchRecord->house_id) == $start->house_id)>
                                        เล้า {{ $start->house->house_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="catch_date" value="วันที่จับไก่" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="catch_date" name="catch_date" type="date" class="mt-1 block w-full" :value="old('catch_date', $catchRecord->catch_date->toDateString())" required />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="sequence" value="คันที่ / ลำดับที่" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="sequence" name="sequence" type="number" min="1" class="mt-1 block w-full text-right" :value="old('sequence', $catchRecord->sequence)" required />
                        </div>
                        <div>
                            <x-input-label for="license_plate" value="ทะเบียนรถ" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="license_plate" name="license_plate" type="text" class="mt-1 block w-full" :value="old('license_plate', $catchRecord->license_plate)" required placeholder="เช่น กข-1234" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="vehicle_type" value="ชนิดรถ" class="font-semibold text-slate-700 text-xs" />
                            <select id="vehicle_type" name="vehicle_type" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="รถ 6 ล้อ" @selected(old('vehicle_type', $catchRecord->vehicle_type) == 'รถ 6 ล้อ')>รถ 6 ล้อ</option>
                                <option value="รถ 10 ล้อ" @selected(old('vehicle_type', $catchRecord->vehicle_type) == 'รถ 10 ล้อ')>รถ 10 ล้อ</option>
                                <option value="รถกระบะ" @selected(old('vehicle_type', $catchRecord->vehicle_type) == 'รถกระบะ')>รถกระบะ</option>
                                <option value="อื่นๆ" @selected(old('vehicle_type', $catchRecord->vehicle_type) == 'อื่นๆ')>อื่นๆ</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="catching_team" value="ทีมจับไก่" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="catching_team" name="catching_team" type="text" class="mt-1 block w-full" :value="old('catching_team', $catchRecord->catching_team)" placeholder="เช่น ทีมผู้ใหญ่แดง" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="catching_fee" value="ค่าจับ (บาท)" class="font-semibold text-slate-700 text-xs" />
                        <x-text-input id="catching_fee" name="catching_fee" type="number" step="0.01" min="0" class="mt-1 block w-full text-right font-semibold" :value="old('catching_fee', $catchRecord->catching_fee)" required />
                    </div>

                    <div>
                        <x-input-label for="note" value="หมายเหตุ / อื่นๆ" class="font-semibold text-slate-700 text-xs" />
                        <textarea id="note" name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('note', $catchRecord->note) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        ยกเลิก
                    </a>
                    <x-primary-button>
                        บันทึกการแก้ไข
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
