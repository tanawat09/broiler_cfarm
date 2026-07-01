<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">ลงน้ำหนักไก่</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->flock_code }} | {{ $flock->farm->farm_name }}</p>
            </div>
            <form method="GET" action="{{ route('flocks.weight-records.index', $flock) }}" class="grid w-full gap-2 sm:grid-cols-3 lg:w-auto lg:min-w-[680px]">
                <div>
                    <x-input-label for="wr_farm_selector" value="เลือกฟาร์ม" />
                    <select id="wr_farm_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($farms as $farm)
                            <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="wr_flock_selector" value="เลือกรุ่น" />
                    <select id="wr_flock_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($flockOptions as $option)
                            <option
                                value="{{ $option->id }}"
                                data-farm-id="{{ $option->farm_id }}"
                                data-index-url="{{ route('flocks.weight-records.index', $option) }}"
                                @selected((int) $option->id === (int) $flock->id)
                            >
                                {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="wr_date" value="วันที่" />
                    <x-text-input id="wr_date" name="date" type="date" class="mt-1 block w-full sm:w-44" :value="$recordDate" />
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
                    กรุณาตรวจสอบค่าน้ำหนักอีกครั้ง
                </div>
            @endif

            {{-- Info Cards --}}
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

            <form method="POST" action="{{ route('flocks.weight-records.store', $flock) }}">
                @csrf
                <input type="hidden" name="record_date" value="{{ $recordDate }}">

                <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-[780px] w-full table-fixed border-separate border-spacing-0 text-xs">
                            <colgroup>
                                <col class="w-[90px]">
                                <col class="w-[70px]">
                                <col class="w-[80px]">
                                <col class="w-[140px]">
                                <col class="w-[160px]">
                                <col class="w-[140px]">
                            </colgroup>
                            <thead class="sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">เล้า</th>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">อายุ</th>
                                    <th rowspan="2" class="border-b border-r border-slate-200 bg-slate-100 px-2 py-2 text-center align-middle font-semibold text-slate-700">เพศ</th>
                                    <th colspan="3" class="border-b border-indigo-200 bg-indigo-50 px-2 py-1.5 text-center font-semibold text-indigo-800">น้ำหนักเฉลี่ย (กิโลกรัม)</th>
                                </tr>
                                <tr>
                                    <th class="border-b border-r border-indigo-200 bg-indigo-50 px-2 py-1.5 text-center font-medium text-indigo-700">ครั้งก่อน</th>
                                    <th class="border-b border-r border-indigo-200 bg-indigo-50 px-2 py-1.5 text-center font-medium text-indigo-700">น้ำหนักวันนี้</th>
                                    <th class="border-b border-indigo-200 bg-indigo-50 px-2 py-1.5 text-center font-medium text-indigo-700">ผลต่าง</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse ($houses as $house)
                                    @php
                                        $record = $recordsByHouse->get($house->id);
                                        $previousRecord = $previousWeightByHouse->get($house->id);
                                        $houseAgeDay = $ageByHouse->get($house->id, $ageDay);
                                        $placement = $placementsByHouse->get($house->id);
                                        $field = 'weights.'.$house->id;
                                        $prevWeight = $previousRecord?->avg_weight !== null ? (float) $previousRecord->avg_weight : null;
                                        $currentWeight = old($field, $record?->avg_weight);
                                    @endphp
                                    <tr class="hover:bg-indigo-50/50 weight-row" data-prev-weight="{{ $prevWeight }}">
                                        <td class="whitespace-nowrap border-r border-slate-100 px-2 py-1.5 text-center font-medium text-slate-900">เล้า {{ $house->house_no }}</td>
                                        <td class="border-r border-slate-100 px-2 py-1.5 text-center text-slate-700">{{ $houseAgeDay }}</td>
                                        <td class="border-r border-slate-100 px-2 py-1.5 text-center text-slate-700">{{ $placement?->sex ?: '-' }}</td>
                                        <td class="border-r border-indigo-100 bg-indigo-50/30 px-2 py-1.5 text-center font-semibold text-slate-700">
                                            @if ($prevWeight !== null)
                                                {{ number_format($prevWeight, 3) }}
                                                <span class="block text-[10px] text-slate-400">{{ thai_date($previousRecord->record_date->toDateString()) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="border-r border-indigo-100 bg-indigo-50/30 px-2 py-1.5 text-center">
                                            <input
                                                data-weight-input
                                                type="number"
                                                step="0.001"
                                                min="0"
                                                max="99.999"
                                                name="weights[{{ $house->id }}]"
                                                value="{{ $currentWeight }}"
                                                placeholder="0.000"
                                                class="mx-auto block h-7 w-28 rounded-md border-slate-300 px-2 text-center text-xs font-semibold text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                            <x-input-error class="mt-1 text-left" :messages="$errors->get($field)" />
                                        </td>
                                        <td class="bg-indigo-50/30 px-2 py-1.5 text-center font-semibold" data-diff-cell>
                                            -
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">ยังไม่มีข้อมูลเล้าเริ่มต้นสำหรับรุ่นนี้</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <x-primary-button :disabled="$houses->isEmpty()">
                        บันทึกน้ำหนักไก่
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.weight-row').forEach(row => {
            const input = row.querySelector('[data-weight-input]');
            const diffCell = row.querySelector('[data-diff-cell]');
            const prevWeight = parseFloat(row.dataset.prevWeight) || null;

            function updateDiff() {
                const current = parseFloat(input.value) || null;
                if (current !== null && prevWeight !== null) {
                    const diff = current - prevWeight;
                    const sign = diff >= 0 ? '+' : '';
                    diffCell.textContent = sign + diff.toFixed(3);
                    diffCell.className = 'bg-indigo-50/30 px-2 py-1.5 text-center font-semibold ' +
                        (diff > 0 ? 'text-emerald-600' : diff < 0 ? 'text-red-600' : 'text-slate-600');
                } else {
                    diffCell.textContent = '-';
                    diffCell.className = 'bg-indigo-50/30 px-2 py-1.5 text-center font-semibold text-slate-400';
                }
            }

            input.addEventListener('input', updateDiff);
            updateDiff();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('wr_farm_selector');
            const flockSelector = document.getElementById('wr_flock_selector');
            const dateInput = document.getElementById('wr_date');

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
