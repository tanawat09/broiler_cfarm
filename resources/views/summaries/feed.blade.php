<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">สรุปการใช้อาหารรายเล้า</h1>
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
                <form method="GET" action="{{ route('flocks.feed-summary', $flock) }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-[220px_260px_1fr_auto] items-end">
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
                                    data-url="{{ route('flocks.feed-summary', $option) }}"
                                    @selected((int) $option->id === (int) $flock->id)
                                >
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div></div>
                    <div class="flex gap-2">
                        <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            ค้นหา
                        </button>
                        <a href="{{ route('flocks.feed-summary', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            ล้าง
                        </a>
                    </div>
                </form>
            </div>

            <!-- Report Card -->
            <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-900">
                        ตารางสรุปปริมาณการใช้อาหารแต่ละเบอร์แยกตามเล้า
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border-collapse text-xs">
                        <thead class="bg-gray-100 font-semibold text-gray-700">
                            <tr class="divide-x divide-gray-200">
                                <th class="px-2 py-3 text-center sticky left-0 bg-gray-100 z-10 min-w-[60px]">เล้า</th>
                                @foreach ($feedCodes as $code)
                                    <th class="px-2 py-3 text-right bg-blue-50/50 min-w-[90px]">{{ $code }}</th>
                                @endforeach
                                <th class="px-2 py-3 text-right font-bold min-w-[90px]">รวม</th>
                                <th class="px-2 py-3 text-right font-bold min-w-[90px]">อาหารรวม</th>
                                <th class="px-2 py-3 text-right min-w-[80px]">ไก่เข้า</th>
                                <th class="px-2 py-3 text-right min-w-[120px]">Feed intake<br>/Bird Balance</th>
                                <th class="px-2 py-3 text-right min-w-[90px]">อาหารสะสม</th>
                                @foreach ($feedCodes as $code)
                                    <th class="px-2 py-3 text-right bg-emerald-50/30 min-w-[80px]">No{{ $code }}</th>
                                    <th class="px-2 py-3 text-right bg-emerald-50/30 min-w-[70px]">%</th>
                                @endforeach
                                <th class="px-2 py-3 text-right bg-emerald-50 font-bold min-w-[80px]">Total</th>
                                <th class="px-2 py-3 text-right bg-emerald-50 font-bold min-w-[70px]">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($rows as $row)
                                <tr class="hover:bg-slate-50 divide-x divide-gray-200">
                                    <td class="px-2 py-2.5 text-center font-bold text-gray-900 sticky left-0 bg-white group-hover:bg-slate-50">
                                        {{ $row['house']->house_no }}
                                    </td>
                                    @foreach ($feedCodes as $code)
                                        <td class="px-2 py-2.5 text-right font-medium">
                                            {{ $row['feeds'][$code] > 0 ? number_format($row['feeds'][$code]) : '-' }}
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2.5 text-right font-bold text-gray-900">
                                        {{ number_format($row['sum_feed']) }}
                                    </td>
                                    <td class="px-2 py-2.5 text-right font-bold text-gray-900">
                                        {{ number_format($row['sum_feed']) }}
                                    </td>
                                    <td class="px-2 py-2.5 text-right text-slate-700">
                                        {{ number_format($row['initial_birds']) }}
                                    </td>
                                    <td class="px-2 py-2.5 text-right font-medium text-slate-900">
                                        {{ number_format($row['feed_intake_per_bird'], 3) }}
                                    </td>
                                    <td class="px-2 py-2.5 text-right font-medium text-slate-900">
                                        {{ number_format($row['cumulative_feed'], 3) }}
                                    </td>
                                    @foreach ($feedCodes as $code)
                                        <td class="px-2 py-2.5 text-right text-emerald-800 bg-emerald-50/10">
                                            {{ number_format($row['feed_bags_ratio'][$code]['qty_per_bird'], 2) }}
                                        </td>
                                        <td class="px-2 py-2.5 text-right text-emerald-700 bg-emerald-50/10">
                                            {{ number_format($row['feed_bags_ratio'][$code]['bags_pct'], 2) }}
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2.5 text-right bg-emerald-50/30 font-bold text-emerald-900">
                                        {{ number_format($row['total_qty_per_bird'], 2) }}
                                    </td>
                                    <td class="px-2 py-2.5 text-right bg-emerald-50/30 font-bold text-emerald-800">
                                        {{ number_format($row['total_bags_pct'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($feedCodes) * 3 + 9 }}" class="px-4 py-12 text-center text-gray-500 text-sm">
                                        ไม่พบข้อมูลสรุปอาหารของรุ่นนี้
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($rows)
                            @php
                                $cnt = count($rows);
                                $avgIntake = $cnt > 0 ? array_sum(collect($rows)->pluck('feed_intake_per_bird')->toArray()) / $cnt : 0;
                                $weightedCum = $totals['initial_birds'] > 0 ? $totals['sum_feed'] / $totals['initial_birds'] : 0;
                                $avgTotalQty = $cnt > 0 ? array_sum(collect($rows)->pluck('total_qty_per_bird')->toArray()) / $cnt : 0;
                                $avgTotalBags = $cnt > 0 ? array_sum(collect($rows)->pluck('total_bags_pct')->toArray()) / $cnt : 0;
                            @endphp
                            <tfoot class="border-t-2 border-gray-300 font-bold text-gray-950">
                                <tr class="bg-gray-100 divide-x divide-gray-200">
                                    <td class="px-2 py-3 text-center sticky left-0 bg-gray-100">รวม</td>
                                    @foreach ($feedCodes as $code)
                                        <td class="px-2 py-3 text-right">
                                            {{ number_format($totals['feeds'][$code]) }}
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-3 text-right">{{ number_format($totals['sum_feed']) }}</td>
                                    <td class="px-2 py-3 text-right">{{ number_format($totals['sum_feed']) }}</td>
                                    <td class="px-2 py-3 text-right">{{ number_format($totals['initial_birds']) }}</td>
                                    <td class="px-2 py-3 text-right">{{ number_format($avgIntake, 3) }}</td>
                                    <td class="px-2 py-3 text-right">{{ number_format($weightedCum, 3) }}</td>
                                    @foreach ($feedCodes as $code)
                                        @php
                                            $avgQty = $cnt > 0 ? array_sum(collect($rows)->pluck('feed_bags_ratio.'.$code.'.qty_per_bird')->toArray()) / $cnt : 0;
                                            $avgBags = $cnt > 0 ? array_sum(collect($rows)->pluck('feed_bags_ratio.'.$code.'.bags_pct')->toArray()) / $cnt : 0;
                                        @endphp
                                        <td class="px-2 py-3 text-right text-emerald-900 bg-emerald-50/20">
                                            {{ number_format($avgQty, 2) }}
                                        </td>
                                        <td class="px-2 py-3 text-right text-emerald-800 bg-emerald-50/20">
                                            {{ number_format($avgBags, 2) }}
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-3 text-right bg-emerald-100/40 text-emerald-950">
                                        {{ number_format($avgTotalQty, 2) }}
                                    </td>
                                    <td class="px-2 py-3 text-right bg-emerald-100/40 text-emerald-900">
                                        {{ number_format($avgTotalBags, 2) }}
                                    </td>
                                </tr>
                                <tr class="bg-emerald-600 text-white divide-x divide-emerald-500">
                                    <td class="px-2 py-3 text-center sticky left-0 bg-emerald-700 text-white">ค่าอาหาร</td>
                                    @foreach ($feedCodes as $code)
                                        <td class="px-2 py-3 text-right font-bold">
                                            {{ $totals['feed_cost'][$code] > 0 ? number_format($totals['feed_cost'][$code]) : '-' }}
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-3 text-right font-bold" colspan="2">
                                        {{ number_format($totals['total_cost']) }}
                                    </td>
                                    <td colspan="5" class="bg-emerald-600"></td>
                                    @foreach ($feedCodes as $code)
                                        <td colspan="2" class="bg-emerald-600"></td>
                                    @endforeach
                                    <td colspan="2" class="bg-emerald-600"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Price Masters Display -->
            @if ($rows)
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                    @foreach ($feedCodes as $code)
                        <div class="rounded-md border border-slate-200 bg-white p-3 shadow-sm text-center">
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">ราคาอาหารเบอร์ {{ $code }}</span>
                            <span class="block mt-1 text-lg font-bold text-slate-900">{{ number_format($prices[$code], 2) }} <span class="text-xs font-normal text-slate-500">บาท/กก.</span></span>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');

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
                window.location.href = option.dataset.url;
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
