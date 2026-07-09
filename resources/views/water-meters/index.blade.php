<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-700 ring-1 ring-sky-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a7 7 0 0 0 7-7c0-4.5-7-13-7-13S5 10.5 5 15a7 7 0 0 0 7 7Z" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-slate-900">มิเตอร์น้ำรายวัน</h1>
                        <p class="mt-0.5 text-sm text-slate-500">{{ $flock->flock_code }} | {{ $flock->farm->farm_name }}</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('flocks.water-meters.index', $flock) }}" class="grid w-full gap-3 rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:grid-cols-3 xl:w-auto xl:min-w-[760px]">
                <div>
                    <x-input-label for="wm_farm_selector" value="เลือกฟาร์ม" />
                    <select id="wm_farm_selector" class="mt-1 block h-10 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        @foreach ($farms as $farm)
                            <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="wm_flock_selector" value="เลือกรุ่น" />
                    <select id="wm_flock_selector" class="mt-1 block h-10 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        @foreach ($flockOptions as $option)
                            <option
                                value="{{ $option->id }}"
                                data-farm-id="{{ $option->farm_id }}"
                                data-index-url="{{ route('flocks.water-meters.index', $option) }}"
                                @selected((int) $option->id === (int) $flock->id)
                            >
                                {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="wm_date" value="วันที่บันทึก" />
                    <x-text-input id="wm_date" name="date" type="date" class="mt-1 block h-10 w-full" :value="$recordDate" />
                </div>
            </form>
        </div>
    </x-slot>

    <div class="bg-slate-50 py-5">
        <div class="w-full px-3 sm:px-5 lg:px-6">
            @if (session('status'))
                <div class="mb-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                    กรุณาตรวจสอบเลขมิเตอร์น้ำอีกครั้ง เลขมิเตอร์ต้องไม่น้อยกว่าวันก่อนหน้าของเล้าเดียวกัน
                </div>
            @endif

            <div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">วันที่บันทึก</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ thai_date($recordDate) }}</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">ช่วงอายุไก่</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">
                        {{ $ageByHouse->min() === $ageByHouse->max() ? $ageByHouse->min().' วัน' : $ageByHouse->min().'-'.$ageByHouse->max().' วัน' }}
                    </div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">จำนวนเล้า</div>
                    <div class="mt-1 text-xl font-bold text-slate-900">{{ number_format($houses->count()) }} เล้า</div>
                </div>
                <div class="rounded-lg border border-sky-200 bg-sky-50 p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">วันที่ถัดไปอัตโนมัติ</div>
                    <div class="mt-1 text-sm font-medium text-sky-900">อิงจากข้อมูลมิเตอร์ล่าสุดของฟาร์มเดียวกัน</div>
                </div>
            </div>

            <form method="POST" action="{{ route('flocks.water-meters.store', $flock) }}">
                @csrf
                <input type="hidden" name="record_date" value="{{ $recordDate }}">

                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-2 border-b border-slate-200 bg-white px-4 py-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">กรอกเลขมิเตอร์น้ำรายเล้า</h2>
                            <p class="mt-0.5 text-sm text-slate-500">กรอกเลขมิเตอร์วันนี้ หากเป็นครั้งแรกให้กรอกเลขตั้งต้นในช่องเลขครั้งก่อน</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 ring-1 ring-amber-200">เลขตั้งต้น ใช้เฉพาะครั้งแรก</span>
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-sky-700 ring-1 ring-sky-200">น้ำใช้ = เลขวันนี้ - เลขครั้งก่อน</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px] table-fixed border-separate border-spacing-0 text-sm">
                            <colgroup>
                                <col class="w-[120px]">
                                <col class="w-[90px]">
                                <col class="w-[220px]">
                                <col class="w-[220px]">
                                <col class="w-[180px]">
                            </colgroup>
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-slate-100 text-xs font-bold uppercase tracking-wide text-slate-600">
                                    <th rowspan="2" class="border-b border-r border-slate-200 px-3 py-3 text-center align-middle">เล้า</th>
                                    <th rowspan="2" class="border-b border-r border-slate-200 px-3 py-3 text-center align-middle">อายุ</th>
                                    <th colspan="3" class="border-b border-sky-200 bg-sky-50 px-3 py-2 text-center text-sky-800">มิเตอร์น้ำ / น้ำใช้</th>
                                </tr>
                                <tr class="bg-sky-50 text-xs font-bold text-sky-800">
                                    <th class="border-b border-r border-sky-200 px-3 py-2 text-center">เลขครั้งก่อน</th>
                                    <th class="border-b border-r border-sky-200 px-3 py-2 text-center">เลขมิเตอร์วันนี้</th>
                                    <th class="border-b border-sky-200 px-3 py-2 text-center">น้ำใช้วันนี้ (ลิตร)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                @forelse ($houses as $house)
                                    @php
                                        $record = $recordsByHouse->get($house->id);
                                        $previousRecord = $previousRecordsByHouse->get($house->id);
                                        $houseAgeDay = $ageByHouse->get($house->id, $ageDay);
                                        $field = 'meters.'.$house->id;
                                        $previousField = 'previous_meters.'.$house->id;
                                        $initialPreviousMeter = '';
                                        if (! $previousRecord && $record?->water_meter_reading !== null) {
                                            $initialPreviousMeter = max(0, (int) $record->water_meter_reading - (int) ($record->water_used ?? 0));
                                        }
                                    @endphp
                                    <tr class="transition hover:bg-sky-50/40">
                                        <td class="whitespace-nowrap border-r border-slate-100 px-3 py-2 text-center font-bold text-slate-900">เล้า {{ $house->house_no }}</td>
                                        <td class="border-r border-slate-100 px-3 py-2 text-center font-semibold text-slate-700">{{ $houseAgeDay }}</td>
                                        <td class="border-r border-sky-100 bg-sky-50/30 px-3 py-2 text-center">
                                            @if ($previousRecord?->water_meter_reading !== null)
                                                <span class="inline-flex h-9 min-w-32 items-center justify-center rounded-md bg-white px-3 text-sm font-bold text-slate-800 ring-1 ring-slate-200">
                                                    {{ number_format((int) $previousRecord->water_meter_reading) }}
                                                </span>
                                            @else
                                                <input
                                                    type="number"
                                                    step="1"
                                                    min="0"
                                                    name="previous_meters[{{ $house->id }}]"
                                                    value="{{ old($previousField, $initialPreviousMeter) }}"
                                                    placeholder="เลขตั้งต้น"
                                                    class="mx-auto block h-9 w-36 rounded-md border-amber-300 bg-amber-50 px-3 text-center text-sm font-bold text-slate-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                                >
                                                <x-input-error class="mt-1 text-left" :messages="$errors->get($previousField)" />
                                            @endif
                                        </td>
                                        <td class="border-r border-sky-100 bg-sky-50/30 px-3 py-2 text-center">
                                            <input
                                                type="number"
                                                step="1"
                                                min="0"
                                                name="meters[{{ $house->id }}]"
                                                value="{{ old($field, $record?->water_meter_reading) }}"
                                                class="mx-auto block h-9 w-36 rounded-md border-sky-300 bg-white px-3 text-center text-sm font-bold text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                            >
                                            <x-input-error class="mt-1 text-left" :messages="$errors->get($field)" />
                                        </td>
                                        <td class="bg-sky-50/30 px-3 py-2 text-center">
                                            <span class="inline-flex h-9 min-w-32 items-center justify-center rounded-md bg-sky-100 px-3 text-sm font-extrabold text-sky-900">
                                                {{ number_format((int) ($record?->water_used ?? 0)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">ยังไม่มีข้อมูลเล้าเริ่มต้นสำหรับรุ่นนี้</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sticky bottom-0 z-20 mt-4 border-t border-slate-200 bg-slate-50/95 py-3 backdrop-blur">
                    <div class="flex justify-end">
                        <x-primary-button :disabled="$houses->isEmpty()" class="px-6 py-3 text-sm">
                            บันทึกมิเตอร์น้ำ
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('wm_farm_selector');
            const flockSelector = document.getElementById('wm_flock_selector');
            const dateInput = document.getElementById('wm_date');

            if (!farmSelector || !flockSelector) return;

            const flockOptions = Array.from(flockSelector.options).map((option) => ({
                value: option.value,
                text: option.textContent,
                farmId: option.dataset.farmId,
                indexUrl: option.dataset.indexUrl,
                selected: option.selected,
            }));

            const showOptionsForFarm = (farmId) => {
                let firstVisible = null;
                flockSelector.innerHTML = '';
                flockOptions
                    .filter((option) => option.farmId === farmId)
                    .forEach((optionData) => {
                        const option = new Option(optionData.text.trim(), optionData.value);
                        option.dataset.farmId = optionData.farmId;
                        option.dataset.indexUrl = optionData.indexUrl;
                        option.selected = optionData.selected;
                        flockSelector.add(option);
                        if (option.selected || firstVisible === null) firstVisible = option;
                    });

                if (firstVisible) {
                    flockSelector.value = firstVisible.value;
                } else {
                    const option = new Option('ไม่มีรุ่นในฟาร์มนี้', '');
                    option.disabled = true;
                    option.selected = true;
                    flockSelector.add(option);
                }

                flockOptions.forEach((option) => {
                    option.selected = option.farmId === farmId && firstVisible !== null && option.value === firstVisible.value;
                });

                return firstVisible;
            };

            showOptionsForFarm(farmSelector.value);

            const redirectToFlock = (option) => {
                if (!option?.dataset.indexUrl) return;
                const url = new URL(option.dataset.indexUrl, window.location.origin);
                url.searchParams.set('date', dateInput?.value || '{{ $recordDate }}');
                window.location.href = url.toString();
            };

            farmSelector.addEventListener('change', () => {
                const firstVisible = showOptionsForFarm(farmSelector.value);
                redirectToFlock(firstVisible);
            });

            flockSelector.addEventListener('change', () => {
                redirectToFlock(flockSelector.selectedOptions[0]);
            });

            dateInput?.addEventListener('change', () => {
                redirectToFlock(flockSelector.selectedOptions[0]);
            });
        });
    </script>
</x-app-layout>
