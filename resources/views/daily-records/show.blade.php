<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">อุณหภูมิ/ความชื้น/สูญเสียรายวัน</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $flock->flock_code }} | {{ $flock->farm->farm_name }}</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-end gap-3 justify-end w-full lg:w-auto">
                <form method="GET" action="{{ route('flocks.daily-records.index', $flock) }}" class="grid gap-2 grid-cols-3 min-w-[480px]">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($flockOptions as $option)
                                <option
                                    value="{{ $option->id }}"
                                    data-farm-id="{{ $option->farm_id }}"
                                    data-index-url="{{ route('flocks.daily-records.index', $option) }}"
                                    @selected((int) $option->id === (int) $flock->id)
                                >
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date" value="วันที่" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="$recordDate" />
                    </div>
                </form>

                @if($flock->status !== 'closed')
                    <div class="pt-5 sm:pt-0">
                        <a href="{{ route('daily-records.import-page') }}?farm_id={{ $flock->farm_id }}&flock_id={{ $flock->id }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition duration-150">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            นำเข้าไฟล์ข้อมูล (.txt)
                        </a>
                    </div>
                @endif
            </div>
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
                    กรุณาตรวจสอบข้อมูลในตารางอีกครั้ง
                </div>
            @endif

            <div class="mb-3 grid gap-2 md:grid-cols-3">
                <div class="rounded-md border border-gray-200 bg-white px-3 py-2 shadow-sm">
                    <div class="text-xs text-gray-500">วันที่บันทึก</div>
                    <div class="text-base font-semibold text-gray-900">{{ thai_date($recordDate) }}</div>
                </div>
                <div class="rounded-md border border-gray-200 bg-white px-3 py-2 shadow-sm">
                    <div class="text-xs text-gray-500">ช่วงอายุไก่</div>
                    <div class="text-base font-semibold text-gray-900">
                        {{ $ageByHouse->min() === $ageByHouse->max() ? $ageByHouse->min().' วัน' : $ageByHouse->min().'-'.$ageByHouse->max().' วัน' }}
                    </div>
                </div>
                <div class="rounded-md border border-gray-200 bg-white px-3 py-2 shadow-sm">
                    <div class="text-xs text-gray-500">จำนวนเล้า</div>
                    <div class="text-base font-semibold text-gray-900">{{ number_format($starts->count()) }} เล้า</div>
                </div>
            </div>

            <form method="POST" action="{{ route('flocks.daily-records.store', $flock) }}">
                @csrf
                <input type="hidden" name="record_date" value="{{ $recordDate }}">

                <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-[980px] w-full table-fixed border-separate border-spacing-0 text-xs">
                            <colgroup>
                                <col class="w-[90px]">
                                <col class="w-[64px]">
                                <col class="w-[120px]">
                                <col class="w-[120px]">
                                <col class="w-[130px]">
                                <col class="w-[110px]">
                                <col class="w-[110px]">
                                <col class="w-[110px]">
                                <col class="w-[110px]">
                            </colgroup>
                            <thead class="sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">เล้า</th>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">อายุ</th>
                                    <th colspan="2" class="border-b border-r border-orange-200 bg-orange-50 px-2 py-1.5 text-center font-semibold text-orange-800">อุณหภูมิ (°C)</th>
                                    <th rowspan="2" class="border-b border-r border-sky-200 bg-sky-50 px-2 py-2 text-center align-middle font-semibold text-sky-800">ความชื้น (%)</th>
                                    <th colspan="2" class="border-b border-r border-red-200 bg-red-50 px-2 py-1.5 text-center font-semibold text-red-800">ไก่ตาย</th>
                                    <th colspan="2" class="border-b border-slate-200 bg-violet-50 px-2 py-1.5 text-center font-semibold text-violet-800">ไก่คัดทิ้ง</th>
                                </tr>
                                <tr>
                                    <th class="border-b border-orange-200 bg-orange-50 px-2 py-1.5 text-center font-medium text-orange-700">ต่ำสุด</th>
                                    <th class="border-b border-r border-orange-200 bg-orange-50 px-2 py-1.5 text-center font-medium text-orange-700">สูงสุด</th>
                                    <th class="border-b border-red-200 bg-red-50 px-2 py-1.5 text-center font-medium text-red-700">เช้า</th>
                                    <th class="border-b border-r border-red-200 bg-red-50 px-2 py-1.5 text-center font-medium text-red-700">เย็น</th>
                                    <th class="border-b border-violet-200 bg-violet-50 px-2 py-1.5 text-center font-medium text-violet-700">เช้า</th>
                                    <th class="border-b border-violet-200 bg-violet-50 px-2 py-1.5 text-center font-medium text-violet-700">เย็น</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($starts as $start)
                                    @php
                                        $house = $start->house;
                                        $record = $recordsByHouse->get($house->id);
                                        $houseAgeDay = $ageByHouse->get($house->id, $ageDay);
                                        $prefix = 'records.'.$house->id.'.';
                                    @endphp
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="whitespace-nowrap border-r border-slate-100 px-2 py-1.5 text-center font-medium text-gray-900">เล้า {{ $house->house_no }}</td>
                                        <td class="border-r border-slate-100 px-2 py-1.5 text-center text-gray-700">{{ $houseAgeDay }}</td>
                                        <td class="bg-orange-50/30 px-2 py-1.5 text-center">
                                            <input type="number" step="0.01" name="records[{{ $house->id }}][temp_min]" value="{{ old($prefix.'temp_min', $record?->temp_min) }}" class="mx-auto block h-7 w-20 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                        <td class="border-r border-orange-100 bg-orange-50/30 px-2 py-1.5 text-center">
                                            <input type="number" step="0.01" name="records[{{ $house->id }}][temp_max]" value="{{ old($prefix.'temp_max', $record?->temp_max) }}" class="mx-auto block h-7 w-20 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            <x-input-error class="mt-1 text-left" :messages="$errors->get($prefix.'temp_max')" />
                                        </td>
                                        <td class="border-r border-sky-100 bg-sky-50/40 px-2 py-1.5 text-center">
                                            <input type="number" step="0.01" min="0" max="100" name="records[{{ $house->id }}][humidity]" value="{{ old($prefix.'humidity', $record?->humidity) }}" class="mx-auto block h-7 w-20 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                        <td class="bg-red-50/30 px-2 py-1.5 text-center">
                                            <input type="number" step="1" min="0" name="records[{{ $house->id }}][dead_morning]" value="{{ old($prefix.'dead_morning', $record?->dead_morning ?? 0) }}" class="mx-auto block h-7 w-16 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                        <td class="border-r border-red-100 bg-red-50/30 px-2 py-1.5 text-center">
                                            <input type="number" step="1" min="0" name="records[{{ $house->id }}][dead_evening]" value="{{ old($prefix.'dead_evening', $record?->dead_evening ?? 0) }}" class="mx-auto block h-7 w-16 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                        <td class="bg-violet-50/30 px-2 py-1.5 text-center">
                                            <input type="number" step="1" min="0" name="records[{{ $house->id }}][cull_morning]" value="{{ old($prefix.'cull_morning', $record?->cull_morning ?? 0) }}" class="mx-auto block h-7 w-16 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                        <td class="bg-violet-50/30 px-2 py-1.5 text-center">
                                            <input type="number" step="1" min="0" name="records[{{ $house->id }}][cull_evening]" value="{{ old($prefix.'cull_evening', $record?->cull_evening ?? 0) }}" class="mx-auto block h-7 w-16 rounded-md border-gray-300 px-2 text-center text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                            ยังไม่มีข้อมูลเล้าเริ่มต้นสำหรับรุ่นนี้
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <x-primary-button :disabled="$starts->isEmpty()">
                        บันทึกทั้งหมด
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');
            const dateInput = document.getElementById('date');

            if (!farmSelector || !flockSelector) {
                return;
            }

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

                        if (option.selected || firstVisible === null) {
                            firstVisible = option;
                        }
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
                if (!option?.dataset.indexUrl) {
                    return;
                }

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
