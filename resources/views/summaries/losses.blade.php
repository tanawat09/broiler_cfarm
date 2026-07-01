<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">รายงานการสูญเสียรายวัน (ไก่ตาย / ไก่คัด)</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $flock->flock_code }} | {{ $flock->farm->farm_name }}</p>
            </div>
            <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                กลับรายละเอียดรุ่น
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1600px] px-3 sm:px-4 lg:px-6">
            
            <!-- Filters -->
            <div class="mb-6 rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('flocks.losses', $flock) }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-[220px_260px_180px_180px_1fr_auto] items-end">
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
                            @foreach ($flockOptions->where('farm_id', $flock->farm_id) as $option)
                                <option
                                    value="{{ $option->id }}"
                                    data-farm-id="{{ $option->farm_id }}"
                                    data-url="{{ route('flocks.losses', $option) }}"
                                    @selected((int) $option->id === (int) $flock->id)
                                >
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="start_date" value="วันที่เริ่มต้น" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full text-sm" :value="$startDate" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="วันที่สิ้นสุด" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full text-sm" :value="$endDate" />
                    </div>
                    <div>
                        <x-input-label value="ประเภทความสูญเสีย" />
                        <div class="mt-1 inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'total']) }}" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-l-md {{ $selectedType === 'total' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                ตาย + คัด
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'dead']) }}" class="px-4 py-2 text-sm font-medium border-t border-b border-gray-300 {{ $selectedType === 'dead' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                ตายอย่างเดียว
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'cull']) }}" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-r-md {{ $selectedType === 'cull' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                คัดอย่างเดียว
                            </a>
                        </div>
                        <input type="hidden" name="type" value="{{ $selectedType }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            ค้นหา
                        </button>
                        <a href="{{ route('flocks.losses', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            ล้าง
                        </a>
                    </div>
                </form>
            </div>

            <!-- Report Matrix Card -->
            <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">
                        ตารางสรุปความสูญเสียสะสมแยกตามเล้า (หน่วย: ตัว) 
                        <span class="text-xs font-normal text-gray-500 ml-2">
                            (ประเภท: {{ $selectedType === 'dead' ? 'ตายอย่างเดียว' : ($selectedType === 'cull' ? 'คัดอย่างเดียว' : 'ตายรวมคัด') }})
                        </span>
                    </h3>
                    <div class="text-sm font-semibold text-emerald-700">
                        สูญเสียรวมทั้งหมด: {{ number_format($grandTotal) }} ตัว
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border-collapse text-xs">
                        <thead class="bg-gray-100 font-semibold text-gray-700">
                            <tr class="divide-x divide-gray-200">
                                <th class="px-3 py-3 text-center sticky left-0 bg-gray-100 z-10 min-w-[120px]">วันที่บันทึก</th>
                                @foreach ($houses as $start)
                                    <th class="px-2 py-3 text-center min-w-[90px]">
                                        เล้า {{ $start->house->house_no }}
                                        <div class="text-[10px] font-normal text-gray-500">
                                            (เริ่ม: {{ number_format($start->initial_birds) }})
                                        </div>
                                    </th>
                                @endforeach
                                <th class="px-3 py-3 text-right bg-emerald-50 text-emerald-800 font-bold min-w-[100px]">ยอดรวม/วัน</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($matrix as $row)
                                <tr class="hover:bg-slate-50 divide-x divide-gray-200">
                                    <td class="px-3 py-2 text-center font-medium text-gray-900 sticky left-0 bg-white group-hover:bg-slate-50 whitespace-nowrap">
                                        {{ thai_date($row['date']) }}
                                    </td>
                                    @foreach ($houses as $start)
                                        @php 
                                            $cell = $row['houses'][$start->house_id];
                                            $val = $cell['value'];
                                            $age = $cell['age'];
                                            $bgColor = $val > 50 ? 'bg-red-100 text-red-900' : ($val > 10 ? 'bg-amber-50 text-amber-950' : '');
                                        @endphp
                                        <td class="px-2 py-2 text-center {{ $bgColor }}">
                                            @if ($cell['has_record'])
                                                <div class="font-bold">{{ number_format($val) }}</div>
                                                <div class="text-[10px] text-gray-400">วันที {{ $age }}</div>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2 text-right bg-emerald-50/50 text-emerald-950 font-bold whitespace-nowrap">
                                        {{ number_format($row['total']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $houses->count() + 2 }}" class="px-4 py-12 text-center text-gray-500 text-sm">
                                        ไม่พบข้อมูลการบันทึกประจำวันของรุ่นนี้ในช่วงเวลาที่เลือก
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($matrix)
                            <tfoot class="bg-gray-100 border-t-2 border-gray-300 font-bold text-gray-950">
                                <tr class="divide-x divide-gray-200">
                                    <td class="px-3 py-3 text-center sticky left-0 bg-gray-100 font-bold">รวมทั้งหมด</td>
                                    @foreach ($houses as $start)
                                        <td class="px-2 py-3 text-center bg-gray-50/80 font-bold text-slate-800 text-sm">
                                            {{ number_format($totalsByHouse[$start->house_id]) }}
                                            <div class="text-[9px] font-normal text-gray-500 mt-0.5">
                                                ({{ number_format(($totalsByHouse[$start->house_id] / $start->initial_birds) * 100, 2) }}%)
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-3 text-right bg-emerald-100 text-emerald-900 text-sm font-bold">
                                        {{ number_format($grandTotal) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (!farmSelector || !flockSelector) {
                return;
            }

            // Sync selectors in same way as daily summary
            const flockOptions = [];
            flockSelector.querySelectorAll('option').forEach(opt => {
                flockOptions.push({
                    value: opt.value,
                    text: opt.text,
                    farmId: opt.dataset.farmId,
                    url: opt.dataset.url,
                    selected: opt.selected
                });
            });

            const showOptionsForFarm = (farmId) => {
                let firstVisible = null;
                flockSelector.innerHTML = '';

                flockOptions
                    .filter((option) => option.farmId === farmId)
                    .forEach((optionData) => {
                        const option = new Option(optionData.text, optionData.value);
                        option.dataset.farmId = optionData.farmId;
                        option.dataset.url = optionData.url;
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

                return firstVisible;
            };

            const redirectToReport = (option) => {
                if (!option?.dataset.url) {
                    return;
                }

                const url = new URL(option.dataset.url, window.location.origin);
                url.searchParams.set('type', '{{ $selectedType }}');

                if (startDateInput?.value) {
                    url.searchParams.set('start_date', startDateInput.value);
                }

                if (endDateInput?.value) {
                    url.searchParams.set('end_date', endDateInput.value);
                }

                window.location.href = url.toString();
            };

            farmSelector.addEventListener('change', () => {
                redirectToReport(showOptionsForFarm(farmSelector.value));
            });

            flockSelector.addEventListener('change', () => {
                redirectToReport(flockSelector.selectedOptions[0]);
            });
        });
    </script>
</x-app-layout>
