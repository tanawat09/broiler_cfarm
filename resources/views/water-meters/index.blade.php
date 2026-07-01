<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">มิเตอร์น้ำรายวัน</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->flock_code }} | {{ $flock->farm->farm_name }}</p>
            </div>
            <form method="GET" action="{{ route('flocks.water-meters.index', $flock) }}" class="grid w-full gap-2 sm:grid-cols-3 lg:w-auto lg:min-w-[680px]">
                <div>
                    <x-input-label for="wm_farm_selector" value="เลือกฟาร์ม" />
                    <select id="wm_farm_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($farms as $farm)
                            <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="wm_flock_selector" value="เลือกรุ่น" />
                    <select id="wm_flock_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                    <x-input-label for="wm_date" value="วันที่" />
                    <x-text-input id="wm_date" name="date" type="date" class="mt-1 block w-full sm:w-44" :value="$recordDate" />
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="w-full px-3 sm:px-5 lg:px-6">
            @if (session('status'))
                <div class="mb-3 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
                    กรุณาตรวจสอบเลขมิเตอร์น้ำอีกครั้ง เลขมิเตอร์ต้องไม่น้อยกว่าวันก่อนหน้าของเล้าเดียวกัน
                </div>
            @endif

            <div class="mb-3 grid gap-2 md:grid-cols-3">
                <div class="rounded-md border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <div class="text-xs text-slate-500">วันที่บันทึก</div>
                    <div class="text-base font-semibold text-slate-900">{{ thai_date($recordDate) }}</div>
                </div>
                <div class="rounded-md border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <div class="text-xs text-slate-500">ช่วงอายุไก่</div>
                    <div class="text-base font-semibold text-slate-900">
                        {{ $ageByHouse->min() === $ageByHouse->max() ? $ageByHouse->min().' วัน' : $ageByHouse->min().'-'.$ageByHouse->max().' วัน' }}
                    </div>
                </div>
                <div class="rounded-md border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <div class="text-xs text-slate-500">จำนวนเล้า</div>
                    <div class="text-base font-semibold text-slate-900">{{ number_format($houses->count()) }} เล้า</div>
                </div>
            </div>

            <form method="POST" action="{{ route('flocks.water-meters.store', $flock) }}">
                @csrf
                <input type="hidden" name="record_date" value="{{ $recordDate }}">

                <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-[860px] w-full table-fixed border-separate border-spacing-0 text-xs">
                            <colgroup>
                                <col class="w-[90px]">
                                <col class="w-[70px]">
                                <col class="w-[170px]">
                                <col class="w-[170px]">
                                <col class="w-[140px]">
                            </colgroup>
                            <thead class="sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">เล้า</th>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">อายุ</th>
                                    <th colspan="3" class="border-b border-sky-200 bg-sky-50 px-2 py-1.5 text-center font-semibold text-sky-800">มิเตอร์น้ำ / น้ำใช้</th>
                                </tr>
                                <tr>
                                    <th class="border-b border-r border-sky-200 bg-sky-50 px-2 py-1.5 text-center font-medium text-sky-700">เลขครั้งก่อน</th>
                                    <th class="border-b border-r border-sky-200 bg-sky-50 px-2 py-1.5 text-center font-medium text-sky-700">เลขมิเตอร์วันนี้</th>
                                    <th class="border-b border-sky-200 bg-sky-50 px-2 py-1.5 text-center font-medium text-sky-700">น้ำใช้วันนี้ (ลิตร)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($houses as $house)
                                    @php
                                        $record = $recordsByHouse->get($house->id);
                                        $previousRecord = $previousRecordsByHouse->get($house->id);
                                        $houseAgeDay = $ageByHouse->get($house->id, $ageDay);
                                        $field = 'meters.'.$house->id;
                                    @endphp
                                    <tr class="hover:bg-sky-50/50">
                                        <td class="whitespace-nowrap border-r border-slate-100 px-2 py-1.5 text-center font-medium text-slate-900">เล้า {{ $house->house_no }}</td>
                                        <td class="border-r border-slate-100 px-2 py-1.5 text-center text-slate-700">{{ $houseAgeDay }}</td>
                                        <td class="border-r border-sky-100 bg-sky-50/30 px-2 py-1.5 text-center font-semibold text-slate-700">
                                            {{ $previousRecord?->water_meter_reading !== null ? number_format((int) $previousRecord->water_meter_reading) : '-' }}
                                        </td>
                                        <td class="border-r border-sky-100 bg-sky-50/30 px-2 py-1.5 text-center">
                                            <input
                                                type="number"
                                                step="1"
                                                min="0"
                                                name="meters[{{ $house->id }}]"
                                                value="{{ old($field, $record?->water_meter_reading) }}"
                                                class="mx-auto block h-7 w-32 rounded-md border-slate-300 px-2 text-center text-xs font-semibold text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                            >
                                            <x-input-error class="mt-1 text-left" :messages="$errors->get($field)" />
                                        </td>
                                        <td class="bg-sky-50/30 px-2 py-1.5 text-center font-semibold text-sky-800">
                                            {{ number_format((int) ($record?->water_used ?? 0)) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">ยังไม่มีข้อมูลเล้าเริ่มต้นสำหรับรุ่นนี้</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <x-primary-button :disabled="$houses->isEmpty()">
                        บันทึกมิเตอร์น้ำ
                    </x-primary-button>
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
                    if (option.farmId === farmId) {
                        option.selected = firstVisible !== null && option.value === firstVisible.value;
                    } else {
                        option.selected = false;
                    }
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

